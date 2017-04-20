<?php
namespace Dadeky\Ddd\Domain\Exception;

class DomainException extends \Exception
{
	private $parameters = array();
	
	public function __construct($message, $parameters)
	{
		$this->parameters = $parameters;
		parent::__construct($message);
	}
	
	public function getParameters()
	{
		return $this->parameters;
	}
}