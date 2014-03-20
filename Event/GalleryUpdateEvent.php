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
 * Class GalleryUpdateEvent
 * @package Gallery\Event
 * @author Marc LEMARCHAND <mlemarchand@hubchannel.fr>
 */
class GalleryUpdateEvent extends GalleryCreateEvent
{
    protected $gallery_id;
    protected $description;

    public function __construct($gallery_id)
    {
        $this->gallery_id = $gallery_id;
    }

    /**
     * @param mixed $gallery_id
     *
     * @return $this
     */
    public function setGalleryId($gallery_id)
    {
        $this->gallery_id = $gallery_id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGalleryId()
    {
        return $this->gallery_id;
    }

    /**
     * @param mixed $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

}
