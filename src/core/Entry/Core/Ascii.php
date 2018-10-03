<?php

namespace ExifEye\core\Entry\Core;

use ExifEye\core\Block\BlockBase;
use ExifEye\core\Data\DataWindow;
use ExifEye\core\ExifEye;
use ExifEye\core\Format;
use ExifEye\core\Utility\ConvertBytes;

/**
 * Class for holding a plain ASCII string.
 */
class Ascii extends EntryBase
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'Ascii';

    /**
     * {@inheritdoc}
     */
    protected $format = Format::ASCII;

    /**
     * {@inheritdoc}
     */
    public static function getInstanceArgumentsFromTagData(BlockBase $parent_block, $format, $components, DataWindow $data_window, $data_offset)
    {
        // Cap bytes to get to remaining data window size.
        $size = $data_window->getSize();
        if ($data_offset + $components > $size) {
            $bytes_to_get = $size - $data_offset;
            $parent_block->warning('Ascii entry reading {actual} bytes instead of {expected} to avoid data window overflow', [
                'actual' => $bytes_to_get,
                'expected' => $components,
            ]);
        } else {
            $bytes_to_get = $components;
        }
        $bytes = $data_window->getBytes($data_offset, $bytes_to_get);

        // Check the last byte is NULL.
        if (substr($bytes, -1) !== "\x0") {
            $parent_block->notice('Ascii entry \'{bytes}\' missing final NUL character.', [
                'bytes' => $bytes,
            ]);
        }

        return [$bytes];
    }

    /**
     * {@inheritdoc}
     */
    public function setValue(array $data)
    {
        parent::setValue($data);

        $str = isset($data[0]) ? $data[0] : '';

        $this->value = $str;
        $this->components = substr($this->value, -1) === "\x0" ? strlen($str) : strlen($str) + 1;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toBytes($byte_order = ConvertBytes::LITTLE_ENDIAN, $offset = 0)
    {
        return substr($this->value, -1) === "\x0" ? $this->value : $this->value . "\x0";
    }

    /**
     * {@inheritdoc}
     */
    public function toString(array $options = [])
    {
        return parent::toString($options) ?: rtrim($this->getValue(), "\x0");
    }
}
