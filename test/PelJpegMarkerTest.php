<?php

namespace ExifEye\Test\core;

use ExifEye\core\ExifEye;
use ExifEye\core\JpegMarker;

class PelJpegMarkerTest extends ExifEyeTestCaseBase
{

    public function testNames()
    {
        $jpegMarker = new JpegMarker();
        $this->assertEquals('SOF0', $jpegMarker::getName(0xC0));
        $this->assertEquals('RST3', $jpegMarker::getName(0xD3));
        $this->assertEquals('APP3', $jpegMarker::getName(0xE3));
        $this->assertEquals('JPG11',$jpegMarker::getName(0xFB));
        $this->assertEquals(null, $jpegMarker::getName(100));
    }

    public function testDescriptions()
    {
        $jpegMarker = new JpegMarker();
        $this->assertEquals('Encoding (baseline)', $jpegMarker::getDescription(0xC0));
        $this->assertEquals(ExifEye::fmt('Restart %d', 3), $jpegMarker::getDescription(0xD3));
        $this->assertEquals(ExifEye::fmt('Application segment %d', 3), $jpegMarker::getDescription(0xE3));
        $this->assertEquals(ExifEye::fmt('Extension %d', 11), $jpegMarker::getDescription(0xFB));
        $this->assertEquals(null, $jpegMarker::getDescription(100));
    }
}
