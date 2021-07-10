<?php

use Integracao\Application\Commands\FilesSender;
use Integracao\Domain\File;
use Integracao\Domain\Queues\DownloadFileProducer;
use Integracao\Domain\Repositories\FilesReadRepository;
use Integracao\Domain\Repositories\FilesRepository;
use PHPUnit\Framework\TestCase;

/**
 * @covers Integracao\Application\Commands\FilesSender
 * @covers Integracao\Domain\File
 */
final class FilesSenderTest extends TestCase
{
    private $filesRepository;
    private $filesReadRepository;
    private $fileDownloadQueue;
    private $filesSender;

    protected function setUp(): void
    {
        $this->filesRepository = $this->getMockBuilder(FilesRepository::class)->disableOriginalConstructor()->getMock();
        $this->filesReadRepository = $this->getMockBuilder(FilesReadRepository::class)->disableOriginalConstructor()->getMock();
        $this->fileDownloadQueue = $this->getMockBuilder(DownloadFileProducer::class)->disableOriginalConstructor()->getMock();
        $this->filesSender = new FilesSender($this->filesRepository, $this->filesReadRepository, $this->fileDownloadQueue);
    }

    public function testSendFilesNotInCache()
    {
        $file = new File("a", "b");
        $this->filesRepository->method('fetch')->willReturn([$file]);
        $this->filesReadRepository->method('exists')->willReturn(false);

        $this->filesReadRepository->expects($this->once())->method('put')->with($file);
        $this->fileDownloadQueue->expects($this->once())->method('publish')->with($file);

        $this->filesSender->execute();
    }

    public function testNotSendFilesInCache()
    {
        $file = new File("a", "b");
        $this->filesRepository->method('fetch')->willReturn([$file]);
        $this->filesReadRepository->method('exists')->willReturn(true);

        $this->filesReadRepository->expects($this->never())->method('put');
        $this->fileDownloadQueue->expects($this->never())->method('publish');

        $this->filesSender->execute();
    }
}
