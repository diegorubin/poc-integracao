<?php

namespace Integracao\Domain;

class File
{
    private string $fullpath;
    private string $source;

    public function __construct(string $fullpath, string $source)
    {
        $this->fullpath = $fullpath;
        $this->source = $source;
    }

    public static function fromJSON($file)
    {
        return new File($file->fullpath, $file->source);
    }

    public function getFullpath(): string
    {
        return $this->fullpath;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function attributes(): array
    {
        return ["fullpath" => $this->fullpath, "source" => $this->source];
    }
}
