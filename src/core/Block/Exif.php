<?php

namespace ExifEye\core\Block;

use ExifEye\core\DataWindow;
use ExifEye\core\ExifEye;
use ExifEye\core\Utility\ConvertBytes;

/**
 * Class representing Exif data.
 */
class Exif extends BlockBase
{
    /**
     * Exif header.
     *
     * The Exif data must start with these six bytes to be considered valid.
     */
    const EXIF_HEADER = "Exif\0\0";

    /**
     * {@inheritdoc}
     */
    protected $type = 'exif';

    /**
     * Construct a new Exif object.
     */
    public function __construct(JpegSegmentApp1 $parent_block)
    {
        parent::__construct($parent_block);
    }

    /**
     * {@inheritdoc}
     */
    public function loadFromData(DataWindow $data_window, $offset = 0, $size = null, array $options = [])
    {
        $end_offset = $data_window->getSize() - 1;

        $this->debug('Parsing Exif data in {start}-{end} (0x{hstart}-0x{hend}), {size} bytes ...', [
          'start' => $offset,
          'end' => $end_offset,
          'hstart' => dechex($offset),
          'hend' => dechex($end_offset),
          'size' => $data_window->getSize() - $offset,
        ]);

        // The rest is TIFF data.
        $tiff = new Tiff($this);
        $tiff->loadFromData($data_window, $offset + strlen(self::EXIF_HEADER));
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function toBytes($byte_order = ConvertBytes::LITTLE_ENDIAN)
    {
        return self::EXIF_HEADER . $this->getElement('tiff')->toBytes();
    }

    /**
     * Determines if the data is an EXIF segment.
     */
    public static function isExifSegment(DataWindow $data_window, $offset = 0)
    {
        // There must be at least 6 bytes for the Exif header.
        if ($data_window->getSize() - $offset < strlen(self::EXIF_HEADER)) {
            return false;
        }

        // Verify the Exif header.
        if ($data_window->strcmp($offset, self::EXIF_HEADER)) {
            return true;
        }

        return false;
    }
}
