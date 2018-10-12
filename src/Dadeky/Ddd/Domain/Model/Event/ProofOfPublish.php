<?php
namespace Dadeky\Ddd\Domain\Model\Event;

use Dadeky\Ddd\Domain\IdentifiableValueObject;

class ProofOfPublish extends IdentifiableValueObject
{
    /** @var string */
    private $exchangeName;
    
    /** @var \DateTime */
    private $publishedOn;
    
    /** @var ProvedStoredEvent */
    private $storedEvent;
    
    public function __construct(
        ProvedStoredEvent $storedEvent, $exchangeName
    ){
        $this->setStoredEvent($storedEvent);
        $this->setExchangeName($exchangeName);
        $this->setPublishedOn(new \DateTime());
    }
    
    /**
     * @return string
     */
    public function getExchangeName()
    {
        return $this->exchangeName;
    }

    /**
     * @return \DateTime
     */
    public function getPublishedOn()
    {
        return $this->publishedOn;
    }

    /**
     * @return ProvedStoredEvent
     */
    public function getStoredEvent()
    {
        return $this->storedEvent;
    }

    /**
     * @param string $exchangeName
     */
    public function setExchangeName($exchangeName)
    {
        $this->exchangeName = $exchangeName;
    }

    /**
     * @param \DateTime $publishedOn
     */
    public function setPublishedOn($publishedOn)
    {
        $this->publishedOn = $publishedOn;
    }

    /**
     * @param ProvedStoredEvent $storedEvent
     */
    public function setStoredEvent($storedEvent)
    {
        $this->storedEvent = $storedEvent;
    }
}

