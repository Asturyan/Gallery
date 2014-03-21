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

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\ActiveQuery\Criteria;

use Thelia\Core\Event\UpdateFilePositionEvent;
use Thelia\Core\HttpFoundation\Response;
use Thelia\Core\Security\AccessManager;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Log\Tlog;
use Thelia\Tools\Rest\ResponseRest;
use Thelia\Controller\Admin\BaseAdminController;

use Gallery\Event\GalleryImageEvent;
use Gallery\Event\GalleryImageCreateOrUpdateEvent;
use Gallery\Event\GalleryImageDeleteEvent;
use Gallery\Form\GalleryImageModificationForm;
use Gallery\Model\GalleryQuery;
use Gallery\Model\GalleryImage;
use Gallery\Model\GalleryImageQuery;
use Gallery\Tools\GalleryFileManager;

/**
 *
 * Control View and Action (Model) via Events
 * Control Gallery Images
 *
 * @package Gallery
 * @author  Marc LEMARCHAND <mlemarchand@hubchannel.fr>
 *
 */
class ImageController extends BaseAdminController
{

    private $resourceCode = 'gallery_image';

    /**
     * Manage how a image collection has to be saved
     *
     * @param int $parentId Parent id owning images being saved
     *
     * @return Response
     */
    public function saveImageAjaxAction($parentId)
    {
        $this->checkAuth($this->resourceCode, array(), AccessManager::UPDATE);
        $this->checkXmlHttpRequest();

        if ($this->getRequest()->isMethod('POST')) {

            /** @var UploadedFile $fileBeingUploaded */
            $fileBeingUploaded = $this->getRequest()->files->get('file');

            $fileManager = new GalleryFileManager();

            // Validate if file is too big
            if ($fileBeingUploaded->getError() == 1) {
                $message = $this->getTranslator()
                ->trans(
                    'File is too heavy, please retry with a file having a size less than %size%.',
                    array('%size%' => ini_get('post_max_size')),
                    'image'
                );

                return new ResponseRest($message, 'text', 403);
            }
            // Validate if it is a image or file
            if (!$fileManager->isImage($fileBeingUploaded->getMimeType())) {
                $message = $this->getTranslator()
                    ->trans(
                        'You can only upload images (.png, .jpg, .jpeg, .gif)',
                        array(),
                        'image'
                    );

                return new ResponseRest($message, 'text', 415);
            }

            $parentModel = GalleryQuery::create()->findPk($parentId);
            $imageModel = new GalleryImage();

            if ($parentModel === null || $imageModel === null || $fileBeingUploaded === null) {
                return new Response('', 404);
            }

            $defaultTitle = $parentModel->getTitle();
            $imageModel->setGalleryId($parentId);
            $imageModel->setTitle($defaultTitle);

            $imageCreateOrUpdateEvent = new GalleryImageCreateOrUpdateEvent(
                $parentId
            );
            $imageCreateOrUpdateEvent->setModelImage($imageModel);
            $imageCreateOrUpdateEvent->setUploadedFile($fileBeingUploaded);
            $imageCreateOrUpdateEvent->setParentName($parentModel->getTitle());

            // Dispatch Event to the Action
            $this->dispatch(
                GalleryImageEvent::IMAGE_SAVE,
                $imageCreateOrUpdateEvent
            );

            $this->adminLogAppend(
                $this->resourceCode,
                AccessManager::UPDATE,
                $this->container->get('thelia.translator')->trans(
                    'Saving images for %parentName% parent id %parentId% (gallery)',
                    array(
                        '%parentName%' => $imageCreateOrUpdateEvent->getParentName(),
                        '%parentId%' => $imageCreateOrUpdateEvent->getParentId()
                    ),
                    'image'
                )
            );

            return new ResponseRest(array('status' => true, 'message' => ''));
        }

        return new Response('', 404);
    }

    /**
     * Manage how a image list will be displayed in AJAX
     *
     * @param int $parentId Parent id owning images being saved
     *
     * @return Response
     */
    public function getImageListAjaxAction($parentId)
    {
        $this->checkAuth($this->resourceCode, array(), AccessManager::UPDATE);
        $this->checkXmlHttpRequest();
        $args = array('parentId' => $parentId);

        return $this->render('includes/gallery-image-upload-list-ajax', $args);
    }

    /**
     * Manage how an image list will be uploaded in AJAX
     *
     * @param int $parentId Parent id owning images being saved
     *
     * @return Response
     */
    public function getImageFormAjaxAction($parentId)
    {
        $this->checkAuth($this->resourceCode, array(), AccessManager::UPDATE);
        $this->checkXmlHttpRequest();
        $args = array('parentId' => $parentId);

        return $this->render('includes/gallery-image-upload-form', $args);
    }

