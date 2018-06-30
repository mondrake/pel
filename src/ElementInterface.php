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
     * Returns the type of this element.
     *
     * @return string
     */
    public function getType();

    /**
     * Gets the root ancestor element of this element.
     *
     * @return \ExifEye\core\ElementInterface
     *            the root ancestor element of this element.
     */
    public function getRootElement();

    /**
     * Gets the parent element of this element.
     *
     * @return \ExifEye\core\ElementInterface
     *            the parent element of this element.
     */
    public function getParentElement();

    /**
     * Gets multiple children elements of this element.
     *
     * @param string $expression
     *            an XPath expression identifying the sub-elements to be
     *            selected.
     *
     * @return \ExifEye\core\ElementInterface[]
     *            the selected children elements of this element.
     */
    public function getMultipleElements($expression);

    /**
     * Gets a single child element of this element.
     *
     * @param string $expression
     *            an XPath expression identifying the sub-element to be
     *            selected.
     *
     * @return \ExifEye\core\ElementInterface
     *            the selected child elements of this element.
     *
     * @throws \ExifEye\core\ExifEyeException
     *            when multiple elements fulfil the XPath expression.
     */
    public function getElement($expression);

    /**
     * Removes a single child element of this element.
     *
     * @param string $expression
     *            an XPath expression identifying the sub-element to be
     *            removed.
     *
     * @return bool
     *            true if the element was removed, false if the element is not
     *            existing.
     *
     * @throws \ExifEye\core\ExifEyeException
     *            when multiple elements fulfil the XPath expression.
     */
    public function removeElement($expression);

    /**
     * Gets the DOM attributes associated to this element.
     *
     * @return string[]
     *            an associative array with the DOM attribute names as keys, and
     *            the DOM attribute values as values.
     */
    public function getAttributes();

    /**
     * Gets the value of a DOM attribute associated to this element.
     *
     * @param string $name
     *            the name of the DOM attribute.
     *
     * @return string|null
     *            the DOM attribute value, or null if the attribute is not
     *            existing.
     */
    public function getAttribute($name);

    /**
     * Sets the value of a DOM attribute associated to this element.
     *
     * @param string $name
     *            the name of the DOM attribute.
     * @param string $value
     *            the value of the DOM attribute.
     */
    public function setAttribute($name, $value);

    /**
     * Returns a context path for this element.
     *
     * It gives whereabouts of the element within the overall structure of the
     * image. Note that this is not an XPath compliant path, it is mainly used
     * for logging purposes.
     *
     * @return string
     */
    public function getContextPath();

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
