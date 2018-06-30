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
     * @return \ExifEye\core\ElementInterface[]
     *            the selected children elements of this element.
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
     * @return \ExifEye\core\ElementInterface[]
     *            the selected children elements of this element.
     *
     * @throws \ExifEye\core\ExifEyeException
     *            when multiple elements fulfil the XPath expression.
     */
    public function removeElement($expression);

    /**
     * Gets the DOM attributes associated to this element.
     *
     * @return \ExifEye\core\ElementInterface[]
     *            the selected children elements of this element.
     *
     * @throws \ExifEye\core\ExifEyeException
     *            when multiple elements fulfil the XPath expression.
     */
    public function getAttributes();

    public function getAttribute($name);
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
