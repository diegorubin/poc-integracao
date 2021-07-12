<?php

use Integracao\Configuration;
use Integracao\Domain\File;
use Integracao\Domain\Queues\ProcessFileConsumer;

class AMQPProcessFileConsumer implements ProcessFileConsumer
{

    // TODO move to abstract class
    // common with download consumer
    private $ampqServerConnection;
    private $channel;
    private $config;

    public function __construct($ampqServerConnection)
    {
        $this->ampqServerConnection = $ampqServerConnection;
        $this->channel = $this->ampqServerConnection->channel();

        $this->config = Configuration::getInstance()->get()["queues"]["process"];

        // TODO move exchange name to configuration
        $this->channel->queue_declare($this->config["queueName"], false, true, false, false);
        $this->channel->queue_bind($this->config["queueName"], 'integracao.files.process', $this->config["routingKey"]);
    }
    public function incoming($callback)
    {
        $consume = function ($message) use (&$callback) {
            $file = File::fromJSON(json_decode($message->body));
            $callback($file);
        };

        $this->channel->basic_consume($this->config["queueName"], '', false, true, false, false, $consume);
        while ($this->channel->is_open()) {
            $this->channel->wait();
        }
    }
}
