<?php
/*
 * ZenMagick - Extensions for zen-cart
 * Copyright (C) 2006,2007 ZenMagick
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
 * Banner.
 *
 * @author mano
 * @package net.radebatz.zenmagick.service
 * @version $Id$
 */
class ZMBanners extends ZMService {
    var $banners_;


    /**
     * Default c'tor.
     */
    function ZMBanners() {
        parent::__construct();

        $this->banners_ = array();
    }

    /**
     * Default c'tor.
     */
    function __construct() {
        $this->ZMBanners();
    }

    /**
     * Default d'tor.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * Get a banner for the given (zen-cart) index.
     *
     * <p>The index is based on the zen-cart defines for banner; eg: <code>SHOW_BANNERS_GROUP_SET3</code>.
     * Here the index would be three.</p>
     *
     * @param integer index The zen-cart index.
     * @return mixed A <code>ZMBanner</code> instance or <code>null</code>.
     */
    function getBannerForIndex($index) {
        $list = $this->_getBannerForName(zm_setting('bannerGroup'.$index));
        return 0 < count($list) ? $list[0] : null;
    }


    /**
     * Get all banner according to zen-cart configuration.
     *
     * <p>this will return all banner as configured using the zen-cart define <code>SHOW_BANNERS_GROUP_SET_ALL</code>.
     *
     * @return array A list of <code>ZMBanner</code> instances.
     */
    function getAllBanners() { return $this->_getBannerForName(zm_setting('bannerGroupAll'), true); }


    /**
     * Get one (random) or more banner based on the given banner group(s).
     *
     * <p>If <code>$all</code> is set to <code>true, all matching banners will be returned.</p>
     * <p>Thus, <code>getAllBanner()</code> translates into <code>getBannerForName(SHOW_BANNERS_GROUP_SET_ALL, true)</code>.</p>
     *
     * @param string identifiers One ore more identifiers, separated by ':'.
     * @param boolean all If set to <code>true</code>, all banners will be returned, ordered in 
     *  the configured sort order.
     * @return array A list of <code>ZMBanner</code> instances.
     */
    function _getBannerForName($identifiers, $all=false) { 
    global $zm_request;

        $db = $this->getDB();
        // filter the banners we are interested in
        $filter = '';
        if ($zm_request->isSecure()) {
            $filter = $db->bindVars(" and banners_on_ssl= :true ", ":true", 1, "integer");
        }

        // handle multiple identifiers
        $identifierList = explode(':', $identifiers);
        if (0 < count($identifierList)) {
            $filter .= " and (";
            $first = true;
            foreach ($identifierList as $identifier) {
                if (!$first) $filter .= " or ";
                $filter .= $db->bindVars("banners_group = :identifier", ":identifier", $identifier, "string");
                $first = false;
            }
            $filter .= ")";
        }
        $orderBy = $all ? " order by banners_sort_order" : " order by rand()";
        $query = "select banners_id, banners_title, banners_image, banners_html_text, banners_open_new_windows, banners_url
                  from " . TABLE_BANNERS . "
                  where status = 1 " .
                  $filter . $orderBy;
        $results = $db->Execute($query);

        $banners = array();
        while (!$results->EOF) {
            array_push($banners, $this->_newBanner($results->fields));
            $results->MoveNext();
        }

        return $banners;
    }


    /**
     * Get a banner for the given id.
     *
     * @param integer id The banner id.
     * @return mixed A <code>ZMBanner</code> instance or <code>null</code>.
     */
    function &getBannerForId($id) { 
    global $zm_request;

        $db = $this->getDB();
        // filter the banners we are interested in
        $filter = '';
        if ($zm_request->isSecure()) {
            $filter = $db->bindVars(" and banners_on_ssl= :true ", ":true", 1, "integer");
        }

        $query = "select banners_id, banners_title, banners_image, banners_html_text, banners_open_new_windows, banners_url
                  from " . TABLE_BANNERS . "
                  where status = 1 and banners_id = :id " .
                  $filter;
        $query = $db->bindVars($query, ":id", $id, "integer");
        $results = $db->Execute($query);

        $banner = null;
        if (0 < $results->RecordCount()) {
            $banner = $this->_newBanner($results->fields);
        }

        return $banner;
    }



    // build banner
    function &_newBanner($fields) {
        $banner = $this->create("ZMBanner");
        $banner->id_ = $fields['banners_id'];
        $banner->title_ = $fields['banners_title'];
        $banner->image_ = $fields['banners_image'];
        $banner->text_ = $fields['banners_html_text'];
        $banner->isNewWin_ = 1 == $fields['banners_open_new_windows'];
        $banner->url_ = $fields['banners_url'];
        return $banner;
    }

}

?>
