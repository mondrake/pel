<?php

namespace ExifEye\core\Block;

use ExifEye\core\DataWindow;
use ExifEye\core\Entry\Core\Undefined;
use ExifEye\core\Spec;
use ExifEye\core\Utility\ConvertBytes;

/**
 * Class for handling a JPEG image data.
 */
class Jpeg extends BlockBase
{
    /**
     * JPEG header.
     */
    const JPEG_HEADER = "\xFF\xD8\xFF";

    /**
     * {@inheritdoc}
     */
    protected $type = 'jpeg';

    /**
     * Constructs a Block for holding a JPEG image.
     */
    public function __construct(BlockBase $parent = null)
    {
        parent::__construct($parent);
    }

    /**
     * {@inheritdoc}
     */
    public function loadFromData(DataWindow $data_window, $offset = 0, array $options = [])
    {
        $this->debug('Parsing {size} bytes of JPEG data...', ['size' => $data_window->getSize()]);

        // JPEG data is stored in big-endian format.
        $data_window->setByteOrder(ConvertBytes::BIG_ENDIAN);

        // Run through the data to read the segments in the image. After each
        // segment is read, the start of the data window will be moved forward,
        // and after the last section we will terminate with no data left in the
        // window.
        while ($data_window->getSize() > 0) {
            $i = $this->getJpgSectionStart($data_window);

            $segment_id = $data_window->getByte($i);

            if (!in_array($segment_id, Spec::getTypeSupportedElementIds($this->getType()))) {
                $this->warning('Invalid marker found at offset {offset}: 0x{marker}', [
                    'offset' => $offset,
                    'marker' => dechex($segment_id),
                ]);
            }

            $segment_name = Spec::getElementName($this->getType(), $segment_id);
            $segment_class = Spec::getElementHandlingClass($this->getType(), $segment_id);
            $segment = new $segment_class($segment_id, $this);

            // Move window so first byte becomes first byte in this section.
            $data_window->setWindowStart($i + 1);

            // Skip loading if the segment is a pure marker.
            if (Spec::getElementPropertyValue($this->getType(), $segment_id, 'markerOnly')) {
                continue;
            }

            if ($segment_name === 'SOS') {
                $len = $data_window->getSize();
dump('aaa-'.$len);
            } else {
                // Read the length of the section. The length includes the two
                // bytes used to store the length.
                $len = $data_window->getShort(0) - 2;
                // Skip past the length.
                $data_window->setWindowStart(2);
            }

            // Load the segment.
            $segment->loadFromData($data_window->getClone(0, $len));

            // In case of SOS, image data will follow and the load complete.
            if ($segment_name === 'SOS') {
                break;
            } else {
                // Skip past the data.
                $data_window->setWindowStart($len);
            }
        }
        return $this;
    }

    /**
     * JPEG sections start with 0xFF. The first byte that is not
     * 0xFF is a marker (hopefully).
     *
     * @param DataWindow $data_window
     *
     * @return integer
     */
    protected function getJpgSectionStart($data_window)
    {
        for ($i = 0; $i < 7; $i ++) {
            if ($data_window->getByte($i) !== JpegSegment::JPEG_DELIMITER) {
                 break;
            }
        }
        return $i;
    }

    /**
     * Returns the MIME type of the image.
     *
     * @returns string
     */
    public function getMimeType()
    {
        return 'image/jpeg';
    }
}
