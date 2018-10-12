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
//         SELECT *
//         FROM zend_apps.ddd_proved_domain_events as `event`
//         where NOT EXISTS (
//             SELECT * FROM zend_apps.ddd_proofs_of_publish
//             WHERE event_id = `event`.`event_id`
//             and exchange_name = 'ppa_bc'
//         )
//         ;
        
        $sub = $this->getEntityManager()->createQueryBuilder();
        $sub->select('pop')
            ->from('Dadeky\Ddd\Domain\Model\Event\ProofOfPublish', 'pop')
            ->join('pop.storedEvent', 'ev')
            ->where('ev.eventId = event.eventId')
            ->andWhere('pop.exchangeName = :exchangeName')
            ;
        
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('event')
            ->from('Dadeky\Ddd\Domain\Model\Event\ProvedStoredEvent', 'event')
            ->where( $qb->expr()->not($qb->expr()->exists(
                $sub->getDQL()
            )))
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

