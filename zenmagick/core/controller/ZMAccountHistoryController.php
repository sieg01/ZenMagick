<?php
/*
 * ZenMagick - Extensions for zen-cart
 * Copyright (C) 2006 ZenMagick
 *
 * Portions Copyright (c) 2003 The zen-cart developers
 * Portions Copyright (c) 2003 osCommerce
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */
?>
<?php


/**
 * Request controller for account history page.
 *
 * @author mano
 * @package net.radebatz.zenmagick.controller
 * @version $Id$
 */
class ZMAccountHistoryController extends ZMController {

    // create new instance
    function ZMAccountHistoryController() {
        parent::__construct();
    }

    // create new instance
    function __construct() {
        $this->ZMAccountHistoryController();
    }

    function __destruct() {
    }


    /** API implementation */

    // process a GET request
    function processGet() {
    global $zm_request, $zm_crumbtrail, $zm_orders;

        $zm_crumbtrail->addCrumb("Account", zm_secure_href(FILENAME_ACCOUNT, '', false));
        $zm_crumbtrail->addCrumb(zm_title(false));

        $orders = $zm_orders->getOrdersForAccountId($zm_request->getAccountId());
        $resultList = new ZMResultList($orders, 10);
        $this->exportGlobal("zm_resultList", $resultList);

        return true;
    }

}

?>
