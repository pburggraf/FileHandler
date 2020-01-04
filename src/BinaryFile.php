<?php

declare(strict_types=1);

namespace PBurggraf\FileHandler;

use PBurggraf\FileHandler\Exception\GetValueException;
use PBurggraf\FileHandler\Exception\ValueExceededException;
use PBurggraf\FileHandler\Exception\InvalidValueSizeException;
use PBurggraf\FileHandler\Handler\AbstractFileHandler;

/**
 * @author Philip Burggraf <philip@pburggraf.de>
 */
class BinaryFile
{
    public const ENDIAN_LITTLE = 'little';
    public const ENDIAN_BIG = 'big';

    protected AbstractFileHandler $fileHandler;

    /**
     * @param AbstractFileHandler $fileHandler
     */
    public function __construct(AbstractFileHandler $fileHandler)
    {
        $this->fileHandler = $fileHandler;
    }

    /**
     * @param int $offset
     *
     * @throws GetValueException
     *
     * @return int
     */
    public function getByte(int $offset): int
    {
        $this->fileHandler->seek($offset);

        $data = $this->fileHandler->read(1);

        if ($data === null) {
            throw new GetValueException();
        }

        return ord($data);
    }

    /**
     * @param int $offset
     * @param int $data
     *
     * @throws ValueExceededException
     */
    public function setByte(int $offset, int $data): void
    {
        $this->fileHandler->seek($offset);

        if ($data > 0xff) {
            throw new ValueExceededException('Byte value exceeded');
        }

        $this->fileHandler->write(chr($data));
    }

    /**
     * @param int $offset
     * @param string $endian
     *
     * @throws GetValueException
     * @throws InvalidValueSizeException
     *
     * @return int
     */
    public function getShort(int $offset, string $endian = self::ENDIAN_LITTLE): int
    {
        $this->fileHandler->seek($offset);

        $data = $this->fileHandler->read(2);

        if ($data === null) {
            throw new GetValueException();
        }

        if (strlen($data) < 2) {
            $data = str_pad($data, 2, chr(0), STR_PAD_LEFT);
        }

        if (strlen($data) > 2) {
            throw new InvalidValueSizeException('Invalid size of value');
        }

        $endianness = 'v';

        if ($endian === self::ENDIAN_BIG) {
            $endianness = 'n';
        }

        $value = unpack($endianness, $data);

        $result = 0;

        if (is_array($value) && count($value) === 1) {
            $result = $value[1];
        }

        return $result;
    }

    /**
     * @param int $offset
     * @param int $value
     *
     * @throws ValueExceededException
     */
    public function setShort(int $offset, int $value): void
    {
        $this->fileHandler->seek($offset);

        if ($value > 0xffff) {
            throw new ValueExceededException('Short value exceeded');
        }

        $this->fileHandler->write(((int) chr($value) >> 8) . chr($value));
    }

    /**
     * @param int $offset
     * @param string $endian
     *
     * @throws GetValueException
     * @throws ValueExceededException
     *
     * @return int
     */
    public function getInteger(int $offset, string $endian = self::ENDIAN_LITTLE): int
    {
        $this->fileHandler->seek($offset);

        $data = $this->fileHandler->read(4);

        if ($data === null) {
            throw new GetValueException();
        }

        if (strlen($data) < 4) {
            $data = str_pad($data, 4, chr(0), STR_PAD_LEFT);
        }

        if (strlen($data) > 4) {
            throw new ValueExceededException('Invalid size of value');
        }

        $endianess = 'V';

        if ($endian === self::ENDIAN_BIG) {
            $endianess = 'N';
        }

        $value = unpack($endianess, $data);

        $result = 0;

        if (is_array($value) && count($value) === 1) {
            $result = $value[1];
        }

        return $result;
    }

    /**
     * @param int $offset
     * @param int $value
     *
     * @throws ValueExceededException
     */
    public function setInteger(int $offset, int $value): void
    {
        $this->fileHandler->seek($offset);

        if ($value > 0xffffffff) {
            throw new ValueExceededException('Integer value exceeded');
        }

        $this->fileHandler->write(((int) chr($value) >> 24) . ((int) chr($value) >> 16) . ((int) chr($value) >> 8) . chr($value));
    }

    /**
     * @param int $offset
     * @param int $length
     *
     * @throws GetValueException
     *
     * @return string
     */
    public function getBytesAsString(int $offset, int $length): string
    {
        $this->fileHandler->seek($offset);

        $data = $this->fileHandler->read($length);

        if ($data === null) {
            throw new GetValueException();
        }

        return $data;
    }

    /**
     * @param int $offset
     * @param int $terminator
     *
     * @throws GetValueException
     *
     * @return string
     */
    public function getBytesAsStringUntilTerminator(int $offset, int $terminator): string
    {
        $this->fileHandler->seek($offset);

        $string = '';

        do {
            $data = $this->fileHandler->read(1);

            if ($data === null) {
                throw new GetValueException();
            }

            $string .= $data;
        } while (ord($data) !== $terminator);

        return $string;
    }

    /**
     * @param int $offset
     * @param int $length
     *
     * @return array<int>
     */
    public function getBytes(int $offset, int $length): array
    {
        $string = $this->getBytesAsString($offset, $length);

        return $this->getBytesFromString($string);
    }

    /**
     * @param string $string
     *
     * @return array<int>
     */
    protected function getBytesFromString(string $string): array
    {
        $byteArray = str_split($string, 1);

        $bytes = array_map(static function ($data) {
            return ord($data);
        }, $byteArray);

        return $bytes;
    }
}
