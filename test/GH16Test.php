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
        $jpeg = new Jpeg($this->file);
        $exif = $jpeg->getExif();
        $ifd0 = $exif->first("tiff/ifd[@name='IFD0']");
        $this->assertCount(1, $ifd0->query("tag"));
        $this->assertEquals('Ïðåâåä, ìåäâåä!', $ifd0->first("tag[@name='WindowsXPSubject']/Entry")->toString());

        $ifd0->remove("tag[@name='WindowsXPSubject']");
        $new_entry_value = "Превед, медвед!";
        new Tag($ifd0, 0x9C9F, 'ExifEye\core\Entry\WindowsString', [$new_entry_value]);
        $this->assertCount(1, $ifd0->query('tag'));

        $jpeg->saveFile($this->file);

        $jpeg = new Jpeg($this->file);
        $exif = $jpeg->getExif();
        $ifd0 = $exif->first("tiff/ifd[@name='IFD0']");
        $this->assertCount(1, $exif->query("tiff/ifd[@name='IFD0']/tag"));
        $written_subject = $ifd0->first("tag[@name='WindowsXPSubject']/Entry")->toString();
        $this->assertEquals($new_entry_value, $written_subject);
    }
}
