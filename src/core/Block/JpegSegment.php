<?php

namespace ExifEye\core\Block;

use ExifEye\core\DataWindow;

/**
 * Class representing a generic JPEG data segment.
 */
class JpegSegment extends JpegSegmentBase
{
    /**
     * {@inheritdoc}
     */
    public function loadFromData(DataWindow $data_window, $offset = 0, array $options = [])
    {
        $this->debug("START... Loading");

        if (Exif::isExifSegment($data_window)) {
            $exif = new Exif($this);
            $ret = $exif->loadFromData($data_window, $offset);
        } else {
            $this->debug('Exif header not found.');
            $ret = false;
        }

        $this->debug(".....END Loading");

        return $ret;
    }
}
