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
     * The segment payload type.
     */
    protected $payload;

    /**
     * The expected segment data length, for fixed size payloads.
     */
    protected $components;

    /**
     * Construct a new JPEG segment object.
     */
    public function __construct($id, Jpeg $jpeg, JpegSegmentBase $reference = null)
    {
        parent::__construct($jpeg, $reference);
        $this->setAttribute('id', $id);
        $name = Spec::getElementName($jpeg->getType(), $id);
        $this->setAttribute('name', $name);
        $this->payload = Spec::getElementPropertyValue($jpeg->getType(), $id, 'payload');
        $this->components = Spec::getElementPropertyValue($jpeg->getType(), $id, 'components');
        $this->debug('{name} segment - {desc}', ['name' => $name, 'desc' => Spec::getElementTitle($jpeg->getType(), $id)]);
    }

    /**
     * Returns the segment payload type.
     *
     * @return int
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Returns the segment data length.
     *
     * @return int
     */
    public function getComponents()
    {
        return $this->components;
    }

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
    public function toBytes($byte_order = ConvertBytes::LITTLE_ENDIAN)
    {
        $bytes = '';

        // Add the delimiter.
        $bytes .= chr(JpegSegment::JPEG_DELIMITER);

        // Add the marker.
        $marker = $this->getAttribute('id');
        $bytes .= chr($marker);

        // Return if no payload.
        if ($this->payload === 'none') {
            return $bytes;
        }

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
