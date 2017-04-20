<?php
namespace Dadeky\Ddd\Domain;

abstract class IdentifiableDomainObject {
	
	protected $id;
	
	protected function id()
	{
		return $this->id;
	}
		
	protected function setId($anId)
	{
		$this->id = $anId;
	}
}