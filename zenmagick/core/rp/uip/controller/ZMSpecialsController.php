<?php
/*
 * ZenMagick - Extensions for zen-cart
 * Copyright (C) 2006-2009 ZenMagick
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
 * Request controller for specials.
 *
 * @author DerManoMann
 * @package org.zenmagick.rp.uip.controller
 * @version $Id$
 */
class ZMSpecialsController extends ZMController {

    /**
     * Create new instance.
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * {@inheritDoc}
     */
    public function processGet() {
        // crumbtrail handling
        ZMCrumbtrail::instance()->addCategoryPath(ZMRequest::getCategoryPathArray());
        ZMCrumbtrail::instance()->addManufacturer(ZMRequest::getManufacturerId());
        ZMCrumbtrail::instance()->addCrumb("Specials");

        $resultList = ZMLoader::make("ResultList");
        $resultSource = ZMLoader::make("ObjectResultSource", 'Product', ZMProducts::instance(), "getSpecials", 0);
        $resultList->setResultSource($resultSource);
        foreach (explode(',', ZMSettings::get('resultListProductFilter')) as $filter) {
            $resultList->addFilter(ZMLoader::make($filter));
        }
        foreach (explode(',', ZMSettings::get('resultListProductSorter')) as $sorter) {
            $resultList->addSorter(ZMLoader::make($sorter));
        }
        $resultList->setPageNumber(ZMRequest::getPageIndex());

        return $this->findView(null, array('zm_resultList' => $resultList));
    }

}

?>
