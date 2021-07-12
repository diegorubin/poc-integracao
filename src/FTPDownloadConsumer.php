<?php

namespace Integracao;

use Aws\S3\S3Client;
use FtpClient\FtpClient;
use Integracao\Application\Commands\ExecuteDownloadFile;
use Integracao\Domain\File;
use Integracao\Infrastructure\AMQPDownloadFileConsumer;
use Integracao\Infrastructure\AMQPProcessFileProducer;
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

        // Download Consumer - AMQP
        $consumer = $this->config['queues']['download'];
        $consumerConnection = new AMQPStreamConnection($consumer['host'], $consumer['port'], $consumer['user'], $consumer['pass']);
        $this->downloadFileConsumer = new AMQPDownloadFileConsumer($consumerConnection);

        // Process Producer - AMQP
        $producer = $this->config['queues']['process'];
        $producerConnection = new AMQPStreamConnection($producer['host'], $producer['port'], $producer['user'], $producer['pass']);
        $processProducer = new AMQPProcessFileProducer($producerConnection);

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
        $this->executeDownload = new ExecuteDownloadFile($filesRepository, $savedFilesRepository, $processProducer, $this->logger);
    }
    public function run()
    {
        $callback = function (File $file) {
            $this->executeDownload->execute($file);
        };
        $this->downloadFileConsumer->incoming($callback);
    }
}
