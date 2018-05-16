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
            $this->assertElement($test['elements'], $exif);
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

    protected function assertElement($expected, $element)
    {
        $this->assertInstanceOf($expected['class'], $element, $expected['path']);

        // Check entry.
        if ($element instanceof EntryInterface) {
            $this->assertEquals($expected['components'], $element->getComponents(), $element->getContextPath());
            $this->assertEquals($expected['format'], Format::getName($element->getFormat()), $element->getContextPath());
            $this->assertEquals(unserialize(base64_decode($expected['value'])), $element->getValue(), $element->getContextPath());
            $this->assertEquals($expected['text'], $element->toString(), $element->getContextPath());
        }

        // Recursively check sub-blocks.
        // xx @todo add checking count of blocks by type
        if (isset($expected['elements'])) {
            foreach ($expected['elements'] as $type => $expected_type_elements) {
                foreach ($expected_type_elements as $i => $expected_element) {
                    if ($element->query($type)[$i] === null) {
                        $str = "\n\n<<< DOM >>>\n\n";
                        $formatter = new \PrettyXml\Formatter();
                        $formatter->setIndentSize(1);
                        $str .= $formatter->format($element->DOMNode->ownerDocument->saveXML());
                        $str .= "\n\n<<< DOM >>>\n\n";
                        dump($str);
                    }
                    $this->assertElement($expected_element, $element->query($type)[$i]);
                }
            }
        }
    }
}
