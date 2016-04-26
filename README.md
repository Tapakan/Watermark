#Watermark
Library that helps you easily add watermark to your images. It can be integrated with any system without problems.

##Install
Edit composer.json file and add next lines in require section.
```json
    "require": {
        "tapakan/watermark": "1.0.0"
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
There are 5 available constants to change the destination position of watermark.

| CONSTANT         | POSITION                                 |
| -----------------| -----------------------------------------|
| RIGHT_TOP_CORNER | Right top. <br> Margin from borders 10px |
| RIGHT_DOWN_CORNER| Right down.<br> Margin from borders 10px |
| LEFT_TOP_CORNER  | Left top.<br> Margin from borders 10px   |
| LEFT_DOWN_CORNER | Left down.<br> Margin from borders 10px  |
| MIDDLE_CENTER    | Middle.                                  |

##Demo