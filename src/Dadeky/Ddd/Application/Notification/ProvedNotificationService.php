<?php
namespace Dadeky\Ddd\Application\Notification;

use Ddd\Application\EventStore;
use Ddd\Application\Notification\MessageProducer;
use JMS\Serializer\SerializerBuilder;
use Ddd\Domain\DomainEvent;

class ProvedNotificationService
{
    private $messageProducer;
    private $serializer;
    private $eventStore;
    
    public function __construct(
        EventStore $anEventStore, 
        MessageProducer $aMessageProducer)
    {
        $this->messageProducer = $aMessageProducer;
        $this->eventStore = $anEventStore;
    }
    
    /**
     * @return int
     */
    public function publishNotifications($exchangeName)
    {
        $notifications = $this->eventStore->allUnpublishedStoredEvents($exchangeName);
        
        if (!$notifications) {
            return 0;
        }
        
        $messageProducer = $this->messageProducer();
        $messageProducer->open($exchangeName);
        try {
            $publishedMessages = 0;
            /** @var \Dadeky\Ddd\Domain\Model\Event\ProvedStoredEvent $notification */
            foreach ($notifications as $notification) {
                $this->publish($exchangeName, $notification, $messageProducer);
                $notification->addProofOfPublish($exchangeName);
                $this->eventStore->saveNotification($notification);
                echo sprintf('Message %1$s sent to exchange.', $notification->eventId()) . PHP_EOL;
                $publishedMessages++;
            }
        } catch(\Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }
        
        return $publishedMessages;
    }
    
    private function publish($exchangeName, DomainEvent $notification, MessageProducer $messageProducer)
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
            ->setCacheDir(__DIR__ . '/../../../../../var/cache/jms-serializer')
            ->build()
            ;
        }
        
        return $this->serializer;
    }
    
    private function messageProducer()
    {
        return $this->messageProducer;
    }
    
}