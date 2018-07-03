<?php

namespace ExifEye\Apple\Block;

use ExifEye\core\Block\Ifd;
use ExifEye\core\DataWindow;
use CFPropertyList\CFPropertyList;

class RunTime extends Ifd
{
    /**
     * {@inheritdoc}
     */
    public function loadFromData(DataWindow $data_window, $offset = 0, array $options = [])
    {
        dump($offset);
        dump($options);
        $plist = new CFPropertyList();
        dump($data_window->getBytes($offset, $options['components']));
        $plist->parse($data_window->getBytes($offset, $options['components']));
        dump($plist->toArray());
    }

    /**
     * xx
     * @param DataWindow $data_window
     *            the data from which the thumbnail will be
     *            extracted.
     */
    public static function runTimetoBlock(DataWindow $data_window, Ifd $ifd)
    {
        $apple_run_time_entry = $ifd->getElement("tag[@name='RunTime']/entry");

        if ($apple_run_time_entry === null) {
            return;
        }

        $plist = new CFPropertyList();
        $plist->parse($apple_run_time_entry->getValue());
dump($plist->toArray());
return;
        $length = $ifd->getElement("tag[@name='ThumbnailLength']/entry")->getValue();

        // Load the thumbnail only if both the offset and the length are
        // available and positive.
        if ($offset <= 0 || $length <= 0) {
            $ifd->warning('Invalid JPEG thumbnail for offset {offset} and length {length}', [
                'offset' => $offset,
                'length' => $length,
            ]);
            return;
        }

        // Some images have a broken length, so we try to carefully check
        // the length before we store the thumbnail.
        if ($offset + $length > $data_window->getSize()) {
            $ifd->warning('Thumbnail length {length} bytes adjusted to {adjusted_length} bytes.', [
                'length' => $length,
                'adjusted_length' => $data_window->getSize() - $offset,
            ]);
            $length = $data_window->getSize() - $offset;
        }

        // Now set the thumbnail normally.
        try {
            $dataxx = $data_window->getClone($offset, $length);
            $size = $dataxx->getSize();
            // Now move backwards until we find the EOI JPEG marker.
            while ($dataxx->getByte($size - 2) != 0xFF || $dataxx->getByte($size - 1) != JpegMarker::EOI) {
                $size --;
            }
            if ($size != $dataxx->getSize()) {
                $ifd->warning('Decrementing thumbnail size to {size} bytes', [
                    'size' => $size,
                ]);
            }
            $thumbnail_data = $dataxx->getClone(0, $size)->getBytes();

            $thumbnail_block = new static($ifd);
            $thumbnail_entry = new Undefined($thumbnail_block, [$thumbnail_data]);
            $thumbnail_block->debug('JPEG thumbnail found at offset {offset} of length {length}', [
                'offset' => $offset,
                'length' => $length,
            ]);
        } catch (DataWindowException $e) {
            $ifd->error($e->getMessage());
        }
    }
}
