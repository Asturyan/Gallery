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

use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

/**
 *
 * Form allowing to process an image collection
 *
 * @package Gallery
 * @author Marc LEMARCHAND <mlemarchand@hubchannel.fr>
 *
 */
class GalleryImageModificationForm extends BaseForm
{

    protected function buildForm()
    {
        $this->formBuilder->add(
            'file',
            'file',
            array(
                'constraints' => array(
                    new Image(
                        array(
//                            'minWidth' => 200,
//                            'minHeight' => 200
                        )
                    )
                ),
                'label' => Translator::getInstance()->trans('Replace current image by this file'),
                'label_attr' => array(
                    'for' => 'file'
                )
            )
        );

        $this->formBuilder
            ->add(
                'title',
                'text',
                array(
                    'constraints' => array(),
                    'label' => Translator::getInstance()->trans('Title'),
                    'label_attr' => array(
                        'for' => 'title'
                    )
                )
            )
            ->add(
                'description',
                'text',
                array(
                    'constraints' => array(),
                    'label' => Translator::getInstance()->trans('Description'),
                    'label_attr' => array(
                        'for' => 'description'
                    )
                )
            )
            /*->add(
                'type',
                'gallery_type',
                array(
                    'empty_value' => 'Choose a type',
                    'label' => Translator::getInstance()->trans('Type'),
                    'label_attr' => array(
                        'for' => 'type'
                    )
                )
            )*/
            ->add(
                'type',
                'choice',
                array(
                    'choices'   => array(
                        'product'   => 'Product',
                        'category'  => 'Category',
                        'folder'    => 'Folder',
                        'content'   => 'Content',
                        'external'  => 'External'
                    ),
                    'empty_value' => 'Choose a type',
                    'label' => Translator::getInstance()->trans('Type'),
                    'label_attr' => array(
                        'for' => 'type'
                    )
                )
            )
            ->add(
                'type_id',
                'hidden',
                array(
                    'constraints' => array(),
                    'label' => Translator::getInstance()->trans('Type Id'),
                    'label_attr' => array(
                        'for' => 'type_id'
                    ),
                    'attr' => array('id'=>'type_id')
                )
            )
            ->add(
                'subtype_id',
                'hidden',
                array(
                    'constraints' => array(),
                    'label' => Translator::getInstance()->trans('SubType Id'),
                    'label_attr' => array(
                        'for' => 'subtype_id'
                    ),
                    'attr' => array('id'=>'subtype_id')
                )
            )
            ->add(
                'url',
                'text',
                array(
                    'constraints' => array(),
                    'label' => Translator::getInstance()->trans('URL'),
                    'label_attr' => array(
                        'for' => 'url'
                    )
                )
            )
            ->add(
                'visible',
                'integer',
                array(
                    'label' => Translator::getInstance()->trans("This image is online."),
                    'label_attr' => array(
                        'for' => 'visible_create'
                    )
            ))
            ->add("locale", "text", array(
                "constraints" => array(
                    new NotBlank()
                ),
                "label_attr" => array("for" => "locale_create")
            ))
        ;
    }
    /**
     * Get form name
     * This name must be unique
     *
     * @return string
     */
    public function getName()
    {
        return 'admin_gallery_image_modification';
    }
}
