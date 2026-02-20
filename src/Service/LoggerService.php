<?php


namespace App\Service;


use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class LoggerService
{
    public function __construct(private LoggerInterface $logger, private ParameterBagInterface $parameterBag)
    {
    }
    public function log($message,$value){
        $this->logger->info($message,$value);
    }
}