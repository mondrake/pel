<?php

namespace ExifEye\core\Block;

use ExifEye\core\DataWindow;
use ExifEye\core\Entry\Core\Ascii;
use ExifEye\core\Utility\ConvertBytes;

/**
 * Class representing a JPEG comment segment.
 */
class JpegComment extends JpegSegment
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'jpegComment';

    /**
     * {@inheritdoc}
     */
    public function loadFromData(DataWindow $data_window, $offset = 0, array $options = [])
    {
        // Set the Comments's entry.
        $entry = new Ascii($this, [$data_window->getBytes()]);
        $this->debug("Text: {text}", [
            'text' => $entry->toString(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function toBytes($byte_order = ConvertBytes::LITTLE_ENDIAN)
    {
        return $this->getElement("entry")->toBytes();
    }
}
