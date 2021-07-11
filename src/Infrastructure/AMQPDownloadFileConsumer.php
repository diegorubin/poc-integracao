<?php

namespace Integracao\Infrastructure;

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

        // TODO move exchange name and queue name to configuration
        $this->channel->queue_declare('ftp.files.download', false, true, false, false);
        $this->channel->queue_bind('ftp.files.download', 'integracao.files.download', 'ftp');
    }
    public function incoming($callback)
    {
        $consume = function ($message) use (&$callback) {
            $file = File::fromJSON(json_decode($message->body));
            $callback($file);
        };

        $this->channel->basic_consume('ftp.files.download', '', false, true, false, false, $consume);
        while ($this->channel->is_open()) {
            $this->channel->wait();
        }
    }
}
