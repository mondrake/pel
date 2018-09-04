<?php

namespace ExifEye\Test\core;

use ExifEye\core\Utility\ConvertBytes;

class ConvertTest extends ExifEyeTestCaseBase
{

    private $bytes = "\x00\x00\x00\x00\x01\x23\x45\x67\x89\xAB\xCD\xEF\xFF\xFF\xFF\xFF";

    public function testLongLittle()
    {
        $o = ConvertBytes::LITTLE_ENDIAN;

        $this->assertEquals(ConvertBytes::toLong($this->bytes, 0, $o), 0x00000000);
        $this->assertEquals(ConvertBytes::toLong($this->bytes, 1, $o), 0x01000000);
        $this->assertEquals(ConvertBytes::toLong($this->bytes, 2, $o), 0x23010000);
        $this->assertEquals(ConvertBytes::toLong($this->bytes, 3, $o), 0x45230100);
        $this->assertEquals(ConvertBytes::toLong($this->bytes, 4, $o), 0x67452301);
        $this->assertEquals(ConvertBytes::toLong($this->bytes, 5, $o), 0x89674523);
        $this->assertEquals(ConvertBytes::toLong($this->bytes, 6, $o), 0xAB896745);
        $this->assertEquals(ConvertBytes::toLong($this->bytes, 7, $o), 0xCDAB8967);
        $this->assertEquals(ConvertBytes::toLong($this->bytes, 8, $o), 0xEFCDAB89);
        $this->assertEquals(ConvertBytes::toLong($this->bytes, 9, $o), 0xFFEFCDAB);
        $this->assertEquals(ConvertBytes::toLong($this->bytes, 10, $o), 0xFFFFEFCD);
        $this->assertEquals(ConvertBytes::toLong($this->bytes, 11, $o), 0xFFFFFFEF);
        $this->assertEquals(ConvertBytes::toLong($this->bytes, 12, $o), 0xFFFFFFFF);
    }

    public function testLongBig()
    {
        $o = ConvertBytes::BIG_ENDIAN;

        $this->assertEquals(ConvertBytes::toLong($this->bytes, 0, $o), 0x00000000);
        $this->assertEquals(ConvertBytes::toLong($this->bytes, 1, $o), 0x00000001);
        $this->assertEquals(ConvertBytes::toLong($this->bytes, 2, $o), 0x00000123);
        $this->assertEquals(ConvertBytes::toLong($this->bytes, 3, $o), 0x00012345);
        $this->assertEquals(ConvertBytes::toLong($this->bytes, 4, $o), 0x01234567);
        $this->assertEquals(ConvertBytes::toLong($this->bytes, 5, $o), 0x23456789);
        $this->assertEquals(ConvertBytes::toLong($this->bytes, 6, $o), 0x456789AB);
        $this->assertEquals(ConvertBytes::toLong($this->bytes, 7, $o), 0x6789ABCD);
        $this->assertEquals(ConvertBytes::toLong($this->bytes, 8, $o), 0x89ABCDEF);
        $this->assertEquals(ConvertBytes::toLong($this->bytes, 9, $o), 0xABCDEFFF);
        $this->assertEquals(ConvertBytes::toLong($this->bytes, 10, $o), 0xCDEFFFFF);
        $this->assertEquals(ConvertBytes::toLong($this->bytes, 11, $o), 0xEFFFFFFF);
        $this->assertEquals(ConvertBytes::toLong($this->bytes, 12, $o), 0xFFFFFFFF);
    }

    public function testSignedLongLittle()
    {
        $o = ConvertBytes::LITTLE_ENDIAN;

        $this->assertEquals(ConvertBytes::toSignedLong($this->bytes, 0, $o), 0);
        $this->assertEquals(ConvertBytes::toSignedLong($this->bytes, 1, $o), 16777216);
        $this->assertEquals(ConvertBytes::toSignedLong($this->bytes, 2, $o), 587268096);
        $this->assertEquals(ConvertBytes::toSignedLong($this->bytes, 3, $o), 1159921920);
        $this->assertEquals(ConvertBytes::toSignedLong($this->bytes, 4, $o), 1732584193);
        $this->assertEquals(ConvertBytes::toSignedLong($this->bytes, 5, $o), -1989720797);
        $this->assertEquals(ConvertBytes::toSignedLong($this->bytes, 6, $o), -1417058491);
        $this->assertEquals(ConvertBytes::toSignedLong($this->bytes, 7, $o), -844396185);
        $this->assertEquals(ConvertBytes::toSignedLong($this->bytes, 8, $o), -271733879);
        $this->assertEquals(ConvertBytes::toSignedLong($this->bytes, 9, $o), -1061461);
        $this->assertEquals(ConvertBytes::toSignedLong($this->bytes, 10, $o), -4147);
        $this->assertEquals(ConvertBytes::toSignedLong($this->bytes, 11, $o), -17);
        $this->assertEquals(ConvertBytes::toSignedLong($this->bytes, 12, $o), -1);
    }

