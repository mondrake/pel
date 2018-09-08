<?php

namespace ExifEye\core;

use ExifEye\core\Block\BlockBase;
use ExifEye\core\Block\Tag;
use ExifEye\core\Entry\Core\EntryInterface;
use ExifEye\core\ExifEye;
use ExifEye\core\ExifEyeException;
use ExifEye\core\Format;

/**
 * Class to retrieve IFD and TAG information from YAML specs.
 */
class Spec
{
    /**
     * The compiled PEL specification map.
     *
     * @var array
     */
    private static $map;

    /**
     * The default tag classes.
     *
     * @var array
     */
    private static $defaultTagClasses = [
        Format::ASCII => 'ExifEye\\core\\Entry\\Core\\Ascii',
        Format::BYTE => 'ExifEye\\core\\Entry\\Core\\Byte',
        Format::SHORT => 'ExifEye\\core\\Entry\\Core\\Short',
        Format::LONG => 'ExifEye\\core\\Entry\\Core\\Long',
        Format::RATIONAL => 'ExifEye\\core\\Entry\\Core\\Rational',
        Format::SBYTE => 'ExifEye\\core\\Entry\\Core\\SignedByte',
        Format::SSHORT => 'ExifEye\\core\\Entry\\Core\\SignedShort',
        Format::SLONG => 'ExifEye\\core\\Entry\\Core\\SignedLong',
        Format::SRATIONAL => 'ExifEye\\core\\Entry\\Core\\SignedRational',
        Format::UNDEFINED => 'ExifEye\\core\\Entry\\Core\\Undefined',
    ];

    /**
     * Returns the compiled PEL specification map.
     *
     * In case the map is not yet initialized, defaults to the pre-compiled
     * one.
     *
     * @return array
     *            the PEL specification map.
     */
    private static function getMap()
    {
        if (!isset(self::$map)) {
            self::setMap(__DIR__ . '/../../resources/spec.php');
        }
        return self::$map;
    }

    /**
     * Sets the compiled PEL specification map.
     *
     * @param string $file
     *            the file containing the PEL specification map.
     */
    public static function setMap($file)
    {
        if ($file === null) {
            self::$map = null;
        } else {
            self::$map = include $file;
        }
    }

    /**
     * Returns the types in the specification.
     *
     * @return array
     *            an simple array, with the specification types.
     */
    public static function getTypes()
    {
        return array_keys(self::getMap()['types']);
    }

    /**
     * Returns the property value of a type.
     *
     * @param string $type
     *            the type.
     *
     * @return string|null
     *            the element handling class.
     */
    public static function getTypeProperty($type, $property)
    {
        return isset(self::getMap()['types'][$type][$property]) ? self::getMap()['types'][$type][$property] : null;
    }

    /**
     * Returns the element ids supported by a type.
     *
     * @param string $type
     *            the type.
     *
     * @return array
     *            an simple array, with values the element ids supported by
     *            the type.
     */
    public static function getTypeSupportedElementIds($type)
    {
        return array_keys(self::getMap()['elements'][$type]);
    }

    /**
     * Returns the property value of an element.
     *
     * @param string $type
     *            the type where this element is placed.
     * @param string|int $element_id
     *            the element id.
     * @param string $property
     *            the property.
     *
     * @return string|null
     *            the element property value or null if not found.
     */
    public static function getElementPropertyValue($type, $element_id, $property)
    {
        if (isset(self::getMap()['elements'][$type][$element_id][$property])) {
            return self::getMap()['elements'][$type][$element_id][$property];
        }
        $element_type = self::getElementType($type, $element_id);
        if ($element_type !== null) {
            return isset(self::getMap()['types'][$element_type][$property]) ? self::getMap()['types'][$element_type][$property] : null;
        }
        return null;
    }

    /**
     * Returns the type of an element.
     *
     * @param string $type
     *            the type where this element is placed.
     * @param string|int $element_id
     *            the element id.
     *
     * @return string|null
     *            the element type.
     */
    public static function getElementType($type, $element_id)
    {
        if (isset(self::getMap()['elements'][$type][$element_id]['type'])) {
            return self::getMap()['elements'][$type][$element_id]['type'];
        }
        return null;
    }

    /**
     * Returns the name of an element.
     *
     * @param string $type
     *            the type where this element is placed.
     * @param string|int $element_id
     *            the element id.
     *
     * @return string|null
     *            the element name.
     */
    public static function getElementName($type, $element_id)
    {
        return self::getElementPropertyValue($type, $element_id, 'name');
    }

    /**
     * Returns the id of an element given its name.
     *
     * @param string $type
     *            the type where this element is placed.
     * @param string $element_name
     *            the element id.
     *
     * @return int|string|null
     *            the element id.
     */
    public static function getElementIdByName($type, $element_name)
    {
        return isset(self::getMap()['elementsByName'][$type][$element_name]) ? self::getMap()['elementsByName'][$type][$element_name] : null;
    }

