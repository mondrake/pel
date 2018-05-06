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
        $parent_path = $this->parentNode && !($this->parentNode instanceof \DOMDocument) ? $this->parentNode->getContextPath() : '';

        $current_fragment = '/' . $this->nodeName;
        if (count($this->attributes)) {
            $current_fragment .= var_export($this->attributes, true);
        }

        return $parent_path . $current_fragment;
    }
}
