<?php

namespace Gallery\Model;

use Gallery\Model\Base\GalleryImage as BaseGalleryImage;
use Propel\Runtime\Connection\ConnectionInterface;

class GalleryImage extends BaseGalleryImage
{
    use \Thelia\Model\Tools\ModelEventDispatcherTrait;
    use \Thelia\Model\Tools\PositionManagementTrait;
    
    /**
     * Calculate next position relative to our parent
     */
    protected function addCriteriaToPositionQuery($query)
    {
        $query->filterByGallery($this->getGallery());
    }

    /**
     * {@inheritDoc}
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        $this->setPosition($this->getNextPosition());

        return true;
    }
    
    public function preDelete(ConnectionInterface $con = null)
    {
        $this->reorderBeforeDelete(
            array(
                "gallery_id" => $this->getGalleryId(),
            )
        );

        return true;
    }
}
