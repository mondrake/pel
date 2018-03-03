<?php

namespace lsolesen\pel;

/**
 * Class used to hold data for MakerNote tags.
 */
class PelEntryMakerNote extends PelEntryUndefined
{
    // TTTT
    public $dataxxx;
    public $componentsxxx;
    public $offsetxxx;

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
