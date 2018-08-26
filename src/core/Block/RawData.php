<?php

namespace ExifEye\core\Block;

use ExifEye\core\DataWindow;
use ExifEye\core\Entry\Core\Undefined;
use ExifEye\core\Spec;
use ExifEye\core\Utility\ConvertBytes;

/**
 * Abstract class for JPEG data segments.
 */
class RawData extends BlockBase
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'rawData';

    /**
     * The data length.
     */
    protected $components;

    /**
     * Construct a new RawData object.
     */
    public function __construct(BlockBase $parent, BlockBase $reference = null)
    {
        parent::__construct($parent, $reference);
        $this->debug('Raw data');
    }

    /**
     * Returns the data length.
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
        $this->components = $options['components'];
        $entry = new Undefined($this, [$data_window->getBytes($offset, $this->components - 2)]);
        $entry->debug("{text}", ['text' => $entry->toString()]);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toBytes($byte_order = ConvertBytes::LITTLE_ENDIAN)
    {
        return $this->getElement("entry")->toBytes();
    }
}