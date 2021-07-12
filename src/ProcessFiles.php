<?php

namespace Integracao;

use Aws\S3\S3Client;
use Integracao\Application\Commands\ProcessFile;
use Integracao\ApplicationLogger;
use Integracao\Configuration;
use Integracao\Domain\File;
use Integracao\Infrastructure\AMQPProcessFileConsumer;
use Integracao\Infrastructure\S3SavedFilesRepository;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class ProcessFiles
{
    private $logger;
    private $config;
    private $processFileConsumer;
    private $processFile;

    public function __construct()
    {
        $this->config = Configuration::getInstance()->get();

        // build dependencies
        // Logger
        $this->logger = ApplicationLogger::getInstance();

        // Process Consumer - AMQP
        $amqp = $this->config['queues']['process'];
        $connection = new AMQPStreamConnection($amqp['host'], $amqp['port'], $amqp['user'], $amqp['pass']);
        $this->processFileConsumer = new AMQPProcessFileConsumer($connection);

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
        $this->processFile = new ProcessFile($savedFilesRepository, $this->logger);
    }

    public function run()
    {
        $callback = function (File $file) {
            $this->processFile->execute($file);
        };
        $this->processFileConsumer->incoming($callback);
    }
}
