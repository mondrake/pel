<?php

namespace ExifEye\core\Block;

use ExifEye\core\Spec;
use ExifEye\core\Utility\ConvertBytes;

/**
 * Class representing a generic JPEG data segment.
 */
abstract class JpegSegmentBase extends BlockBase
{
    /**
     * JPEG delimiter.
     */
    const JPEG_DELIMITER = 0xFF;

    /**
     * {@inheritdoc}
     */
    protected $type = 'jpegSegment';

    /**
     * Construct a new JPEG segment object.
     */
    public function __construct($id, Jpeg $jpeg, JpegSegmentBase $reference = null)
    {
        parent::__construct($jpeg, $reference);
        $this->setAttribute('id', $id);
        $name = Spec::getElementName($jpeg->getType(), $id);
        $this->setAttribute('name', $name);
        $this->debug('{name} segment - {desc}', ['name' => $name, 'desc' => Spec::getElementTitle($jpeg->getType(), $id)]);
    }

    /**
     * {@inheritdoc}
     */
    public function toBytes($byte_order = ConvertBytes::LITTLE_ENDIAN)
    {
        $bytes = '';

        // Add the delimiter.
        $bytes .= chr(JpegSegment::JPEG_DELIMITER);

        // Add the marker.
        $marker = $this->getAttribute('id');
        $bytes .= chr($marker);

        // Get the segment data.
        $data = '';
        foreach ($this->getMultipleElements("*") as $sub) {
            $data .= $sub->toBytes();
        }

        // Add the segment bytes.
        if ($data !== '') {
            $size = strlen($data) + 2;
            $bytes .= ConvertBytes::fromShort($size, ConvertBytes::BIG_ENDIAN);
            $bytes .= $data;

            // In case of SOS, we need to write the JPEG data.
/*            if ($marker == Spec::getElementIdByName($this->getParentElement()->getType(), 'SOS')) {
                $bytes .= $this->getParentElement()->jpeg_data->getBytes();
            }*/
        }

        return $bytes;
    }
}