    /**
     * Manage how an image is viewed
     *
     * @param int $imageId Parent id owning images being saved
     *
     * @return Response
     */
    public function viewImageAction($imageId)
    {
        if (null !== $response = $this->checkAuth($this->resourceCode, array(), AccessManager::UPDATE)) {
            return $response;
        }

        try {
            $fileManager = new GalleryFileManager();
            $image = GalleryImageQuery::create()->findPk($imageId);
            $redirectUrl = $fileManager->getRedirectionUrl($image->getGalleryId());

            return $this->render('gallery-image-edit', array(
                'imageId' => $imageId,
                'redirectUrl' => $redirectUrl,
                'formId' => 'admin.gallery.image.modification'
            ));
        } catch (\Exception $e) {
            $this->pageNotFound();
        }
    }

    /**
     * Manage how an image is updated
     *
     * @param int $imageId Parent id owning images being saved
     *
     * @return Response
     */
    public function updateImageAction($imageId)
    {
        if (null !== $response = $this->checkAuth($this->resourceCode, array(), AccessManager::UPDATE)) {
            return $response;
        }

        $message = false;

        $fileManager = new GalleryFileManager();
        $imageModification = new GalleryImageModificationForm($this->getRequest());

        try {
            $image = GalleryImageQuery::create()->findPk($imageId);
            $oldImage = clone $image;
            if (null === $image) {
                throw new \InvalidArgumentException(sprintf('%d image id does not exist', $imageId));
            }

            $form = $this->validateForm($imageModification);

            $event = $this->createImageEventInstance($image, $form->getData());
            $event->setOldModelImage($oldImage);

            $files = $this->getRequest()->files;
            $fileForm = $files->get($imageModification->getName());
            if (isset($fileForm['file'])) {
                $event->setUploadedFile($fileForm['file']);
            }

            $this->dispatch(GalleryImageEvent::IMAGE_UPDATE, $event);

            $imageUpdated = $event->getModelImage();

            $this->adminLogAppend($this->resourceCode, AccessManager::UPDATE, sprintf('Image with Ref %s (ID %d) modified', $imageUpdated->getTitle(), $imageUpdated->getId()));

            if ($this->getRequest()->get('save_mode') == 'close') {
                $redirectUrl = $fileManager->getRedirectionUrl($image->getGalleryId());
                $this->redirectToRoute($redirectUrl);
            } else {
                $this->redirectSuccess($imageModification);
            }

        } catch (FormValidationException $e) {
            $message = sprintf('Please check your input: %s', $e->getMessage());
        } catch (PropelException $e) {
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $message = sprintf('Sorry, an error occurred: %s', $e->getMessage().' '.$e->getFile());
        }

        if ($message !== false) {
            Tlog::getInstance()->error(sprintf('Error during image editing : %s.', $message));

            $imageModification->setErrorMessage($message);

            $this->getParserContext()
                ->addForm($imageModification)
                ->setGeneralError($message);
        }

        $redirectUrl = $fileManager->getRedirectionUrl($image->getGalleryId());

        return $this->render('gallery-image-edit', array(
            'imageId' => $imageId,
            'redirectUrl' => $redirectUrl,
            'formId' => 'admin.gallery.image.modification'
        ));
    }

    /**
     * Manage how a image has to be deleted (AJAX)
     *
     * @param int $imageId Parent id owning image being deleted
     *
     * @return Response
     */
    public function deleteImageAction($imageId)
    {
        $message = null;

        $this->checkAuth($this->resourceCode, array(), AccessManager::UPDATE);
        $this->checkXmlHttpRequest();

        $fileManager = new GalleryFileManager();
        $imageModelQuery = new GalleryImageQuery();
        $model = $imageModelQuery->findPk($imageId);

        if ($model == null) {
            return $this->pageNotFound();
        }

        // Feed event
        $imageDeleteEvent = new GalleryImageDeleteEvent(
            $model
        );

        // Dispatch Event to the Action
        try {
            $this->dispatch(
                GalleryImageEvent::IMAGE_DELETE,
                $imageDeleteEvent
            );

            $this->adminLogAppend(
                $this->resourceCode,
                AccessManager::UPDATE,
                $this->container->get('thelia.translator')->trans(
                    'Deleting image for %id% with parent id %parentId%',
                    array(
                        '%id%' => $imageDeleteEvent->getImageToDelete()->getId(),
                        '%parentId%' => $imageDeleteEvent->getImageToDelete()->getGalleryId(),
                    ),
                    'image'
                )
            );
        } catch (\Exception $e) {
            $this->adminLogAppend(
                $this->resourceCode,
                AccessManager::UPDATE,
                $this->container->get('thelia.translator')->trans(
                    'Fail to delete image for %id% with parent id %parentId% (Exception : %e%)',
                    array(
                        '%id%' => $imageDeleteEvent->getImageToDelete()->getId(),
                        '%parentId%' => $imageDeleteEvent->getImageToDelete()->getGalleryId(),
                        '%e%' => $e->getMessage()
                    ),
                    'image'
                )
            );
            $message = $this->getTranslator()
                ->trans(
                    'Fail to delete image for %id% with parent id %parentId% (Exception : %e%)',
                    array(
                        '%id%' => $imageDeleteEvent->getImageToDelete()->getId(),
                        '%parentId%' => $imageDeleteEvent->getImageToDelete()->getGalleryId(),
                        '%e%' => $e->getMessage()
                    ),
                    'image'
                );
        }

        if (null === $message) {
            $message = $this->getTranslator()
                ->trans(
                    'Images deleted successfully',
                    array(),
                    'image'
                );
        }

        return new Response($message);
    }

