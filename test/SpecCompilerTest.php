<?php

namespace ExifEye\Test\core;

use ExifEye\core\Block\Ifd;
use ExifEye\core\Utility\SpecCompiler;
use ExifEye\core\Spec;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Test compilation of a set of ExifEye specification YAML files.
 */
class SpecCompilerTest extends ExifEyeTestCaseBase
{
    /** @var Filesystem */
    private $fs;

    /** @var string */
    private $testResourceDirectory;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->testResourceDirectory = __DIR__ . '/../test_resources';
        $this->fs = new Filesystem();
        $this->fs->mkdir($this->testResourceDirectory);
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $this->fs->remove($this->testResourceDirectory);
        Spec::setMap(null);
        parent::tearDown();
    }

    /**
     * Tests that compiling an invalid YAML file raises exception.
     */
    public function testInvalidYaml()
    {
        //@todo change below to ParseException::class once PHP 5.4 support is removed.
        $this->fcExpectException('Symfony\Component\Yaml\Exception\ParseException');
        $compiler = new SpecCompiler();
        $compiler->compile(__DIR__ . '/fixtures/spec/invalid_yaml', $this->testResourceDirectory);
    }

    /**
     * Tests that compiling a YAML file with invalid IFD keys raises exception.
     */
/*    public function testInvalidIfdKeys()
    {
        //@todo change below to SpecCompilerException::class once PHP 5.4 support is removed.
        $this->fcExpectException('ExifEye\core\Utility\SpecCompilerException', 'ifd_ifd0.yaml: invalid IFD key(s) found - bork');
        $compiler = new SpecCompiler();
        $compiler->compile(__DIR__ . '/fixtures/spec/invalid_ifd_keys', $this->testResourceDirectory);
    }*/

    /**
     * Tests that compiling a YAML file with invalid TAG keys raises exception.
     */
/*    public function testInvalidTagKeys()
    {
        //@todo change below to SpecCompilerException::class once PHP 5.4 support is removed.
        $this->fcExpectException('ExifEye\core\Utility\SpecCompilerException', "ifd_ifd0.yaml: invalid key(s) found for TAG 'ImageWidth' - bork");
        $compiler = new SpecCompiler();
        $compiler->compile(__DIR__ . '/fixtures/spec/invalid_tag_keys', $this->testResourceDirectory);
    }*/

    /**
     * Tests that compiling a YAML file with invalid sub IFD raises exception.
     */
/*    public function testInvalidSubIfd()
    {
        //@todo change below to SpecCompilerException::class once PHP 5.4 support is removed.
        $this->fcExpectException('ExifEye\core\Utility\SpecCompilerException', "Invalid sub IFD(s) found for TAG 'ExifIFDPointer': *** EXPECTED FAILURE ***");
        $compiler = new SpecCompiler();
        $compiler->compile(__DIR__ . '/fixtures/spec/invalid_subifd', $this->testResourceDirectory);
    }*/

    /**
     * Tests compiling a valid specifications stub set.
     */
    public function testValidStubSpec()
    {
        $compiler = new SpecCompiler();
        $compiler->compile(__DIR__ . '/fixtures/spec/valid_stub', $this->testResourceDirectory);
        Spec::setMap($this->testResourceDirectory . '/spec.php');
        $this->assertCount(3, Spec::getTypes());

        $tiff_mock = $this->getMockBuilder('ExifEye\core\Block\Tiff')
            ->disableOriginalConstructor()
            ->getMock();
        $ifd_0 = new Ifd('ifd0', 'IFD0', $tiff_mock);
        $ifd_exif = new Ifd('ifdExif', 'Exif', $ifd_0);

        $this->assertEquals(0x0100, Spec::getElementIdByName($ifd_0->getType(), 'ImageWidth'));
        $this->assertEquals(0x8769, Spec::getElementIdByName($ifd_0->getType(), 'ExifIfd'));
        $this->assertEquals(0x829A, Spec::getElementIdByName($ifd_exif->getType(), 'ExposureTime'));

        // Compression is missing from the stub specs.
        $this->assertNull(Spec::getElementIdByName($ifd_0->getType(), 'Compression'));
    }
}
