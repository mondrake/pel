<?php

namespace ExifEye\core\Block;

use ExifEye\core\DataWindow;
use ExifEye\core\Entry\Core\Undefined;

/**
 * Class representing a JPEG SOS segment.
 */
class JpegSegmentSos extends JpegSegmentBase
{
    /**
     * JPEG EOI marker.
     */
    const JPEG_EOI = 0xD9;

    /**
     * {@inheritdoc}
     */
    public function loadFromData(DataWindow $data_window, $offset = 0, array $options = [])
    {
        // This segment is last before End Of Image, and its lenght needs to be
        // determined by finding the EOI marker backwards from the end of data.
        // Some images have some trailing (garbage?) following the EOI marker,
        // which we store in a RawData object.
dump('offset:' . $offset);
        $length = $data_window->getSize();
dump('length:' . $offset);
        while ($data_window->getByte($length - 2) !== JpegSegment::JPEG_DELIMITER || $data_window->getByte($length - 1) != self::JPEG_EOI) {
            $length --;
        }
        $this->components = $length - $offset;
dump('comp:' . $this->components);

        // Load data in an Undefined entry.
        $entry = new Undefined($this, [$data_window->getBytes($offset, $this->components - 1)]);
        $entry->debug("Scan: {text}", ['text' => $entry->toString()]);
        $this->debug('JPEG data: {data}', ['data' => $data_window->toString()]);

        // Append the EOI.
        new JpegSegment(self::JPEG_EOI, $this->getParentElement());

        // Now check to see if there are any trailing data.
/*        if ($length != $data_window->getSize()) {
            $this->warning('Found trailing content after EOI: {size} bytes', [
                'size' => $data_window->getSize() - $length,
            ]);
            // We don't have a proper JPEG marker for trailing
            // garbage, so we just use 0x00...
            $trail_segment = new JpegSegment(0x00, $this->getParentElement());
            $dxx = $data_window->getClone($length);
            new Undefined($trail_segment, [$dxx->getBytes()]);
        }*/

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toDumpArray()
    {
        $dump = parent::toDumpArray();

        // xx only if not exif
        unset($dump['elements']['entry'][0]['value']);
        unset($dump['elements']['entry'][0]['clear_value']);

        return $dump;
    }
}
