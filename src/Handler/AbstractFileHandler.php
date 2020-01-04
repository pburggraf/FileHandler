<?php

declare(strict_types=1);

namespace PBurggraf\FileHandler\Handler;

/**
 * @author Philip Burggraf <philip@pburggraf.de>
 */
abstract class AbstractFileHandler
{
    public const MODE_OVERWRITE = 0;
    public const MODE_INSERT = 1;

    /**
     * @var int
     */
    protected $offset;

    /**
     * @param string $filename
     *
     * @return AbstractFileHandler
     */
    abstract public function open(string $filename): AbstractFileHandler;

    /**
     * @param int $offset
     *
     * @return AbstractFileHandler
     */
    abstract public function seek(int $offset): AbstractFileHandler;

    /**
     * @param int $length
     *
     * @return string|null
     */
    abstract public function read(int $length): ?string;

    /**
     * @param string $data
     *
     * @return AbstractFileHandler
     */
    abstract public function write(string $data): AbstractFileHandler;
}
