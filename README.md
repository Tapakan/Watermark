# Watermark

##Using

```
require 'vendor/autoload.php'

use eva\tools\Watermark;

$watermark = new Watermark($arg1, $arg2);

$arg1 - Path to watermark file; // For example: images/watermark.png
$arg2 - 'By second argument you can change destination position of watermark' // See Available positions sector.

// Then add watermark to someone image.

$watermark->add('/images/my_new_image_with_watermark');
```

### Available Positions
```
Watermark::RIGHT_TOP_CORNER  - Right top.  Margin form borders 10px.
Watermark::RIGHT_DOWN_CORNER - Right down. Margin form borders 10px.
Watermark::LEFT_TOP_CORNER   - Left top.   Margin form borders 10px.
Watermark::LEFT_DOWN_CORNER  - Left down.  Margin form borders 10px.
Watermark::MIDDLE_CENTER     - Middle.
```
