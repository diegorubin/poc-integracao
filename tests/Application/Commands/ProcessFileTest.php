<?php

use Integracao\Application\Commands\ProcessFile;
use Integracao\ApplicationLogger;
use Integracao\Domain\File;
use Integracao\Domain\Repositories\SavedFilesRepository;
use PHPUnit\Framework\TestCase;

/**
 * @covers Integracao\Application\Commands\ProcessFile
 * @covers Integracao\Domain\File
 */
final class ProcessFileTest extends TestCase
{
    private $savedFilesRepository;
    private $logger;

    protected function setUp(): void
    {
        $this->savedFilesRepository = $this->getMockBuilder(SavedFilesRepository::class)->disableOriginalConstructor()->getMock();
        $this->logger = $this->getMockBuilder(ApplicationLogger::class)->disableOriginalConstructor()->getMock();
        $this->processFile = new ProcessFile($this->savedFilesRepository, $this->logger);
    }

    public function testProcessFileWithSuccess()
    {
        $file = new File("c/a", "b");
        $this->savedFilesRepository->expects($this->once())->method('load')->with('b', 'c/a', '/tmp/process-temp.integracao');
        $this->logger->expects($this->exactly(2))->method('info')->with(
            $this->logicalOr(
                $this->equalTo('processing file!', ['file' => ['fullpath' => 'c/a', 'source' => 'b']]),
                $this->equalTo('file processed with success!', ['file' => ['fullpath' => 'c/a', 'source' => 'b']])
            )
        );
        $this->processFile->execute($file);
    }
}
