<?php

namespace lsolesen\pel;

/**
 * Class used to hold data for MakerNote tags.
 */
class PelEntryMakerNote extends PelEntryUndefined
{
    // TTTT
    public $offsetxxx;
    /*   TTTT */
/*                } elseif (PelSpec::getTagName($this->type, $tag) === 'MakerNote') {
                    $this->loadSingleValue($d, $offset, $i, $tag);
                    $o = $d->getLong($offset + 12 * $i + 8);
                    $mn = $this->getEntry($tag);
                    $mn->offsetxxx = $o;*/

    public function __construct($tag, $data = '', $offset)
    {
        $this->tag = $tag;
        $this->format = PelFormat::UNDEFINED;
        $this->setValue($data);
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
     *
     * @return array a list or arguments to be passed to the PelEntry subclass
     *            constructor.
     */
    public static function getInstanceArgumentsFromData($ifd_id, $tag_id, $format, $components, PelDataWindow $data, /* TTTT */ $offset)
    {
        $args = parent::getInstanceArgumentsFromData();
        $args[] = $offset;
        return $args;
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
