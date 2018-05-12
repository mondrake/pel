<?php

namespace ExifEye\Test\core;

use ExifEye\core\Block\Exif;
use ExifEye\core\Block\Ifd;
use ExifEye\core\Block\Tag;
use ExifEye\core\Block\Tiff;
use ExifEye\core\ExifEye;
use ExifEye\core\Entry\WindowsString;
use ExifEye\core\Block\Jpeg;

class GH16Test extends ExifEyeTestCaseBase
{
    protected $file;

    public function setUp()
    {
        parent::setUp();
        $this->file = dirname(__FILE__) . '/images/gh-16-tmp.jpg';
        $file = dirname(__FILE__) . '/images/gh-16.jpg';
        copy($file, $this->file);
    }

    public function tearDown()
    {
        unlink($this->file);
    }

    public function testThisDoesNotWorkAsExpected()
    {
        $subject = "Превед, медвед!";

        $jpeg = new Jpeg($this->file);
        $exif = $jpeg->getExif();
        $tiff = $exif->first("tiff");
        $ifd0 = $tiff->first("Ifd[@name='IFD0']");
        $this->assertCount(1, $tiff->query("Ifd[@name='IFD0']/Tag"));

        $ifd0->xxAddSubBlock(new Tag($ifd0, 0x9C9F, 'ExifEye\core\Entry\WindowsString', [$subject]));
        $this->assertCount(1, $ifd0->xxGetSubBlocks('Tag'));

        $jpeg->saveFile($this->file);

        $jpeg = new Jpeg($this->file);
        $exif = $jpeg->getExif();
        $tiff = $exif->first("tiff");
        $ifd0 = $tiff->first("Ifd[@name='IFD0']");
        $this->assertCount(1, $tiff->query("Ifd[@name='IFD0']/Tag"));
        $written_subject = $ifd0->xxGetSubBlockByName('Tag', 'WindowsXPSubject')->getEntry()->toString();
        $this->assertEquals($subject, $written_subject);
    }
}
