<?php

namespace ExifEye\core\Block;

use ExifEye\core\Block\Tag;
use ExifEye\core\Data\DataElement;
use ExifEye\core\Data\DataWindow;
use ExifEye\core\Data\DataException;
use ExifEye\core\ElementInterface;
use ExifEye\core\Entry\Core\EntryInterface;
use ExifEye\core\ExifEye;
use ExifEye\core\Format;
use ExifEye\core\Utility\ConvertBytes;
use ExifEye\core\Spec;

/**
 * Class representing an Image File Directory (IFD).
 */
class Ifd extends IfdBase
{
    /**
     * {@inheritdoc}
     */
    public function loadFromData(DataElement $data_element, $offset = 0, $size = null, array $options = [])
    {
        // Get the number of tags.
        $n = $this->getEntriesCountFromData($data_element, $offset);

        // Load Tags.
        for ($i = 0; $i < $n; $i++) {
            $i_offset = $offset + 2 + 12 * $i;
            $entry = $this->getEntryFromData($i, $data_element, $i_offset);

            // Build the TAG object.
            $tag_entry_class = Spec::getElementHandlingClass($this->getType(), $entry['id'], $entry['format']);

            $element_type = Spec::getElementType($this->getType(), $entry['id']);
            if ($element_type === 'tag' || $element_type === null) {
                $tag_entry_arguments = call_user_func($tag_entry_class . '::getInstanceArgumentsFromTagData', $this, $entry['format'], $entry['components'], $data_element, $entry['data_offset']);
                $tag = new Tag('tag', $this, $entry['id'], $tag_entry_class, $tag_entry_arguments, $entry['format'], $entry['components']);
            } else {
                // If the tag is an IFD pointer, loads the IFD.
                $ifd_type = Spec::getElementType($this->getType(), $entry['id']);
                $ifd_name = Spec::getElementName($this->getType(), $entry['id']);
                $o = $data_element->getLong($i_offset + 8);
                if ($offset != $o) {
                    $ifd_class = Spec::getTypeHandlingClass($ifd_type);
                    $ifd = new $ifd_class($ifd_type, $ifd_name, $this, $entry['id'], $entry['format']);
                    try {
                        $ifd->loadFromData($data_element, $o, $size, [
                            'data_offset' => $entry['data_offset'],
                            'components' => $entry['components'],
                        ]);
                    } catch (DataException $e) {
                        $this->error($e->getMessage());
                    }
                } else {
                    $this->error('Bogus offset to next IFD: {offset}, same as offset being loaded from.', [
                        'offset' => $o,
                    ]);
                }
                continue;
            }
        }

        // Invoke post-load callbacks.
        $this->executePostLoadCallbacks($data_element);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toBytes($byte_order = ConvertBytes::LITTLE_ENDIAN, $offset = 0, $has_next_ifd = false)
    {
        $bytes = '';

        // Number of sub-elements. 2 bytes running.
        $n = count($this->getMultipleElements('*'));
        if ($thumbnail = $this->getElement('thumbnail')) {
            $n += 1;
        }
        $bytes .= ConvertBytes::fromShort($n, $byte_order);

        // Data area. We need to reserve 12 bytes for each IFD tag + 4 bytes
        // at the end for the link to next IFD as space occupied by IFD
        // entries.
        $data_area_offset = $offset + strlen($bytes) + $n * 12 + 4;
        $data_area_bytes = '';

        // Fill in the TAG entries in the IFD.
        foreach ($this->getMultipleElements('*') as $tag => $sub_block) {
            if ($sub_block->getType() === 'thumbnail') {
                continue;
            }

            $bytes .= ConvertBytes::fromShort($sub_block->getAttribute('id'), $byte_order);
            $bytes .= ConvertBytes::fromShort($sub_block->getFormat(), $byte_order);
            $bytes .= ConvertBytes::fromLong($sub_block->getComponents(), $byte_order);

            $data = $sub_block->toBytes($byte_order, $data_area_offset);
            $s = strlen($data);
            if ($s > 4) {
                $bytes .= ConvertBytes::fromLong($data_area_offset, $byte_order);
                $data_area_bytes .= $data;
                $data_area_offset += $s;
            } else {
                // Copy data directly, pad with NULL bytes as necessary to
                // fill out the four bytes available.
                $bytes .= $data . str_repeat(chr(0), 4 - $s);
            }
        }

        // Thumbnail.
        if ($thumbnail) {
            $thumbnail_entry = $thumbnail->getElement('entry');
            // Add offset.
            $bytes .= ConvertBytes::fromShort(Spec::getElementIdByName($this->getType(), 'ThumbnailOffset'), $byte_order);
            $bytes .= ConvertBytes::fromShort(Format::LONG, $byte_order);
            $bytes .= ConvertBytes::fromLong(1, $byte_order);
            $bytes .= ConvertBytes::fromLong($data_area_offset, $byte_order);
            // Add length.
            $bytes .= ConvertBytes::fromShort(Spec::getElementIdByName($this->getType(), 'ThumbnailLength'), $byte_order);
            $bytes .= ConvertBytes::fromShort(Format::LONG, $byte_order);
            $bytes .= ConvertBytes::fromLong(1, $byte_order);
            $bytes .= ConvertBytes::fromLong($thumbnail_entry->getComponents(), $byte_order);
            // Add thumbnail.
            $data_area_bytes .= $thumbnail_entry->toBytes();
            $data_area_offset += $thumbnail_entry->getComponents();
        }

        // Append link to next IFD.
        if ($has_next_ifd) {
            $bytes .= ConvertBytes::fromLong($data_area_offset, $byte_order);
        } else {
            $bytes .= ConvertBytes::fromLong(0, $byte_order);
        }

        // Append data area.
        $bytes .= $data_area_bytes;

        return $bytes;
    }
}
