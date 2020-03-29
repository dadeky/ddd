<?php
namespace Dadeky\Ddd\Domain;

use Ramsey\Uuid\Uuid;

abstract class DomainUuid
{
    private $id;
    
    public function __construct($anId = null){
        $this->id = $anId ?: Uuid::uuid4()->toString();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public static function create($anId = null)
    {
        return new static($anId);
    }
    
    public function __toString()
    {
        return $this->id;
    }
}

