<?php
namespace src\Dadeky\Application;

use Ddd\Application\EventStore;
use Ddd\Application\Notification\MessageProducer;
use Ddd\Application\Notification\NotificationService;
use Ddd\Application\Notification\PublishedMessageTracker;

class CustomNotificationService extends NotificationService
{

    public function __construct(
        EventStore $anEventStore, 
        PublishedMessageTracker $aPublishedMessageTracker, 
        MessageProducer $aMessageProducer)
    {
        parent::__construct($anEventStore, $aPublishedMessageTracker, $aMessageProducer);
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
    
}

