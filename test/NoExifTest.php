<?php

namespace ExifEye\Test\core;

use ExifEye\core\ExifEye;
use ExifEye\core\Image;
use ExifEye\core\Block\Jpeg;

class NoExifTest extends ExifEyeTestCaseBase
{
    public function testRead()
    {
        $image = Image::loadFromFile(dirname(__FILE__) . '/image_files/no-exif.jpg');
        $this->assertNull($image->getElement("jpeg/segment/exif"));
        $this->assertEmpty($image->dumpLog());
    }
}
