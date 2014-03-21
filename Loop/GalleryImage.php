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

namespace Gallery\Loop;

use Propel\Runtime\ActiveQuery\Criteria;

use Thelia\Core\Template\Element\BaseI18nLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Model\ConfigQuery;
use Thelia\Log\Tlog;
use Thelia\Type\TypeCollection;
use Thelia\Type\BooleanOrBothType;
use Thelia\Type\EnumListType;
use Thelia\Type\EnumType;

use Gallery\Event\GalleryImageEvent;
use Gallery\Model\GalleryImageQuery;
/**
 *
 * List loop
 *
 * Class List
 * @package Gallery\Loop
 * @author Marc LEMARCHAND <mlemarchand@hubchannel.fr>
 */
/**
 *
 * Gallery Image loop
 *
 * Class Gallery
 * @package Gallery\Loop
 * @author Marc LEMARCHAND <mlemarchand@hubchannel.fr>
 */
class GalleryImage extends BaseI18nLoop implements PropelSearchLoopInterface
{
    protected $timestampable = true;

    /**
     * @return ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        $collection = new ArgumentCollection(
            Argument::createIntListTypeArgument('id'),
            Argument::createBooleanTypeArgument('with_prev_next_info', false),
            Argument::createIntListTypeArgument('exclude'),
            Argument::createBooleanOrBothTypeArgument('visible', 1),
            new Argument(
                'order',
                new TypeCollection(
                    new EnumListType(array('id', 'id_reverse', 'alpha', 'alpha_reverse', 'manual', 'manual_reverse', 'visible', 'visible_reverse', 'random'))
                ),
                'manual'
            ),
            Argument::createIntTypeArgument('lang'),

            Argument::createIntTypeArgument('width'),
            Argument::createIntTypeArgument('height'),
            Argument::createIntTypeArgument('rotation', 0),
            Argument::createAnyTypeArgument('background_color'),
            Argument::createIntTypeArgument('quality'),
            new Argument(
                'resize_mode',
                new TypeCollection(
                    new EnumType(array('crop', 'borders', 'none'))
                ),
                'none'
            ),
            Argument::createAnyTypeArgument('effects'),
            Argument::createIntTypeArgument('gallery_id'),
            Argument::createBooleanTypeArgument('force_return', true)
        );

        return $collection;
    }

    public function buildModelCriteria()
    {
        $search = GalleryImageQuery::create();

        /* manage translations */
        $this->configureI18nProcessing($search, array('TITLE', 'DESCRIPTION'));

        $id = $this->getId();

        if (!is_null($id)) {
            $search->filterById($id, Criteria::IN);
        }

        $gallery_id = $this->getGalleryId();

        if (!is_null($gallery_id)) {
            $search->filterByGalleryId($gallery_id, Criteria::IN);
        }

        $visible = $this->getVisible();

        if ($visible !== BooleanOrBothType::ANY) $search->filterByVisible($visible ? 1 : 0);

        $orders  = $this->getOrder();

        foreach ($orders as $order) {
            switch ($order) {
                case "id":
                    $search->orderById(Criteria::ASC);
                    break;
                case "id_reverse":
                    $search->orderById(Criteria::DESC);
                    break;
                case "alpha":
                    $search->addAscendingOrderByColumn('i18n_TITLE');
                    break;
                case "alpha_reverse":
                    $search->addDescendingOrderByColumn('i18n_TITLE');
                    break;
                case "manual_reverse":
                    $search->orderByPosition(Criteria::DESC);
                    break;
                case "manual":
                    $search->orderByPosition(Criteria::ASC);
                    break;
                case "visible":
                    $search->orderByVisible(Criteria::ASC);
                    break;
                case "visible_reverse":
                    $search->orderByVisible(Criteria::DESC);
                    break;
                case "random":
                    $search->clearOrderByColumns();
                    $search->addAscendingOrderByColumn('RAND()');
                    break(2);
                    break;
            }
        }

