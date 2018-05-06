<?php

namespace ExifEye\core;

use ExifEye\core\DOM\ExifEyeDOMElement;
use ExifEye\core\ExifEye;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

/**
 * Base class for ElementInterface objects.
 *
 * As this class is abstract you cannot instantiate objects from it. It only
 * serves as a common ancestor to define the methods common to all ExifEye
 * elements (Block and Entry objects).
 */
abstract class ElementBase implements ElementInterface, LoggerInterface
{
    use LoggerTrait;

    protected $doc;

    /**
     * The DOM node associated to this element.
     *
     * @var \DOMNode
     */
    protected $DOMNode;

    /**
     * The type of this element.
     *
     * @var string
     */
    protected $type;

    /**
     * Whether this element is valid.
     *
     * @var bool
     */
    protected $valid = true;

    /**
     * Constructs an Element object.
     *
     * @param \ExifEye\core\ElementInterface $parent
     *            the parent element of this element.
     */
    public function __construct(ElementInterface $parent = null)
    {
        if ($parent) {
            $this->doc = $parent->xxgetDoc();
            if ($this->doc) {
                $this->DOMNode = $this->doc->createElement($this->getType());
                $parent->DOMNode->appendChild($this->DOMNode);
                $this->DOMNode->setExifEyeElement($this);
            }
        }
    }

    public function xxgetDoc()
    {
        return $this->doc;
    }

    /**
     * {@inheritdoc}
     */
    public function setDOMNode(ExifEyeDOMElement $DOM_node)
    {
        $this->DOMNode = $DOM_node;
        $this->DOMNode->setExifEyeElement($this);
        $this->doc = $this->DOMNode->ownerDocument;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute($name, $value)
    {
        return $this->DOMNode->setAttribute($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($name)
    {
        return $this->DOMNode->getAttribute($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentElement()
    {
        return $this->DOMNode ? $this->DOMNode->parentNode->getExifEyeElement() : null;
        //return $this->parentElement;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->DOMNode ? $this->DOMNode->getNodePath() : '';
        //return $this->getParentElement() ? $this->getParentElement()->getPath() . '/' . $this->getElementPathFragment() : $this->getElementPathFragment();
    }

    /**
     * {@inheritdoc}
     */
    public function getElementPathFragment()
    {
        return $this->getType() . ':' . $this->getAttribute('name');
    }

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * {@inheritdoc}
     */
    public function toDumpArray()
    {
        return [
            'path' => $this->getPath(),
            'class' => get_class($this),
            'id' => $this->getAttribute('id'),
            'name' => $this->getAttribute('name'),
            'valid' => $this->isValid(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = [])
    {
        $context['path'] = $this->getPath();
        ExifEye::logger()->log($level, $message, $context);
    }
}
