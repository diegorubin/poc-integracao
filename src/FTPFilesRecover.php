<?php

namespace Integracao;

use FtpClient\FtpClient;
use Integracao\Application\Commands\FilesSender;
use Integracao\Infrastructure\FTPFilesRepository;

class FTPFilesRecover
{
    private $files_sender;

    public function __construct()
    {
        // build dependencies
        $files_repository = new FTPFilesRepository(new FtpClient());

        $this->files_sender = new FilesSender($files_repository);
    }

    public function run()
    {
        $this->files_sender->execute();
    }
}
