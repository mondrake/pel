<?php

namespace ExifEye\Test\core;

use ExifEye\core\Block\Exif;
use ExifEye\core\Block\JpegSegment;
use ExifEye\core\ExifEye;
use ExifEye\core\JpegMarker;
use ExifEye\core\Block\Jpeg;

class MisplacedExifTest extends ExifEyeTestCaseBase
{
    // NOTE: this test relies on the assumption that internal Jpeg::sections order is kept between section
    // manipulations. It may fail it this changes.
    public function testRead()
    {
        // Image contains non-EXIF APP1 section ahead of the EXIF one.
        $jpeg = new Jpeg(dirname(__FILE__) . '/broken_images/misplaced-exif.jpg');

        // Assert we just have loaded correct file for the test.
        $app1 = $jpeg->query("segment[@name='APP1']");
        $this->assertCount(2, $app1);
        $this->assertNull($app1[0]->first("exif"));
        $this->assertInstanceOf('ExifEye\core\Block\Exif', $app1[1]->first("exif"));

        $app1_segment = new JpegSegment('APP1', $jpeg);
        $newExif = new Exif($app1_segment);
        $jpeg->setExif($newExif);
        // Ensure EXIF is set to correct position among sections
        $sections2 = $jpeg->getSections();
        $this->assertSame($sections1[$exifIdx][0], $sections2[$exifIdx][0]);
        $this->assertNotSame($sections1[$exifIdx][1], $sections2[$exifIdx][1]);
        $this->assertSame($newExif, $sections2[$exifIdx][1]);

        $this->assertInstanceOf('ExifEye\core\Block\Exif', $jpeg->first("segment/exif"));
        $jpeg->clearExif();
        // Assert that only EXIF section is gone and all other shifted correctly.
        $sections3 = $jpeg->getSections();
        $numSections3 = count($sections3);
        for ($idx = 0; $idx < $numSections3; ++$idx) {
            if ($idx >= $exifIdx) {
                $s2idx = $idx + 1;
            } else {
                $s2idx = $idx;
            }
            $this->assertSame($sections2[$s2idx][0], $sections3[$idx][0]);
            $this->assertSame($sections2[$s2idx][1], $sections3[$idx][1]);
        }
        $this->assertNotInstanceOf('ExifEye\core\Block\Exif', $jpeg->first("segment/exif"));
    }
}
