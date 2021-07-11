<?php

namespace Integracao\Application\Commands;

class SendFileToDownloadQueue
{
    private $filesRepository;
    private $filesReadRepository;
    private $fileDownloadQueue;
    private $logger;

    public function __construct($filesRepository, $filesReadRepository, $fileDownloadQueue, $logger)
    {
        $this->filesRepository = $filesRepository;
        $this->filesReadRepository = $filesReadRepository;
        $this->fileDownloadQueue = $fileDownloadQueue;
        $this->logger = $logger;
    }

    public function execute()
    {
        foreach ($this->filesRepository->fetch() as $file) {
            if (!$this->filesReadRepository->exists($file)) {
                $this->fileDownloadQueue->publish($file);
                $this->filesReadRepository->put($file);
                $this->logger->info('new file found!', ['file' => $file->attributes()]);
            }
        }
    }
}
