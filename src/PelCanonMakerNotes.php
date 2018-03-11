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
 * @author Thanks to Benedikt Rosenkranz <beluro@web.de>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public
 *          License (GPL)
 * @package
 *
 */
class PelCanonMakerNotes
{
    private static $undefinedCameraSettingsTags = [
        0x0006,
        0x0008,
        0x0015,
        0x001e,
        0x001f,
        0x0026,
        0x002b,
        0x002c,
        0x002d,
        0x002f,
        0x0030,
        0x0031
    ];

    private static $undefinedShotInfoTags = [
        0x0001,
        0x0006,
        0x000a,
        0x000b,
        0x000c,
        0x000d,
        0x0011,
        0x0012,
        0x0014,
        0x0018,
        0x0019,
        0x001d,
        0x001e,
        0x001f,
        0x0020,
        0x0021,
        0x0022
    ];

    private static $undefinedPanoramaTags = [
        0x0001,
        0x0003,
        0x0004
    ];

    private static $undefinedPicInfoTags = [
        0x0001,
        0x0006,
        0x0007,
        0x0008,
        0x0009,
        0x000a,
        0x000b,
        0x000c,
        0x000d,
        0x000e,
        0x000f,
        0x0010,
        0x0011,
        0x0012,
        0x0013,
        0x0014,
        0x0015,
        0x0017,
        0x0018,
        0x0019,
        0x001b,
        0x001c
    ];

    private static $undefinedFileInfoTags = [
        0x0002,
        0x000a,
        0x000b,
        0x0011,
        0x0012,
        0x0016,
        0x0017,
        0x0018,
        0x001a,
        0x001b,
        0x001c,
        0x001d,
        0x001e,
        0x001f,
        0x0020
    ];

    public static function parseCameraSettings($type, $size, $_ifd, $data, $offset, $components)
    {
        for ($i=0; $i<$components; $i++) {
            // check if tag is defined
            if (in_array($i+1, static::$undefinedCameraSettingsTags)) {
                continue;
            }
            PelMakerNotes::loadSingleMakerNotesValue($_ifd, $type, $data, $offset, $size, $i, PelFormat::SSHORT);
        }
    }

    public static function parseShotInfo($type, $size, $_ifd, $data, $offset, $components)
    {
        for ($i=0; $i<$components; $i++) {
            // check if tag is defined
            if (in_array($i+1, static::$undefinedShotInfoTags)) {
                continue;
            }
            PelMakerNotes::loadSingleMakerNotesValue($_ifd, $type, $data, $offset, $size, $i, PelFormat::SHORT);
        }
    }

    public static function parsePanorama($type, $size, $_ifd, $data, $offset, $components)
    {
        for ($i=0; $i<$components; $i++) {
            // check if tag is defined
            if (in_array($i+1, static::$undefinedPanoramaTags)) {
                continue;
            }
            PelMakerNotes::loadSingleMakerNotesValue($_ifd, $type, $data, $offset, $size, $i, PelFormat::SHORT);
        }
    }

    public static function parseFileInfo($type, $size, $_ifd, $data, $offset, $components)
    {
        for ($i=0; $i<$components; $i++) {
            // check if tag is defined
            if (in_array($i+1, static::$undefinedFileInfoTags)) {
                continue;
            }
            $format = PelFormat::SSHORT;
            if ($i + 1 == PelSpec::getTagIdByName($type, 'FileNumber')) {
                $format = PelFormat::LONG;
            }
            PelMakerNotes::loadSingleMakerNotesValue($_ifd, $type, $data, $offset, $size, $i, $format);
        }
    }
}
