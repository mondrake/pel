<?php

namespace ExifEye\core;

/**
 * This class defines the constants that are to be used whenever one has to
 * refer to the format of an Exif tag. They will be collectively denoted by the
 * pseudo-type Format throughout the documentation.
 *
 * All the methods defined here are static, and they all operate on a single
 * argument which should be one of the class constants.
 *
 * @author Martin Geisler <mgeisler@users.sourceforge.net>
 * @author Johannes Weberhofer <jweberhofer@weberhofer.at>
 */
abstract class Format
{
    /**
     * Returns the name of a format like 'Ascii' for the ASCII format.
     *
     * @param integer $type
     *
     * @return string|null
     */
    public static function getName($type)
    {
        if (array_key_exists($type, self::$formatName)) {
            return self::$formatName[$type];
        }
        return null;
    }

    /**
     * Returns the id of a format from its name.
     *
     * @param string $name
     *
     * @return integer|null
     */
    public static function getIdFromName($name)
    {
        static $map;

        if (!isset($map)) {
            $map = array_flip(static::$formatName);
        }

        return isset($map[$name]) ? $map[$name] : null;
    }

    /**
     * Return the size of components in a given format in bytes needed to store
     * one component with the given format.
     *
     * @param integer $type
     *
     * @return integer|null
     */
    public static function getSize($type)
    {
        if (array_key_exists($type, self::$formatLength)) {
            return self::$formatLength[$type];
        }
        return null;
    }
}
