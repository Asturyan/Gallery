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

namespace Gallery\Controller\Admin;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Gallery\Event\GalleryToggleVisibilityEvent;
use Gallery\Event\GalleryDeleteEvent;
use Gallery\Event\GalleryEvent;
use Gallery\Event\GalleryUpdateEvent;
use Gallery\Event\GalleryCreateEvent;
use Gallery\Form\GalleryCreationForm;
use Gallery\Form\GalleryModificationForm;
use Gallery\Model\GalleryQuery;

use Thelia\Controller\Admin\AbstractCrudController;
use Thelia\Core\Event\UpdatePositionEvent;
use Thelia\Core\Security\AccessManager;

use Thelia\Tools\URL;

/**
 *
 * Control View and Action (Model) via Events
 * Control Gallery
 *
 * @package Gallery
 * @author  Marc LEMARCHAND <mlemarchand@hubchannel.fr>
 *
 */
class GalleryController extends AbstractCrudController
{

    public function __construct()
    {
        parent::__construct(
            'gallery',
            'manual',
            'gallery_order',

            'admin.gallery',

            GalleryEvent::GALLERY_CREATE,
            GalleryEvent::GALLERY_UPDATE,
            GalleryEvent::GALLERY_DELETE,
            GalleryEvent::GALLERY_TOGGLE_VISIBILITY,
            GalleryEvent::GALLERY_UPDATE_POSITION
        );
    }

    protected function getCreationForm()
    {
        return new GalleryCreationForm($this->getRequest());
    }

    protected function getUpdateForm()
    {
        return new GalleryModificationForm($this->getRequest());
    }

    protected function getCreationEvent($formData)
    {
        $createEvent = new GalleryCreateEvent();

        $createEvent
            ->setTitle($formData['title'])
            ->setLocale($formData["locale"])
            ->setVisible($formData['visible'])
        ;

        return $createEvent;
    }

    protected function getUpdateEvent($formData)
    {
        $changeEvent = new GalleryUpdateEvent($formData['id']);

        // Create and dispatch the change event
        $changeEvent
            ->setLocale($formData['locale'])
            ->setTitle($formData['title'])
            ->setDescription($formData['description'])
            ->setVisible($formData['visible'])
        ;

        return $changeEvent;
    }

    protected function createUpdatePositionEvent($positionChangeMode, $positionValue)
    {
        return new UpdatePositionEvent(
                $this->getRequest()->get('gallery_id', null),
                $positionChangeMode,
                $positionValue
        );
    }

    protected function getDeleteEvent()
    {
        return new GalleryDeleteEvent($this->getRequest()->get('gallery_id', 0));
    }

    protected function eventContainsObject($event)
    {
        return $event->hasGallery();
    }

    protected function hydrateObjectForm($object)
    {

        // The "General" tab form
        $data = array(
            'id'           => $object->getId(),
            'locale'       => $object->getLocale(),
            'title'        => $object->getTitle(),
            'description'  => $object->getDescription(),
            'visible'      => $object->getVisible()
        );

        // Setup the object form
        return new GalleryModificationForm($this->getRequest(), "form", $data);
    }

    protected function getObjectFromEvent($event)
    {
        return $event->hasGallery() ? $event->getGallery() : null;
    }

    protected function getExistingObject()
    {
        $gallery = GalleryQuery::create()
            ->findOneById($this->getRequest()->get('gallery_id', 0));

        if (null !== $gallery) {
            $gallery->setLocale($this->getCurrentEditionLocale());
        }

        return $gallery;
    }

    protected function getObjectLabel($object)
    {
        return $object->getTitle();
    }

    protected function getObjectId($object)
    {
        return $object->getId();
    }

    protected function getEditionArguments()
    {
        return array(
            'gallery_id' => $this->getRequest()->get('gallery_id', 0),
            'current_tab' => $this->getRequest()->get('current_tab', 'general')
        );
    }

    protected function renderListTemplate($currentOrder)
    {
        return $this->render('gallery',
                array(
                    'gallery_order' => $currentOrder
        ));
    }

    protected function redirectToListTemplate()
    {
       return RedirectResponse::create(URL::getInstance()->absoluteUrl('/admin/module/Gallery'));
    }

    protected function renderEditionTemplate()
    {
        return $this->render('gallery-edit', $this->getEditionArguments());
    }

    protected function redirectToEditionTemplate()
    {
        $args = $this->getEditionArguments();

        return RedirectResponse::create(URL::getInstance()->absoluteUrl('/admin/module/Gallery/update?gallery_id='.$args['gallery_id'].'&current_tab='.$args['current_tab']));
    }

    /**
     * Online status toggle category
     */
    public function setToggleVisibilityAction()
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth($this->resourceCode, array(), AccessManager::UPDATE)) return $response;

        $event = new GalleryToggleVisibilityEvent($this->getExistingObject());

        try {
            $this->dispatch(GalleryEvent::GALLERY_TOGGLE_VISIBILITY, $event);
        } catch (\Exception $ex) {
            // Any error
            return $this->errorPage($ex);
        }

        // Ajax response -> no action
        return $this->nullResponse();
    }

}
