<?php

namespace ExifEye\Test\core;

use ExifEye\core\DataString;
use ExifEye\core\DataElement;
use ExifEye\core\DataWindow;
use ExifEye\core\DataException;
use ExifEye\core\Utility\ConvertBytes;

class DataWindowTest extends ExifEyeTestCaseBase
{
    public function testReadBytes()
    {
        $data = new DataString('abcdefgh');
        $window = new DataWindow($data, 0, $data->getSize());

        $this->assertEquals($window->getSize(), 8);
        $this->assertEquals($window->getBytes(), 'abcdefgh');

        $this->assertEquals($window->getBytes(0), 'abcdefgh');
        $this->assertEquals($window->getBytes(1), 'bcdefgh');
        $this->assertEquals($window->getBytes(7), 'h');
        // $this->assertEquals($window->getBytes(8), '');

        $this->assertEquals($window->getBytes(- 1), 'h');
        $this->assertEquals($window->getBytes(- 2), 'gh');
        $this->assertEquals($window->getBytes(- 7), 'bcdefgh');
        $this->assertEquals($window->getBytes(- 8), 'abcdefgh');

        $clone = $window->getClone(2, 4);
        $this->assertEquals($clone->getSize(), 4);
        $this->assertEquals($clone->getBytes(), 'cdef');

        $this->assertEquals($clone->getBytes(0), 'cdef');
        $this->assertEquals($clone->getBytes(1), 'def');
        $this->assertEquals($clone->getBytes(3), 'f');
        // $this->assertEquals($clone->getBytes(4), '');

        $this->assertEquals($clone->getBytes(- 1), 'f');
        $this->assertEquals($clone->getBytes(- 2), 'ef');
        $this->assertEquals($clone->getBytes(- 3), 'def');
        $this->assertEquals($clone->getBytes(- 4), 'cdef');

        $caught = false;
        try {
            $clone->getBytes(0, 6);
        } catch (DataException $e) {
            $caught = true;
        }
        $this->assertTrue($caught);
    }

    public function testReadIntegers()
    {
        $data = new DataString("\x01\x02\x03\x04");
        $window = new DataWindow($data, 0, $data->getSize(), null, ConvertBytes::BIG_ENDIAN);

        $this->assertEquals($window->getSize(), 4);
        $this->assertEquals($window->getBytes(), "\x01\x02\x03\x04");

        $this->assertEquals($window->getByte(0), 0x01);
        $this->assertEquals($window->getByte(1), 0x02);
        $this->assertEquals($window->getByte(2), 0x03);
        $this->assertEquals($window->getByte(3), 0x04);

        $this->assertEquals($window->getShort(0), 0x0102);
        $this->assertEquals($window->getShort(1), 0x0203);
        $this->assertEquals($window->getShort(2), 0x0304);

        $this->assertEquals($window->getLong(0), 0x01020304);

        $window->setByteOrder(ConvertBytes::LITTLE_ENDIAN);
        $this->assertEquals($window->getSize(), 4);
        $this->assertEquals($window->getBytes(), "\x01\x02\x03\x04");

        $this->assertEquals($window->getByte(0), 0x01);
        $this->assertEquals($window->getByte(1), 0x02);
        $this->assertEquals($window->getByte(2), 0x03);
        $this->assertEquals($window->getByte(3), 0x04);

        $this->assertEquals($window->getShort(0), 0x0201);
        $this->assertEquals($window->getShort(1), 0x0302);
        $this->assertEquals($window->getShort(2), 0x0403);

        $this->assertEquals($window->getLong(0), 0x04030201);
    }

    public function testReadBigIntegers()
    {
        $data = new DataString("\x89\xAB\xCD\xEF");
        $window = new DataWindow($data, 0, $data->getSize(), null, ConvertBytes::BIG_ENDIAN);

        $this->assertEquals($window->getSize(), 4);
        $this->assertEquals($window->getBytes(), "\x89\xAB\xCD\xEF");

        $this->assertEquals($window->getByte(0), 0x89);
        $this->assertEquals($window->getByte(1), 0xAB);
        $this->assertEquals($window->getByte(2), 0xCD);
        $this->assertEquals($window->getByte(3), 0xEF);

        $this->assertEquals($window->getShort(0), 0x89AB);
        $this->assertEquals($window->getShort(1), 0xABCD);
        $this->assertEquals($window->getShort(2), 0xCDEF);

        $this->assertEquals($window->getLong(0), 0x89ABCDEF);

        $window->setByteOrder(ConvertBytes::LITTLE_ENDIAN);
        $this->assertEquals($window->getSize(), 4);
        $this->assertEquals($window->getBytes(), "\x89\xAB\xCD\xEF");

        $this->assertEquals($window->getByte(0), 0x89);
        $this->assertEquals($window->getByte(1), 0xAB);
        $this->assertEquals($window->getByte(2), 0xCD);
        $this->assertEquals($window->getByte(3), 0xEF);

        $this->assertEquals($window->getShort(0), 0xAB89);
        $this->assertEquals($window->getShort(1), 0xCDAB);
        $this->assertEquals($window->getShort(2), 0xEFCD);

        $this->assertEquals($window->getLong(0), 0xEFCDAB89);
    }
}
