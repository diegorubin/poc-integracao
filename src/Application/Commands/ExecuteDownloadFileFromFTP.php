<?php

namespace Integracao\Application\Commands;

use Integracao\Domain\File;

class ExecuteDownloadFileFromFTP
{
    private $logger;

    public function __construct($logger)
    {
        $this->logger = $logger;
    }

    public function execute(File $file)
    {
        $this->logger->info("download from ftp finished!", ["file" => $file->attributes()]);
    }
}
