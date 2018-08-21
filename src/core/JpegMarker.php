<?php

namespace ExifEye\core;

/**
 * Classes for dealing with JPEG markers.
 *
 * This class defines the constants to be used whenever one refers to
 * a JPEG marker. All the methods defined are static, and they all
 * operate on one argument which should be one of the class constants.
 * They will all be denoted by JpegMarker in the documentation.
 *
 * @author Martin Geisler <mgeisler@users.sourceforge.net>
 * @author Johannes Weberhofer <jweberhofer@weberhofer.at>
 */
class JpegMarker
{
    /**
     * Check if a byte is a valid JPEG marker.
     * If the byte is recognized true is returned, otherwise false will be returned.
     *
     * @param integer $marker
     *            the marker as defined in {@link JpegMarker}
     *
     * @return boolean
     */
    public static function isValid($marker)
    {
        return ($marker >= 192 && $marker <= self::COM);
    }

    /**
     * Turn a JPEG marker into bytes.
     * This will be a string with just a single byte since all JPEG markers are simply single bytes.
     *
     * @param integer $marker
     *            the marker as defined in {@link JpegMarker}
     *
     * @return string
     */
    public static function getBytes($marker)
    {
        return chr($marker);
    }

    /**
     * Return the short name for a marker, e.g., 'SOI' for the Start
     * of Image marker.
     *
     * @param integer $marker
     *            the marker as defined in {@link JpegMarker}
     *
     * @return string
     */
    public static function getName($marker)
    {
        return Spec::getElementName('jpeg', $marker) ?: ExifEye::fmt('Unknown marker: 0x%02X', $marker);
    }

    /**
     * Returns a description of a JPEG marker.
     *
     * @param integer $marker
     *            the marker as defined in {@link JpegMarker}
     *
     * @return string
     */
    public static function getDescription($marker)
    {
        return Spec::getElementTitle('jpeg', $marker) ?: ExifEye::fmt('Unknown marker: 0x%02X', $marker);
    }
}
