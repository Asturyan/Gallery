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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Thelia\Action\Image;
use Gallery\Event\GalleryImageEvent;
use Gallery\Event\GalleryImageCreateOrUpdateEvent;
use Gallery\Event\GalleryImageDeleteEvent;
use Thelia\Core\Event\Image\ImageEvent;
use Thelia\Core\Event\UpdateFilePositionEvent;
use Thelia\Model\ConfigQuery;
use Gallery\Tools\GalleryFileManager;
use Thelia\Tools\URL;

use Imagine\Image\Color;
use Thelia\Exception\ImageException;

/**
 *
 * Gallery Image management actions. This class handles image processing and caching.
 *
 * Extend Image Class
 *
 * @package Gallery\Action
 * @author Marc LEMARCHAND <mlemarchand@hubchannel.fr>
 *
 */
class GalleryImage extends Image implements EventSubscriberInterface
{

    /**
     * Process image and write the result in the image cache.
     *
     * If the image already exists in cache, the cache file is immediately returned, without any processing
     * If the original (full resolution) image is required, create either a symbolic link with the
     * original image in the cache dir, or copy it in the cache dir.
     *
     * This method updates the cache_file_path and file_url attributes of the event
     *
     * @param  \Thelia\Core\Event\Image\ImageEvent $event
     * @throws \InvalidArgumentException,          ImageException
     */
    public function processImage(ImageEvent $event)
    {

        $source_file = $event->getSourceFilepath();

        if (null == $source_file) {
            throw new \InvalidArgumentException("Cache sub-directory and source file path cannot be null");
        }

        // Find cached file path
        $cacheFilePath = $this->getCacheFilePath($subdir, $source_file, $event->isOriginalImage(), $event->getOptionsHash());

        $originalImagePathInCache = $this->getCacheFilePath($subdir, $source_file, true);

        if (! file_exists($cacheFilePath)) {

            if (! file_exists($source_file)) {
                throw new ImageException(sprintf("Source image file %s does not exists.", $source_file));
            }

            // Create a chached version of the original image in the web space, if not exists

            if (! file_exists($originalImagePathInCache)) {

                $mode = ConfigQuery::read('original_image_delivery_mode', 'symlink');

                if ($mode == 'symlink') {
                    if (false == symlink($source_file, $originalImagePathInCache)) {
                         throw new ImageException(sprintf("Failed to create symbolic link for %s in %s image cache directory", basename($source_file), $subdir));
                    }
                } else {// mode = 'copy'
                    if (false == @copy($source_file, $originalImagePathInCache)) {
                        throw new ImageException(sprintf("Failed to copy %s in %s image cache directory", basename($source_file), $subdir));
                    }
                }
            }

            // Process image only if we have some transformations to do.
            if (! $event->isOriginalImage()) {

                // We have to process the image.
                $imagine = $this->createImagineInstance();

                $image = $imagine->open($source_file);

                if ($image) {

                    $background_color = $event->getBackgroundColor();

                    if ($background_color != null) {
                        $bg_color = new Color($background_color);
                    } else
                        $bg_color = null;

                    // Apply resize
                    $image = $this->applyResize($imagine, $image, $event->getWidth(), $event->getHeight(), $event->getResizeMode(), $bg_color);

                    // Rotate if required
                    $rotation = intval($event->getRotation());

                    if ($rotation != 0)
                        $image->rotate($rotation, $bg_color);

                    // Flip
                    // Process each effects
                    foreach ($event->getEffects() as $effect) {

                        $effect = trim(strtolower($effect));

                        $params = explode(':', $effect);

                        switch ($params[0]) {

                        case 'greyscale':
                        case 'grayscale':
                            $image->effects()->grayscale();
                            break;

                        case 'negative':
                            $image->effects()->negative();
                            break;

                        case 'horizontal_flip':
                        case 'hflip':
                            $image->flipHorizontally();
                            break;

                        case 'vertical_flip':
                        case 'vflip':
                            $image-> flipVertically();
                            break;

                        case 'gamma':
                            // Syntax: gamma:value. Exemple: gamma:0.7
                            if (isset($params[1])) {
                                $gamma = floatval($params[1]);

                                $image->effects()->gamma($gamma);
                            }
                            break;

                        case 'colorize':
                            // Syntax: colorize:couleur. Exemple: colorize:#ff00cc
                            if (isset($params[1])) {
                                $the_color = new Color($params[1]);

                                $image->effects()->colorize($the_color);
                            }
                            break;
                        }
                    }

                    $quality = $event->getQuality();

                    if (is_null($quality)) $quality = ConfigQuery::read('default_image_quality_percent', 75);

                    $image->save(
                            $cacheFilePath,
                            array('quality' => $quality)
                     );
                } else {
                    throw new ImageException(sprintf("Source file %s cannot be opened.", basename($source_file)));
                }
            }
        }

        // Compute the image URL
        $processed_image_url = $this->getCacheFileURL($subdir, basename($cacheFilePath));

        // compute the full resolution image path in cache
        $original_image_url = $this->getCacheFileURL($subdir, basename($originalImagePathInCache));

        // Update the event with file path and file URL
        $event->setCacheFilepath($cacheFilePath);
        $event->setCacheOriginalFilepath($originalImagePathInCache);

        $event->setFileUrl(URL::getInstance()->absoluteUrl($processed_image_url, null, URL::PATH_TO_FILE));
        $event->setOriginalFileUrl(URL::getInstance()->absoluteUrl($original_image_url, null, URL::PATH_TO_FILE));
    }

