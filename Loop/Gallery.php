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
use Thelia\Type\TypeCollection;
use Thelia\Type;
use Thelia\Type\BooleanOrBothType;

use Gallery\Model\GalleryQuery;
/**
 *
 * Gallery loop, all params available :
 *
 * - id : can be an id (eq : 3) or a "string list" (eg: 3, 4, 5)
 * - not_empty : if value is 1, gallery must have at least 1 image
 * - visible : default 1, if you want gallery not visible put 0
 * - order : all value available :  'alpha', 'alpha_reverse', 'manual' (default), 'manual_reverse', 'random'
 * - exclude : all gallery id you want to exclude (as for id, an integer or a "string list" can be used)
 *
 * Class Gallery
 * @package Gallery\Loop
 * @author Marc LEMARCHAND <mlemarchand@hubchannel.fr>
 */
class Gallery extends BaseI18nLoop implements PropelSearchLoopInterface
{
    protected $timestampable = true;

    /**
     * @return ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('id'),
            Argument::createBooleanTypeArgument('not_empty', 0),
            Argument::createBooleanTypeArgument('with_prev_next_info', false),
            Argument::createBooleanTypeArgument('need_images_count', false),
            Argument::createBooleanOrBothTypeArgument('visible', 1),
            new Argument(
                'order',
                new TypeCollection(
                    new Type\EnumListType(array('id', 'id_reverse', 'alpha', 'alpha_reverse', 'manual', 'manual_reverse', 'visible', 'visible_reverse', 'random'))
                ),
                'manual'
            ),
            Argument::createIntListTypeArgument('exclude')
        );
    }

    public function buildModelCriteria()
    {
        $search = GalleryQuery::create();

        /* manage translations */
        $this->configureI18nProcessing($search, array('TITLE', 'DESCRIPTION'));

        $id = $this->getId();

        if (!is_null($id)) {
            $search->filterById($id, Criteria::IN);
        }

        $exclude = $this->getExclude();

        if (!is_null($exclude)) {
            $search->filterById($exclude, Criteria::NOT_IN);
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

        /* @todo */
        $notEmpty  = $this->getNot_empty();

        return $search;

    }

    public function parseResults(LoopResult $loopResult)
    {
        foreach ($loopResult->getResultDataCollection() as $gallery) {

            $loopResultRow = new LoopResultRow($gallery);

            $loopResultRow
                ->set("ID"                      , $gallery->getId())
                ->set("IS_TRANSLATED"           , $gallery->getVirtualColumn('IS_TRANSLATED'))
                ->set("LOCALE"                  , $this->locale)
                ->set("TITLE"                   , $gallery->getVirtualColumn('i18n_TITLE'))
                ->set("DESCRIPTION"             , $gallery->getVirtualColumn('i18n_DESCRIPTION'))
                ->set("VISIBLE"                 , $gallery->getVisible() ? "1" : "0")
                ->set("POSITION"                , $gallery->getPosition())
            ;

            if ($this->getNeedImagesCount()) {
                $loopResultRow->set("IMAGES_COUNT", $gallery->countAllImages());
            }

            if ($this->getBackend_context() || $this->getWithPrevNextInfo()) {
                // Find previous and next gallery
                $previous = GalleryQuery::create()
                    ->filterByPosition($gallery->getPosition(), Criteria::LESS_THAN)
                    ->orderByPosition(Criteria::DESC)
                    ->findOne()
                ;

                $next = GalleryQuery::create()
                    ->filterByPosition($gallery->getPosition(), Criteria::GREATER_THAN)
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
        }

        return $loopResult;

    }
}
