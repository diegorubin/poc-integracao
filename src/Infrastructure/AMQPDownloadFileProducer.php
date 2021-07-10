<?php

namespace Integracao\Infrastructure;

use Integracao\Domain\File;
use PhpAmqpLib\Message\AMQPMessage;

class AMQPDownloadFileProducer
{
    private $ampqServerConnection;
    private $channel;

    public function __construct($ampqServerConnection)
    {
        $this->ampqServerConnection = $ampqServerConnection;
        $this->channel = $this->ampqServerConnection->channel();

        $this->channel->exchange_declare('integracao.files.download', 'direct', false, true);
    }
    public function publish(File $file)
    {
        $message = new AMQPMessage(json_encode($file->attributes()));
        $this->channel->basic_publish($message, 'integracao.files.download', '');
    }
}
