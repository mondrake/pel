<?php

/*  PEL: PHP EXIF Library.  A library with support for reading and
 *  writing all EXIF headers in JPEG and TIFF images using PHP.
 *
 *  Copyright (C) 2004  Martin Geisler <gimpster@users.sourceforge.net>
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program in the file COPYING; if not, write to the
 *  Free Software Foundation, Inc., 59 Temple Place, Suite 330,
 *  Boston, MA 02111-1307 USA
 */

/* $Id$ */


class sony_dsc_v1 extends UnitTestCase {

  function __construct() {
    require_once('../PelJpeg.php');
    parent::__construct('PEL sony-dsc-v1.jpg Tests');
  }

  function testRead() {
    $jpeg = new PelJpeg();
    $jpeg->loadFile(dirname(__FILE__) . '/sony-dsc-v1.jpg');

    $app1 = $jpeg->getSection(PelJpegMarker::APP1);
    $this->assertIsA($app1, 'PelExif');
    
    $tiff = $app1->getTiff();
    $this->assertIsA($tiff, 'PelTiff');
    
    /* The first IFD. */
    $ifd0 = $tiff->getIfd();
    $this->assertIsA($ifd0, 'PelIfd');
    
    /* Start of IDF $ifd0. */
    $this->assertEqual(count($ifd0->getEntries()), 10);
    
    $entry = $ifd0->getEntry(270); // ImageDescription
    $this->assertIsA($entry, 'PelEntryAscii');
    $this->assertEqual($entry->getValue(), '                               ');
    $this->assertEqual($entry->getText(), '                               ');
    
    $entry = $ifd0->getEntry(271); // Make
    $this->assertIsA($entry, 'PelEntryAscii');
    $this->assertEqual($entry->getValue(), 'SONY');
    $this->assertEqual($entry->getText(), 'SONY');
    
    $entry = $ifd0->getEntry(272); // Model
    $this->assertIsA($entry, 'PelEntryAscii');
    $this->assertEqual($entry->getValue(), 'DSC-V1');
    $this->assertEqual($entry->getText(), 'DSC-V1');
    
    $entry = $ifd0->getEntry(274); // Orientation
    $this->assertIsA($entry, 'PelEntryShort');
    $this->assertEqual($entry->getValue(), 6);
    $this->assertEqual($entry->getText(), 'right - top');
    
    $entry = $ifd0->getEntry(282); // XResolution
    $this->assertIsA($entry, 'PelEntryRational');
    $this->assertEqual($entry->getValue(), array (
  0 => 72,
  1 => 1,
));
    $this->assertEqual($entry->getText(), '72/1');
    
    $entry = $ifd0->getEntry(283); // YResolution
    $this->assertIsA($entry, 'PelEntryRational');
    $this->assertEqual($entry->getValue(), array (
  0 => 72,
  1 => 1,
));
    $this->assertEqual($entry->getText(), '72/1');
    
    $entry = $ifd0->getEntry(296); // ResolutionUnit
    $this->assertIsA($entry, 'PelEntryShort');
    $this->assertEqual($entry->getValue(), 2);
    $this->assertEqual($entry->getText(), 'Inch');
    
    $entry = $ifd0->getEntry(306); // DateTime
    $this->assertIsA($entry, 'PelEntryTime');
    $this->assertEqual($entry->getValue(), 1089482993);
    $this->assertEqual($entry->getText(), '2004:07:10 18:09:53');
    
    $entry = $ifd0->getEntry(531); // YCbCrPositioning
    $this->assertIsA($entry, 'PelEntryShort');
    $this->assertEqual($entry->getValue(), 2);
    $this->assertEqual($entry->getText(), 'co-sited');
    
    $entry = $ifd0->getEntry(50341); // Unknown: 0xC4A5
    $this->assertIsA($entry, 'PelEntryUndefined');
    $this->assertEqual($entry->getValue(), 'PrintIM 0250           ');
    $this->assertEqual($entry->getText(), '(undefined)');
    
    /* Sub IFDs of $ifd0. */
    $this->assertEqual(count($ifd0->getSubIfds()), 1);
    $ifd0_0 = $ifd0->getSubIfd(34665); // ExifIFDPointer
    $this->assertIsA($ifd0_0, 'PelIfd');
    
    /* Start of IDF $ifd0_0. */
    $this->assertEqual(count($ifd0_0->getEntries()), 26);
    
    $entry = $ifd0_0->getEntry(33434); // ExposureTime
    $this->assertIsA($entry, 'PelEntryRational');
    $this->assertEqual($entry->getValue(), array (
  0 => 10,
  1 => 600,
));
    $this->assertEqual($entry->getText(), '1/60 sec.');
    
    $entry = $ifd0_0->getEntry(33437); // FNumber
    $this->assertIsA($entry, 'PelEntryRational');
    $this->assertEqual($entry->getValue(), array (
  0 => 32,
  1 => 10,
));
    $this->assertEqual($entry->getText(), 'f/3.2');
    
    $entry = $ifd0_0->getEntry(34850); // ExposureProgram
    $this->assertIsA($entry, 'PelEntryShort');
    $this->assertEqual($entry->getValue(), 2);
    $this->assertEqual($entry->getText(), 'Normal program');
    
    $entry = $ifd0_0->getEntry(34855); // ISOSpeedRatings
    $this->assertIsA($entry, 'PelEntryShort');
    $this->assertEqual($entry->getValue(), 100);
    $this->assertEqual($entry->getText(), '100');
    
    $entry = $ifd0_0->getEntry(36864); // ExifVersion
    $this->assertIsA($entry, 'PelEntryVersion');
    $this->assertEqual($entry->getValue(), 2.2);
    $this->assertEqual($entry->getText(), 'Exif Version 2.2');
    
    $entry = $ifd0_0->getEntry(36867); // DateTimeOriginal
    $this->assertIsA($entry, 'PelEntryTime');
    $this->assertEqual($entry->getValue(), 1089482993);
    $this->assertEqual($entry->getText(), '2004:07:10 18:09:53');
    
    $entry = $ifd0_0->getEntry(36868); // DateTimeDigitized
    $this->assertIsA($entry, 'PelEntryTime');
    $this->assertEqual($entry->getValue(), 1089482993);
    $this->assertEqual($entry->getText(), '2004:07:10 18:09:53');
    
    $entry = $ifd0_0->getEntry(37121); // ComponentsConfiguration
    $this->assertIsA($entry, 'PelEntryUndefined');
    $this->assertEqual($entry->getValue(), ' ');
    $this->assertEqual($entry->getText(), 'Y Cb Cr -');
    
    $entry = $ifd0_0->getEntry(37122); // CompressedBitsPerPixel
    $this->assertIsA($entry, 'PelEntryRational');
    $this->assertEqual($entry->getValue(), array (
  0 => 2,
  1 => 1,
));
    $this->assertEqual($entry->getText(), '2/1');
    
    $entry = $ifd0_0->getEntry(37380); // ExposureBiasValue
    $this->assertIsA($entry, 'PelEntrySRational');
    $this->assertEqual($entry->getValue(), array (
  0 => 7,
  1 => 10,
));
    $this->assertEqual($entry->getText(), '+0.7');
    
    $entry = $ifd0_0->getEntry(37381); // MaxApertureValue
    $this->assertIsA($entry, 'PelEntryRational');
    $this->assertEqual($entry->getValue(), array (
  0 => 48,
  1 => 16,
));
    $this->assertEqual($entry->getText(), '48/16');
    
    $entry = $ifd0_0->getEntry(37383); // MeteringMode
    $this->assertIsA($entry, 'PelEntryShort');
    $this->assertEqual($entry->getValue(), 2);
    $this->assertEqual($entry->getText(), 'Center-Weighted Average');
    
    $entry = $ifd0_0->getEntry(37384); // LightSource
    $this->assertIsA($entry, 'PelEntryShort');
    $this->assertEqual($entry->getValue(), 0);
    $this->assertEqual($entry->getText(), 'Unknown');
    
    $entry = $ifd0_0->getEntry(37385); // Flash
    $this->assertIsA($entry, 'PelEntryShort');
    $this->assertEqual($entry->getValue(), 31);
    $this->assertEqual($entry->getText(), 'Flash fired, auto mode, return light detected.');
    
    $entry = $ifd0_0->getEntry(37386); // FocalLength
    $this->assertIsA($entry, 'PelEntryRational');
    $this->assertEqual($entry->getValue(), array (
  0 => 139,
  1 => 10,
));
    $this->assertEqual($entry->getText(), '13.9 mm');
    
    $entry = $ifd0_0->getEntry(37500); // MakerNote
    $this->assertIsA($entry, 'PelEntryUndefined');
    $this->assertEqual($entry->getValue(), 'SONY DSC     � �   @  � �   �  � �   �  �    d  � x   ~  � �   �  � �   �  � �   �  ������������������������������������������������           n~� �, �            �A�     	� `�^��J�    J0 �J0J� p  p  � ^ �  V   c  ��  Ґ�� p  � �^ �o  @P                    � �� t�� �u=   �            �  � �tד�g��"0c    ק�!                                                                                                                     � } (� ^  � ����ד@   ��sF � � # # G �  U�$1��ys�y�y��[��}����  i        �  �  G�  j  # ^   V ��  ��E�8T i$�c���#(G�E�!�IX/ �F�YZ�� �R�nQ�������������������������������������������������������  �ܼ_  ��Q � � � �             }     {                             �Q                   �� �        ��  ��  ͕Q                   } @    �+�G^���� ����^+Vi��ت�`j�SV�V���] ��X^�����W},�$]�^��؏�O}n0(�K}���}	�&}Ĉ$��@�}�}�^F@�pD��                                    } ^      @   � �      @          }   �  }       �                                                        @C��^����0� 9Eζ1��0$0w0��e �}���C�il���E(�� ܾ[�>��l5    J"�@�^K����A^�V݊Ci�i[p�� ����}WV�i���i�V����i#i�����    @C}@}T�T��p�^��V��p�p��V �0c�ؾ[���!ז׆�"�!׶���t��׳ק                                                                                                                                                                                                            ');
    $this->assertEqual($entry->getText(), '1504 bytes unknown MakerNote data');
    
    $entry = $ifd0_0->getEntry(40960); // FlashPixVersion
    $this->assertIsA($entry, 'PelEntryVersion');
    $this->assertEqual($entry->getValue(), 1);
    $this->assertEqual($entry->getText(), 'FlashPix Version 1.0');
    
    $entry = $ifd0_0->getEntry(40961); // ColorSpace
    $this->assertIsA($entry, 'PelEntryShort');
    $this->assertEqual($entry->getValue(), 1);
    $this->assertEqual($entry->getText(), 'sRGB');
    
    $entry = $ifd0_0->getEntry(40962); // PixelXDimension
    $this->assertIsA($entry, 'PelEntryLong');
    $this->assertEqual($entry->getValue(), 640);
    $this->assertEqual($entry->getText(), '640');
    
    $entry = $ifd0_0->getEntry(40963); // PixelYDimension
    $this->assertIsA($entry, 'PelEntryLong');
    $this->assertEqual($entry->getValue(), 480);
    $this->assertEqual($entry->getText(), '480');
    
    $entry = $ifd0_0->getEntry(41728); // FileSource
    $this->assertIsA($entry, 'PelEntryUndefined');
    $this->assertEqual($entry->getValue(), '');
    $this->assertEqual($entry->getText(), 'DSC');
    
    $entry = $ifd0_0->getEntry(41729); // SceneType
    $this->assertIsA($entry, 'PelEntryUndefined');
    $this->assertEqual($entry->getValue(), '');
    $this->assertEqual($entry->getText(), 'Directly photographed');
    
    $entry = $ifd0_0->getEntry(41985); // CustomRendered
    $this->assertIsA($entry, 'PelEntryShort');
    $this->assertEqual($entry->getValue(), 0);
    $this->assertEqual($entry->getText(), 'Normal process');
    
    $entry = $ifd0_0->getEntry(41986); // ExposureMode
    $this->assertIsA($entry, 'PelEntryShort');
    $this->assertEqual($entry->getValue(), 1);
    $this->assertEqual($entry->getText(), 'Manual exposure');
    
    $entry = $ifd0_0->getEntry(41987); // WhiteBalance
    $this->assertIsA($entry, 'PelEntryShort');
    $this->assertEqual($entry->getValue(), 0);
    $this->assertEqual($entry->getText(), 'Auto white balance');
    
    $entry = $ifd0_0->getEntry(41990); // SceneCaptureType
    $this->assertIsA($entry, 'PelEntryShort');
    $this->assertEqual($entry->getValue(), 0);
    $this->assertEqual($entry->getText(), 'Standard');
    
    /* Sub IFDs of $ifd0_0. */
    $this->assertEqual(count($ifd0_0->getSubIfds()), 1);
    $ifd0_0_0 = $ifd0_0->getSubIfd(40965); // InteroperabilityIFDPointer
    $this->assertIsA($ifd0_0_0, 'PelIfd');
    
    /* Start of IDF $ifd0_0_0. */
    $this->assertEqual(count($ifd0_0_0->getEntries()), 2);
    
    $entry = $ifd0_0_0->getEntry(1); // InteroperabilityIndex
    $this->assertIsA($entry, 'PelEntryAscii');
    $this->assertEqual($entry->getValue(), 'R98');
    $this->assertEqual($entry->getText(), 'R98');
    
    $entry = $ifd0_0_0->getEntry(2); // InteroperabilityVersion
    $this->assertIsA($entry, 'PelEntryVersion');
    $this->assertEqual($entry->getValue(), 1);
    $this->assertEqual($entry->getText(), 'Interoperability Version 1.0');
    
    /* Sub IFDs of $ifd0_0_0. */
    $this->assertEqual(count($ifd0_0_0->getSubIfds()), 0);
    
    $this->assertEqual($ifd0_0_0->getThumbnailData(), '');
    
    /* Next IFD. */
    $ifd0_0_1 = $ifd0_0_0->getNextIfd();
    $this->assertNull($ifd0_0_1);
    /* End of IFD $ifd0_0_0. */
    
    $this->assertEqual($ifd0_0->getThumbnailData(), '');
    
    /* Next IFD. */
    $ifd0_1 = $ifd0_0->getNextIfd();
    $this->assertNull($ifd0_1);
    /* End of IFD $ifd0_0. */
    
    $this->assertEqual($ifd0->getThumbnailData(), '');
    
    /* Next IFD. */
    $ifd1 = $ifd0->getNextIfd();
    $this->assertIsA($ifd1, 'PelIfd');
    /* End of IFD $ifd0. */
    
    /* Start of IDF $ifd1. */
    $this->assertEqual(count($ifd1->getEntries()), 8);
    
    $entry = $ifd1->getEntry(259); // Compression
    $this->assertIsA($entry, 'PelEntryShort');
    $this->assertEqual($entry->getValue(), 6);
    $this->assertEqual($entry->getText(), 'JPEG compression');
    
    $entry = $ifd1->getEntry(271); // Make
    $this->assertIsA($entry, 'PelEntryAscii');
    $this->assertEqual($entry->getValue(), 'SONY');
    $this->assertEqual($entry->getText(), 'SONY');
    
    $entry = $ifd1->getEntry(272); // Model
    $this->assertIsA($entry, 'PelEntryAscii');
    $this->assertEqual($entry->getValue(), 'DSC-V1');
    $this->assertEqual($entry->getText(), 'DSC-V1');
    
    $entry = $ifd1->getEntry(274); // Orientation
    $this->assertIsA($entry, 'PelEntryShort');
    $this->assertEqual($entry->getValue(), 6);
    $this->assertEqual($entry->getText(), 'right - top');
    
    $entry = $ifd1->getEntry(282); // XResolution
    $this->assertIsA($entry, 'PelEntryRational');
    $this->assertEqual($entry->getValue(), array (
  0 => 72,
  1 => 1,
));
    $this->assertEqual($entry->getText(), '72/1');
    
    $entry = $ifd1->getEntry(283); // YResolution
    $this->assertIsA($entry, 'PelEntryRational');
    $this->assertEqual($entry->getValue(), array (
  0 => 72,
  1 => 1,
));
    $this->assertEqual($entry->getText(), '72/1');
    
    $entry = $ifd1->getEntry(296); // ResolutionUnit
    $this->assertIsA($entry, 'PelEntryShort');
    $this->assertEqual($entry->getValue(), 2);
    $this->assertEqual($entry->getText(), 'Inch');
    
    $entry = $ifd1->getEntry(306); // DateTime
    $this->assertIsA($entry, 'PelEntryTime');
    $this->assertEqual($entry->getValue(), 1089482993);
    $this->assertEqual($entry->getText(), '2004:07:10 18:09:53');
    
    /* Sub IFDs of $ifd1. */
    $this->assertEqual(count($ifd1->getSubIfds()), 0);
    
    $thumb_data = file_get_contents(dirname(__FILE__) .
                                    '/sony-dsc-v1-thumb.jpg');
    $this->assertEqual($ifd1->getThumbnailData(), $thumb_data);
    
    /* Next IFD. */
    $ifd2 = $ifd1->getNextIfd();
    $this->assertNull($ifd2);
    /* End of IFD $ifd1. */
    
  }
}

?>
