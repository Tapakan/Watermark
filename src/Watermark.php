<?php
/**
 * Watermark tool.
 * @package     eva\tools
 * @version     1.0.1
 * @license     http://mit-license.org/
 * @author      Tapakan https://github.com/Tapakan
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

namespace eva\tools;

use eva\helpers\Path;
use eva\helpers\File;

/**
 * Class Watermark
 * @package eva\tools
 */
class Watermark
{
    /**
     * Watermark file that will be placed on the image.
     * @var string Path to watermark file.
     */
    protected $watermark;
    
    /**
     * Side of image where watermark will be placed.
     * @var int See declare constants
     */
    protected $position;
    
    /**
     * @var array Allowed type of images.
     */
    protected $allowed;
    
    /**
     * Path helper object.
     * @var Path
     */
    private $path;
    
    const WHITE_LIST = ['image/jpg', 'image/jpeg', 'image/gif', 'image/png'];
    
    const RIGHT_TOP_CORNER  = 0;
    const RIGHT_DOWN_CORNER = 1;
    const LEFT_TOP_CORNER   = 2;
    const LEFT_DOWN_CORNER  = 3;
    const MIDDLE_CENTER     = 4;
    const EVERYWHERE        = 5;

    /**
     * Watermark constructor.
     *
     * @param string $path Path to watermark file.
     * @param int    $position
     */
    public function __construct($path, $position = self::MIDDLE_CENTER)
    {
        $this->path = Path::getInstance('watermark');

        // Set watermark path
        $this->setWatermark($path);

        // Set watermark destination position.
        $this->setPosition($position);

        $this->allowed = self::WHITE_LIST;
    }

    /**
     * Set watermark destination position.
     *
     * @param integer $position One of available positions. See class constants.
     *
     * @throws \InvalidArgumentException Throws Exception if position not isset.
     * @return $this
     */
    public function setPosition($position)
    {
        $position = (int)$position;

        // Check if position is valid.
        if ($position < 0 || $position > 4) {
            throw new \InvalidArgumentException(
                "Unavailable position {$position}"
            );
        }

        $this->position = $position;

        return $this;
    }

    /**
     * @param string $path Path to watermark file.
     *
     * @return $this
     */
    public function setWatermark($path)
    {
        // Check if watermark exists.
        if (!$watermark = $this->path->path($path)) {
            throw new \InvalidArgumentException(
                "Water mark file {$path} doesn't exists"
            );
        }

        $this->watermark = $watermark;

        return $this;
    }
    
    /**
     * Add watermark to image.
     *
     * @param string $path Path to image file.
     */
    public function add($path)
    {
        if (!$image = $this->path->path($path)) {
            throw new \InvalidArgumentException(
                "Image \"{$path}\" doesn't exists"
            );
        }

        $img = $this->getResource($image);
        $img = $this->addWatermark($img);
        
        $ext  = File::getExt($image);
        $func = 'image' . ($ext == 'jpg' ? 'jpeg' : $ext);
        
        return call_user_func_array($func, [$img, $image, 100]);
    }

    /**
     * Place watermark on image.
     *
     * @param Resource $img Image resource identifier
     *
     * @return Resource
     */
    protected function addWatermark($img)
    {
        $watermark = $this->getResource($this->watermark);

        $imageX = imagesx($img);
        $imageY = imagesy($img);
        
        $waterX = imagesx($watermark);
        $waterY = imagesy($watermark);

        switch ($this->position) {
            case self::RIGHT_TOP_CORNER:
                $posY = 10;
                $posX = $imageX - $waterX - 10;
                break;

            case self::RIGHT_DOWN_CORNER:
                $posY = $imageY - $waterY - 10;
                $posX = $imageX - $waterX - 10;
                break;

            case self::LEFT_DOWN_CORNER:
                $posY = $imageX - $waterX - 10;
                $posX = 10;
                break;
            
            case self::LEFT_TOP_CORNER:
                $posX = 10;
                $posY = 10;
                break;
            
            case self::MIDDLE_CENTER:
                $posY = ($imageY / 2) - ($waterY / 2);
                $posX = ($imageX / 2) - ($waterX / 2);

                break;
        }
        imagecopy($img, $watermark, $posX, $posY, 0, 0, $waterX, $waterY);

        return $img;
    }

    /**
     * Return image id resource.
     *
     * @param string $image Path to image
     *
     * @return Resource
     */
    protected function getResource($image)
    {
        $type = mime_content_type($image);
        if (!in_array($type, $this->allowed)) {
            throw new \InvalidArgumentException("{$type} not allowed. Allowed types - " . implode(PHP_EOL, $this->allowed));
        }

        switch ($type) {
            case 'image/jpg' :
            case 'image/jpeg':
                $resource = imagecreatefromjpeg($image);
                break;

            case 'image/png' :
                $resource = imagecreatefrompng($image);
                break;

            case 'image/gif' :
                $resource = imagecreatefromgif($image);
                break;

            default          :
                $resource = imagecreatefromjpeg($image);
        }

        return $resource;
    }
}
