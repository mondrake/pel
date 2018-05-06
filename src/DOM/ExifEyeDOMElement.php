<?php

namespace ExifEye\core\DOM;

use ExifEye\core\ElementInterface;

class ExifEyeDOMElement extends \DOMElement
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

    public function getContextPath()
    {
        $parent_path = $this->parentNode ? $this->parentNode->getContextPath() : '';

        $current_fragment = '/' . $this->nodeName;

        return $parent_path . $current_fragment;
    }
}
