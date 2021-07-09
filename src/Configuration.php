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
            'ftp' => [
                'host' => $this->env('INTEGRACAO_FTP_HOST', '172.17.0.1'),
                'user' => $this->env('INTEGRACAO_FTP_USER', 'ftp'),
                'pass' => $this->env('INTEGRACAO_FTP_PASS', 'ftp'),
                'pasv' => $this->env('INTEGRACAO_FTP_PASV', 'true', 'bool')
            ],
            'redis' => [
                'host' => $this->env('INTEGRACAO_REDIS_HOST', 'localhost'),
                'port' => $this->env('INTEGRACAO_REDIS_HOST', '6379'),
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
