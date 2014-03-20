<?php

namespace Gallery\Model;

use Gallery\Model\Base\Gallery as BaseGallery;
use Propel\Runtime\Connection\ConnectionInterface;

class Gallery extends BaseGallery
{
    use \Thelia\Model\Tools\ModelEventDispatcherTrait;
    use \Thelia\Model\Tools\PositionManagementTrait;  
    
    /**
     *
     * count all images for current gallery
     *
     * @return int
     */
    public function countAllImages()
    {
        $children = GalleryQuery::findAllChild($this->getId());
        array_push($children, $this);

        $countImage = 0;

        foreach ($children as $child) {
            $countImage += GalleryImageQuery::create()
                ->filterByGallery($child)
                ->count();
        }

        return $countImage;
    }
    
    public function deleteImages(ConnectionInterface $con = null)
    {
        $images = GalleryImageQuery::create()
            ->filterByGalleryId($this->getId())
            ->find($con);

        if ($images) {
            foreach ($images as $image) {
                $image->delete($con);
            }
        }
    }
    
    public function preInsert(ConnectionInterface $con = null)
    {
        $this->setPosition($this->getNextPosition());

        return true;
    }
    
    public function preDelete(ConnectionInterface $con = null)
    {
        $this->reorderBeforeDelete(
            array()
        );
        $this->deleteImages($con);

        return true;
    }
}
