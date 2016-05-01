<?php
/**
 * Watermark tool.
 * @package     Tapakan\Watermark
 * @version     1.0.1
 * @license     http://mit-license.org/
 * @author      Tapakan https://github.com/Tapakan
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

namespace Tapakan\Watermark;

use Tapakan\Path\Path;
use Tapakan\File\File;

/**
 * Class Watermark
 * @package Tapakan\Watermark
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
    
    const WHITE_LIST = array('image/jpg', 'image/jpeg', 'image/gif', 'image/png');
    
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
        if ($position !== self::RIGHT_TOP_CORNER
            && $position !== self::RIGHT_DOWN_CORNER
            && $position !== self::LEFT_TOP_CORNER
            && $position !== self::LEFT_DOWN_CORNER
            && $position !== self::MIDDLE_CENTER
            && $position !== self::EVERYWHERE
        ) {
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
        
        $pos = array();
        switch ($this->position) {
            case self::RIGHT_TOP_CORNER:
                $pos[] = array(
                    'x' => $imageX - $waterX - 10,
                    'y' => 10
                );
                break;
            
            case self::RIGHT_DOWN_CORNER:
                $pos[] = array(
                    'x' => $imageX - $waterX - 10,
                    'y' => $imageY - $waterY - 10
                );
                break;
            
            case self::LEFT_DOWN_CORNER:
                $pos[] = array(
                    'x' => 10,
                    'y' => $imageX - $waterX - 10
                );
                break;
            
            case self::LEFT_TOP_CORNER:
                $pos[] = array(
                    'x' => 10,
                    'y' => 10
                );
                break;
            
            case self::MIDDLE_CENTER:
                $pos[] = array(
                    'x' => ($imageX / 2) - ($waterX / 2),
                    'y' => ($imageY / 2) - ($waterY / 2)
                );
                break;
            
            case self::EVERYWHERE:
                $countX = floor($imageX / $waterX); // Get number of images on the X
                $countY = floor($imageY / $waterY); // Get number of images on the Y
                for ($v = 0; $v <= $countY; $v++) {
                    for ($h = 0; $h <= $countX; $h++) {
                        $pos[] = array(
                            'x' => $v * $waterY,
                            'y' => $waterX * $h,
                        );
                    }
                }
                break;
        }
        
        foreach ($pos as $position => $positions) {
            imagecopy($img, $watermark, $positions['x'], $positions['y'], 0, 0, $waterX, $waterY);
        }
        
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
