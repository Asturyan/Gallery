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
namespace Gallery\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
/**
 *
 * @author Marc LEMARCHAND <mlemarchand@hubchannel.fr>
 *
 */

class GalleryType extends AbstractType
{
    private $galleryTypeChoices;

    public function __construct(array $galleryTypeChoices)
    {
        echo 'test';die();$this->galleryTypeChoices = $galleryTypeChoices;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        echo 'test';die();
        $resolver->setDefaults(array(
            'choices' => $this->galleryTypeChoices,
        ));
    }
    
    public function getParent()
    {
        return 'choice';
    }
    
    public function getName()
    {
        return 'gallery_type';
    }
}
