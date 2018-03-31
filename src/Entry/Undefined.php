<?php

namespace ExifEye\core\Entry;

use ExifEye\core\DataWindow;
use ExifEye\core\Format;

/**
 * Class for holding data of any kind.
 *
 * This class can hold bytes of undefined format.
 *
 * @author Martin Geisler <mgeisler@users.sourceforge.net>
 */
class Undefined extends EntryBase
{
    /**
     * {@inheritdoc}
     */
    protected $format = Format::UNDEFINED;

    /**
     * The value held by this entry.
     *
     * @var array
     */
    protected $value = [];

    /**
     * {@inheritdoc}
     */
    public static function getInstanceArgumentsFromData($ifd_id, $tag_id, $format, $components, DataWindow $data_window, $data_offset)
    {
        return [$data_window->getBytes($data_offset, $components)];
    }

    /**
     * Set the data of this undefined entry.
     *
     * @param array $data
     *            the data that this entry will be holding. Since the format is
     *            undefined, no checking will be done on the data.
     */
    public function setValue(array $data)
    {
        $this->value[0] = $data[0];
        $this->components = strlen($data[0]);
        $this->bytes = $data[0];
    }

    /**
     * Get the data of this undefined entry.
     *
     * @return string the data that this entry is holding.
     */
    public function getValue()
    {
        return $this->bytes;
    }

    /**
     * Decode text for an Exif/FileSource tag.
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
    public static function decodeFileSource($ifd_id, $tag_id, EntryBase $entry, $brief = false)
    {
        $value = $entry->getValue();
        switch (ord($value{0})) {
            case 0x03:
                return 'DSC';
            default:
                return sprintf('0x%02X', ord($value{0}));
        }
    }

    /**
     * Decode text for an Exif/SceneType tag.
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
    public static function decodeSceneType($ifd_id, $tag_id, EntryBase $entry, $brief = false)
    {
        $value = $entry->getValue();
        switch (ord($value{0})) {
            case 0x01:
                return 'Directly photographed';
            default:
                return sprintf('0x%02X', ord($value{0}));
        }
    }

    /**
     * Decode text for an Exif/ComponentsConfiguration tag.
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
    public static function decodeComponentsConfiguration($ifd_id, $tag_id, EntryBase $entry, $brief = false)
    {
        $value = $entry->getValue();
        $v = '';
        for ($i = 0; $i < 4; $i ++) {
            switch (ord($value{$i})) {
                case 0:
                    $v .= '-';
                    break;
                case 1:
                    $v .= 'Y';
                    break;
                case 2:
                    $v .= 'Cb';
                    break;
                case 3:
                    $v .= 'Cr';
                    break;
                case 4:
                    $v .= 'R';
                    break;
                case 5:
                    $v .= 'G';
                    break;
                case 6:
                    $v .= 'B';
                    break;
                default:
                    $v .= 'reserved';
                    break;
            }
            if ($i < 3) {
                $v .= ' ';
            }
        }
        return $v;
    }

    /**
     * Get the value of this entry as text.
     *
     * The value will be returned in a format suitable for presentation.
     *
     * @param
     *            boolean some values can be returned in a long or more
     *            brief form, and this parameter controls that.
     *
     * @return string the value as text.
     */
    public function getText($brief = false)
    {
        // If Spec can return the text, return it.
        if (($tag_text = parent::getText($brief)) !== null) {
            return $tag_text;
        }

        return '(undefined)';
    }
}
