<?php

namespace Integracao\Application\Commands;

use Exception;
use Integracao\Domain\File;
use SimpleXMLElement;

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

        $this->logger->info('processing file!', ['file' => $file->attributes()]);

        try {
            $this->savedFilesRepository->load($file->getSource(), $file->getFullpath(), $tmpFile);

            $fileContent = fread(fopen($tmpFile, "r"), filesize($tmpFile));
            $content = new SimpleXMLElement($fileContent);

            $this->logger->info('file processed with success!', ['file' => $file->attributes()]);
        } catch (Exception $e) {
            $this->logger->error('error on process file!', ['file' => $file->attributes(), 'exception' => $e->getMessage()]);
        }
    }
}
