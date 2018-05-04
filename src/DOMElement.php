<?php

namespace ExifEye\core;

class DOMElement extends \DOMElement
{
    protected $exifEyeElement;

    public function __construct(ElementInterface $element)
    {
        parent::__construct($element->getType());
        $this->exifEyeElement = $element;
    }
}
