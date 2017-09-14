<?php
namespace Dadeky\Application\Notification;

use Ddd\Application\EventStore;
use Ddd\Application\Notification\MessageProducer;
use Ddd\Application\Notification\NotificationService;
use Ddd\Application\Notification\PublishedMessageTracker;
use Ddd\Domain\Event\StoredEvent;
use JMS\Serializer\SerializerBuilder;

class CustomNotificationService extends NotificationService
{
    private $messageProducer;
    
    public function __construct(
        EventStore $anEventStore, 
        PublishedMessageTracker $aPublishedMessageTracker, 
        MessageProducer $aMessageProducer)
    {
        parent::__construct($anEventStore, $aPublishedMessageTracker, $aMessageProducer);
        $this->messageProducer = $aMessageProducer;
    }
    
    /**
     * @return int
     */
    public function publishNotifications($exchangeName)
    {
        $publishedMessageTracker = $this->publishedMessageTracker();
        $notifications = $this->listUnpublishedNotifications(
            $publishedMessageTracker->mostRecentPublishedMessageId($exchangeName)
            );
        
        if (!$notifications) {
            return 0;
        }
        
        $messageProducer = $this->messageProducer();
        $messageProducer->open($exchangeName);
        try {
            $publishedMessages = 0;
            $lastPublishedNotification = null;
            foreach ($notifications as $notification) {
                $lastPublishedNotification = $this->publish($exchangeName, $notification, $messageProducer);
                $publishedMessages++;
            }
        } catch(\Exception $e) {
            
        }
        
        $this->trackMostRecentPublishedMessage($publishedMessageTracker, $exchangeName, $lastPublishedNotification);
        //$messageProducer->close($exchangeName);
        
        return $publishedMessages;
    }
    
    /**
     * @param $mostRecentPublishedMessageId
     * @return StoredEvent[]
     */
    private function listUnpublishedNotifications($mostRecentPublishedMessageId)
    {
        $storeEvents = $this->eventStore()->allStoredEventsSince($mostRecentPublishedMessageId);
        
        // Vaughn Vernon converts StoredEvents into another objects: Notification
        // Is it really needed?
        
        return $storeEvents;
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
    
    private function trackMostRecentPublishedMessage(PublishedMessageTracker $publishedMessageTracker, $exchangeName, $notification)
    {
        $publishedMessageTracker->trackMostRecentPublishedMessage($exchangeName, $notification);
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
    
    private function messageProducer()
    {
        return $this->messageProducer;
    }
    
}