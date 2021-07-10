<?php

namespace Integracao\Application\Commands;

class FilesSender
{
    private $filesRepository;
    private $filesReadRepository;
    private $fileDownloadQueue;

    public function __construct($filesRepository, $filesReadRepository, $fileDownloadQueue)
    {
        $this->filesRepository = $filesRepository;
        $this->filesReadRepository = $filesReadRepository;
        $this->fileDownloadQueue = $fileDownloadQueue;
    }

    public function execute()
    {
        foreach ($this->filesRepository->fetch() as $file) {
            if (!$this->filesReadRepository->exists($file)) {
                $this->filesReadRepository->put($file);
                $this->fileDownloadQueue->publish($file);
            }
        }
    }
}
