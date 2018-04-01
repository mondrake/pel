<?php

namespace ExifEye\core\Entry;

use ExifEye\core\DataWindow;
use ExifEye\core\Entry\Core\EntryBase;
use ExifEye\core\Entry\Core\Undefined;
// xx use ExifEye\core\Entry\Exception\UnexpectedFormatException;
use ExifEye\core\ExifEye;
use ExifEye\core\Format;

/**
 * Class to hold version information.
 */
class Version extends Undefined
{
    /**
     * {@inheritdoc}
     */
    public static function getInstanceArgumentsFromTagData($format, $components, DataWindow $data_window, $data_offset)
    {
// xx        if ($format != Format::UNDEFINED) {
// xx            throw new UnexpectedFormatException($ifd_id, $tag_id, $format, Format::UNDEFINED);
// xx        }
dump($format, $components, $data_offset);
        return [$data_window->getBytes($data_offset, $components) / 100];
    }

    /**
     * {@inheritdoc}
     */
    public function setValue(array $data)
    {
        $version = isset($data[0]) ? $data[0] : 0.0;
        $major = floor($version);
        $minor = ($version - $major) * 100;
        $bytes = sprintf('%02.0f%02.0f', $major, $minor);

        $this->value[0] = (string) ($version . ($minor === 0.0 ? '.0' : ''));
        $this->components = strlen($bytes);
        $this->bytes = $bytes;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->value[0];
    }

    /**
     * {@inheritdoc}
     */
    public function getText($short = false)
    {
        if ($short) {
            return $this->getValue();
        } else {
            return ExifEye::fmt('Version %s', $this->getValue());
        }
    }

    /**
     * Decode text for an Exif/ExifVersion tag.
     *
     * @param int $ifd_id
     *            the IFD id.
     * @param int $tag_id
     *            the TAG id.
     * @param EntryBase $entry
     *            the TAG EntryBase object.
     * @param bool $brief
     *            (Optional) indicates to use brief output.
     *
     * @return string
     *            the TAG text.
     */
    public static function decodeExifVersion($ifd_id, $tag_id, EntryBase $entry, $brief = false)
    {
        if ($brief) {
            return ExifEye::fmt('%s', $entry->getValue());
        } else {
            return ExifEye::fmt('Exif Version %s', $entry->getValue());
        }
    }

    /**
     * Decode text for an Exif/FlashPixVersion tag.
     *
     * @param int $ifd_id
     *            the IFD id.
     * @param int $tag_id
     *            the TAG id.
     * @param EntryBase $entry
     *            the TAG EntryBase object.
     * @param bool $brief
     *            (Optional) indicates to use brief output.
     *
     * @return string
     *            the TAG text.
     */
    public static function decodeFlashPixVersion($ifd_id, $tag_id, EntryBase $entry, $brief = false)
    {
        if ($brief) {
            return ExifEye::fmt('%s', $entry->getValue());
        } else {
            return ExifEye::fmt('FlashPix Version %s', $entry->getValue());
        }
    }

    /**
     * Decode text for an Interoperability/InteroperabilityVersion tag.
     *
     * @param int $ifd_id
     *            the IFD id.
     * @param int $tag_id
     *            the TAG id.
     * @param EntryBase $entry
     *            the TAG EntryBase object.
     * @param bool $brief
     *            (Optional) indicates to use brief output.
     *
     * @return string
     *            the TAG text.
     */
    public static function decodeInteroperabilityVersion($ifd_id, $tag_id, EntryBase $entry, $brief = false)
    {
        if ($brief) {
            return ExifEye::fmt('%s', $entry->getValue());
        } else {
            return ExifEye::fmt('Interoperability Version %s', $entry->getValue());
        }
    }
}
