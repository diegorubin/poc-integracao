<?php

namespace Integracao\Application\Commands;

use Integracao\Domain\File;

class ProcessFile
{
    private $savedFilesRepository;
    private $logger;

    public function __construct($savedFilesRepository, $logger)
    {
        $this->savedFilesRepository= $savedFilesRepository;
        $this->logger = $logger;
    }

    public function execute(File $file)
    {
        // TODO: generate random tmpfile name and remove after
        $tmpFile = "/tmp/process-temp.integracao";

        $this->logger->info('processing file!', ['file' => $file]);

        $this->savedFilesRepository->load($file->getSource(), $file->getFullpath(), $tmpFile);

        $this->logger->info('file processed with success!', ['file' => $file]);
    }
}
