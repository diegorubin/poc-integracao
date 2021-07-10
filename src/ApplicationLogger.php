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
        
        $handler = new StreamHandler(STDOUT, Logger::INFO);
        $formatter = new JsonFormatter();
        $handler->setFormatter($formatter);

        $this->logger->pushHandler($handler);
    }

    public static function getInstance()
    {
        if (!self::$applicationLogger) {
            self::$applicationLogger = new ApplicationLogger();
        }
        return self::$applicationLogger;
    }

    public function info($message)
    {
        $this->logger->info($message);
    }
}
