<?php

namespace ExifEye\Test\core;

use ExifEye\core\Block\Ifd;
use ExifEye\core\Block\Tag;
use ExifEye\core\Block\Tiff;
use ExifEye\core\Entry\Core\Ascii;
use ExifEye\core\Entry\Time;
use ExifEye\core\Spec;

class IfdTest extends ExifEyeTestCaseBase
{
    public function testIfd()
    {
        $doc = new \DOMDocument();
        $doc->registerNodeClass('DOMElement', 'ExifEye\core\DOM\ExifEyeDOMElement');
        $test_dom_node = $doc->createElement('test-tiff');
        $doc->appendChild($test_dom_node);
        $tiff = new Tiff();
        $tiff->setDOMNode($test_dom_node);

        $ifd = new Ifd($tiff, Spec::getIfdIdByType('IFD0'));

        $this->assertCount(0, $ifd->xxGetSubBlocks('Tag'));

        $desc = new Ascii(['Hello?']);
        $ifd->xxAddSubBlock(new Tag($ifd, 0x010E, 'ExifEye\core\Entry\Core\Ascii', ['Hello?']));

        $date = new Time([12345678]);
        $ifd->xxAddSubBlock(new Tag($ifd, 0x0132, 'ExifEye\core\Entry\Time', [12345678]));

        $this->assertCount(2, $ifd->xxGetSubBlocks('Tag'));

        $tags = [];
        foreach ($ifd->xxGetSubBlocks('Tag') as $tag) {
            $tags[$tag->getAttribute('id')] = $tag->getEntry();
        }

        $this->assertSame($tags[0x010E]->getValue(), $desc->getValue());
        $this->assertSame($tags[0x0132]->getValue(), $date->getValue());
    }
}
