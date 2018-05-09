<?php

namespace ExifEye\core\Block;

use ExifEye\core\DataWindow;
use ExifEye\core\ElementBase;
use ExifEye\core\Entry\Core\EntryInterface;

/**
 * Class representing an Exif TAG.
 */
abstract class BlockBase extends ElementBase
{
    /**
     * The block has a specification description.
     *
     * @var string
     */
    protected $hasSpecification;

    /**
     * The child blocks.
     *
     * @var BlockBase[]
     */
    protected $subBlocks = [];

    public function hasSpecification()
    {
        return $this->hasSpecification;
    }

    /**
     * Loads data into a block.
     *
     * @param DataWindow $data_window
     *            the data window that will provide the data.
     * @param int $offset
     *            (Optional) the offset within the window where the block will
     *            be found.
     * @param array $options
     *            (Optional) an array with additional options for the load.
     *
     * @returns BlockBase
     */
    public function loadFromData(DataWindow $data_window, $offset = 0, array $options = [])
    {
    }

    /**
     * Adds a sub-block.
     *
     * @param BlockBase $sub_block
     *            the sub-block that will be added.
     */
    public function xxAddSubBlock(BlockBase $sub_block)
    {
        $type = $sub_block->getType();
        for ($i = 0; $i < count($this->xxGetSubBlocks($type)); $i++) {
            if ($sub_block->getAttribute('id') === $this->xxGetSubBlockByIndex($type, $i)->getAttribute('id')) {
                $this->subBlocks[$type][$i] = $sub_block;
                return $this;
            }
        }
        return $this->xxAppendSubBlock($sub_block);
    }

    /**
     * Appends a sub-block.
     *
     * @param BlockBase $sub_block
     *            the sub-block that will be appended.
     */
    public function xxAppendSubBlock(BlockBase $sub_block)
    {
        $this->subBlocks[$sub_block->getType()][] = $sub_block;
        return $this;
    }

    public function xxGetSubBlock($type, $id)
    {
        foreach ($this->xxGetSubBlocks($type) as $sub_block) {
            if ($sub_block->getAttribute('id') == $id) {
                return $sub_block;
            }
        }
        return null;
    }

    public function xxGetSubBlockByName($type, $name)
    {
        foreach ($this->xxGetSubBlocks($type) as $sub_block) {
            if ($sub_block->getAttribute('name') === $name) {
                return $sub_block;
            }
        }
        return null;
    }

    /**
     * Retrieves a sub-block.
     *
     * @param int $index
     *            the index identifying the sub-block.
     *
     * @return BlockBase the sub-block associated with the index, or null if no
     *         such block exists.
     */
    public function xxGetSubBlockByIndex($type, $index)
    {
        return isset($this->subBlocks[$type][$index]) ? $this->subBlocks[$type][$index] : null;
    }

    /**
     * Returns all sub-blocks.
     *
     * @return BlockBase[]
     */
    public function xxGetSubBlocks($type = null)
    {
        if ($type === null) {
            return $this->subBlocks;
        } else {
            return isset($this->subBlocks[$type]) ? $this->subBlocks[$type] : [];
        }
    }

    /**
     * Gets the block's associated entry.
     *
     * @return EntryInterface
     */
    public function getEntry()
    {
        $entry = $this->query('Entry');
dump($entry);
        if ($entry) {
            return $entry[0];
        }
        return null;
/*        if (!$this->DOMNode) {
            return null;
        }
        $children = $this->DOMNode->getElementsByTagName('Entry');
        if ($children->length === 1) {
            return $children[0]->getExifEyeElement();
        }
        return null;*/
    }

    /**
     * {@inheritdoc}
     */
    public function toDumpArray()
    {
        $dump = array_merge(parent::toDumpArray(), $this->getAttributes());

        // Dump Entry if existing.
        if ($this->getEntry()) {
            $dump['Entry'] = $this->getEntry()->toDumpArray();
        }

        // Dump sub-Blocks.
        foreach ($this->xxGetSubBlocks() as $type => $sub_blocks) {
            $dump['blocks'][$type] = [];
            foreach ($sub_blocks as $sub_block) {
                $dump['blocks'][$type][] = $sub_block->toDumpArray();
            }
        }

        return $dump;
    }
}
