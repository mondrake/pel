# INSTALL


## Requirements

ExifEye requires PHP version 5.4 or above.


## Installation

### Composer

The preferred way of installing ExifEye is through composer. Simply add a
dependency on ´exifeye/exifeye´ to your projects composer.json.

    {
        "require": {
            "exifeye/exifeye": "^1"
        }
    }

For a system-wide installation via Composer, you can run:

    composer global require "exifeye/exifeye=^1"


### Clone via git

You can also use git to install it using:

  git clone git://github.com/ExifEye/ExifEye.git
  git checkout <tag name>

Finally, you can install ExifEye by extracting it to a local directory. You can find
the compressed files here: https://github.com/ExifEye/ExifEye/downloads.

Make sure that you extract the files to a path included in your include path:
You can set the include path using.

    set_include_path('/path/to/ExifEye' . PATH_SEPARATOR . get_include_path());


## Upgrading

If you have already been using a previous version of ExifEye, then be sure
to read the CHANGELOG.md file before starting with a new version.


## Using ExifEye

Your application should include Jpeg.php or Tiff.php for working
with JPEG or TIFF files.  The files will define the Jpeg and
Tiff classes, which can hold a JPEG or TIFF image, respectively.
Please see the API documentation in the doc directory or online at

  http://lsolesen.github.com/pel/doc/

for the full story about those classes and all other available classes
in ExifEye.

Still, an example would be good.  The following example will load a
JPEG file given as a command line argument, parse the Exif data
within, change the image description to 'Edited by ExifEye', and finally
save the file again.  All in just six lines of code:

  ```php5
  <?php
  require_once('Jpeg.php');

  $jpeg = new Jpeg($argv[1]);
  $ifd0 = $jpeg->getExif()->getTiff()->getIfd();
  $entry = $ifd0->getEntry(Spec::getTagIdByName($ifd0, 'ImageDescription'));
  $entry->setValue('Edited by ExifEye');
  $jpeg->saveFile($argv[1]);
  ?>
  ```

See the examples directory for this example (or rather a more
elaborate version in the file edit-description.php) and others as PHP
files.  You may have to adjust the path to PHP, found in the very
first line of the files before you can execute them.


## Changing ExifEye

If you find a bug in ExifEye then please send a report back so that it can
be fixed in the next version.  You can submit your bugs and other
requests here:

  http://github.com/pel/pel/issues

If you change the code (to fix bugs or to implement enhancements), it
is highly recommended that you test your changes against known good
data.  Please see the test/README.md file for more information about
running the ExifEye test suite.