    public function testSignedLongBig()
    {
        $o = ConvertBytes::BIG_ENDIAN;

        $this->assertEquals(ConvertBytes::toSignedLong($this->bytes, 0, $o), 0);
        $this->assertEquals(ConvertBytes::toSignedLong($this->bytes, 1, $o), 1);
        $this->assertEquals(ConvertBytes::toSignedLong($this->bytes, 2, $o), 291);
        $this->assertEquals(ConvertBytes::toSignedLong($this->bytes, 3, $o), 74565);
        $this->assertEquals(ConvertBytes::toSignedLong($this->bytes, 4, $o), 19088743);
        $this->assertEquals(ConvertBytes::toSignedLong($this->bytes, 5, $o), 591751049);
        $this->assertEquals(ConvertBytes::toSignedLong($this->bytes, 6, $o), 1164413355);
        $this->assertEquals(ConvertBytes::toSignedLong($this->bytes, 7, $o), 1737075661);
        $this->assertEquals(ConvertBytes::toSignedLong($this->bytes, 8, $o), -1985229329);
        $this->assertEquals(ConvertBytes::toSignedLong($this->bytes, 9, $o), -1412567041);
        $this->assertEquals(ConvertBytes::toSignedLong($this->bytes, 10, $o), -839909377);
        $this->assertEquals(ConvertBytes::toSignedLong($this->bytes, 11, $o), -268435457);
        $this->assertEquals(ConvertBytes::toSignedLong($this->bytes, 12, $o), -1);
    }

    public function testShortLittle()
    {
        $this->assertSame(     0, ConvertBytes::toShort("\x00\x00\x00\x00\x01\x23\x45\x67\x89\xAB\xCD\xEF\xFF\xFF\xFF\xFF"), ConvertBytes::LITTLE_ENDIAN);
        $this->assertSame(     0, ConvertBytes::toShort("\x00\x00\x00\x01\x23\x45\x67\x89\xAB\xCD\xEF\xFF\xFF\xFF\xFF"), ConvertBytes::LITTLE_ENDIAN);
        $this->assertSame(     0, ConvertBytes::toShort("\x00\x00\x01\x23\x45\x67\x89\xAB\xCD\xEF\xFF\xFF\xFF\xFF"), ConvertBytes::LITTLE_ENDIAN);
        $this->assertSame(     0, ConvertBytes::toShort("\x00\x01\x23\x45\x67\x89\xAB\xCD\xEF\xFF\xFF\xFF\xFF"), ConvertBytes::LITTLE_ENDIAN);
        $this->assertSame(   256, ConvertBytes::toShort("\x01\x23\x45\x67\x89\xAB\xCD\xEF\xFF\xFF\xFF\xFF"), ConvertBytes::LITTLE_ENDIAN);
        $this->assertSame(  8961, ConvertBytes::toShort("\x23\x45\x67\x89\xAB\xCD\xEF\xFF\xFF\xFF\xFF"), ConvertBytes::LITTLE_ENDIAN);
        $this->assertSame( 17699, ConvertBytes::toShort("\x45\x67\x89\xAB\xCD\xEF\xFF\xFF\xFF\xFF"), ConvertBytes::LITTLE_ENDIAN);
        $this->assertSame( 26437, ConvertBytes::toShort("\x67\x89\xAB\xCD\xEF\xFF\xFF\xFF\xFF"), ConvertBytes::LITTLE_ENDIAN);
        $this->assertSame( 35175, ConvertBytes::toShort("\x89\xAB\xCD\xEF\xFF\xFF\xFF\xFF"), ConvertBytes::LITTLE_ENDIAN);
        $this->assertSame( 43913, ConvertBytes::toShort("\xAB\xCD\xEF\xFF\xFF\xFF\xFF"), ConvertBytes::LITTLE_ENDIAN);
        $this->assertSame( 52651, ConvertBytes::toShort("\xCD\xEF\xFF\xFF\xFF\xFF"), ConvertBytes::LITTLE_ENDIAN);
        $this->assertSame( 61389, ConvertBytes::toShort("\xEF\xFF\xFF\xFF\xFF"), ConvertBytes::LITTLE_ENDIAN);
        $this->assertSame( 65519, ConvertBytes::toShort("\xFF\xFF\xFF\xFF"), ConvertBytes::LITTLE_ENDIAN);
        $this->assertSame( 65535, ConvertBytes::toShort("\xFF\xFF\xFF"), ConvertBytes::LITTLE_ENDIAN);
        $this->assertSame( 65535, ConvertBytes::toShort("\xFF\xFF"), ConvertBytes::LITTLE_ENDIAN);
        if (method_exists($this, 'expectException')) {
            $this->expectException('InvalidArgumentException');
        } else {
            $this->setExpectedException('InvalidArgumentException');
        }
        ConvertBytes::toShort("\xFF");
    }

