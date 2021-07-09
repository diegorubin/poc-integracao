<?php

namespace Integracao;

use Redis;
use FtpClient\FtpClient;
use Integracao\Application\Commands\FilesSender;
use Integracao\Infrastructure\FTPFilesRepository;
use Integracao\Infrastructure\RedisFilesReadRepository;

class FTPFilesRecover
{
    private $files_sender;

    public function __construct()
    {
        // build dependencies
        $filesRepository = new FTPFilesRepository(new FtpClient());
        $filesReadRepository = new RedisFilesReadRepository(new Redis());

        $this->files_sender = new FilesSender($filesRepository, $filesReadRepository);
    }

    public function run()
    {
        $this->files_sender->execute();
    }
}
