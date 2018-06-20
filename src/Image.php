<?php

namespace ExifEye\core;

use ExifEye\core\Block\Jpeg;
use ExifEye\core\Block\Tiff;

/**
 * Class to handle image data.
 */
class Image
{
    /**
     * The MIME type of the image.
     *
     * @var string
     */
    protected $mimeType;

    /**
     * The MIME type of the image.
     *
     * @var string
     */
    protected $root;

    /**
     * Constructs a new Image object.
     */
    public function __construct($mime_type, DataWindow $data_window)
    {
        $this->mimeType = $mime_type;
        switch ($this->mimeType) {
            case 'image/jpeg':
                $this->root = new Jpeg($data_window);
                return;
            case 'image/tiff':
                $this->root = new Tiff($data_window);
                return;
            default:
                throw new ExifEyeException('Unrecognized image format.');
        }
    }

    public function getMimeType()
    {
        return $this->mimeType;
    }

    public function first($expression)
    {
        return $this->root->first($expression);
    }

    public function remove($expression)
    {
        return $this->root->remove($expression);
    }

    public function query($expression)
    {
        return $this->root->query($expression);
    }

    public static function loadFromFile($path)
    {
        $data_window = new DataWindow(file_get_contents($filename));
        
        // Is file a JPEG image?
        if ($d->getBytes(0, 2) === '0xFFD8') {
            return new static('image/jpeg', $data_window);
        }
        
        // Is file a TIFF image?
        return new static('image/tiff', $data_window);
        
        $d->setByteOrder(ConvertBytes::BIG_ENDIAN);
        $i = self::getJpgSectionStart($d);
        
    }

    /**
     * Save the Image object as a file.
     */
    public static function saveToFile($path)
    {
        return file_put_contents($path, $this->root->toBytes());
    }
}

    /**
     * Test data to see if it could be a valid JPEG image.
     *
     * The function will only look at the first few bytes of the data,
     * and try to determine if it could be a valid JPEG image based on
     * those bytes. This means that the check is more like a heuristic
     * than a rigorous check.
     *
     * @param
     *            DataWindow the bytes that will be checked.
     *
     * @return boolean true if the bytes look like the beginning of a
     *         JPEG image, false otherwise.
     *
     * @see Tiff::isValid()
     */
    public static function xxisValid(DataWindow $d)
    {
        /* JPEG data is stored in big-endian format. */
        $d->setByteOrder(ConvertBytes::BIG_ENDIAN);

        $i = self::getJpgSectionStart($d);

        return $d->getByte($i) == JpegMarker::SOI;
    }
    /**
     * JPEG sections start with 0xFF. The first byte that is not
     * 0xFF is a marker (hopefully).
     *
     * @param DataWindow $d
     *
     * @return integer
     */
    protected static function getJpgSectionStart($d)
    {
        for ($i = 0; $i < 7; $i ++) {
            if ($d->getByte($i) != 0xFF) {
                 break;
            }
        }
        return $i;
    }
