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
        // Some images have some trailing (garbage?) following the
        // EOI marker. To handle this we seek backwards until we
        // find the EOI marker. Any trailing content is stored as
        // a Undefined Entry object.
        $length = $data_window->getSize();
dump('xxx-a-'.$length);
        while ($data_window->getByte($length - 2) !== JpegSegment::JPEG_DELIMITER || $data_window->getByte($length - 1) != self::JPEG_EOI) {
            $length --;
        }
dump('xxx-b-'.$length);
  //                $this->jpeg_data = $data_window->getClone(0, $length - 2);
  //dump($this->jpeg_data);
        // Load data in an Undefined entry.
        $entry = new Undefined($this, [$data_window->getClone(0, $length - 2)]);
        $entry->debug("Scan: {text}", ['text' => $entry->toString()]);

//        $this->debug('JPEG data: {data}', ['data' => $this->jpeg_data->toString()]);
        $this->debug('JPEG data ---');

        // Append the EOI.
        new JpegSegment(self::JPEG_EOI, $this->getParentElement());

        // Now check to see if there are any trailing data.
        if ($length != $data_window->getSize()) {
            $this->warning('Found trailing content after EOI: {size} bytes', [
                'size' => $data_window->getSize() - $length,
            ]);
            // We don't have a proper JPEG marker for trailing
            // garbage, so we just use 0x00...
            $trail_segment = new JpegSegment(0x00, $this->getParentElement());
            $dxx = $data_window->getClone($length);
            new Undefined($trail_segment, [$dxx->getBytes()]);
        }

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
