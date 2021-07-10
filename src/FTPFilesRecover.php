<?php

namespace Integracao;

use Redis;
use FtpClient\FtpClient;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Integracao\Application\Commands\FilesSender;
use Integracao\Infrastructure\AMQPDownloadFileProducer;
use Integracao\Infrastructure\FTPFilesRepository;
use Integracao\Infrastructure\RedisFilesReadRepository;

class FTPFilesRecover
{
    private $files_sender;

    public function __construct()
    {
        // build dependencies
        // FTP
        $filesRepository = new FTPFilesRepository(new FtpClient());

        // Cache - Redis
        $filesReadRepository = new RedisFilesReadRepository(new Redis());

        // Download Producer - AMQP
        $amqp = Configuration::getInstance()->get()['queues']['download'];
        $connection = new AMQPStreamConnection($amqp['host'], $amqp['port'], $amqp['user'], $amqp['pass']);
        $downloadFileProducer = new AMQPDownloadFileProducer($connection);

        $this->files_sender = new FilesSender($filesRepository, $filesReadRepository, $downloadFileProducer);
    }

    public function run()
    {
        $this->files_sender->execute();
    }
}
