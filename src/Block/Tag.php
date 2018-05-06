<?php

namespace ExifEye\core\Block;

use ExifEye\core\Block\Exception\TagException;
use ExifEye\core\DataWindow;
use ExifEye\core\Entry\Core\EntryInterface;
use ExifEye\core\ExifEye;
use ExifEye\core\ExifEyeException;
use ExifEye\core\Format;
use ExifEye\core\Spec;

/**
 * Class representing an Exif TAG as an ExifEye block.
 */
class Tag extends BlockBase
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'Tag';

    /**
     * Constructs a Tag block object.
     */
    public function __construct(BlockBase $parent_block, $id, $entry_class, $entry_arguments, $format = null, $components = null)
    {
        parent::__construct($parent_block);

        $this->setAttribute('id', $id);
        $this->setAttribute('name', Spec::getTagName($parent_block->getAttribute('id'), $id));
        $this->hasSpecification = $id > 0xF000 || in_array($id, Spec::getIfdSupportedTagIds($parent_block->getAttribute('id')));

        // Check if ExifEye has a definition for this TAG.
        if (!$this->hasSpecification()) {
            $this->notice("No tag info available; Format {format_name}, Components {components}", [
                'format_name' => Format::getName($format),
                'components' => $components,
            ]);
        } else {
            $this->debug("Format {format_name}, Components {components}", [
                'format_name' => Format::getName($format),
                'components' => $components,
            ]);
        }

        // Warn if format is not as expected.
        $expected_format = Spec::getTagFormat($parent_block->getAttribute('id'), $id);
        if ($expected_format !== null && $format !== null && !in_array($format, $expected_format)) {
            $expected_format_names = [];
            foreach ($expected_format as $expected_format_id) {
                $expected_format_names[] = Format::getName($expected_format_id);
            }
            $this->warning("Found {format_name} data format, expected {expected_format_names}", [
                'format_name' => Format::getName($format),
                'expected_format_names' => implode(', ', $expected_format_names),
            ]);
        }

        // Warn if components are not as expected.
        $expected_components = Spec::getTagComponents($parent_block->getAttribute('id'), $id);
        if ($expected_components !== null && $components !== null && $components !== $expected_components) {
            $this->warning("Found {components} data components, expected {expected_components}", [
                'components' => $components,
                'expected_components' => $expected_components,
            ]);
        }

        // Set the Tag's entry.
        $this->setEntry(new $entry_class($entry_arguments, $this));
        $this->debug("Text: {text}", [
            'text' => $this->getEntry()->toString(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getElementPathFragment()
    {
        $tag_path = $this->getType() . ':0x' . str_pad(dechex($this->getAttribute('id')), 4, '0', STR_PAD_LEFT);
        $tag_path .= $this->getAttribute('name') ? ':' . $this->getAttribute('name') : '';
        return $tag_path;
    }

    /**
     * Turn this entry into a string.
     *
     * @return string a string representation of this entry. This is
     *         mostly for debugging.
     */
    public function __toString()
    {
        if (!$this->getAttribute('name')) {
            return '';
        }
        $entry_title = Spec::getTagTitle($this->getParentElement()->getAttribute('id'), $this->getAttribute('id')) ?: '*** UNKNOWN ***';
        return substr(str_pad($entry_title, 30, ' '), 0, 30) . ' = ' . $this->getEntry()->toString() . "\n";
    }
}
