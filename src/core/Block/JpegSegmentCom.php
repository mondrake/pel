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
    public function loadFromData(DataWindow $data_window, $offset = 0, array $options = [])
    {
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
}
