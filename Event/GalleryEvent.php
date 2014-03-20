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

use Gallery\Model\Gallery;
use Thelia\Core\Event\ActionEvent;

/**
 *
 * This class contains all Gallery events identifiers used by Gallery Core
 *
 * @author Marc LEMARCHAND <mlemarchand@hubchannel.fr>
 */

class GalleryEvent extends ActionEvent
{
    public $gallery = null;
    
    const GALLERY_CREATE            = 'gallery.action.create';
    const GALLERY_UPDATE            = 'gallery.action.update';
    const GALLERY_DELETE            = 'gallery.action.delete';
    const GALLERY_TOGGLE_VISIBILITY = 'gallery.action.toggleVisibility';
    const GALLERY_UPDATE_POSITION   = 'gallery.action.updatePosition';

    public function __construct(Gallery $gallery = null)
    {
        $this->gallery = $gallery;
    }

    /**
     * @param  \Gallery\Model\Gallery $gallery
     * @return $this
     */
    public function setGallery(Gallery $gallery)
    {
        $this->gallery = $gallery;

        return $this;
    }

    /**
     * @return \Gallery\Model\Gallery
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * check if gallery exists
     *
     * @return bool
     */
    public function hasGallery()
    {
        return null !== $this->gallery;
    }
}
