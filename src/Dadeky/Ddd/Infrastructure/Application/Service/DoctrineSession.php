<?php
namespace Dadeky\Ddd\Infrastructure\Application\Service;

use Ddd\Application\Service\TransactionalSession;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineSession implements TransactionalSession
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    
    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * 
     * {@inheritDoc}
     * @see \Ddd\Application\Service\TransactionalSession::executeAtomically()
     */
    public function executeAtomically(callable $operation)
    {
        return $this->entityManager->transactional($operation);
    }
}

