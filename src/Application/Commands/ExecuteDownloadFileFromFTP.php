<?php

namespace Integracao\Application\Commands;

use Integracao\Domain\File;

class ExecuteDownloadFileFromFTP
{
    private $filesRepository;
    private $logger;

    public function __construct($filesRepository, $logger)
    {
        $this->filesRepository = $filesRepository;
        $this->logger = $logger;
    }

    public function execute(File $file)
    {
        // TODO: generate random tmpFile name and remove after
        $tmpFile = "/tmp/ftp-temp.integracao";

        $this->filesRepository->download($file->getFullpath(), $tmpFile);

        $this->logger->info("download from ftp finished!", ["file" => $file->attributes()]);
    }
}