    /**
     * Returns the title of an element.
     *
     * @param string $type
     *            the type where this element is placed.
     * @param string|int $element_id
     *            the element id.
     *
     * @return string|null
     *            the element title.
     */
    public static function getElementTitle($type, $element_id)
    {
        return self::getElementPropertyValue($type, $element_id, 'title');
    }

    /**
     * Returns the handling class of an element.
     *
     * @param string $type
     *            the type where this element is placed.
     * @param string|int $element_id
     *            the element id.
     *
     * @return string|null
     *            the element handling class.
     */
    public static function getElementHandlingClass($type, $element_id)
    {
        return self::getElementPropertyValue($type, $element_id, 'class');
    }

    /**
     * Returns the text of an element.
     *
     * @param string $type
     *            the element type.
     * @param string $id
     *            the element id.
     * @param mixed $value
     *            the element value.
     * @param array $options
     *            (Optional) an array of options to format the value.
     *
     * @return string|null
     *            the element text, or NULL if not applicable.
     */
    public static function getElementText($type, $id, $value, $options = [])
    {
        if (isset(self::getMap()['elements'][$type][$id]['text']['mapping']) && is_scalar($value)) {
            $map = self::getMap()['elements'][$type][$id]['text']['mapping'];
            // If the code to be mapped is a non-int, change to string.
            $id = is_int($value) ? $value : (string) $value;
            return isset($map[$id]) ? ExifEye::tra($map[$id]) : null;
        }
        return null;
    }

    /**
     * Returns the IFD types in the specification.
     *
     * @return array
     *            an associative array, with keys the IFD identifiers, and
     *            values the IFD types.
     */
    public static function xxgetIfdTypes()
    {
        return self::getMap()['ifds'];
    }

    /**
     * Returns the IFD id given its type.
     *
     * @param string $ifd_type
     *            the IFD type.
     *
     * @return int|null
     *            the IFD id.
     */
    public static function xxgetIfdIdByType($ifd_type)
    {
        return isset(self::getMap()['ifdsByType'][$ifd_type]) ? self::getMap()['ifdsByType'][$ifd_type] : null;
    }

    /**
     * Returns the IFD class.
     *
     * @param int $ifd_id
     *            the IFD id.
     *
     * @return string|null
     *            the IFD class.
     */
    public static function xxgetIfdClass($block_name)
    {
        $xx_block_id = self::getIfdIdByType($block_name);

        return isset(self::getMap()['ifdClasses'][$xx_block_id]) ? self::getMap()['ifdClasses'][$xx_block_id] : null;
    }

    /**
     * Returns a Pel IFD to use for loading maker notes.
     *
     * @param string $ifd_id
     *            the IFD id.
     *
     * @return int|null
     *            an IFD id.
     */
    public static function xxgetMakerNoteIfdName($make, $model)
    {
        $ifd_id = isset(self::getMap()['makerNotes'][$make]) ? self::getMap()['makerNotes'][$make] : null;
        if ($ifd_id !== null) {
            return self::getMap()['ifds'][$ifd_id];
        }
        return null;
    }

    /**
     * Determines if the TAG is an IFD pointer.
     *
     * @param int $ifd_id
     *            the IFD id.
     * @param int $tag_id
     *            the TAG id.
     *
     * @return bool
     *            TRUE or FALSE.
     */
    public static function xxisTagAnIfdPointer(BlockBase $parent_block, $tag_id)
    {
        $xx_parent_block_id = self::getIfdIdByType($parent_block->getAttribute('name'));

        return isset(self::getMap()['tags'][$xx_parent_block_id][$tag_id]['ifd']);
    }

    /**
     * Returns the IFD id the TAG points to.
     *
     * @param int $xx_parent_block_id
     *            the IFD id.
     * @param int $tag_id
     *            the TAG id.
     *
     * @return int|null
     *            the IFD id, or null if the TAG is not an IFD pointer.
     */
    public static function xxgetIfdNameFromTag(BlockBase $parent_block, $tag_id)
    {
        $xx_parent_block_id = self::getIfdIdByType($parent_block->getAttribute('name'));

        $ifd_id = isset(self::getMap()['tags'][$xx_parent_block_id][$tag_id]['ifd']) ? self::getMap()['tags'][$xx_parent_block_id][$tag_id]['ifd'] : null;

        if ($ifd_id !== null) {
            return self::getMap()['ifds'][$ifd_id];
        }
        return null;
    }

    /**
     * Returns the IFD post-load callbacks.
     *
     * @param int $xx_parent_block_id
     *            the IFD id.
     *
     * @return array
     *            the post-load callbacks.
     */
    public static function xxgetIfdPostLoadCallbacks(BlockBase $block)
    {
        $xx_block_id = self::getIfdIdByType($block->getAttribute('name'));

        return self::getMap()['ifdPostLoadCallbacks'][$xx_block_id];
    }

