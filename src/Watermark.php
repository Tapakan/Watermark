<?php
/**
 * Watermark tool.
 * @package     eva\tools
 * @version     1.0
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
    
    /**
     * Watermark constructor.
     *
     * @param string $waterPath Path to watermark file.
     * @param int    $position
     * @param array  $allowed   Array of allowed extensions
     */
    public function __construct($waterPath, $position = self::MIDDLE_CENTER, array $allowed = self::WHITE_LIST)
    {
        $this->path = Path::getInstance('watermark');
        
        // Check if watermark exists.
        if (!$watermark = $this->path->path($waterPath)) {
            throw new \InvalidArgumentException(
                "Water mark file {$waterPath} doesn't exists"
            );
        }
        $position = (int)$position;
        
        // Check if position is valid.
        if ($position < 0 || $position > 4) {
            throw new \InvalidArgumentException(
                "Unavailable position {$position}"
            );
        }
        
        $this->watermark = $watermark;
        $this->position  = $position;
        $this->allowed   = $allowed;
    }
    
    /**
     * Add watermark to image.
     *
     * @param string $image Path to image file.
     */
    public function add($image)
    {
        if (!$image = $this->path->path($image)) {
            throw new \InvalidArgumentException(
                "File {$image} doesn't exists"
            );
        }
        
        $img       = $this->getResource($image);
        $watermark = $this->getResource($this->watermark);
        
        $img = $this->place($img, $watermark);
        
        $ext  = File::getExt($image);
        $func = 'image' . ($ext == 'jpg' ? 'jpeg' : $ext);
        
        return call_user_func_array($func, [$img, $image, 100]);
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
    
    /**
     * Place watermark on image.
     *
     * @param Resource $img       Image resource identifier
     * @param Resource $watermark Image Resource identifier
     *
     * @return Resource
     */
    protected function place($img, $watermark)
    {
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
}
