<?php
namespace Dadeky\Ddd\Infrastructure\Application\Notification;

use Doctrine\ORM\EntityRepository;
use Ddd\Application\EventStore;
use JMS\Serializer\SerializerBuilder;
use Dadeky\Ddd\Domain\Model\Event\ProvedStoredEvent;

class DoctrineProvedEventStore extends EntityRepository implements EventStore
{
    private $serializer;
    
    /**
     * 
     * {@inheritDoc}
     * @see \Ddd\Application\EventStore::append()
     */
    public function append($aDomainEvent)
    {
        $storedEvent = new ProvedStoredEvent(
            get_class($aDomainEvent),
            $aDomainEvent->occurredOn(),
            $this->serializer()->serialize($aDomainEvent, 'json')
            );
        
        $this->getEntityManager()->persist($storedEvent);
        $this->getEntityManager()->flush($storedEvent);
    }
    
    public function allStoredEventsSince($anEventId)
    {
        $query = $this->createQueryBuilder('e');
        if ($anEventId) {
            $query->where('e.eventId > :eventId');
            $query->setParameters(array('eventId' => $anEventId));
        }
        $query->orderBy('e.eventId');

        return $query->getQuery()->getResult();
    }

    public function allUnpublishedStoredEvents($exchangeName)
    {
        // SELECT pe.*
        // FROM ddd_proved_domain_events AS pe
        // LEFT JOIN ddd_proofs_of_publish AS pop ON pop.event_id = pe.event_id AND pop.exchange_name = ?
        // WHERE pop.published_on IS NULL;
        
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('event')
            ->from('Dadeky\Ddd\Domain\Model\Event\ProvedStoredEvent', 'event')
            ->leftJoin('event.proofsOfPublish', 'pop', Expr\Join::WITH, $qb->expr()->andX( $qb->expr()->eq('pop.exchangeName', ':exchangeName') ))
            ->where($qb->expr()->isNull('pop.publishedOn'))
            ->orderBy('event.eventId', 'ASC')
            ->setParameter('exchangeName', $exchangeName)
        ;
             
        $query = $qb->getQuery();
        return $query->getResult();
    }
    
    public function saveNotification($notification)
    {
        $this->getEntityManager()->persist($notification);
        $this->getEntityManager()->flush($notification);
    }
    
    /**
     * @return \JMS\Serializer\Serializer
     */
    private function serializer()
    {
        if (null === $this->serializer) {
            $this->serializer =
                SerializerBuilder::create()
                    ->addMetadataDir(__DIR__ . '/../../../Infrastructure/Application/Serialization/JMS/Config')
                    ->setCacheDir(__DIR__ . '/../../../../var/cache/jms-serializer')
                ->build()
            ;
        }

        return $this->serializer;
    }
}

