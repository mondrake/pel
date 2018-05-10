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
    public function __construct(ElementInterface $parent)
    {
        $this->doc = $parent->xxgetDoc();
        if ($this->doc) {
            $this->DOMNode = $this->doc->createElement($this->getType());
            $parent->xxgetDOMNode()->appendChild($this->xxgetDOMNode());
        } else {
            $this->doc = new \DOMDocument();
            $this->doc->registerNodeClass('DOMElement', 'ExifEye\core\DOM\ExifEyeDOMElement');
            $this->DOMNode = $this->doc->createElement($this->getType());
            $this->doc->appendChild($this->xxgetDOMNode());
        }
        $this->xxgetDOMNode()->setExifEyeElement($this);
    }

    private function xxgetDOMNode()
    {
        return $this->DOMNode;
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
        $this->xxgetDOMNode()->setExifEyeElement($this);
        $this->doc = $this->xxgetDOMNode()->ownerDocument;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        $attr = [];
        foreach ($this->xxgetDOMNode()->attributes as $attribute) {
            $attr[$attribute->name] = $attribute->value;
        }
        return $attr;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute($name, $value)
    {
        return $this->xxgetDOMNode()->setAttribute($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($name)
    {
        return $this->xxgetDOMNode()->getAttribute($name);
    }

    /**
     * {@inheritdoc}
     */
    public function query($expression)
    {
        $x_path = new \DOMXPath($this->doc);
        $node_list = $x_path->query($expression, $this->xxgetDOMNode());
        $ret = [];
        for ($i = 0; $i < $node_list->length; $i++) {
            $ret[] = $node_list->item($i)->getExifEyeElement();
        }
        return $ret;
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
        return $this->xxgetDOMNode() ? $this->xxgetDOMNode()->parentNode->getExifEyeElement() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getContextPath()
    {
        return $this->xxgetDOMNode() ? $this->xxgetDOMNode()->getContextPath() : '';
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
