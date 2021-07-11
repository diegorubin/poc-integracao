<?php

use Aws\S3\S3Client;
use Integracao\Infrastructure\S3SavedFilesRepository;
use PHPUnit\Framework\TestCase;

/**
 * @covers Integracao\Infrastructure\S3SavedFilesRepository
 */
class S3SavedFilesRepositoryTest extends TestCase
{
    private $clientStub;
    private $savedFilesRepository;

    protected function setUp(): void
    {
        $this->clientStub = $this->getMockBuilder(S3Client::class)->disableOriginalConstructor()->getMock();
        $this->savedFilesRepository = new S3SavedFilesRepository($this->clientStub);
    }

    public function testSave()
    {
        $this->clientStub->expects($this->once())->method('__call')->with('putObject', $this->captureArg($arguments));
        $this->savedFilesRepository->save('tests/fixtures/file.xml', 'bucket', 'key');

        $this->assertEquals($arguments[0]['Bucket'], 'bucket');
        $this->assertEquals($arguments[0]['Key'], 'key');
        $this->assertEquals($arguments[0]['ACL'], 'public-read');
        $this->assertEquals(fread($arguments[0]['Body'], filesize('tests/fixtures/file.xml')), "content\n");
    }

    private function captureArg(&$arg)
    {
        return $this->callback(function ($argToMock) use (&$arg) {
            $arg = $argToMock;
            return true;
        });
    }
}
