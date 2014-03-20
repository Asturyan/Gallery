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

/**
 *
 * Occurring when an Image is saved
 *
 * @package GalleryImage
 * @author Marc LEMARCHAND <mlemarchand@hubchannel.fr>
 *
 */
class GalleryImageCreateOrUpdateEvent extends GalleryImageEvent
{

    /** @var Gallery\Model\GalleryImage model to save */
    protected $oldModelImage;
    protected $type;
    protected $type_id;
    protected $subtype_id;
    protected $url;
    protected $visible;
    
    /**
     * Set old model value
     *
     * @param \Gallery\Model\GalleryImage $oldModelImage
     */
    public function setOldModelImage($oldModelImage)
    {
        $this->oldModelImage = $oldModelImage;
    }

    /**
     * Get old model value
     *
     * @return \Gallery\Model\GalleryImage
     */
    public function getOldModelImage()
    {
        return $this->oldModelImage;
    }
    
    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * @param mixed $type_id
     */
    public function setTypeId($type_id)
    {
        $this->type_id = $type_id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTypeId()
    {
        return $this->type_id;
    }
    
     /**
     * @param mixed $type_id
     */
    public function setSubTypeId($subtype_id)
    {
        $this->subtype_id = $subtype_id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubTypeId()
    {
        return $this->subtype_id;
    }
    
    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }
    
    /**
     * @param mixed $visible
     *
     * @return $this
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getVisible()
    {
        return $this->visible;
    }

}
