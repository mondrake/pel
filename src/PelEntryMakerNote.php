<?php

namespace lsolesen\pel;

/**
 * Class used to hold data for MakerNote tags.
 */
class PelEntryMakerNote extends PelEntryUndefined
{
    // TTTT
    public $offsetxxx;

    /**
     * Make a new PelEntry that can hold MakerNote data.
     *
     * @param integer $tag
     *            the MakerNote TAG id.
     * @param string $data
     *            the MakerNote data.
     * @param integer $data_offset
     *            the offset of the MakerNote IFD vs the main DataWindow.
     */
    public function __construct($tag, $data, $data_offset)
    {
        parent::__construct($tag, $data);
        $this->offsetxxx = $data_offset;
    }

    /**
     * Get arguments for the instance constructor from file data.
     *
     * @param int $ifd_id
     *            the IFD id.
     * @param int $tag_id
     *            the TAG id.
     * @param int $format
     *            the format of the entry as defined in {@link PelFormat}.
     * @param int $components
     *            the components in the entry.
     * @param PelDataWindow $data
     *            the data which will be used to construct the entry.
     * @param int $data_offset
     *            the offset of the main DataWindow where data is stored.
     *
     * @return array a list or arguments to be passed to the PelEntry subclass
     *            constructor.
     */
    public static function getInstanceArgumentsFromData($ifd_id, $tag_id, $format, $components, PelDataWindow $data, $data_offset)
    {
        return [$data->getBytes(), $data_offset];
    }

    /**
     * Get the value of this entry as text.
     *
     * The value will be returned in a format suitable for presentation.
     *
     * @param
     *            boolean some values can be returned in a long or more
     *            brief form, and this parameter controls that.
     *
     * @return string the value as text.
     */
    public function getText($brief = false)
    {
        return $this->components . ' bytes unknown MakerNote data';
    }
}
