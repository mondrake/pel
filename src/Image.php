<?php

namespace ExifEye\core;

use ExifEye\core\Block\Jpeg;
use ExifEye\core\Block\Tiff;
use ExifEye\core\Utility\ConvertBytes;

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
                $this->root = new Jpeg();
                $this->root->loadFromData($data_window);
                return;
            case 'image/tiff':
                $this->root = new Tiff();
                $this->root->loadFromData($data_window);
                return;
            default:
                throw new ExifEyeException('Unrecognized image format.');
        }
    }

    public function getMimeType()
    {
        return $this->mimeType;
    }

    public function root()
    {
        return $this->root;
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
        return static::loadFromData(new DataWindow(file_get_contents($path)));
    }

    public static function loadFromData(DataWindow $data_window)
    {
        // JPEG image?
//        if ($data_window->getBytes(0) === 0xFF && $data_window->getBytes(1) === 0xD8 && $data_window->getBytes(2) === 0xFF) {
        if ($data_window->getBytes(0, 3) === "\xFF\xD8\xFF") {
            return new static('image/jpeg', $data_window);
        }

        // TIFF image?
        $byte_order = $data_window->getBytes(0, 2);
        if ($byte_order === 'II' || $byte_order === 'MM') {
            $data_window->setByteOrder($byte_order === 'II' ? ConvertBytes::LITTLE_ENDIAN : ConvertBytes::BIG_ENDIAN);
            if ($data_window->getShort(2) === Tiff::TIFF_HEADER) {
                return new static('image/tiff', $data_window);
            }
        }

        throw new ExifEyeException('Unrecognized image format.');
    }

    /**
     * Save the Image object as a file.
     */
    public function saveToFile($path)
    {
        return file_put_contents($path, $this->root->toBytes());
    }
}
