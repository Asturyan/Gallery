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

namespace Gallery\Action;

use Gallery\Model\GalleryQuery;
use Gallery\Model\Gallery as GalleryModel;

use Gallery\Event\GalleryEvent;
use Gallery\Event\GalleryUpdateEvent;
use Gallery\Event\GalleryCreateEvent;
use Gallery\Event\GalleryDeleteEvent;
use Gallery\Event\GalleryToggleVisibilityEvent;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Action\BaseAction;
use Thelia\Core\Event\UpdatePositionEvent;

/**
 *
 * Gallery actions.
 *
 * @package Gallery\Action
 * @author Marc LEAMRCHAND <mlemarchand@hubchannel.fr>
 *
 */

class Gallery extends BaseAction implements EventSubscriberInterface
{
    /**
     * Create a new gallery entry
     *
     * @param \Gallery\Event\GalleryCreateEvent $event
     */
    public function create(GalleryCreateEvent $event)
    {
        $gallery = new GalleryModel();

        $gallery
            ->setDispatcher($event->getDispatcher())

            ->setLocale($event->getLocale())
            ->setVisible($event->getVisible())
            ->setTitle($event->getTitle())

            ->save()
        ;

        $event->setGallery($gallery);
    }

    /**
     * Change a gallery
     *
     * @param \Gallery\Event\GalleryUpdateEvent $event
     */
    public function update(GalleryUpdateEvent $event)
    {
        if (null !== $gallery = GalleryQuery::create()->findPk($event->getGalleryId())) {

            $gallery
                ->setDispatcher($event->getDispatcher())

                ->setLocale($event->getLocale())
                ->setTitle($event->getTitle())
                ->setDescription($event->getDescription())

                ->setVisible($event->getVisible())

                ->save();

            $event->setGallery($gallery);
        }
    }

    /**
     * Delete a gallery entry
     *
     * @param \Gallery\Event\GalleryDeleteEvent $event
     */
    public function delete(GalleryDeleteEvent $event)
    {
        if (null !== $gallery = GalleryQuery::create()->findPk($event->getGalleryId())) {

            $gallery
                ->setDispatcher($event->getDispatcher())
                ->delete()
            ;

            $event->setGallery($gallery);
        }
    }

    /**
     * Toggle gallery visibility. No form used here
     *
     * @param \Gallery\Event\GalleryToggleVisibilityEvent $event
     */
    public function toggleVisibility(GalleryToggleVisibilityEvent $event)
    {
         $gallery = $event->getGallery();

         $gallery
            ->setDispatcher($event->getDispatcher())
            ->setVisible($gallery->getVisible() ? false : true)
            ->save()
            ;

        $event->setGallery($gallery);
    }

    /**
     * Changes position, selecting absolute ou relative change.
     *
     * @param UpdatePositionEvent $event
     */
    public function updatePosition(UpdatePositionEvent $event)
    {
        $this->genericUpdatePosition(GalleryQuery::create(), $event);
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            GalleryEvent::GALLERY_CREATE            => array("create", 128),
            GalleryEvent::GALLERY_UPDATE            => array("update", 128),
            GalleryEvent::GALLERY_DELETE            => array("delete", 128),
            GalleryEvent::GALLERY_TOGGLE_VISIBILITY => array("toggleVisibility", 128),
            GalleryEvent::GALLERY_UPDATE_POSITION   => array("updatePosition", 128)
        );
    }
}
