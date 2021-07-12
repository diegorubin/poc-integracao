<?php

namespace Integracao\Infrastructure;

use Integracao\Configuration;
use Integracao\Domain\File;
use Integracao\Domain\Queues\ProcessFileProducer;
use PhpAmqpLib\Message\AMQPMessage;

class AMQPProcessFileProducer implements ProcessFileProducer
{
    private $ampqServerConnection;
    private $config;
    private $channel;

    // TODO duplicate: common to AMQPDownloadFileProducer
    // extract to abstract class
    public function __construct($ampqServerConnection)
    {
        $this->config = Configuration::getInstance()->get()['queues']['process'];

        $this->ampqServerConnection = $ampqServerConnection;
        $this->channel = $this->ampqServerConnection->channel();

        // TODO move exchange name to configuration
        $this->channel->exchange_declare('integracao.files.process', 'direct', false, true);
    }
    public function publish(File $file)
    {
        $message = new AMQPMessage(json_encode($file->attributes()));
        $this->channel->basic_publish($message, 'integracao.files.process', $this->config["routingKey"]);
    }
}
