<?php

use Integracao\Infrastructure\AMQPProcessFileConsumer;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPUnit\Framework\TestCase;

class ProcessMessage
{
    public $body = '{"fullpath": "a", "source": "b"}';
}

/**
 * @covers Integracao\Infrastructure\AMQPProcessFileConsumer
 * @covers Integracao\Domain\File
 * @covers Integracao\Configuration
 * @covers AMQPProcessFileConsumerTest::testConsumer
 */
final class AMQPProcessFileConsumerTest extends TestCase
{
    public function testConsumer()
    {
        $amqpConnectionStub = $this->getMockBuilder(AMQPStreamConnection::class)->disableOriginalConstructor()->getMock();
        $channelStub = $this->getMockBuilder(AMQPChannel::class)->disableOriginalConstructor()->getMock();

        $amqpConnectionStub->method('channel')->willReturn($channelStub);

        $channelStub->expects($this->once())->method('queue_declare')->with('process.file', false, true, false, false);
        $channelStub->expects($this->once())->method('queue_bind')->with('process.file', 'integracao.files.process', '');
        $channelStub->expects($this->once())->method('basic_consume')->with('process.file', '', false, true, false, false, $this->captureArg($consumerCallback));

        $amqpProcessFileProducer = new AMQPProcessFileConsumer($amqpConnectionStub);

        $testThis = $this;
        $amqpProcessFileProducer->incoming(function ($content) use (&$testThis) {
            $testThis->assertEquals($content->attributes(), [
                'fullpath' => 'a',
                'source' => 'b'
            ]);
        });

        $consumerCallback(new ProcessMessage());
    }

    private function captureArg(&$arg)
    {
        return $this->callback(function ($argToMock) use (&$arg) {
            $arg = $argToMock;
            return true;
        });
    }
}
