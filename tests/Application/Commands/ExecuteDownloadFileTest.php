<?php

use Integracao\Application\Commands\ExecuteDownloadFile;
use Integracao\ApplicationLogger;
use Integracao\Domain\File;
use Integracao\Domain\Queues\ProcessFileProducer;
use Integracao\Domain\Repositories\FilesRepository;
use Integracao\Domain\Repositories\SavedFilesRepository;
use PHPUnit\Framework\TestCase;

/**
 * @covers Integracao\Application\Commands\ExecuteDownloadFile
 * @covers Integracao\Domain\File
 */
final class ExecuteDownloadFileTest extends TestCase
{
    private $filesRepository;
    private $savedFilesRepository;
    private $processFileProducer;
    private $logger;

    protected function setUp(): void
    {
        $this->filesRepository = $this->getMockBuilder(FilesRepository::class)->disableOriginalConstructor()->getMock();
        $this->savedFilesRepository = $this->getMockBuilder(SavedFilesRepository::class)->disableOriginalConstructor()->getMock();
        $this->processFileProducer = $this->getMockBuilder(ProcessFileProducer::class)->disableOriginalConstructor()->getMock();
        $this->logger = $this->getMockBuilder(ApplicationLogger::class)->disableOriginalConstructor()->getMock();
        $this->filesDownloader = new ExecuteDownloadFile($this->filesRepository, $this->savedFilesRepository, $this->processFileProducer, $this->logger);
    }

    public function testExecuteDownloadWithSuccess()
    {
        $file = new File("c/a", "b");
        $this->logger->expects($this->once())->method('info')->with('download from ftp finished!', ['file' => [
            'fullpath' => 'c/a',
            'source' => 'b'
        ]]);

        $this->filesRepository->expects($this->once())->method('download')->with('c/a', '/tmp/ftp-temp.integracao');
        $this->savedFilesRepository->expects($this->once())->method('save')->with('/tmp/ftp-temp.integracao', 'b', 'c-a');
        $this->processFileProducer->expects($this->once())->method('publish')->with($file);

        $this->filesDownloader->execute($file);
    }
}
