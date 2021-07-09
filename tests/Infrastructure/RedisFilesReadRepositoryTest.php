<?php

use Integracao\Domain\File;
use Integracao\Infrastructure\RedisFilesReadRepository;
use PHPUnit\Framework\TestCase;

/**
 * @covers Integracao\Infrastructure\RedisFilesReadRepository
 * @covers Integracao\Configuration
 * @covers Integracao\Domain\File
 */
final class RedisFilesReadRepositoryTest extends TestCase
{
    private $redisClientStub;

    protected function setUp(): void
    {
        $this->redisClientStub = $this->getMockBuilder(Redis::class)->disableOriginalConstructor()->getMock();
    }

    public function testConstruct()
    {
        $this->redisClientStub->expects($this->once())->method('connect')->with('localhost', '6379');
        new RedisFilesReadRepository($this->redisClientStub);
    }

    public function testPut()
    {
        $this->redisClientStub->expects($this->once())->method('set')->with('b:a', '{"fullpath":"a","source":"b"}');
        $this->redisClientStub->method('set')->willReturn(true);
        $repository = new RedisFilesReadRepository($this->redisClientStub);

        $repository->put(new File('a', 'b'));
    }

    public function testExists()
    {
        $this->redisClientStub->method('exists')->willReturn(true);
        $this->redisClientStub->expects($this->once())->method('exists')->with('b:a');
        $repository = new RedisFilesReadRepository($this->redisClientStub);

        $result = $repository->exists(new File('a', 'b'));
        $this->assertTrue($result);
    }
}
