<?php

namespace Integracao;

use Aws\S3\S3Client;
use FtpClient\FtpClient;
use Integracao\Application\Commands\ExecuteDownloadFile;
use Integracao\Domain\File;
use Integracao\Infrastructure\AMQPDownloadFileConsumer;
use Integracao\Infrastructure\FTPFilesRepository;
use Integracao\Infrastructure\S3SavedFilesRepository;
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

        // Saved Files Repository
        $client = new S3Client([
            'region' => '',
            'version' => '2006-03-01',
            'endpoint' => $this->config["s3"]["endpoint"],
            'credentials' => [
                'key' => $this->config["s3"]["key"],
                'secret' => $this->config["s3"]["secret"]
            ],
            'use_path_style_endpoint' => true
        ]);
        $savedFilesRepository = new S3SavedFilesRepository($client);

        // Command
        $this->executeDownload = new ExecuteDownloadFile($filesRepository, $savedFilesRepository, $this->logger);
    }
    public function run()
    {
        $callback = function (File $file) {
            $this->executeDownload->execute($file);
        };
        $this->downloadFileConsumer->incoming($callback);
    }
}
