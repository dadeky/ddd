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
     * @param integer $delay Delay in seconds
     * @return StoredEvent[]
     */
    public function allStoredEventsSinceAnEventIdDelayed($anEventId, $delay)
    {
        $currentTime = new \DateTimeImmutable();
        $currentTimeMinusDelay = $currentTime->sub(\DateInterval::createFromDateString($delay.' seconds'));
        $anEventId = (int) $anEventId;
        $query = $this->eventStore->createQueryBuilder('e');
        if ($anEventId) {
            $query->where('e.eventId > :eventId');
            $query->andWhere('e.occurredOn < :aTime');
            $query->setParameters(array('eventId' => $anEventId, 'aTime' => $currentTimeMinusDelay));
        }
        $query->orderBy('e.eventId');
        
        return $query->getQuery()->getResult();
    }
}

