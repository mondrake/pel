<?php

namespace ExifEye\core\Block;

use ExifEye\core\DataWindow;
use ExifEye\core\ExifEye;
use ExifEye\core\Format;
use ExifEye\core\InvalidDataException;
use ExifEye\core\Spec;

/**
 * Class representing an index of Short values as an IFD.
 */
class IfdIndexShort extends Ifd
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'IfdIndexShort';

    /**
     * Load data into a Image File Directory (IFD).
     *
     * @param DataWindow $data_window
     *            the data window that will provide the data.
     * @param int $offset
     *            the offset within the window where the directory will
     *            be found.
     * @param int $components
     *            (Optional) the number of components held by this IFD.
     */
    public function loadFromData(\DOMElement $dom, DataWindow $data_window, $offset = 0, array $options = [])
    {
        $components = $options['components'];

        $ifd_dom = $this->doc->createElement($this->getType());
        $ifd_dom->setAttribute('name', $this->getName());
        $dom->appendChild($ifd_dom);

        $this->debug("START... Loading with {tags} TAGs at offset {offset} from {total} bytes", [
            'tags' => $components,
            'offset' => $offset,
            'total' => $data_window->getSize(),
        ]);

        $index_size = $data_window->getShort($offset);
        if ($index_size / $components !== Format::getSize(Format::SHORT)) {
            $this->warning('Size of {ifd_type} does not match the number of entries.', [
                'ifd_type' => $this->getName(),
            ]);
        }
        $offset += 2;
        for ($i = 0; $i < $components; $i++) {
            // Check if this tag ($i + 1) should be skipped.
            if (Spec::getTagSkip($this->getId(), $i + 1)) {
                continue;
            };
            $item_format = Spec::getTagFormat($this->getId(), $i + 1)[0];
            switch ($item_format) {
                case Format::BYTE:
                    $item_value = $data_window->getByte($offset + $i * 2);
                    break;
                case Format::SHORT:
                    $item_value = $data_window->getShort($offset + $i * 2);
                    break;
                case Format::LONG:
                    $item_value = $data_window->getLong($offset + $i * 2);
                    break;
                case Format::RATIONAL:
                    $item_value = $data_window->getRational($offset + $i * 2);
                    break;
                case Format::SBYTE:
                    $item_value = $data_window->getSignedByte($offset + $i * 2);
                    break;
                case Format::SSHORT:
                    $item_value = $data_window->getSignedShort($offset + $i * 2);
                    break;
                case Format::SLONG:
                    $item_value = $data_window->getSignedLong($offset + $i * 2);
                    break;
                case Format::SRATIONAL:
                    $item_value = $data_window->getSRattional($offset + $i * 2);
                    break;
                default:
                    $item_value = $data_window->getSignedShort($offset + $i * 2);
                    $item_format = Format::SSHORT;
                    break;
            }
            if ($entry_class = Spec::getEntryClass($this->getId(), $i + 1, $item_format)) {
                $this->xxAppendSubBlock(new Tag($this, $i + 1, $entry_class, [$item_value], $item_format, 1, $this->doc, $ifd_dom));
            }
        }
        $this->debug(".....END Loading");
    }
}
