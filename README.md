#Watermark
Library that helps you easily add watermark to your images. It can be integrated with any system without problems.

##Install
Edit composer.json file and add next lines in require section.
```json
    "require-dev": {
        "tapakan\watermark": "1.0"
    }
```
##Using
```php
require 'vendor/autoload.php'

use eva\tools\Watermark;

$watermark = new Watermark($arg1, $arg2);

//$arg1 - Path to watermark file. For example: images/watermark.png
//$arg2 - By second argument you can change destination position of watermark. See Available positions sector.

// Then add watermark to someone image.
$watermark->add('/images/image.jpeg');
```
Image will be saved with same name and to the same directory.

### Available Positions
```
Watermark::RIGHT_TOP_CORNER  - Right top.  Margin from borders 10px.
Watermark::RIGHT_DOWN_CORNER - Right down. Margin from borders 10px.
Watermark::LEFT_TOP_CORNER   - Left top.   Margin from borders 10px.
Watermark::LEFT_DOWN_CORNER  - Left down.  Margin from borders 10px.
Watermark::MIDDLE_CENTER     - Middle.
```
