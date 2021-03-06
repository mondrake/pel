<?php

namespace ExifEye\core\Block;

use ExifEye\core\Data\DataElement;
use ExifEye\core\Data\DataWindow;
use ExifEye\core\Entry\Core\EntryInterface;
use ExifEye\core\ExifEye;
use ExifEye\core\ExifEyeException;
use ExifEye\core\Spec;
use ExifEye\core\Utility\ConvertBytes;

/**
 * Class representing an Exif TAG as an ExifEye block.
 */
class Tag extends BlockBase
{
    /**
     * {@inheritdoc}
     */
    protected $DOMNodeName = 'tag';

    /**
     * Constructs a Tag block object.
     */
    public function __construct($type, BlockBase $parent_block, $id, $entry_class, $entry_data, $format = null, $components = null)
    {
        parent::__construct($type, $parent_block);

        $this->setAttribute('id', $id);
        $tag_name = Spec::getElementName($parent_block->getType(), $id);
        if ($tag_name !== null) {
            $this->setAttribute('name', $tag_name);
        }
        $this->hasSpecification = $id > 0xF000 || in_array($id, Spec::getTypeSupportedElementIds($parent_block->getType()));

        // Check if ExifEye has a definition for this TAG.
        if (!$this->hasSpecification()) {
            $this->notice("No tag specification for tag {tagId} in ifd {ifdName}", [
                'tagId' => '0x' . strtoupper(dechex($id)),
                'ifdName' => $parent_block->getAttribute('name'),
            ]);
        }

        // Warn if format is not as expected.
        $expected_format = Spec::getElementPropertyValue($parent_block->getType(), $id, 'format');
        if ($expected_format !== null && $format !== null && !in_array($format, $expected_format)) {
            $expected_format_names = [];
            foreach ($expected_format as $expected_format_id) {
                $expected_format_names[] = Spec::getFormatName($expected_format_id);
            }
            $this->warning("Found {format_name} data format, expected {expected_format_names}", [
                'format_name' => Spec::getFormatName($format),
                'expected_format_names' => implode(', ', $expected_format_names),
            ]);
        }

        // Warn if components are not as expected.
        $expected_components = Spec::getElementPropertyValue($parent_block->getType(), $id, 'components');
        if ($expected_components !== null && $components !== null && $components !== $expected_components) {
            $this->warning("Found {components} data components, expected {expected_components}", [
                'components' => $components,
                'expected_components' => $expected_components,
            ]);
        }

        // Set the Tag's entry.
        $entry = new $entry_class($this, $entry_data);
        $this->debug("Text: {text}", [
            'text' => $entry->toString(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function loadFromData(DataElement $data_element, $offset, $size, array $options = [])
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(array $options = [])
    {
        return $this->getElement("entry")->getValue($options);
    }

    /**
     * {@inheritdoc}
     */
    public function toString(array $options = [])
    {
        return $this->getElement("entry")->toString($options);
    }

    /**
     * {@inheritdoc}
     */
    public function toBytes($order = ConvertBytes::LITTLE_ENDIAN, $offset = 0)
    {
        return $this->getElement("entry")->toBytes($order, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return $this->getElement("entry")->getFormat();
    }

    /**
     * {@inheritdoc}
     */
    public function getComponents()
    {
        return $this->getElement("entry")->getComponents();
    }
}
