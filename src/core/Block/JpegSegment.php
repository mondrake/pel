<?php

namespace ExifEye\core\Block;

use ExifEye\core\DataElement;
use ExifEye\core\DataWindow;
use ExifEye\core\Entry\Core\Undefined;

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
    public function loadFromData(DataElement $data_element, $offset = 0, $size = null, array $options = [])
    {
        parent::loadFromData($data_element, $offset, $size, $options);

        $this->components = $size;

        if ($size) {
            $entry = new Undefined($this, [$data_element->getBytes($offset, $size)]);
            $entry->debug("{text}", ['text' => $entry->toString()]);
        }

        return $this;
    }
}
