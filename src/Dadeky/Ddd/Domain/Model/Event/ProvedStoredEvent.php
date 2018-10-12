<?php
namespace Dadeky\Ddd\Domain\Model\Event;

use Ddd\Domain\DomainEvent;
use Doctrine\Common\Collections\ArrayCollection;

class ProvedStoredEvent implements DomainEvent
{
    /**
     * @var int
     */
    private $eventId;
    
    /**
     * @var string
     */
    private $eventBody;
    
    /**
     * @var \DateTime
     */
    private $occurredOn;
    
    /**
     * @var string
     */
    private $typeName;
    
    /** @var ProofOfPublish[] */
    private $proofsOfPublish;
    
    /**
     * @param string $aTypeName
     * @param \DateTime $anOccurredOn
     * @param string $anEventBody
     */
    public function __construct($aTypeName, \DateTime $anOccurredOn, $anEventBody)
    {
        $this->eventBody = $anEventBody;
        $this->typeName = $aTypeName;
        $this->occurredOn = $anOccurredOn;
        $this->proofsOfPublish = new ArrayCollection();
    }
    
    public function addProofOfPublish($exchangeName)
    {
        if (!$this->isPublished($exchangeName))
            $this->proofsOfPublish[] = new ProofOfPublish($this, $exchangeName);
    }
    
    public function isPublished($exchangeName)
    {
        $proofsOfPublish = $this->getProofsOfPublish();
        foreach ($proofsOfPublish as $proofOfPublish){
            if ($proofOfPublish->getExchangeName() == $exchangeName)
                return true;
        }
        return false;
    }
    
    /**
     * @return ProofOfPublish[]
     */
    public function getProofsOfPublish()
    {
        return $this->proofsOfPublish;
    }
    
    /**
     * @return string
     */
    public function eventBody()
    {
        return $this->eventBody;
    }
    
    /**
     * @return int
     */
    public function eventId()
    {
        return $this->eventId;
    }
    
    /**
     * @return string
     */
    public function typeName()
    {
        return $this->typeName;
    }
    
    /**
     * @return \DateTime
     */
    public function occurredOn()
    {
        return $this->occurredOn;
    }
    
}

