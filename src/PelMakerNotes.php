<?php
/*
 * PEL: PHP Exif Library.
 * A library with support for reading and
 * writing all Exif headers in JPEG and TIFF images using PHP.
 *
 * Copyright (C) 2004, 2005 Martin Geisler.
 * Copyright (C) 2017 Johannes Weberhofer.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program in the file COPYING; if not, write to the
 * Free Software Foundation, Inc., 51 Franklin St, Fifth Floor,
 * Boston, MA 02110-1301 USA
 */
namespace lsolesen\pel;

/**
 * Namespace for functions operating on Exif formats.
 *
 * This class defines the constants that are to be used whenever one
 * has to refer to the format of an Exif tag. They will be
 * collectively denoted by the pseudo-type PelFormat throughout the
 * documentation.
 *
 * All the methods defined here are static, and they all operate on a
 * single argument which should be one of the class constants.
 *
 * @author Vinzenz Rosenkranz <vinzenz.rosenkranz@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public
 *          License (GPL)
 * @package
 *
 */
abstract class PelMakerNotes
{
    protected $type;
    protected $parent;
    protected $data;
    protected $components;
    protected $offset;

    /**
     * Load a single value which didn't match any special {@link PelTag}.
     *
     * This method will add a single value given by the {@link PelDataWindow} and it's offset ($offset) and element counter ($i).
     *
     * Please note that the data you pass to this method should come
     * from an image, that is, it should be raw bytes. If instead you
     * want to create an entry for holding, say, an short integer, then
     * create a {@link PelEntryShort} object directly and load the data
     * into it.
     *
     * @param int $type
     *            the type of the ifd
     *
     * @param PelDataWindow $data
     *            the data window that will provide the data.
     *
     * @param integer $offset
     *            the offset within the window where the directory will
     *            be found.
     *
     * @param int $size
     *            the size in bytes of the maker notes section
     *
     * @param int $i
     *            the element's position in the {@link PelDataWindow} $data.
     *
     * @param int $format
     *            the format {@link PelFormat} of the entry.
     */
    public static function loadSingleMakerNotesValue($ifd, $type, $data, $offset, $size, $i, $format)
    {
//Pel::debug('--tag: %d %d %d', $i, $offset, $format);
        $elemSize = PelFormat::getSize($format);
        if ($size > 0) {
            $subdata = $data->getClone($offset + $i * $elemSize, $elemSize);
        } else {
            $subdata = new PelDataWindow();
        }

        try {
            $class = PelSpec::getTagClass($ifd->getType(), $i + 1, $format);
            $arguments = call_user_func($class . '::getInstanceArgumentsFromData', $ifd->getType(), $i + 1, $format, 1, $subdata, null);
            $entry = call_user_func($class . '::createInstance', $ifd->getType(), $i + 1, $arguments);
            $ifd->addEntry($entry);
        } catch (PelException $e) {
            // Throw the exception when running in strict mode, store otherwise.
            Pel::maybeThrow($e);
        }
    }

    public function __construct($parent, $data, $size, $offset)
    {
        $this->parent = $parent;
        $this->data = $data;
        $this->size = $size;
        $this->offset = $offset;
        $this->components = 0;
        Pel::debug('Creating MakerNotes with %d bytes at offset %d.', $size, $offset);
    }

    abstract public function load();

    public static function tagToIfd(PelDataWindow $d, PelIfd $ifd)
    {
        // Get the Exif subIfd if existing.
        if (!$exif_ifd = $ifd->getSubIfd(PelSpec::getIfdIdByType('Exif'))) {
            return;
        }

        // Get MakerNotes from Exif IFD.
        if (!$maker_note = $exif_ifd->getEntry(PelSpec::getTagIdByName($exif_ifd->getType(), 'MakerNote'))) {
            return;
        }

        // Get Make tag from IFD0.
        if (!$make = $ifd->getEntry(PelSpec::getTagIdByName($ifd->getType(), 'Make'))) {
            return;
        }

        // Get Model tag from IFD0.
        $model = $ifd->getEntry(PelSpec::getTagIdByName($ifd->getType(), 'Model'));

        // TTTT
        if ($make->getValue() !== 'Canon') {
          return;
        }
        $ifd_id = PelSpec::getIfdIdByType('Canon Maker Notes');
        $x = new PelIfd($ifd_id);
        $x->load($d, $maker_note->getDataOffset());
        $exif_ifd->addSubIfd($x);
        $exif_ifd->offsetUnset(PelSpec::getTagIdByName($exif_ifd->getType(), 'MakerNote'));
    }
}
