<?php
namespace Dadeky\Ddd\Infrastructure\Application\Notification;

use Ddd\Domain\Event\StoredEvent;
use Ddd\Infrastructure\Application\Notification\DoctrineEventStore as BaseEventStore;

class DoctrineEventStore
{   
    /** @var BaseEventStore */ 
    private $eventStore;

    public function __construct(
        BaseEventStore $eventStore
    ){
        $this->eventStore = $eventStore;
    }
    
    /**
     * @param integer $anEventId
     * @param integer $limit
     * @return StoredEvent[]
     */
    public function allStoredEventsSinceAnEventIdLimited($anEventId, $limit)
    {
        $anEventId = (int) $anEventId;
        $limit = (int) $limit;
        $query = $this->eventStore->createQueryBuilder('e');
        if ($anEventId) {
            $query->where('e.eventId > :eventId');
            $query->setParameters(array('eventId' => $anEventId));
        }
        $query->setMaxResults($limit);
        $query->orderBy('e.eventId');
        
        return $query->getQuery()->getResult();
    }
}

