<?php

use Integracao\Application\Commands\ExecuteDownloadFileFromFTP;
use Integracao\ApplicationLogger;
use Integracao\Domain\File;
use Integracao\Domain\Repositories\FilesRepository;
use PHPUnit\Framework\TestCase;

/**
 * @covers Integracao\Application\Commands\ExecuteDownloadFileFromFTP
 * @covers Integracao\Domain\File
 */
final class ExecuteDownloadFileFromFTPTest extends TestCase
{
    private $filesRepository;
    private $logger;

    protected function setUp(): void
    {
        $this->filesRepository = $this->getMockBuilder(FilesRepository::class)->disableOriginalConstructor()->getMock();
        $this->logger = $this->getMockBuilder(ApplicationLogger::class)->disableOriginalConstructor()->getMock();
        $this->filesDownloader = new ExecuteDownloadFileFromFTP($this->filesRepository, $this->logger);
    }

    public function testExecuteDownloadWithSuccess()
    {
        $file = new File("a", "b");
        $this->logger->expects($this->once())->method('info')->with('download from ftp finished!', ['file' => [
            'fullpath' => 'a',
            'source' => 'b'
        ]]);

        $this->filesRepository->expects($this->once())->method('download')->with('a', '/tmp/ftp-temp.integracao');

        $this->filesDownloader->execute($file);
    }
}
