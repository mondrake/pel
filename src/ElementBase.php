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
    protected $xPath;

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
                $this->xPath = new \DOMXPath($this->doc);
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
    public function setDOMNode(ExifEyeDOMElement $node)
    {
        $this->DOMNode = $node;
        $this->DOMNode->setExifEyeElement($this);
        $this->doc = $this->DOMNode->ownerDocument;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        $attr = [];
        foreach ($this->DOMNode->attributes as $attribute) {
            $attr[$attribute->name] = $attribute->value;
        }
        return $attr;
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
    public function query($expression)
    {
dump($this->DOMNode);
        $node_list = $this->xPath->query($expression, $this->DOMNode);
dump($node_list);
        $ret = [];
        foreach ($node_list as $node) {
            $ret = $node->getExifEyeElement();
        }
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
    }

    /**
     * {@inheritdoc}
     */
    public function getContextPath()
    {
        return $this->DOMNode ? $this->DOMNode->getContextPath() : '';
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
            'path' => $this->getContextPath(),
            'class' => get_class($this),
            'valid' => $this->isValid(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = [])
    {
        $context['path'] = $this->getContextPath();
        ExifEye::logger()->log($level, $message, $context);
    }
}
