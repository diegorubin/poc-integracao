<?php

use Integracao\Application\Commands\FilesSender;
use Integracao\Domain\File;
use Integracao\Infrastructure\FTPFilesRepository;
use Integracao\Infrastructure\RedisFilesReadRepository;
use PHPUnit\Framework\TestCase;

/**
 * @covers Integracao\Application\Commands\FilesSender
 * @covers Integracao\Domain\File
 */
final class FilesSenderTest extends TestCase
{
    private $filesRepository;
    private $filesReadRepository;
    private $filesSender;

    protected function setUp(): void
    {
        $this->filesRepository = $this->getMockBuilder(FTPFilesRepository::class)->disableOriginalConstructor()->getMock();
        $this->filesReadRepository = $this->getMockBuilder(RedisFilesReadRepository::class)->disableOriginalConstructor()->getMock();
        $this->filesSender = new FilesSender($this->filesRepository, $this->filesReadRepository);
    }

    public function testSendFilesNotInCache()
    {
        $file = new File("a", "b");
        $this->filesRepository->method('fetch')->willReturn([$file]);
        $this->filesReadRepository->method('exists')->willReturn(false);

        $this->filesReadRepository->expects($this->once())->method('put')->with($file);

        $this->filesSender->execute();
    }

    public function testNotSendFilesInCache()
    {
        $file = new File("a", "b");
        $this->filesRepository->method('fetch')->willReturn([$file]);
        $this->filesReadRepository->method('exists')->willReturn(true);

        $this->filesReadRepository->expects($this->never())->method('put');

        $this->filesSender->execute();
    }
}
