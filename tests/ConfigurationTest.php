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

    public function testConfigurationSingleton()
    {
        $this->assertInstanceOf(Configuration::class, $this->configuration);
        $this->assertSame($this->configuration, Configuration::getInstance());
    }
}
