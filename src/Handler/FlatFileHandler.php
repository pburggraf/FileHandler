<?php

declare(strict_types=1);

namespace PBurggraf\FileHandler\Handler;

use PBurggraf\FileHandler\Exception\CouldNotOpenFileException;
use function fopen;
use function fread;
use function fseek;
use function fwrite;

/**
 * @author Philip Burggraf <philip@pburggraf.de>
 */
class FlatFileHandler extends AbstractFileHandler
{
    /**
     * @var resource
     */
    protected $handle;

    /**
     * @param string $filename
     *
     * @throws CouldNotOpenFileException
     *
     * @return AbstractFileHandler
     */
    public function open(string $filename): AbstractFileHandler
    {
        $resource = fopen($filename, 'rb+');

        if ($resource === false) {
            throw new CouldNotOpenFileException();
        }

        $this->handle = $resource;

        return $this;
    }

    /**
     * @param int $offset
     *
     * @return AbstractFileHandler
     */
    public function seek(int $offset): AbstractFileHandler
    {
        fseek($this->handle, $offset);

        return $this;
    }

    /**
     * @param int $length
     *
     * @return string|null
     */
    public function read(int $length): ?string
    {
        $value = fread($this->handle, $length);

        if ($value === false) {
            $value = null;
        }

        return $value;
    }

    /**
     * @param string $data
     *
     * @return AbstractFileHandler
     */
    public function write(string $data): AbstractFileHandler
    {
        fwrite($this->handle, $data, strlen($data));

        return $this;
    }
}
