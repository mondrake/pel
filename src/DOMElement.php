<?php

namespace ExifEye\core;

class DOMElement extends \DOMElement
{
    protected $exifEyeElement;

    public function setExifEyeElement(ElementInterface $element)
    {
        $this->exifEyeElement = $element;
    }
    public function getExifEyeElement()
    {
        return $this->exifEyeElement;
    }
}