    public function updateImagePositionAction($parentId)
    {
        $message = null;

        $imageId = $this->getRequest()->request->get('image_id');
        $position = $this->getRequest()->request->get('position');

        $this->checkAuth($this->resourceCode, array(), AccessManager::UPDATE);
        $this->checkXmlHttpRequest();

        $fileManager = new GalleryFileManager();
        $imageModelQuery = new GalleryImageQuery();
        $model = $imageModelQuery->findPk($imageId);

        if ($model === null || $position === null) {
            return $this->pageNotFound();
        }

        // Feed event
        $imageUpdateImagePositionEvent = new UpdateFilePositionEvent(
            new GalleryImageQuery(),
            $imageId,
            UpdateFilePositionEvent::POSITION_ABSOLUTE,
            $position
        );

        // Dispatch Event to the Action
        try {
            $this->dispatch(
                GalleryImageEvent::IMAGE_UPDATE_POSITION,
                $imageUpdateImagePositionEvent
            );
        } catch (\Exception $e) {

            $message = $this->getTranslator()
                ->trans(
                    'Fail to update image position',
                    array(),
                    'image'
                ) . $e->getMessage();
        }

        if (null === $message) {
            $message = $this->getTranslator()
                ->trans(
                    'Image position updated',
                    array(),
                    'image'
                );
        }

        return new Response($message);
    }

    /**
     * Log error message
     *
     * @param string     $action  Creation|Update|Delete
     * @param string     $message Message to log
     * @param \Exception $e       Exception to log
     *
     * @return $this
     */
    protected function logError($action, $message, $e)
    {
        Tlog::getInstance()->error(
            sprintf(
                'Error during ' . $action . ' process : %s. Exception was %s',
                $message,
                $e->getMessage()
            )
        );

        return $this;
    }

    /**
     * Create GalleryImage Event instance
     *
     * @param GalleryImage $model Image model
     * @param array        $data  Post data
     *
     * @return GalleryImageCreateOrUpdateEvent
     */
    protected function createImageEventInstance($model, $data)
    {
        $imageCreateEvent = new GalleryImageCreateOrUpdateEvent(null);
        $model->setLocale($data['locale']);

        if (isset($data['title'])) {
            $model->setTitle($data['title']);
        }
        if (isset($data['type'])) {
            $model->setType($data['type']);
        }
        if (isset($data['description'])) {
            $model->setDescription($data['description']);
        }
        if (isset($data['file'])) {
            $model->setFile($data['file']);
        }
        if (isset($data['type_id'])) {
            $model->setTypeId($data['type_id']);
        }

        if (isset($data['subtype_id'])) {
            $model->setSubTypeId($data['subtype_id']);
        }

        if (isset($data['visible'])) {
            $model->setVisible($data['visible']);
        }
        
        if (isset($data['url'])) {
            $model->setUrl($data['url']);
        }

        $imageCreateEvent->setModelImage($model);

        return $imageCreateEvent;
    }

    public function getAvailableTypesAction($type)
    {
        $result = array();

        $object = ucfirst($type);
        $queryClass   = sprintf("\Thelia\Model\%sQuery", $object);

        $method = new \ReflectionMethod($queryClass, 'create');
        $search = $method->invoke(null); // Static !

        $items = $search->joinWithI18n($this->getCurrentEditionLocale())->find();

        if ($items !== null) {

            foreach ($items as $item) {
                $result[] = array('id' => $item->getId(), 'title' => $item->getTitle());
            }
        }

        return $this->jsonResponse(json_encode($result));
    }

    public function getAvailableSubTypesAction($parent, $parentId, $type)
    {
        $result = array();

        $object = ucfirst($parent);
        $queryClass = sprintf("\Thelia\Model\%sQuery", $object);
        $filterMethod = sprintf("filterBy%s", $object);

        $method = new \ReflectionMethod($queryClass, 'create');
        $search = $method->invoke(null);

        $items = $search->filterById($parentId)->find();

        if ($items !== null) {
            $object = ucfirst($type);
            $queryClass = sprintf("\Thelia\Model\%sQuery", $object);
            $method = new \ReflectionMethod($queryClass, 'create');
            $search = $method->invoke(null);
            
            $method = new \ReflectionMethod($queryClass, $filterMethod);
            $method->invoke($search, $items, Criteria::IN);

            $list = $search
                ->joinWithI18n($this->getCurrentEditionLocale())
                ->find();

            if ($list !== null) {
                foreach ($list as $item) {
                    $result[] = array('id' => $item->getId(), 'title' => $item->getTitle());
                }
            }
        }

        return $this->jsonResponse(json_encode($result));
    }

}
