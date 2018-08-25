<?php

namespace ExifEye\core\Block;

use ExifEye\core\DataWindow;
use ExifEye\core\Entry\Core\Undefined;
use ExifEye\core\Utility\ConvertBytes;

/**
 * Class representing a generic JPEG data segment.
 *
 * This is the default segment processor in case no specific class are defined.
 */
class JpegSegment extends JpegSegmentBase
{
    /**
     * {@inheritdoc}
     */
    public function loadFromData(DataWindow $data_window, $offset = 0, array $options = [])
    {
        switch ($this->payload) {
            case 'none':
                // No need to load anything if the segment is a pure marker.
                $this->components = 0;
                return $this;
            case 'variable':
                // Read the length of the segment. The length includes the two
                // bytes used to store the length.
                $this->components = $data_window->getShort($offset);
                // Load data in an Undefined entry.
                $entry = new Undefined($this, [$data_window->getBytes($offset + 2, $this->components - 2)]);
                break;
            case 'fixed':
                // Load data in an Undefined entry.
                $entry = new Undefined($this, [$data_window->getBytes($offset, $this->components)]);
                break;
        }
        $entry->debug("{text}", ['text' => $entry->toString()]);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toDumpArray()
    {
        $dump = parent::toDumpArray();

        unset($dump['elements']['entry'][0]['value']);
        unset($dump['elements']['entry'][0]['clear_value']);

        return $dump;
    }
}
