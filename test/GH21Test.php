<?php

namespace ExifEye\Test\core;

use ExifEye\core\DataWindow;
use ExifEye\core\Block\Exif;
use ExifEye\core\Block\Jpeg;
use ExifEye\core\Block\JpegSegment;
use ExifEye\core\Image;

class GH21Test extends ExifEyeTestCaseBase
{
    protected $file;

    public function setUp()
    {
        parent::setUp();
        $this->file = dirname(__FILE__) . '/image_files/gh-21-tmp.jpg';
        $file = dirname(__FILE__) . '/image_files/gh-21.jpg';
        copy($file, $this->file);
    }

    public function tearDown()
    {
        unlink($this->file);
    }

    public function testThisDoesNotWorkAsExpected()
    {
        $scale = 0.75;
        $input_image = Image::loadFromFile($this->file);
        $input_jpeg = $input_image->getElement("jpeg");

        $original = ImageCreateFromString($input_jpeg->toBytes());
        $original_w = ImagesX($original);
        $original_h = ImagesY($original);

        $scaled_w = $original_w * $scale;
        $scaled_h = $original_h * $scale;

        $scaled = ImageCreateTrueColor($scaled_w, $scaled_h);
        ImageCopyResampled(
            $scaled,
            $original,
            0,
            0, /* dst (x,y) */
            0,
            0, /* src (x,y) */
            $scaled_w,
            $scaled_h,
            $original_w,
            $original_h
        );

        $out_image = Image::createFromData(new DataWindow($scaled));
        $out_jpeg = $out_image->getElement("jpeg");

        $exif = $input_jpeg->getElement("jpegSegment/exif");

        // Find the COM segment in the output file.
        $out_com_segment = $out_jpeg->getElement("jpegSegment[@name='COM']");

        // Insert the APP1 segment before the COM one.
        $out_app1_segment = new JpegSegment(0xE1, $out_jpeg, $out_com_segment);

        // Add the EXIF block to the APP1 segment.
        $exif_block = new Exif($out_app1_segment);
        $exif_block->loadFromData(new DataWindow($exif->toBytes()));

        $out_image->saveToFile($this->file);

        $image = Image::loadFromFile($this->file);
dump($image);
        $jpeg = $image->getElement("jpeg");
        $exifin = $jpeg->getElement("jpegSegment/exif");
        $this->assertEquals($exif, $exifin);
    }
}
