<?php

namespace Integracao\Infrastructure;

use Integracao\Configuration;
use Integracao\Domain\File;
use Integracao\Domain\Queues\DownloadFileConsumer;

class AMQPDownloadFileConsumer implements DownloadFileConsumer
{
    private $ampqServerConnection;
    private $channel;

    public function __construct($ampqServerConnection)
    {
        $this->ampqServerConnection = $ampqServerConnection;
        $this->channel = $this->ampqServerConnection->channel();

        $this->config = Configuration::getInstance()->get()["queues"]["download"];

        // TODO move exchange name to configuration
        $this->channel->queue_declare($this->config["queueName"], false, true, false, false);
        $this->channel->queue_bind($this->config["queueName"], 'integracao.files.download', $this->config["routingKey"]);
    }
    public function incoming($callback)
    {
        $consume = function ($message) use (&$callback) {
            $file = File::fromJSON(json_decode($message->body));
            $callback($file);
        };

        $this->channel->basic_consume('download.ftp', '', false, true, false, false, $consume);
        while ($this->channel->is_open()) {
            $this->channel->wait();
        }
    }
}
