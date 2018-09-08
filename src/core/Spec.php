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
     * Returns the type's PHP handling class.
     *
     * @param string $type
     *            the type.
     *
     * @return string
     *            a fully qualified class name.
     */
    public static function getTypeHandlingClass($type)
    {
        return self::getTypeProperty($type, 'class');
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
    public static function getElementHandlingClass($type, $element_id, $format = null)
    {
        // Return the element class, if defined.
        $class = self::getElementPropertyValue($type, $element_id, 'class');
        if ($class !== null) {
            return $class;
        }

        // If format is not passed in, try getting it from the spec.
        if ($format === null) {
            $formats = self::getElementPropertyValue($type, $element_id, 'format');
            if (empty($formats)) {
                throw new ExifEyeException(
                    'No format can be derived for tag: 0x%04X (%s) in ifd: \'%s\'',
                    $element_id,
                    self::getElementName($type, $element_id),
                    $type
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
     * Returns a Pel IFD to use for loading maker notes.
     *
     * @param string $ifd_id
     *            the IFD id.
     *
     * @return int|null
     *            an IFD id.
     */
    public static function getMakerNoteIfdType($make, $model)
    {
        $ifd_id = isset(self::getMap()['makerNotes'][$make]) ? self::getMap()['makerNotes'][$make] : null;
        if ($ifd_id !== null) {
            return self::getMap()['ifds'][$ifd_id];
        }
        return null;
    }
}
