<?php

namespace ExifEye\Test\core;

use ExifEye\core\Entry\Core\EntryInterface;
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

        if (isset($test['elements'])) {
            $this->assertBlock($test['elements'], $exif);
        }

        $handler = ExifEye::logger()->getHandlers()[0]; // xx
        $errors = 0;
        $warnings = 0;
        $notices = 0;
        foreach ($handler->getRecords() as $record) {
            switch ($record['level_name']) {
                case 'NOTICE':
                    ++$notices;
                    break;
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
        if (isset($test['notices'])) {
            $this->assertEquals(count($test['notices']), $notices);
        }
    }

    protected function assertBlock($expected, $block)
    {
        $this->assertInstanceOf($expected['class'], $block, $block->getContextPath());

        // Check entry.
        if ($block instanceof EntryInterface) {
            $this->assertEquals($expected['components'], $block->getComponents(), $block->getContextPath());
            $this->assertEquals($expected['format'], Format::getName($block->getFormat()), $block->getContextPath());
            $this->assertEquals(unserialize(base64_decode($expected['value'])), $block->getValue(), $block->getContextPath());
            $this->assertEquals($expected['text'], $block->toString(), $block->getContextPath());
        }

        // Recursively check sub-blocks.
        // xx @todo add checking count of blocks by type
        if (isset($expected['elements'])) {
            foreach ($expected['elements'] as $i => $expected_block) {
                $this->assertBlock($expected_block, $block->query($block->getType())[$i]);
            }
        }
    }
}
