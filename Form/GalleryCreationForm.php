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

namespace Gallery\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

/**
 *
 * Form allowing to process a gallery
 *
 * @package Gallery
 * @author Marc LEMARCHAND <mlemarchand@hubchannel.fr>
 *
 */
class GalleryCreationForm extends BaseForm
{
    protected function buildForm()
    {
        $this->formBuilder
            ->add("title", "text", array(
                "constraints" => array(
                    new NotBlank()
                ),
                "label" => Translator::getInstance()->trans("Gallery title *", [], 'gallery'),
                "label_attr" => array(
                    "for" => "title"
                )
            ))
            ->add("locale", "text", array(
                "constraints" => array(
                    new NotBlank()
                ),
               "label_attr" => array("for" => "locale_create")
            ))
            ->add("visible", "integer", array(
                "label" => Translator::getInstance()->trans("This gallery is online.", [], 'gallery'),
                "label_attr" => array("for" => "visible_create")
            ))
        ;
    }

    public function getName()
    {
        return "admin_gallery_creation";
    }
}
