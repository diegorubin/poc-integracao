<?php

namespace Integracao;

class Configuration
{
    private static $configuration;

    public static function getInstance()
    {
        if (!self::$configuration) {
            self::$configuration = new Configuration();
        }
        return self::$configuration;
    }

    public function get()
    {
        return [
            'ftp' => [
                'host' => $this->env('INTEGRACAO_FTP_HOST', '172.17.0.1'),
                'user' => $this->env('INTEGRACAO_FTP_USER', 'ftp'),
                'pass' => $this->env('INTEGRACAO_FTP_PASS', 'ftp'),
                'pasv' => $this->env('INTEGRACAO_FTP_PASV', 'true', 'bool')
            ]
        ];
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
