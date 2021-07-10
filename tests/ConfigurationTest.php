<?php
use PHPUnit\Framework\TestCase;

use Integracao\Configuration;

/**
 * @covers Integracao\Configuration
 */
final class ConfigurationTest extends TestCase
{
    private $configuration;

    protected function setUp(): void
    {
        $this->configuration = Configuration::getInstance();
    }

    public function testFTPDefaultValues()
    {
        $ftp_values = $this->configuration->get()['ftp'];

        $this->assertEquals($ftp_values, [
            'host' => '172.17.0.1',
            'user' => 'ftp',
            'pass' => 'ftp',
            'pasv' => true
        ]);
    }

    public function testRedisDefaultValues()
    {
        $redis_values = $this->configuration->get()['redis'];

        $this->assertEquals($redis_values, [
            'host' => 'localhost',
            'port' => '6379'
        ]);
    }

    public function testAMQPDefaultValues()
    {
        $amqp_values = $this->configuration->get()['queues'];

        $this->assertEquals($amqp_values, [
            'download' => [
                'type' => 'amqp',
                'host' => 'localhost',
                'port' => '5672',
                'user' => 'guest',
                'pass' => 'guest'
            ]
        ]);
    }

    public function testMetaDefaultValues()
    {
        $amqp_values = $this->configuration->get()['meta'];

        $this->assertEquals($amqp_values, [
            'applicationName' => 'integracao'
        ]);
    }

    public function testConfigurationSingleton()
    {
        $this->assertInstanceOf(Configuration::class, $this->configuration);
        $this->assertSame($this->configuration, Configuration::getInstance());
    }
}
