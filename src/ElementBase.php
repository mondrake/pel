<?php

namespace ExifEye\core;

use ExifEye\core\DOM\ExifEyeDOMElement;
use ExifEye\core\ExifEye;
use ExifEye\core\ExifEyeException;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Monolog\Logger;

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

    /**
     * The DOM node associated to this element.
     *
     * @var \DOMNode
     */
    protected $DOMNode;

    /**
     * The Xpath object associated to the root element.
     *
     * @var \DOMXPath|null
     */
    protected $xPath;

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
     * @param \ExifEye\core\ElementInterface|null $parent
     *            (Optional) the parent element of this element.
     * @param \ExifEye\core\ElementInterface|null $reference
     *            (Optional) if specified, the new element will be inserted
     *            before the reference element.
     */
    public function __construct(ElementInterface $parent = null, ElementInterface $reference = null)
    {
        if (!$parent || !is_object($parent->DOMNode)) {
            $doc = new \DOMDocument();
            $doc->registerNodeClass('DOMElement', 'ExifEye\core\DOM\ExifEyeDOMElement');
            $this->xPath = new \DOMXPath($doc);
            $parent_node = $doc;
        } else {
            $doc = $parent->DOMNode->ownerDocument;
            $parent_node = $parent->DOMNode;
        }

        $this->DOMNode = $doc->createElement($this->getType());

        if ($reference) {
            $parent_node->insertBefore($this->DOMNode, $reference->DOMNode);
        } else {
            $parent_node->appendChild($this->DOMNode);
        }

        $this->DOMNode->setExifEyeElement($this);
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
    public function getMultipleElements($expression)
    {
        $node_list = $this->getRootElement()->xPath->getMultipleElements($expression, $this->DOMNode);
        $ret = [];
        for ($i = 0; $i < $node_list->length; $i++) {
            $ret[] = $node_list->item($i)->getExifEyeElement();
        }
        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function getElement($expression)
    {
        $ret = $this->getMultipleElements($expression);
        switch (count($ret)) {
            case 0:
                return null;
            case 1:
                return $ret[0];
            default:
                throw new ExifEyeException("Multiple elements returned for '%s'", $expression);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeElement($expression)
    {
        $ret = $this->getMultipleElements($expression);
        if ($ret) {
            $ret[0]->DOMNode->parentNode->removeChild($ret[0]->DOMNode);
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
        return $this->DOMNode->parentNode && !($this->DOMNode->parentNode instanceof \DOMDocument) ? $this->DOMNode->parentNode->getExifEyeElement() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getRootElement()
    {
        return $this->DOMNode->ownerDocument->documentElement->getExifEyeElement();
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
        $root_element = $this->getRootElement();
        if (method_exists($root_element, 'logger')) {  // xx should be logging anyway
            $root_element->logger()->log($level, $message, $context);
        }
        if (method_exists($root_element, 'externalLogger') && $root_element->externalLogger()) {  // xx should be logging anyway
            $root_element->externalLogger()->log($level, $message, $context);
        }
        if (method_exists($root_element, 'getFailLevel') && $root_element->getFailLevel() !== false && Logger::toMonologLevel($level) >= $root_element->getFailLevel()) {  // xx should be logging anyway
            throw new ExifEyeException($message);
        }
    }
}
