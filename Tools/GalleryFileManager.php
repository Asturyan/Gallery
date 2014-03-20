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
namespace Gallery\Tools;

use Thelia\Tools\FileManager;

/**
 *
 * Gallery File Manager
 *
 * Extend File Manager
 *
 * @package Gallery\Tools
 * @author Marc LEMARCHAND <mlemarchand@hubchannel.fr>
 */
class GalleryFileManager extends FileManager
{
    CONST TYPE_GALLERY  = 'gallery';

    CONST FILE_TYPE_IMAGES   = 'images';

    /**
     * Copy UploadedFile into the server storage directory
     *
     * @param int                                                                                                                 $parentId     Parent id
     * @param GalleryImage                                                                                                        $model        Model saved
     * @param UploadedFile                                                                                                        $uploadedFile Ready to be uploaded file
     *
     * @throws \Thelia\Exception\ImageException
     * @return UploadedFile
     */
    public function copyUploadedFile($parentId, $model, $uploadedFile)
    {
        $newUploadedFile = null;
        if ($uploadedFile !== null) {
            $directory = $this->getUploadDir();
            $fileName = $this->renameFile($model->getId(), $uploadedFile);

            $newUploadedFile = $uploadedFile->move($directory, $fileName);
            $model->setFile($fileName);

            if (!$model->save()) {
                throw new ImageException(
                    sprintf(
                        '%s failed to be saved (image file)',
                        $model->getFile()
                    )
                );
            }
        }

        return $newUploadedFile;
    }

    /**
     * Save image into the database
     *
     * @param GalleryImageCreateOrUpdateEvent                     $event      Image event
     * @param GalleryImage                                        $modelImage Image to save
     *
     * @return int                              Nb lines modified
     * @throws \Thelia\Exception\ImageException
     * @todo refactor make all pictures using propel inheritance and factorise image behaviour into one single clean action
     */
    public function saveImage(GalleryImageCreateOrUpdateEvent $event, $modelImage)
    {
        $nbModifiedLines = 0;

        if ($modelImage->getFile() !== null) {
            $modelImage->setGalleryId($event->getParentId());

            $nbModifiedLines = $modelImage->save();
            if (!$nbModifiedLines) {
                throw new ImageException(
                    sprintf(
                        'Image %s failed to be saved (image content)',
                        $modelImage->getFile()
                    )
                );
            }
        }

        return $nbModifiedLines;
    }

    /**
     * Sanitizes a filename replacing whitespace with dashes
     *
     * Removes special characters that are illegal in filenames on certain
     * operating systems and special characters requiring special escaping
     * to manipulate at the command line.
     *
     * @param string $string The filename to be sanitized
     *
     * @return string The sanitized filename
     */
    public function sanitizeFileName($string)
    {
        return strtolower(preg_replace('/[^a-zA-Z0-9-_\.]/', '', $string));
    }

    /**
     * Delete image from file storage and database
     *
     * @todo refactor make all pictures using propel inheritance and factorise image behaviour into one single clean action
     */
    public function deleteFile($model)
    {
        $url = $this->getUploadDir() . '/' . $model->getFile();
        unlink(str_replace('..', '', $url));
        $model->delete();
    }

    /**
     * Get image upload dir
     *
     *
     * @return string Uri
     */
    public function getUploadDir()
    {
        
        $uri = THELIA_LOCAL_DIR . 'media/images/gallery';

        return $uri;

    }

    /**
     * Deduce image redirecting URL from parent type
     *
     * @param int    $parentId   Parent id
     *
     * @return string
     */
    public function getRedirectionUrl($parentId)
    {
        $uri = '/admin/module/Gallery/update?gallery_id=' . $parentId . '&current_tab=images';

        return $uri;

    }

}
