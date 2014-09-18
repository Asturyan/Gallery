<?php
/*************************************************************************************/
/*                                                                                   */
/*      HubChannel	                                                             */
/*                                                                                   */
/*      Copyright (c) HubChannel                                                     */
/*      email : mlemarchand@hubchannel.fr                                            */
/*      web : http://www.hubchannel.fr                                               */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.     */
/*                                                                                   */
/*************************************************************************************/

namespace Gallery\Event;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Thelia\Core\Event\Image\ImageEvent;
use Gallery\Model\GalleryImage;
/**
 *
 * Occurring when an Image is saved
 *
 * @package Image
 * @author Marc LEMARCHAND <mlemarchand@hubchannel.fr>
 *
 */
class GalleryImageEvent extends ImageEvent
{
    /**
     * Sent on image processing
     */
    const IMAGE_PROCESS = "gallery.action.image.processImage";
    /**
     * Save given images
     */
    const IMAGE_SAVE = "gallery.action.image.saveImage";

    /**
     * Save given images
     */
    const IMAGE_UPDATE = "gallery.action.image.updateImage";
    const IMAGE_UPDATE_POSITION = "gallery.action.image.updateImagePosition";

    /**
     * Delete given image
     */
    const IMAGE_DELETE = "gallery.action.image.deleteImage";

    protected $modelImage = null;

    /** @var UploadedFile Image file to save */
    protected $uploadedFile = null;

    /** @var int Image parent id */
    protected $parentId = null;

    /** @var string Parent name */
    protected $parentName = null;

    protected $locale;

    /**
     * Constructor
     *
     * @param int $parentId Image parent id
     */
    public function __construct($parentId)
    {
        $this->parentId  = $parentId;
    }

    /**
     * @param mixed $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set Image parent id
     *
     * @param int $parentId Image parent id
     *
     * @return $this
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Get Image parent id
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set uploaded file
     *
     * @param UploadedFile $uploadedFile File being uploaded
     *
     * @return $this
     */
    public function setUploadedFile($uploadedFile)
    {
        $this->uploadedFile = $uploadedFile;

        return $this;
    }

    /**
     * Get uploaded file
     *
     * @return UploadedFile
     */
    public function getUploadedFile()
    {
        return $this->uploadedFile;
    }

    /**
     * Set parent name
     *
     * @param string $parentName Parent name
     *
     * @return $this
     */
    public function setParentName($parentName)
    {
        $this->parentName = $parentName;

        return $this;
    }

    /**
     * Get parent name
     *
     * @return string
     */
    public function getParentName()
    {
        return $this->parentName;
    }

    /**
     * Set old model value
     *
     * @param \Gallery\Model\GalleryImage $oldModelImage
     */
    public function setOldModel($oldModelImage)
    {
        $this->oldModel = $oldModelImage;
    }

    /**
     * Get old model value
     *
     * @return \Gallery\Model\GalleryImage
     */
    public function getOldModel()
    {
        return $this->oldModel;
    }

}
