<?php

namespace Integracao;

use FtpClient\FtpClient;
use Integracao\Application\Commands\ExecuteDownloadFileFromFTP;
use Integracao\Domain\File;
use Integracao\Infrastructure\AMQPDownloadFileConsumer;
use Integracao\Infrastructure\FTPFilesRepository;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class FTPDownloadConsumer
{
    private $logger;
    private $config;
    private $downloadFileConsumer;

    public function __construct()
    {
        $this->config = Configuration::getInstance()->get();

        // build dependencies
        // FTP
        $filesRepository = new FTPFilesRepository(new FtpClient());

        // Logger
        $this->logger = ApplicationLogger::getInstance();

        // Download Producer - AMQP
        $amqp = $this->config['queues']['download'];
        $connection = new AMQPStreamConnection($amqp['host'], $amqp['port'], $amqp['user'], $amqp['pass']);
        $this->downloadFileConsumer = new AMQPDownloadFileConsumer($connection);

        // Command
        $this->executeDownload = new ExecuteDownloadFileFromFTP($filesRepository, $this->logger);
    }
    public function run()
    {
        $callback = function (File $file) {
            $this->executeDownload->execute($file);
        };
        $this->downloadFileConsumer->incoming($callback);
    }
}
