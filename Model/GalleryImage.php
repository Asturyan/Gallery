<?php

namespace Gallery\Model;

use Gallery\Model\Base\GalleryImage as BaseGalleryImage;
use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Files\FileModelInterface;
use Thelia\Files\FileModelParentInterface;

class GalleryImage extends BaseGalleryImage implements FileModelInterface
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

    /**
     * @inheritdoc
     */
    public function setParentId($parentId)
    {
        $this->setGalleryId($parentId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getParentId()
    {
        return $this->getProductId();
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

    /**
     * @return FileModelParentInterface the parent file model
     */
    public function getParentFileModel()
    {
        return new static;
    }

    /**
     * Get the ID of the form used to change this object information
     *
     * @return BaseForm the form
     */
    public function getUpdateFormId()
    {
        return 'admin.gallery.image.modification';
    }

    /**
     * Get the form instance used to change this object information
     *
     * @param \Thelia\Core\HttpFoundation\Request $request
     *
     * @return BaseForm the form
     */
    public function getUpdateFormInstance(Request $request)
    {
        return new GalleryImageModificationForm($request);
    }

    /**
     * @return string the path to the upload directory where files are stored, without final slash
     */
    public function getUploadDir()
    {
        return THELIA_LOCAL_DIR . 'media'.DS.'images'.DS.'gallery';
    }

    /**
     * @param int $objectId the ID of the object
     *
     * @return string the URL to redirect to after update from the back-office
     */
    public function getRedirectionUrl()
    {
        return '/admin/module/Gallery/update?gallery_id=' . $this->getGalleryId();
    }

    /**
     * Get the Query instance for this object
     *
     * @return ModelCriteria
     */
    public function getQueryInstance()
    {
        return GalleryImageQuery::create();
    }

    /**
     * Set the chapo
     *
     * @param  string             $chapo the chapo in the current locale
     * @return FileModelInterface
     */
    public function setChapo($chapo)
    {
        // TODO: Implement setChapo() method.
    }

    /**
     * Set the description
     *
     * @param  string             $description the description in the current locale
     * @return FileModelInterface
     */
    public function setDescription($description)
    {
        // TODO: Implement setDescription() method.
    }

    /**
     * Set the postscriptum
     *
     * @param  string             $postscriptum the postscriptum in the current locale
     * @return FileModelInterface
     */
    public function setPostscriptum($postscriptum)
    {
        // TODO: Implement setPostscriptum() method.
    }
}
