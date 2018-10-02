<?php

namespace ExifEye\core\Block;

use ExifEye\core\Data\DataElement;
use ExifEye\core\Data\DataWindow;
use ExifEye\core\ExifEye;
use ExifEye\core\Format;
use ExifEye\core\Spec;
use ExifEye\core\Utility\ConvertBytes;

/**
 * Class representing an index of Short values as an IFD.
 */
class IfdIndexShort extends Ifd
{
    /**
     * {@inheritdoc}
     */
    public function loadFromData(DataElement $data_element, $offset = 0, $size = null, array $options = [])
    {
        if (isset($options['format'])) {
            $this->format = $options['format'];
        }
        if (isset($options['components'])) {
            $this->components = $options['components'];
        }

        $this->debug("START... Loading with {tags} TAGs at offset {offset} from {total} bytes", [
            'tags' => $this->components,
            'offset' => $offset,
            'total' => $data_element->getSize(),
        ]);

        $index_size = $data_element->getShort($offset);
        if ($index_size / $this->components !== Format::getSize(Format::SHORT)) {
            $this->warning('Size of {ifd_name} does not match the number of entries.', [
                'ifd_name' => $this->getAttribute('name'),
            ]);
        }
        $offset += 2;
        for ($i = 0; $i < $this->components; $i++) {
            // Check if this tag ($i + 1) should be skipped.
            if (Spec::getElementPropertyValue($this->getType(), $i + 1, 'skip')) {
                continue;
            };
            $item_format = Spec::getElementPropertyValue($this->getType(), $i + 1, 'format')[0];
            switch ($item_format) {
                case Format::BYTE:
                    $item_value = $data_element->getByte($offset + $i * 2);
                    break;
                case Format::SHORT:
                    $item_value = $data_element->getShort($offset + $i * 2);
                    break;
                case Format::LONG:
                    $item_value = $data_element->getLong($offset + $i * 2);
                    break;
                case Format::RATIONAL:
                    $item_value = $data_element->getRational($offset + $i * 2);
                    break;
                case Format::SBYTE:
                    $item_value = $data_element->getSignedByte($offset + $i * 2);
                    break;
                case Format::SSHORT:
                    $item_value = $data_element->getSignedShort($offset + $i * 2);
                    break;
                case Format::SLONG:
                    $item_value = $data_element->getSignedLong($offset + $i * 2);
                    break;
                case Format::SRATIONAL:
                    $item_value = $data_element->getSRattional($offset + $i * 2);
                    break;
                default:
                    $item_value = $data_element->getSignedShort($offset + $i * 2);
                    $item_format = Format::SSHORT;
                    break;
            }
            if ($entry_class = Spec::getElementHandlingClass($this->getType(), $i + 1, $item_format)) {
                new Tag('tag', $this, $i + 1, $entry_class, [$item_value], $item_format, 1);
            }
        }
        $this->debug(".....END Loading");
    }

    /**
     * {@inheritdoc}
     */
    public function toBytes($byte_order = ConvertBytes::LITTLE_ENDIAN, $offset = 0)
    {
        $data_bytes = '';

        foreach ($this->getMultipleElements('*') as $tag => $sub_block) {
            $data_bytes .= $sub_block->toBytes($byte_order);
        }

        return ConvertBytes::fromShort(strlen($data_bytes)) . $data_bytes
    }
}
