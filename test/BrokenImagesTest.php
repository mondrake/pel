<?php

namespace ExifEye\Test\core;

use ExifEye\core\Image;

class BrokenImagesTest extends ExifEyeTestCaseBase
{
    public function testWindowWindowExceptionIsCaught()
    {
        $image = Image::loadFromFile(dirname(__FILE__) . '/broken_images/gh-10-a.jpg');
        $this->assertInstanceOf('ExifEye\core\Block\Jpeg', $image->first("jpeg"));
    }

    public function testWindowOffsetExceptionIsCaught()
    {
        $image = Image::loadFromFile(dirname(__FILE__) . '/broken_images/gh-10-b.jpg');
        $this->assertInstanceOf('ExifEye\core\Block\Jpeg', $image->first("jpeg"));
    }

    public function testParsingNotFailingOnRecursingIfd()
    {
        $image = Image::loadFromFile(dirname(__FILE__) . '/broken_images/gh-11.jpg');
        $this->assertInstanceOf('ExifEye\core\Block\Jpeg', $image->first("jpeg"));
    }
}
