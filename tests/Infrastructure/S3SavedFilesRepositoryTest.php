<?php

use Aws\S3\S3Client;
use Integracao\Infrastructure\S3SavedFilesRepository;
use PHPUnit\Framework\TestCase;

class S3BodyMock
{
    public function getContents()
    {
        return "content";
    }
}

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
        $this->assertEquals(fread($arguments[0]['Body'], filesize('tests/fixtures/file.xml')), "<?xml version='1.0' standalone='yes'?>\n<data>\n    <title>example</title>\n</data>\n");
    }

    public function testLoad()
    {
        $this->clientStub->expects($this->once())->method('__call')->with('getObject', $this->captureArg($arguments))->willReturn(['Body' => new S3BodyMock()]);
        $this->savedFilesRepository->load('bucket', 'key', '/tmp/file.test');

        $this->assertEquals($arguments[0]['Bucket'], 'bucket');
        $this->assertEquals($arguments[0]['Key'], 'key');
    }

    private function captureArg(&$arg)
    {
        return $this->callback(function ($argToMock) use (&$arg) {
            $arg = $argToMock;
            return true;
        });
    }
}