    public function testShortBig()
    {
        $o = ConvertBytes::BIG_ENDIAN;

        $this->assertEquals(ConvertBytes::toShort($this->bytes, 0, $o), 0x0000);
        $this->assertEquals(ConvertBytes::toShort($this->bytes, 1, $o), 0x0000);
        $this->assertEquals(ConvertBytes::toShort($this->bytes, 2, $o), 0x0000);
        $this->assertEquals(ConvertBytes::toShort($this->bytes, 3, $o), 0x0001);
        $this->assertEquals(ConvertBytes::toShort($this->bytes, 4, $o), 0x0123);
        $this->assertEquals(ConvertBytes::toShort($this->bytes, 5, $o), 0x2345);
        $this->assertEquals(ConvertBytes::toShort($this->bytes, 6, $o), 0x4567);
        $this->assertEquals(ConvertBytes::toShort($this->bytes, 7, $o), 0x6789);
        $this->assertEquals(ConvertBytes::toShort($this->bytes, 8, $o), 0x89AB);
        $this->assertEquals(ConvertBytes::toShort($this->bytes, 9, $o), 0xABCD);
        $this->assertEquals(ConvertBytes::toShort($this->bytes, 10, $o), 0xCDEF);
        $this->assertEquals(ConvertBytes::toShort($this->bytes, 11, $o), 0xEFFF);
        $this->assertEquals(ConvertBytes::toShort($this->bytes, 12, $o), 0xFFFF);
        $this->assertEquals(ConvertBytes::toShort($this->bytes, 13, $o), 0xFFFF);
        $this->assertEquals(ConvertBytes::toShort($this->bytes, 14, $o), 0xFFFF);
    }

