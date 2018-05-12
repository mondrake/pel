<?php

namespace ExifEye\Test\core;

use ExifEye\core\Block\Jpeg;
use ExifEye\core\Block\Tiff;
use ExifEye\core\ExifEye;

class GH77Test extends ExifEyeTestCaseBase
{
    public function testReturnModel()
    {
        $file = dirname(__FILE__) . '/images/gh-77.jpg';

        $input_jpeg = new Jpeg($file);
        $app1 = $input_jpeg->getExif();

        $ifd0 = $app1->first("tiff/Ifd[@name='IFD0']");

        $model = $ifd0->first("Tag[@name='Model']/Entry")->getValue();
        $this->assertEquals($model, "Canon EOS 5D Mark III");

        $copyright_entry = $ifd0->first("Tag[@name='Copyright']/Entry");
        $this->assertInstanceOf('ExifEye\core\Entry\IfdCopyright', $copyright_entry);
        $this->assertEquals(['Copyright 2016', ''], $copyright_entry->getValue());
        $this->assertEquals('Copyright 2016 (Photographer)', $copyright_entry->toString());
    }
}
