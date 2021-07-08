<?php
use PHPUnit\Framework\TestCase;

use Integracao\Configuration;

/**
 * @covers Integracao\Configuration
 */
final class ConfigurationTest extends TestCase
{
    public function testFTPDefaultValues()
    {
        $configuration = new Configuration();
        $ftp_values = $configuration->get()['ftp'];

        $this->assertEquals($ftp_values, [
            'host' => '172.17.0.1',
            'user' => 'ftp',
            'pass' => 'ftp',
            'pasv' => true
        ]);
    }
}
