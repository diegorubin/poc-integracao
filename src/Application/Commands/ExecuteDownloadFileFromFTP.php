<?php

namespace Integracao\Application\Commands;

use Integracao\Domain\File;

class ExecuteDownloadFileFromFTP
{
    private $filesRepository;
    private $savedFilesRepository;
    private $logger;

    public function __construct($filesRepository, $savedFilesRepository, $logger)
    {
        $this->filesRepository = $filesRepository;
        $this->savedFilesRepository = $savedFilesRepository;
        $this->logger = $logger;
    }

    public function execute(File $file)
    {
        // TODO: generate random tmpFile name and remove after
        $tmpFile = "/tmp/ftp-temp.integracao";

        $this->filesRepository->download($file->getFullpath(), $tmpFile);

        $this->savedFilesRepository->save($tmpFile, $file->getSource(), str_replace("/", "-", $file->getFullpath()));

        $this->logger->info("download from ftp finished!", ["file" => $file->attributes()]);
    }
}
