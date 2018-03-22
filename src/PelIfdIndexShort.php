<?php

namespace lsolesen\pel;

use ExifEye\core\Format;

/**
 * Class representing an index of Short values as an IFD.
 */
class PelIfdIndexShort extends PelIfd
{
    /**
     * Load data into a Image File Directory (IFD).
     *
     * @param PelDataWindow $d
     *            the data window that will provide the data.
     * @param int $offset
     *            the offset within the window where the directory will
     *            be found.
     * @param int $components
     *            (Optional) the number of components held by this IFD.
     * @param int $nesting_level
     *            (Optional) the level of nesting of this IFD in the overall
     *            structure.
     */
    public function load(PelDataWindow $d, $offset, $components = 1, $nesting_level = 0)
    {
        Pel::debug(
            str_repeat("  ", $nesting_level) . "** Constructing IFD '%s' with %d entries at offset %d...",
            $this->getName(),
            $components,
            $offset
        );

        $index_size = $d->getShort($offset);
        if ($index_size / $components !== Format::getSize(Format::SHORT)) {
            Pel::maybeThrow(new PelInvalidDataException('Size of %s does not match the number of entries.', $this->getName()));
        }
        $offset += 2;
        for ($i = 0; $i < $components; $i++) {
            // Check if PEL can support this TAG.
            if (!$this->isValidTag($i + 1)) {
                Pel::debug(
                    str_repeat("  ", $nesting_level) . "No specification available for tag 0x%04X, skipping (%d of %d)...",
                    $i + 1,
                    $i + 1,
                    $components
                );
                continue;
            }

            $item_format = PelSpec::getTagFormat($this->type, $i + 1)[0];
            Pel::debug(
                str_repeat("  ", $nesting_level) . 'Tag 0x%04X: (%s) Fmt: %d (%s) Components: %d (%d of %d)...',
                $i + 1,
                PelSpec::getTagName($this->type, $i + 1),
                $item_format,
                Format::getName($item_format),
                1,
                $i + 1,
                $components
            );
            switch ($item_format) {
                case Format::BYTE:
                    $item_value = $d->getByte($offset + $i * 2);
                    break;
                case Format::SHORT:
                    $item_value = $d->getShort($offset + $i * 2);
                    break;
                case Format::LONG:
                    $item_value = $d->getLong($offset + $i * 2);
                    break;
                case Format::RATIONAL:
                    $item_value = $d->getRational($offset + $i * 2);
                    break;
                case Format::SBYTE:
                    $item_value = $d->getSByte($offset + $i * 2);
                    break;
                case Format::SSHORT:
                    $item_value = $d->getSShort($offset + $i * 2);
                    break;
                case Format::SLONG:
                    $item_value = $d->getSLong($offset + $i * 2);
                    break;
                case Format::SRATIONAL:
                    $item_value = $d->getSRattional($offset + $i * 2);
                    break;
            }
            if ($entry = PelEntry::createNew($this->type, $i + 1, [$item_value])) {
                $this->addEntry($entry);
            }
        }
        Pel::debug(str_repeat("  ", $nesting_level) . "** End of loading IFD '%s'.", $this->getName());
    }
}
