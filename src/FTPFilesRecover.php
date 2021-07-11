<?php

namespace Integracao;

use Exception;
use Redis;
use FtpClient\FtpClient;
use Integracao\Application\Commands\SendFileToDownloadQueue;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Integracao\Infrastructure\AMQPDownloadFileProducer;
use Integracao\Infrastructure\FTPFilesRepository;
use Integracao\Infrastructure\RedisFilesReadRepository;

class FTPFilesRecover
{
    private $files_sender;
    private $config;
    private $logger;

    public function __construct()
    {
        $this->config = Configuration::getInstance()->get();

        // build dependencies
        // FTP
        $filesRepository = new FTPFilesRepository(new FtpClient());

        // Cache - Redis
        $filesReadRepository = new RedisFilesReadRepository(new Redis());

        // Download Producer - AMQP
        $amqp = $this->config['queues']['download'];
        $connection = new AMQPStreamConnection($amqp['host'], $amqp['port'], $amqp['user'], $amqp['pass']);
        $downloadFileProducer = new AMQPDownloadFileProducer($connection);

        // Logger
        $this->logger = ApplicationLogger::getInstance();

        $this->files_sender = new SendFileToDownloadQueue($filesRepository, $filesReadRepository, $downloadFileProducer, $this->logger);
    }

    public function run()
    {
        while (1) {
            try {
                $this->logger->info("executing ftp list!", ["source" => $this->config['meta']['applicationName']]);
                $this->files_sender->execute();
            } catch (Exception $e) {
                $this->logger->error("exception in ftp polling: ". $e->getMessage());
            }
            sleep($this->config["ftp"]["pollingInterval"]);
        }
    }
}