    /**
     * Returns the TAG name.
     *
     * @param int $ifd_id
     *            the IFD id.
     * @param int $tag_id
     *            the TAG id.
     *
     * @return string|null
     *            the TAG name.
     */
    public static function xxgetTagName(BlockBase $parent_block, $tag_id)
    {
        $xx_parent_block_id = self::getIfdIdByType($parent_block->getAttribute('name'));

        return isset(self::getMap()['tags'][$xx_parent_block_id][$tag_id]['name']) ? self::getMap()['tags'][$xx_parent_block_id][$tag_id]['name'] : null;
    }

    /**
     * Returns the TAG id given its name.
     *
     * @param int $ifd_id
     *            the IFD id.
     * @param string $tag_name
     *            the TAG name.
     *
     * @return int|null
     *            the TAG id.
     */
    public static function xxgetTagIdByName(BlockBase $parent_block, $tag_name)
    {
        $xx_parent_block_id = self::getIfdIdByType($parent_block->getAttribute('name'));

        return isset(self::getMap()['tagsByName'][$xx_parent_block_id][$tag_name]) ? self::getMap()['tagsByName'][$xx_parent_block_id][$tag_name] : null;
    }

    /**
     * Returns the TAG format.
     *
     * @param int $ifd_id
     *            the IFD id.
     * @param int $tag_id
     *            the TAG id.
     *
     * @return array
     *            the array of formats supported by the TAG.
     */
    public static function xxgetTagFormat(BlockBase $parent_block, $tag_id)
    {
        $xx_parent_block_id = self::getIfdIdByType($parent_block->getAttribute('name'));

        $format = isset(self::getMap()['tags'][$xx_parent_block_id][$tag_id]['format']) ? self::getMap()['tags'][$xx_parent_block_id][$tag_id]['format'] : [];
        return empty($format) ? null : $format;
    }

    /**
     * Returns the TAG components.
     *
     * @param int $ifd_id
     *            the IFD id.
     * @param int $tag_id
     *            the TAG id.
     *
     * @return int|null
     *            the TAG count of data components.
     */
    public static function xxgetTagComponents(BlockBase $parent_block, $tag_id)
    {
        $xx_parent_block_id = self::getIfdIdByType($parent_block->getAttribute('name'));

        return isset(self::getMap()['tags'][$xx_parent_block_id][$tag_id]['components']) ? self::getMap()['tags'][$xx_parent_block_id][$tag_id]['components'] : null;
    }

    /**
     * Returns whether the TAG should be skipped.
     *
     * @param int $ifd_id
     *            the IFD id.
     * @param int $tag_id
     *            the TAG id.
     *
     * @return bool
     */
    public static function xxgetTagSkip(BlockBase $parent_block, $tag_id)
    {
        $xx_parent_block_id = self::getIfdIdByType($parent_block->getAttribute('name'));

        return isset(self::getMap()['tags'][$xx_parent_block_id][$tag_id]['skip']) ? self::getMap()['tags'][$xx_parent_block_id][$tag_id]['skip'] : false;
    }

    /**
     * Returns the TAG class.
     *
     * @param int $ifd_id
     *            the IFD id.
     * @param int $tag_id
     *            the TAG id.
     *
     * @return string
     *            the TAG class.
     */
    public static function xxgetEntryClass(BlockBase $parent_block, $tag_id, $format = null)
    {
        $xx_parent_block_id = self::getIfdIdByType($parent_block->getAttribute('name'));

        // Return the specific tag class, if defined.
        if (isset(self::getMap()['tags'][$xx_parent_block_id][$tag_id]['class'])) {
            return self::getMap()['tags'][$xx_parent_block_id][$tag_id]['class'];
        }

        // If format is not passed in, try getting it from the spec.
        if ($format === null) {
            $formats = self::getTagFormat($parent_block, $tag_id);
            if (empty($formats)) {
                throw new ExifEyeException(
                    'No format can be derived for tag: 0x%04X (%s) in ifd: \'%s\'',
                    $tag_id,
                    self::getTagName($parent_block, $tag_id),
                    $parent_block->getAttribute('name')
                );
            }
            $format = $formats[0];
        }

        if (!isset(self::$defaultTagClasses[$format])) {
            throw new ExifEyeException('Unsupported format: %s', Format::getName($format));
        }
        return self::$defaultTagClasses[$format];
    }

    /**
     * Returns the TAG title.
     *
     * @param int $ifd_id
     *            the IFD id.
     * @param int $tag_id
     *            the TAG id.
     *
     * @return string|null
     *            the TAG title.
     */
    public static function xxgetTagTitle(BlockBase $parent_block, $tag_id)
    {
        $xx_parent_block_id = self::getIfdIdByType($parent_block->getAttribute('name'));

        return isset(self::getMap()['tags'][$xx_parent_block_id][$tag_id]['title']) ? self::getMap()['tags'][$xx_parent_block_id][$tag_id]['title'] : null;
    }
}
