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

namespace Gallery;

use Propel\Runtime\Connection\ConnectionInterface;

use Thelia\Module\BaseModule;
use Thelia\Module\BaseModuleInterface;
use Thelia\Install\Database;

use Symfony\Component\Filesystem\Filesystem;

class Gallery extends BaseModule implements BaseModuleInterface
{
    public function postActivation(ConnectionInterface $con = null)
    {
        $database = new Database($con->getWrappedConnection());

        $database->insertSql(null, array(__DIR__ . '/Config/thelia.sql'));

        $fs = new Filesystem();

        if (!$fs->exists(THELIA_LOCAL_DIR.'/media/images/gallery')) $fs->mkdir(THELIA_LOCAL_DIR.'/media/images/gallery', 0777);
    }

    public function getCode()
    {
        return 'Gallery';
    }
}
