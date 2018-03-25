<?php

namespace ExifEye\core\Entry;

use ExifEye\core\DataWindow;
use ExifEye\core\Format;
use ExifEye\core\Utility\Convert;

/**
 * Class for holding signed shorts.
 *
 * This class can hold shorts, either just a single short or an array
 * of shorts. The class will be used to manipulate any of the Exif
 * tags which has format {@link Format::SSHORT}.
 *
 * @author Martin Geisler <mgeisler@users.sourceforge.net>
 */
class SignedShort extends NumberBase
{
    /**
     * Make a new entry that can hold a signed short.
     *
     * The method accept several integer arguments. The {@link
     * getValue} method will always return an array except for when a
     * single integer argument is given here.
     *
     * @param int $tag
     *            the tag which this entry represents. This
     *            should be one of the constants defined in {@link PelTag}
     *            which has format {@link Format::SSHORT}.
     * @param int $value...
     *            the signed short(s) that this entry will
     *            represent. The argument passed must obey the same rules as the
     *            argument to {@link setValue}, namely that it should be within
     *            range of a signed short, that is between -32768 to 32767
     *            (inclusive). If not, then a {@link PelOverFlowException} will be
     *            thrown.
     */
    public function __construct($tag, $value = null)
    {
        $this->tag = $tag;
        $this->min = - 32768;
        $this->max = 32767;
        $this->format = Format::SSHORT;

        $value = func_get_args();
        array_shift($value);
        $this->setValueArray($value);
    }

    /**
     * Get arguments for the instance constructor from file data.
     *
     * @param int $ifd_id
     *            the IFD id.
     * @param int $tag_id
     *            the TAG id.
     * @param int $format
     *            the format of the entry as defined in {@link Format}.
     * @param int $components
     *            the components in the entry.
     * @param DataWindow $data
     *            the data which will be used to construct the entry.
     * @param int $data_offset
     *            the offset of the main DataWindow where data is stored.
     *
     * @return array a list or arguments to be passed to the PelEntry subclass
     *            constructor.
     */
    public static function getInstanceArgumentsFromData($ifd_id, $tag_id, $format, $components, DataWindow $data, $data_offset)
    {
        $args = [];
        for ($i = 0; $i < $components; $i ++) {
            $args[] = $data->getSShort($i * 2);
        }
        return $args;
    }

    /**
     * Convert a number into bytes.
     *
     * @param int $number
     *            the number that should be converted.
     * @param boolean $order
     *            one of {@link Convert::LITTLE_ENDIAN} and
     *            {@link Convert::BIG_ENDIAN}, specifying the target byte order.
     *
     * @return string bytes representing the number given.
     */
    public function numberToBytes($number, $order)
    {
        return Convert::sShortToBytes($number, $order);
    }
}