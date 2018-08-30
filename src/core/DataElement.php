<?php

namespace ExifEye\core;

use ExifEye\core\ExifEye;
use ExifEye\core\Utility\ConvertBytes;

/**
 * A primitive data object.
 */
abstract class DataElement
{
    /**
     * The size of the data.
     *
     * @var int
     */
    protected $size = 0;

    /**
     * Get the size of the data window.
     *
     * @return integer the number of bytes covered by the window. The
     *         allowed offsets go from 0 up to this number minus one.
     *
     * @see getBytes()
     */
    public function getSize()
    {
        return $this->size;
    }
}
