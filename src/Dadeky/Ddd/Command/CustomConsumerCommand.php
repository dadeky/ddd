<?php
namespace src\Dadeky\Ddd\Command;

use OldSound\RabbitMqBundle\Command\BaseConsumerCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CustomConsumerCommand extends BaseConsumerCommand
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            parent::execute($input, $output);
        }catch (\Dadeky\Ddd\Domain\Exception\DomainException $dex){
            $mess = $dex->getMessage();
        }
    }
    
    protected function configure()
    {
        parent::configure();
        $this->setDescription('Executes a custom consumer');
        $this->setName('rabbitmq:custom:consumer');
    }
    
    protected function getConsumerService()
    {
        return 'old_sound_rabbit_mq.%s_consumer';
    }
}