        return $search;

    }

    public function parseResults(LoopResult $loopResult)
    {
        // Create image processing event
        $event = new GalleryImageEvent($this->request);

        // Prepare tranformations
        $width = $this->getWidth();
        $height = $this->getHeight();
        $rotation = $this->getRotation();
        $background_color = $this->getBackgroundColor();
        $quality = $this->getQuality();
        $effects = $this->getEffects();

        if (! is_null($effects)) {
            $effects = explode(',', $effects);
        }

        switch ($this->getResizeMode()) {
            case 'crop' :
                $resize_mode = \Thelia\Action\Image::EXACT_RATIO_WITH_CROP;
                break;

            case 'borders' :
                $resize_mode = \Thelia\Action\Image::EXACT_RATIO_WITH_BORDERS;
                break;

            case 'none' :
            default:
                $resize_mode = \Thelia\Action\Image::KEEP_IMAGE_RATIO;

        }

        foreach ($loopResult->getResultDataCollection() as $result) {

            // Setup required transformations
            if (! is_null($width)) $event->setWidth($width);
            if (! is_null($height)) $event->setHeight($height);
            $event->setResizeMode($resize_mode);
            if (! is_null($rotation)) $event->setRotation($rotation);
            if (! is_null($background_color)) $event->setBackgroundColor($background_color);
            if (! is_null($quality)) $event->setQuality($quality);
            if (! is_null($effects)) $event->setEffects($effects);

            // Put source image file path
            $source_filepath = sprintf("%s%s/%s/%s",
                THELIA_ROOT,
                ConfigQuery::read('images_library_path', 'local/media/images'),
                'gallery',
                $result->getFile()
            );

            $event->setSourceFilepath($source_filepath);
             $event->setCacheSubdirectory('gallery');

            try {
                // Dispatch image processing event
                $this->dispatcher->dispatch(GalleryImageEvent::IMAGE_PROCESS, $event);

                $loopResultRow = new LoopResultRow($result);
                
                $currentType = '';
                
                if ($result->getType()) {
                    if ($result->getType() != 'external') {
                        if ($result->getSubTypeId()) {
                            $object = ucfirst($result->getType() == 'product'?'category':'folder');
                            $queryClass = sprintf("\Thelia\Model\%sQuery", $object);
        
                            $method = new \ReflectionMethod($queryClass, 'create');
                            $search = $method->invoke(null);
                    
                            $item = $search->joinWithI18n($this->locale)->filterById($result->getSubTypeId())->findOne();
                            
                            $currentType .= $item->getTitle(). ' > ';
                        }
                        
                        $object = ucfirst($result->getType());
                        $queryClass = sprintf("\Thelia\Model\%sQuery", $object);
    
                        $method = new \ReflectionMethod($queryClass, 'create');
                        $search = $method->invoke(null);
                
                        $item = $search->joinWithI18n($this->locale)->filterById($result->getTypeId())->findOne();
                        
                        $currentType .= $item->getTitle();
                    } else {
                        $currentType .= $result->getUrl();
                        
                    }
                }

                $loopResultRow
                    ->set("ID"                  , $result->getId())
                    ->set("LOCALE"              , $this->locale)
                    ->set("IMAGE_URL"           , $event->getFileUrl())
                    ->set("ORIGINAL_IMAGE_URL"  , $event->getOriginalFileUrl())
                    ->set("IMAGE_PATH"          , $event->getCacheFilepath())
                    ->set("ORIGINAL_IMAGE_PATH" , $source_filepath)
                    ->set("TITLE"               , $result->getVirtualColumn('i18n_TITLE'))
                    ->set("DESCRIPTION"         , $result->getVirtualColumn('i18n_DESCRIPTION'))
                    ->set("TYPE"                , $result->getType())
                    ->set("SUBTYPE_ID"          , $result->getSubTypeId())
                    ->set("TYPE_ID"             , $result->getTypeId())
                    ->set("URL"                 , $result->getUrl())
                    ->set("CURRENT_TYPE"        , $currentType)
                    ->set("VISIBLE"             , $result->getVisible() ? "1" : "0")
                    ->set("POSITION"            , $result->getPosition())
                ;
                
                if ($this->getBackend_context() || $this->getWithPrevNextInfo()) {
                    // Find previous and next image gallery
                    $previous = GalleryImageQuery::create()
                        ->filterByPosition($result->getPosition(), Criteria::LESS_THAN)
                        ->filterByGalleryId($result->getGalleryId())
                        ->orderByPosition(Criteria::DESC)
                        ->findOne()
                    ;

                    $next = GalleryImageQuery::create()
                        ->filterByPosition($result->getPosition(), Criteria::GREATER_THAN)
                        ->filterByGalleryId($result->getGalleryId())
                        ->orderByPosition(Criteria::ASC)
                        ->findOne()
                    ;
    
                    $loopResultRow
                        ->set("HAS_PREVIOUS"            , $previous != null ? 1 : 0)
                        ->set("HAS_NEXT"                , $next != null ? 1 : 0)
    
                        ->set("PREVIOUS"                , $previous != null ? $previous->getId() : -1)
                        ->set("NEXT"                    , $next != null ? $next->getId() : -1)
                    ;
                }

                $loopResult->addRow($loopResultRow);
            } catch (\Exception $ex) {
                // Ignore the result and log an error
                Tlog::getInstance()->addError("Failed to process image in image loop: ", $ex->getMessage(), " - Arguments:", $this->args);
            }

        }

        return $loopResult;

    }
}
