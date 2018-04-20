<?php

namespace ExifEye\Test\core;

use ExifEye\core\ExifEye;
use ExifEye\core\Format;
use ExifEye\core\Block\Jpeg;
use ExifEye\Test\core\ExifEyeTestCaseBase;
use ExifEye\core\Spec;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

/**
 * Test camera images stored in the imagetest directory.
 */
class CameraTest extends ExifEyeTestCaseBase
{
    public function imageFileProvider()
    {
        $finder = new Finder();
        $finder->files()->in(dirname(__FILE__) . '/imagetests')->name('*.test.yml');
        $result = [];
        foreach ($finder as $file) {
            $result[$file->getBasename()] = [$file];
        }
        return $result;
    }

    /**
     * @dataProvider imageFileProvider
     */
    public function testParse($imageDumpFile)
    {
        $test = Yaml::parse($imageDumpFile->getContents());

        $jpeg = new Jpeg(dirname(__FILE__) . '/imagetests/' . $test['jpeg']);

        $exif = $jpeg->getExif();
        $tiff = $exif->getTiff();
        $ifd0 = $tiff->getIfd();

        if (isset($test['blocks']['IFD0'])) {
            $this->assertIfd($test['blocks']['IFD0'], $ifd0);
        }

        if (isset($test['blocks']['IFD1'])) {
            $this->assertIfd($test['blocks']['IFD1'], $ifd0->getNextIfd());
        }

        // @todo readd testing of thumbnail correctness

        $handler = ExifEye::logger()->getHandlers()[0]; // xx
        $errors = 0;
        $warnings = 0;
        foreach ($handler->getRecords() as $record) {
            switch ($record['level_name']) {
                case 'WARNING':
                    ++$warnings;
                    break;
                case 'ERROR':
                    ++$errors;
                    break;
                default:
                    continue;
            }
        }

        if (isset($test['errors'])) {
            $this->assertEquals(count($test['errors']), $errors);
        }
        if (isset($test['warnings'])) {
            $this->assertEquals(count($test['warnings']), $warnings);
        }
    }

    protected function assertIfd($expected, $ifd)
    {
        $this->assertInstanceOf($expected['class'], $ifd, $expected['id'] . $expected['name']);

        if (isset($expected['blocks']['Tag'])) {
            $expected_tags = $expected['blocks']['Tag'];
            $tags = $ifd->xxGetSubBlocks('Tag');
            $this->assertCount(count($expected_tags), $tags, "IFD: '{$ifd->getName()}' - TAGs count");
            for ($i = 0; $i < count($tags); $i++) {
                $this->assertEquals($expected_tags[$i]['id'], $tags[$i]->getId(), "Ifd: '{$ifd->getName()}' Tag: '{$tags[$i]->getId()}'");
                $this->assertEquals($expected_tags[$i]['name'], $tags[$i]->getName(), "Ifd: '{$ifd->getName()}' Tag: '{$tags[$i]->getId()}'");
                $entry = $tags[$i]->getEntry();
                $this->assertInstanceOf($expected_tags[$i]['Entry']['class'], $entry, "Ifd: '{$ifd->getName()}' Tag: '{$tags[$i]->getId()}'");
                $this->assertEquals($expected_tags[$i]['Entry']['components'], $entry->getComponents(), "Ifd: '{$ifd->getName()}' Tag: '{$tags[$i]->getId()}'");
                $this->assertEquals($expected_tags[$i]['Entry']['format'], Format::getName($entry->getFormat()), "Ifd: '{$ifd->getName()}' Tag: '{$tags[$i]->getId()}'");
                $this->assertEquals(unserialize(base64_decode($expected_tags[$i]['Entry']['value'])), $tags[$i]->getEntry()->getValue(), "Ifd: '{$ifd->getName()}' Tag: '{$tags[$i]->getId()}'");
                $this->assertEquals($expected_tags[$i]['Entry']['text'], $tags[$i]->getEntry()->toString(), "Ifd: '{$ifd->getName()}' Tag: '{$tags[$i]->getId()}'");
            }
        }

        if (isset($expected['blocks']['Ifd'])) {
            $expected_ifds = $expected['blocks']['Ifd'];
            $this->assertCount(count($expected_ifds), $ifd->xxGetSubBlocks('Ifd'), "Block: '{$ifd->getName()}' - sub-blocks count");
            foreach ($expected_ifds as $test_block => $test_block_data) {
                $block = $ifd->xxGetSubBlock('Ifd', Spec::getIfdIdByType($test_block));
                $this->assertIfd($test_block_data, $block);
            }
        }
    }
}
