<?php

namespace ExifEye\core\Entry\Exception;

use ExifEye\core\ExifEyeException;

/**
 * Exception indicating a problem with an entry.
 *
 * This file defines two exception classes and the abstract class
 * {@link EntryBase} which provides the basic methods that all Exif
 * entries will have. All Exif entries will be represented by
 * descendants of the {@link EntryBase} class --- the class itself is
 * abstract and so it cannot be instantiated.
 *
 * @author Martin Geisler <mgeisler@users.sourceforge.net>
 */
class EntryException extends ExifEyeException
{
    /**
     * The IFD type (if known).
     *
     * @var int
     */
    protected $type;

    /**
     * The ID of the entry (if known).
     *
     * @var int
     */
    protected $id;

    /**
     * Get the IFD type associated with the exception.
     *
     * @return int one of {@link Ifd::IFD0}, {@link Ifd::IFD1},
     *         {@link Ifd::EXIF}, {@link Ifd::GPS}, or {@link
     *         Ifd::INTEROPERABILITY}. If no type is set, null is returned.
     */
    public function getIfdType()
    {
        return $this->type;
    }

    /**
     * Get the ID of the tag associated with the exception.
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }
}
