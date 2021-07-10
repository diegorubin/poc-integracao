<?php

namespace Integracao;

use Monolog\Logger;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;

class ApplicationLogger
{
    private $logger;

    private static $applicationLogger;

    public function __construct()
    {
        $this->logger = new Logger(Configuration::getInstance()->get()['meta']['applicationName']);
        
        $formatter = new JsonFormatter();

        $handler = new StreamHandler(STDOUT, Logger::INFO);
        $handler->setFormatter($formatter);

        $handlerFile = new StreamHandler('/tmp/application/application.log', Logger::INFO);
        $handlerFile->setFormatter($formatter);

        $this->logger->pushHandler($handler);
        $this->logger->pushHandler($handlerFile);
    }

    public static function getInstance()
    {
        if (!self::$applicationLogger) {
            self::$applicationLogger = new ApplicationLogger();
        }
        return self::$applicationLogger;
    }

    public function info($message, $context=[])
    {
        $this->logger->info($message, $context);
    }

    public function error($message)
    {
        $this->logger->error($message);
    }
}
