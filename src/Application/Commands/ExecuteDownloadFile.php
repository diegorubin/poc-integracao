<?php

namespace Integracao\Application\Commands;

use Integracao\Domain\File;

class ExecuteDownloadFile
{
    private $filesRepository;
    private $savedFilesRepository;
    private $processFileProducer;
    private $logger;

    public function __construct($filesRepository, $savedFilesRepository, $processFileProducer, $logger)
    {
        $this->filesRepository = $filesRepository;
        $this->savedFilesRepository = $savedFilesRepository;
        $this->processFileProducer = $processFileProducer;
        $this->logger = $logger;
    }

    public function execute(File $file)
    {
        // TODO: generate random tmpFile name and remove after
        $tmpFile = "/tmp/ftp-temp.integracao";

        $this->filesRepository->download($file->getFullpath(), $tmpFile);

        $this->savedFilesRepository->save($tmpFile, $file->getSource(), str_replace("/", "-", $file->getFullpath()));

        $this->processFileProducer->publish($file);

        $this->logger->info("download from ftp finished!", ["file" => $file->attributes()]);
    }
}
