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
    public function toDumpArray()
    {
        $dump = parent::toDumpArray();

        unset($dump['elements']['entry'][0]['value']);
        unset($dump['elements']['entry'][0]['clear_value']);

        return $dump;
    }
}
