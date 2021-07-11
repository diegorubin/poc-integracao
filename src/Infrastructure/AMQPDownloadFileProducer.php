<?php

namespace Integracao\Infrastructure;

use Integracao\Domain\File;
use Integracao\Domain\Queues\DownloadFileProducer;
use PhpAmqpLib\Message\AMQPMessage;

class AMQPDownloadFileProducer implements DownloadFileProducer
{
    private $ampqServerConnection;
    private $channel;

    public function __construct($ampqServerConnection)
    {
        $this->ampqServerConnection = $ampqServerConnection;
        $this->channel = $this->ampqServerConnection->channel();

        // TODO move exchange name to configuration
        $this->channel->exchange_declare('integracao.files.download', 'direct', false, true);
    }
    public function publish(File $file)
    {
        $message = new AMQPMessage(json_encode($file->attributes()));
        $this->channel->basic_publish($message, 'integracao.files.download', 'ftp');
    }
}
