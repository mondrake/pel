<?php

namespace ExifEye\core;

/**
 * Interface for Element objects.
 *
 * ExifEye Block and Entry objects all implement this interface.
 */
interface ElementInterface
{
    /**
     * Gets the parent element of this element.
     *
     * @return \ExifEye\core\ElementInterface
     *            the parent element of this element.
     */
    public function getParentElement();

    /**
     * Returns the type of this element.
     *
     * @return string
     */
    public function getType();

    /**
     * Returns the full path of this element.
     *
     * @return string
     */
    public function getPath();

    /**
     * Returns the path fragment of this element.
     *
     * @return string
     */
    public function getElementPathFragment();

    /**
     * Gets validity of the element.
     *
     * @return bool
     */
    public function isValid();

    /**
     * Returns a dump of the element in an array.
     *
     * @return array
     */
    public function toDumpArray();
}
