<?php
namespace Dadeky\Ddd\Application\Notification;

use Ddd\Application\Notification\MessageProducer;
use Ddd\Application\Notification\PublishedMessageTracker;
use Ddd\Domain\Event\StoredEvent;
use JMS\Serializer\SerializerBuilder;

class NotificationService
{
    const MAX_LIMIT = 200;
    
    private $serializer;
    private $eventStore;
    private $publishedMessageTracker;
    private $messageProducer;
    
    public function __construct(
        $anEventStore,
        PublishedMessageTracker $aPublishedMessageTracker,
        MessageProducer $aMessageProducer
    ){
        $this->eventStore = $anEventStore;
        $this->publishedMessageTracker = $aPublishedMessageTracker;
        $this->messageProducer = $aMessageProducer;
    }
    
    public function publishNotifications($exchangeName, $limit)
    {
        if ($limit > self::MAX_LIMIT)
            throw new \Exception(sprintf('Maximum limit of %1$s exceeded.', self::MAX_LIMIT));
        
        $publishedMessageTracker = $this->publishedMessageTracker;
        /** @var \Ddd\Domain\Event\StoredEvent[] $notifications */
        $notifications = $this->eventStore->allStoredEventsSinceAnEventIdLimited(
            $publishedMessageTracker->mostRecentPublishedMessageId($exchangeName), 
            $limit);
        
        if (!$notifications) {
            return 0;
        }
        
        $messageProducer = $this->messageProducer;
        $messageProducer->open($exchangeName);
        try {
            $publishedMessages = 0;
            $lastPublishedNotification = null;
            foreach ($notifications as $notification) {
                $lastPublishedNotification = $this->publish($exchangeName, $notification, $messageProducer);
                $this->publishedMessageTracker->trackMostRecentPublishedMessage($exchangeName, $lastPublishedNotification);
                echo sprintf('Message %1$s sent to exchange.', $notification->eventId()) . PHP_EOL;
                $publishedMessages++;
            }
        } catch(\Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }
        return $publishedMessages;
    }
    
    private function publish($exchangeName, StoredEvent $notification, MessageProducer $messageProducer)
    {
        $messageProducer->send(
            $exchangeName,
            $this->serializer()->serialize($notification, 'json'),
            $notification->typeName(),
            $notification->eventId(),
            $notification->occurredOn()
            );
        
        return $notification;
    }
    
    /**
     * @return \JMS\Serializer\Serializer
     */
    private function serializer()
    {
        if (null === $this->serializer) {
            $this->serializer =
            SerializerBuilder::create()
            ->addMetadataDir(__DIR__ . '/../../Infrastructure/Application/Serialization/JMS/Config')
            ->setCacheDir(__DIR__ . '/../../../var/cache/jms-serializer')
            ->build()
            ;
        }
        
        return $this->serializer;
    }
}

