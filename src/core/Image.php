<?php

namespace ExifEye\core;

use ExifEye\core\Block\BlockBase;
use ExifEye\core\Block\Jpeg;
use ExifEye\core\Block\Tiff;
use ExifEye\core\Utility\ConvertBytes;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\TestHandler;
use Monolog\Processor\PsrLogMessageProcessor;

/**
 * Class to handle image data.
 */
class Image extends BlockBase
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'image';

    /**
     * The handling class for the image.
     *
     * @var string
     */
    protected $imageClass;

    /**
     * The internal logger instance for this Image object.
     *
     * @var \Monolog\Logger
     */
    protected $logger;

    /**
     * A PSR-3 compliant logger callback.
     *
     * Consuming code can have higher level logging facilities in place. Any
     * entry sent to the internal logger will also be sent to the callback, if
     * specified.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $externalLogger;

    /**
     * The minimum log level for failure.
     *
     * ExifEye normally intercepts and logs image parsing issues without
     * breaking the flow. However it is possible to enable hard failures by
     * defining the minimum log level at which the parsing process will breaking
     * and throw an ExifEyeException.
     *
     * @var int
     */
    protected $failLevel;

    /**
     * Quality setting for encoding JPEG images.
     *
     * This controls the quality used then PHP image resources are encoded into
     * JPEG images. This happens when you create a Jpeg object based on an image
     * resource.
     *
     * The default is 75 for average quality images, but you can change this to
     * an integer between 0 and 100.
     *
     * @var int
     */
    protected $quality = 75;

    /**
     * Creates an Image object from a file.
     *
     * @param string $path
     *            the path to an image file on the file system.
     * @param \Psr\Log\LoggerInterface $external_logger
     *            (Optional) a PSR-3 compliant logger callback.
     * @param string $fail_level
     *            (Optional) a PSR-3 compliant log level. Any log entry at this
     *            level or above will force image parsing to stop.
     *
     * @returns Image|false
     *            the Image object if successful, or false if the file cannot
     *            be parsed.
     */
    public static function loadFromFile($path, LoggerInterface $external_logger = null, $fail_level = false)
    {
        $magic_file_info = new DataWindow(file_get_contents($path, false, null, 0, 10));
        $handling_class = static::determineImageHandlingClass($magic_file_info);

        if ($handling_class !== false) {
            $image = new static($handling_class, $external_logger, $fail_level);
            $image->loadFromData(new DataWindow(file_get_contents($path)));
            return $image;
        }

        return false;
    }

    /**
     * Creates an Image object from data.
     *
     * @param DataWindow $data_window
     *            the data window that will provide the data.
     * @param \Psr\Log\LoggerInterface $external_logger
     *            (Optional) a PSR-3 compliant logger callback.
     * @param string $fail_level
     *            (Optional) a PSR-3 compliant log level. Any log entry at this
     *            level or above will force image parsing to stop.
     *
     * @returns Image|false
     *            the Image object if successful, or false if the data cannot
     *            be parsed.
     */
    public static function createFromData(DataWindow $data_window, LoggerInterface $external_logger = null, $fail_level = false)
    {
        $handling_class = static::determineImageHandlingClass($data_window);

        if ($handling_class !== false) {
            $image = new static($handling_class, $external_logger, $fail_level);
            $image->loadFromData($data_window);
            return $image;
        }

        return false;
    }

    /**
     * Determines the PHP class to use for parsing the image data.
     *
     * @param DataWindow $data_window
     *            the data window that will provide the data.
     *
     * @returns string|false
     *            the PHP fully qualified class name if successful, or false if
     *            the data cannot be parsed.
     */
    protected static function determineImageHandlingClass(DataWindow $data_window)
    {
        // JPEG image?
        if ($data_window->getBytes(0, 3) === "\xFF\xD8\xFF") {
            return '\ExifEye\core\Block\Jpeg';
        }

        // TIFF image?
        $byte_order = $data_window->getBytes(0, 2);
        if ($byte_order === 'II' || $byte_order === 'MM') {
            $data_window->setByteOrder($byte_order === 'II' ? ConvertBytes::LITTLE_ENDIAN : ConvertBytes::BIG_ENDIAN);
            if ($data_window->getShort(2) === Tiff::TIFF_HEADER) {
                return '\ExifEye\core\Block\Tiff';
            }
        }

        return false;
    }

    /**
     * Constructs an Image object.
     *
     * @param string $image_class
     *            The handling class for the image.
     * @param \Psr\Log\LoggerInterface $external_logger
     *            (Optional) a PSR-3 compliant logger callback.
     * @param string $fail_level
     *            (Optional) a PSR-3 compliant log level. Any log entry at this
     *            level or above will force image parsing to stop.
     */
    public function __construct($image_class, LoggerInterface $external_logger = null, $fail_level = false)
    {
        parent::__construct();
        $this->imageClass = $image_class;
        $this->logger = (new Logger('exifeye'))
          ->pushHandler(new TestHandler(Logger::INFO))
          ->pushProcessor(new PsrLogMessageProcessor());
        $this->externalLogger = $external_logger;
        $this->failLevel = $fail_level !== false ? Logger::toMonologLevel($fail_level) : false;
    }

    /**
     * {@inheritdoc}
     */
    public function loadFromData(DataWindow $data_window, $offset = 0, array $options = [])
    {
        $image_handler = new $this->imageClass($this);
        $image_handler->loadFromData($data_window);
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function toBytes($byte_order = ConvertBytes::LITTLE_ENDIAN)
    {
        return $this->getElement('*')->toBytes();
    }

    /**
     * Set the JPEG encoding quality.
     *
     * @param int $quality
     *            an integer between 0 and 100 with 75 being average quality
     *            and 95 very good quality.
     */
    public function setJPEGQuality($quality)
    {
        $this->$quality = $quality;
    }

    /**
     * Get current setting for JPEG encoding quality.
     *
     * @return int the quality.
     */
    public function getJPEGQuality()
    {
        return $this->$quality;
    }

    /**
     * Determines the MIME type of the image.
     *
     * @returns string
     */
    public function getMimeType()
    {
        return $this->imageClass::getMimeType();
    }

    /**
     * Save the Image object as a file.
     *
     * @param string $path
     *            the path to the image file on the file system.
     *
     * @return int|false
     *            The number of bytes that were written to the file, or FALSE on
     *            failure.
     */
    public function saveToFile($path)
    {
        return file_put_contents($path, $this->toBytes());
    }

    /**
     * Returns the DOM structure of the Image object as an XML string.
     *
     * @returns string
     */
    public function toXML()
    {
        return $this->DOMNode->ownerDocument->saveXML();
    }

    /**
     * Returns the log entries of the Image.
     *
     * @returns array
     */
    public function dumpLog()
    {
        $handler = $this->logger->getHandlers()[0]; // xx
        $ret = [];
        foreach ($handler->getRecords() as $record) {
            $ret[$record['level_name']][] = $record;
        }
        return $ret;
    }
}
