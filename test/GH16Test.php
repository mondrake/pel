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

dump($ifd0->DOMNode->ownerDocument->saveXML());
        $ifd0->remove("tag[@name='WindowsXPSubject']");
dump($ifd0->DOMNode->ownerDocument->saveXML());
        $new_entry_value = "Превед, медвед!";
        new Tag($ifd0, 0x9C9F, 'ExifEye\core\Entry\WindowsString', [$new_entry_value]);
dump($ifd0->DOMNode->ownerDocument->saveXML());
        $this->assertCount(1, $ifd0->query('tag'));
        $this->assertEquals($new_entry_value, $ifd0->first("tag[@name='WindowsXPSubject']/Entry")->toString());
dump($ifd0->DOMNode->ownerDocument->saveXML());
        $jpeg->saveFile($this->file);

        $r_jpeg = new Jpeg($this->file);
        $r_exif = $r_jpeg->getExif();
        $r_ifd0 = $r_exif->first("tiff/ifd[@name='IFD0']");
        $this->assertCount(1, $r_exif->query("tiff/ifd[@name='IFD0']/tag"));
        $written_subject = $r_ifd0->first("tag[@name='WindowsXPSubject']/Entry")->toString();
        $this->assertEquals($new_entry_value, $written_subject);
    }
}
