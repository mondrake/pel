<?php

namespace ExifEye\core\Block;

use ExifEye\core\DataWindow;
use ExifEye\core\Entry\Core\Ascii;
use ExifEye\core\Utility\ConvertBytes;

/**
 * Class representing a JPEG comment segment.
 */
class JpegSegmentCom extends JpegSegmentBase
{
    /**
     * {@inheritdoc}
     */
    public function loadFromData(DataWindow $data_window, $offset = 0, $size = null, array $options = [])
    {
        $this->debug('Parsing JPEG segment data in {start}-{end} (0x{hstart}-0x{hend}), {size} bytes ...', [
          'start' => $offset,
          'end' => $offset + $size - 1,
          'hstart' => dechex($offset),
          'hend' => dechex($offset + $size - 1),
          'size' => dsize,
        ]);

        // Read the length of the segment. The length includes the two bytes
        // used to store the length.
        $this->components = $data_window->getShort($offset);

        // Set the Comments's entry.
        $entry = new Ascii($this, [$data_window->getBytes($offset + 2, $this->components - 2)]);
        $entry->debug("Text: {text}", [
            'text' => $entry->toString(),
        ]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toBytes($byte_order = ConvertBytes::LITTLE_ENDIAN)
    {
        $bytes = $this->getMarkerBytes();

        // Get the payload.
        $comment = $this->getElement("entry");
        $data = $comment->toBytes();

        // Add the data lenght, include the two bytes of the length itself.
        $bytes .= ConvertBytes::fromShort(strlen($data) + 2, ConvertBytes::BIG_ENDIAN);

        // Add the data.
        $bytes .= $data;

        return $bytes;
    }
}