    public function testSignedShortLittle()
    {
        $o = ConvertBytes::LITTLE_ENDIAN;

        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 0, $o), 0);
        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 1, $o), 0);
        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 2, $o), 0);
        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 3, $o), 256);
        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 4, $o), 8961);
        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 5, $o), 17699);
        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 6, $o), 26437);
        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 7, $o), -30361);
        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 8, $o), -21623);
        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 9, $o), -12885);
        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 10, $o), -4147);
        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 11, $o), -17);
        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 12, $o), -1);
        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 13, $o), -1);
        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 14, $o), -1);
    }

    public function testSignedShortBig()
    {
        $o = ConvertBytes::BIG_ENDIAN;

        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 0, $o), 0);
        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 1, $o), 0);
        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 2, $o), 0);
        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 3, $o), 1);
        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 4, $o), 291);
        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 5, $o), 9029);
        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 6, $o), 17767);
        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 7, $o), 26505);
        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 8, $o), -30293);
        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 9, $o), -21555);
        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 10, $o), -12817);
        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 11, $o), -4097);
        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 12, $o), -1);
        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 13, $o), -1);
        $this->assertEquals(ConvertBytes::toSignedShort($this->bytes, 14, $o), -1);
    }

    public function testByte()
    {
        $this->assertSame(   0, ConvertBytes::toByte("\x00\x00\x00\x00\x01\x23\x45\x67\x89\xAB\xCD\xEF\xFF\xFF\xFF\xFF"));
        $this->assertSame(   0, ConvertBytes::toByte("\x00\x00\x00\x01\x23\x45\x67\x89\xAB\xCD\xEF\xFF\xFF\xFF\xFF"));
        $this->assertSame(   0, ConvertBytes::toByte("\x00\x00\x01\x23\x45\x67\x89\xAB\xCD\xEF\xFF\xFF\xFF\xFF"));
        $this->assertSame(   0, ConvertBytes::toByte("\x00\x01\x23\x45\x67\x89\xAB\xCD\xEF\xFF\xFF\xFF\xFF"));
        $this->assertSame(   1, ConvertBytes::toByte("\x01\x23\x45\x67\x89\xAB\xCD\xEF\xFF\xFF\xFF\xFF"));
        $this->assertSame(  35, ConvertBytes::toByte("\x23\x45\x67\x89\xAB\xCD\xEF\xFF\xFF\xFF\xFF"));
        $this->assertSame(  69, ConvertBytes::toByte("\x45\x67\x89\xAB\xCD\xEF\xFF\xFF\xFF\xFF"));
        $this->assertSame( 103, ConvertBytes::toByte("\x67\x89\xAB\xCD\xEF\xFF\xFF\xFF\xFF"));
        $this->assertSame( 137, ConvertBytes::toByte("\x89\xAB\xCD\xEF\xFF\xFF\xFF\xFF"));
        $this->assertSame( 171, ConvertBytes::toByte("\xAB\xCD\xEF\xFF\xFF\xFF\xFF"));
        $this->assertSame( 205, ConvertBytes::toByte("\xCD\xEF\xFF\xFF\xFF\xFF"));
        $this->assertSame( 239, ConvertBytes::toByte("\xEF\xFF\xFF\xFF\xFF"));
        $this->assertSame( 255, ConvertBytes::toByte("\xFF\xFF\xFF\xFF"));
        $this->assertSame( 255, ConvertBytes::toByte("\xFF\xFF\xFF"));
        $this->assertSame( 255, ConvertBytes::toByte("\xFF\xFF"));
        $this->assertSame( 255, ConvertBytes::toByte("\xFF"));
        if (method_exists($this, 'expectException')) {
            $this->expectException('InvalidArgumentException');
        } else {
            $this->setExpectedException('InvalidArgumentException');
        }
        ConvertBytes::toByte("");
    }

    public function testSignedByte()
    {
        $this->assertSame(   0, ConvertBytes::toSignedByte("\x00\x00\x00\x00\x01\x23\x45\x67\x89\xAB\xCD\xEF\xFF\xFF\xFF\xFF"));
        $this->assertSame(   0, ConvertBytes::toSignedByte("\x00\x00\x00\x01\x23\x45\x67\x89\xAB\xCD\xEF\xFF\xFF\xFF\xFF"));
        $this->assertSame(   0, ConvertBytes::toSignedByte("\x00\x00\x01\x23\x45\x67\x89\xAB\xCD\xEF\xFF\xFF\xFF\xFF"));
        $this->assertSame(   0, ConvertBytes::toSignedByte("\x00\x01\x23\x45\x67\x89\xAB\xCD\xEF\xFF\xFF\xFF\xFF"));
        $this->assertSame(   1, ConvertBytes::toSignedByte("\x01\x23\x45\x67\x89\xAB\xCD\xEF\xFF\xFF\xFF\xFF"));
        $this->assertSame(  35, ConvertBytes::toSignedByte("\x23\x45\x67\x89\xAB\xCD\xEF\xFF\xFF\xFF\xFF"));
        $this->assertSame(  69, ConvertBytes::toSignedByte("\x45\x67\x89\xAB\xCD\xEF\xFF\xFF\xFF\xFF"));
        $this->assertSame( 103, ConvertBytes::toSignedByte("\x67\x89\xAB\xCD\xEF\xFF\xFF\xFF\xFF"));
        $this->assertSame(-119, ConvertBytes::toSignedByte("\x89\xAB\xCD\xEF\xFF\xFF\xFF\xFF"));
        $this->assertSame( -85, ConvertBytes::toSignedByte("\xAB\xCD\xEF\xFF\xFF\xFF\xFF"));
        $this->assertSame( -51, ConvertBytes::toSignedByte("\xCD\xEF\xFF\xFF\xFF\xFF"));
        $this->assertSame( -17, ConvertBytes::toSignedByte("\xEF\xFF\xFF\xFF\xFF"));
        $this->assertSame(  -1, ConvertBytes::toSignedByte("\xFF\xFF\xFF\xFF"));
        $this->assertSame(  -1, ConvertBytes::toSignedByte("\xFF\xFF\xFF"));
        $this->assertSame(  -1, ConvertBytes::toSignedByte("\xFF\xFF"));
        $this->assertSame(  -1, ConvertBytes::toSignedByte("\xFF"));
        if (method_exists($this, 'expectException')) {
            $this->expectException('InvalidArgumentException');
        } else {
            $this->setExpectedException('InvalidArgumentException');
        }
        ConvertBytes::toSignedByte("");
    }
}
