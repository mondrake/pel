<?php

namespace ExifEye\core\Block;

use ExifEye\core\DataWindow;
use ExifEye\core\ExifEye;
use ExifEye\core\Image;
use ExifEye\core\Utility\ConvertBytes;
use ExifEye\core\Spec;

/**
 * Class for handling TIFF data.
 */
class Tiff extends BlockBase
{
    /**
     * TIFF header.
     *
     * This must follow after the two bytes indicating the byte order.
     */
    const TIFF_HEADER = 0x002A;

    /**
     * {@inheritdoc}
     */
    protected $type = 'tiff';

    /**
     * Constructs a Block for holding a TIFF image.
     */
    public function __construct(BlockBase $parent = null)
    {
        parent::__construct($parent);
    }

    /**
     * {@inheritdoc}
     */
    public function loadFromData(DataWindow $data_window, $offset = 0, array $options = [])
    {
        $this->debug('Parsing {size} bytes of TIFF data...', ['size' => $data_window->getSize()]);

        // There must be at least 8 bytes available: 2 bytes for the byte
        // order, 2 bytes for the TIFF header, and 4 bytes for the offset to
        // the first IFD.
        if ($data_window->getSize() < 8) {
            $this->error('Expected at least 8 bytes of TIFF data, found only {size} bytes', [
                'size' => $data_window->getSize(),
            ]);
            $this->valid = false;
            return false;
        }

        // Byte order.
        if ($data_window->strcmp(0, 'II')) {
            $this->debug('Found Intel byte order');
            $data_window->setByteOrder(ConvertBytes::LITTLE_ENDIAN);
        } elseif ($data_window->strcmp(0, 'MM')) {
            $this->debug('Found Motorola byte order');
            $data_window->setByteOrder(ConvertBytes::BIG_ENDIAN);
        } else {
            $this->error('Unknown byte order found in TIFF data: 0x{byte0} 0x{byte1}', [
                'byte0' => dechex($data_window->getByte(0)),
                'byte1' => dechex($data_window->getByte(1)),
            ]);
            $this->valid = false;
            return false;
        }

        // Verify the TIFF header.
        if ($data_window->getShort(2) != self::TIFF_HEADER) {
            $this->error('Missing TIFF magic value');
            $this->valid = false;
            return false;
        }

        // IFD0.
        $offset = $data_window->getLong(4);
        $this->debug('First IFD at offset {offset}.', ['offset' => $offset]);

        if ($offset > 0) {
            // Parse IFD0, this will automatically parse any sub IFDs.
            $ifd0 = new Ifd($this, 'IFD0');
            $next_offset = $ifd0->loadFromData($data_window, $offset);
        }

        // Next IFD. xx @todo iterate on next_offset
        if ($next_offset > 0) {
            // Sanity check: we need 6 bytes.
            if ($next_offset > $data_window->getSize() - 6) {
                $this->error('Bogus offset to next IFD: {offset} > {size}!', [
                    'offset' => $next_offset,
                    'size' => $data_window->getSize() - 6,
                ]);
            } else {
/*                if (Spec::getIfdType($this->getAttribute('id')) === 'IFD1') {
                    // IFD1 shouldn't link further...
                    $this->error('IFD1 links to another IFD!');
                }*/
                $ifd1 = new Ifd($this, 'IFD1');
                $next_offset = $ifd1->loadFromData($data_window, $next_offset);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toBytes($order = ConvertBytes::LITTLE_ENDIAN)
    {
        if ($order == ConvertBytes::LITTLE_ENDIAN) {
            $bytes = 'II';
        } else {
            $bytes = 'MM';
        }

        // TIFF magic number --- fixed value.
        $bytes .= ConvertBytes::fromShort(self::TIFF_HEADER, $order);

        $ifd0 = $this->getElement("ifd[@name='IFD0']");
        if ($ifd0) {
            // IFD0 offset. We will always start IFD0 at an offset of 8
            // bytes (2 bytes for byte order, another 2 bytes for the TIFF
            // header, and 4 bytes for the IFD0 offset make 8 bytes together).
            $bytes .= ConvertBytes::fromLong(8, $order);

            // The argument specifies the offset of this IFD. The IFD will
            // use this to calculate offsets from the entries to their data,
            // all those offsets are absolute offsets counted from the
            // beginning of the data.
            $ifd0_bytes = $ifd0->toBytes($order, 8);

            // Deal with IFD1.
            $ifd1 = $this->getElement("ifd[@name='IFD1']");
            if (!$ifd1) {
                // No IFD1, link to next IFD is 0.
                $bytes .= $ifd0_bytes['ifd_area'] . ConvertBytes::fromLong(0, $order) . $ifd0_bytes['data_area'];
            } else {
                $ifd0_length = strlen($ifd0_bytes['ifd_area']) + 4 + strlen($ifd0_bytes['data_area']);
                $ifd1_offset = 8 + $ifd0_length;
                $bytes .= $ifd0_bytes['ifd_area'] . ConvertBytes::fromLong($ifd1_offset, $order) . $ifd0_bytes['data_area'];
                $ifd1_bytes = $ifd1->toBytes($order, $ifd1_offset);
                $bytes .= $ifd1_bytes['ifd_area'] . ConvertBytes::fromLong(0, $order) . $ifd1_bytes['data_area'];
            }
        } else {
            $bytes .= ConvertBytes::fromLong(0, $order);
        }

        return $bytes;
    }

    /**
     * Returns the MIME type of the image.
     *
     * @returns string
     */
    public function getMimeType()
    {
        return 'image/tiff';
    }
}