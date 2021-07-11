<?php

use Integracao\Application\Commands\ExecuteDownloadFileFromFTP;
use Integracao\ApplicationLogger;
use Integracao\Domain\File;
use PHPUnit\Framework\TestCase;

/**
 * @covers Integracao\Application\Commands\ExecuteDownloadFileFromFTP
 * @covers Integracao\Domain\File
 */
final class ExecuteDownloadFileFromFTPTest extends TestCase
{
    private $logger;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(ApplicationLogger::class)->disableOriginalConstructor()->getMock();
        $this->filesDownloader = new ExecuteDownloadFileFromFTP($this->logger);
    }

    public function testExecuteDownloadWithSuccess()
    {
        $file = new File("a", "b");
        $this->logger->expects($this->once())->method('info')->with('download from ftp finished!', ['file' => [
            'fullpath' => 'a',
            'source' => 'b'
        ]]);

        $this->filesDownloader->execute($file);
    }
}
