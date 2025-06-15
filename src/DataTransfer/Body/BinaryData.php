<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\DataTransfer\Body;

use Chr15k\HttpCliGenerator\Contracts\BodyDataTransfer;
use finfo;
use Throwable;

final readonly class BinaryData implements BodyDataTransfer
{
    public function __construct(private string $filePath = '')
    {
        //
    }

    public function getContent(): string
    {
        return $this->filePath;
    }

    public function getContentTypeHeader(): string
    {
        if ($this->filePath === '' || $this->filePath === '0' || ! file_exists($this->filePath)) {
            return '';
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);

        try {
            $mimeType = $finfo->file($this->filePath);

            return $mimeType ?: '';
        } catch (Throwable) {
            return '';
        }
    }
}
