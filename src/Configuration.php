<?php

namespace Integracao;

class Configuration
{
    private static $configuration;
    private $config;

    public static function getInstance()
    {
        if (!self::$configuration) {
            self::$configuration = new Configuration();
        }
        return self::$configuration;
    }

    public function __construct()
    {
        $this->config = [
            'meta' => [
                'applicationName' => $this->env('INTEGRACAO_META_APPLICATION_NAME', 'integracao')
            ],
            'ftp' => [
                'host' => $this->env('INTEGRACAO_FTP_HOST', '172.17.0.1'),
                'user' => $this->env('INTEGRACAO_FTP_USER', 'ftp'),
                'pass' => $this->env('INTEGRACAO_FTP_PASS', 'ftp'),
                'pasv' => $this->env('INTEGRACAO_FTP_PASV', 'true', 'bool')
            ],
            'redis' => [
                'host' => $this->env('INTEGRACAO_REDIS_HOST', 'localhost'),
                'port' => $this->env('INTEGRACAO_REDIS_HOST', '6379'),
            ],
            'queues' => [
                'download' => [
                    'type' => $this->env('INTEGRACAO_DOWNLOAD_QUEUE_TYPE', 'amqp'),
                    'host' => $this->env('INTEGRACAO_DOWNLOAD_QUEUE_HOST', 'localhost'),
                    'port' => $this->env('INTEGRACAO_DOWNLOAD_QUEUE_PORT', '5672'),
                    'user' => $this->env('INTEGRACAO_DOWNLOAD_QUEUE_USER', 'guest'),
                    'pass' => $this->env('INTEGRACAO_DOWNLOAD_QUEUE_PASS', 'guest')
                ]
            ]
        ];
    }

    public function get()
    {
        return $this->config;
    }

    private function env($varname, $default_value, $vartype = 'string')
    {
        $current_value = getenv($varname);
        if (!$current_value) {
            $current_value = $default_value;
        }

        if ($vartype == 'bool') {
            return $current_value == 'true';
        }

        return $current_value;
    }
}
