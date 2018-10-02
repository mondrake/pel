<?php

namespace ExifEye\Apple\Block;

use ExifEye\core\Block\Ifd;
use ExifEye\core\Block\Tag;
use ExifEye\core\Data\DataElement;
use ExifEye\core\Data\DataWindow;
use ExifEye\core\ExifEye;
use CFPropertyList\CFPropertyList;
use ExifEye\core\Spec;

class RunTime extends Ifd
{
    protected $pList;

    /**
     * {@inheritdoc}
     */
    public function loadFromData(DataElement $data_element, $offset = 0, $size = null, array $options = [])
    {
        if (isset($options['format'])) {
            $this->format = $options['format'];
        }
        if (isset($options['components'])) {
            $this->components = $options['components'];
        }

        $this->debug("START... Loading");

        $plist = new CFPropertyList();
dump(ExifEye::dumpHex($data_element->getBytes($options['data_offset'], $options['components']), 40));
        $plist->parse($data_element->getBytes($options['data_offset'], $options['components']));
        $this->pList = $plist;

        // Build a TAG object for each PList item.
        foreach ($plist->toArray() as $tag_name => $value) {
            $tag_id = Spec::getElementIdByName($this->getType(), $tag_name);
            $item_format = Spec::getElementPropertyValue($this->getType(), $tag_id, 'format')[0];
            $tag_entry_class = Spec::getElementHandlingClass($this->getType(), $tag_id, $item_format);
            $tag = new Tag('tag', $this, $tag_id, $tag_entry_class, [$value], $item_format, 1);
        }

        $this->debug(".....END Loading");
    }

    /**
     * {@inheritdoc}
     */
    public function toBytes($byte_order = ConvertBytes::LITTLE_ENDIAN, $offset = 0, $has_next_ifd = false)
    {
dump(ExifEye::dumpHex($this->pList->toBinary(), 40));
//dump($this->pList);
        return $this->pList->toBinary();
    }
}