    /**
     * Take care of saving image in the database and file storage
     *
     * @param \Gallery\Event\ImageCreateOrUpdateEvent $event Image event
     *
     * @throws \Thelia\Exception\ImageException
     * @todo refactor make all pictures using propel inheritance and factorise image behaviour into one single clean action
     */
    public function saveImage(GalleryImageCreateOrUpdateEvent $event)
    {
        $fileManager = new GalleryFileManager();
        $model = $event->getModelImage();

        $nbModifiedLines = $model->save();
        $event->setModelImage($model);

        if (!$nbModifiedLines) {
            throw new ImageException(
                sprintf(
                    'Image "%s" with parent id %s failed to be saved',
                    $event->getParentName(),
                    $event->getParentId()
                )
            );
        }

        $newUploadedFile = $fileManager->copyUploadedFile($event->getParentId(), $event->getModelImage(), $event->getUploadedFile());
        $event->setUploadedFile($newUploadedFile);
    }

    /**
     * Take care of updating image in the database and file storage
     *
     * @param GalleryImageCreateOrUpdateEvent $event Image event
     *
     * @throws \Thelia\Exception\ImageException
     * @todo refactor make all pictures using propel inheritance and factorise image behaviour into one single clean action
     */
    public function updateImage(GalleryImageCreateOrUpdateEvent $event)
    {
        $fileManager = new GalleryFileManager();
        // Copy and save file
        if ($event->getUploadedFile()) {
            // Remove old picture file from file storage
            $url = $fileManager->getUploadDir() . '/' . $event->getOldModelImage()->getFile();
            unlink(str_replace('..', '', $url));

            $newUploadedFile = $fileManager->copyUploadedFile($event->getParentId(), $event->getModelImage(), $event->getUploadedFile());
            $event->setUploadedFile($newUploadedFile);
        }

        // Update image modifications
        $event->getModelImage()->save();
        $event->setModelImage($event->getModelImage());
    }

    public function updatePosition(UpdateFilePositionEvent $event)
    {
        $this->genericUpdatePosition($event->getQuery(), $event);
    }

    /**
     * Take care of deleting image in the database and file storage
     *
     * @param GalleryImageDeleteEvent $event Image event
     *
     * @throws \Exception
     * @todo refactor make all pictures using propel inheritance and factorise image behaviour into one single clean action
     */
    public function deleteImage(GalleryImageDeleteEvent $event)
    {
        $fileManager = new GalleryFileManager();

        $fileManager->deleteFile($event->getImageToDelete());
    }

    public static function getSubscribedEvents()
    {
        return array(
            GalleryImageEvent::IMAGE_PROCESS => array("processImage", 128),
            GalleryImageEvent::IMAGE_DELETE => array("deleteImage", 128),
            GalleryImageEvent::IMAGE_SAVE => array("saveImage", 128),
            GalleryImageEvent::IMAGE_UPDATE => array("updateImage", 128),
            GalleryImageEvent::IMAGE_UPDATE_POSITION => array("updatePosition", 128),
        );
    }
}
