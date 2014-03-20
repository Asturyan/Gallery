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

use Thelia\Core\Event\ActionEvent;
use Thelia\Model\CategoryImage;
use Thelia\Model\ContentImage;
use Thelia\Model\FolderImage;
use Thelia\Model\ProductImage;

/**
 *
 * Occurring when a Image is about to be deleted
 *
 * @package GalleryImage
 * @author Marc LEMARCHAND <mlemarchand@hubchannel.fr>
 *
 */
class GalleryImageDeleteEvent extends GalleryImageEvent
{

    /** @var GalleryImage Image about to be deleted */
    protected $imageToDelete = null;

    /**
     * Constructor
     *
     * @param GalleryImage $imageToDelete Image about to be deleted

     */
    public function __construct($imageToDelete)
    {
        $this->imageToDelete = $imageToDelete;
    }

    /**
     * Set Image about to be deleted
     *
     * @param GalleryImage $imageToDelete Image about to be deleted
     *
     * @return $this
     */
    public function setImageToDelete(GalleryImage $imageToDelete)
    {
        $this->imageToDelete = $imageToDelete;

        return $this;
    }

    /**
     * Get Image about to be deleted
     *
     * @return GalleryImage
     */
    public function getImageToDelete()
    {
        return $this->imageToDelete;
    }

}
