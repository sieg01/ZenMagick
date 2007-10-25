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
 * Request controller for gv redeem page.
 *
 * @author mano
 * @package org.zenmagick.rp.uip.controller
 * @version $Id$
 */
class ZMGvRedeemController extends ZMController {

    /**
     * Default c'tor.
     */
    function ZMGvRedeemController() {
        parent::__construct();
    }

    /**
     * Default c'tor.
     */
    function __construct() {
        $this->ZMGvRedeemController();
    }

    /**
     * Default d'tor.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * Process a HTTP GET request.
     * 
     * @return ZMView A <code>ZMView</code> that handles presentation or <code>null</code>
     * if the controller generates the contents itself.
     */
    function processGet() {
    global $zm_request, $zm_crumbtrail, $zm_coupons, $zm_messages;

        $zm_crumbtrail->addCrumb(zm_title(false));

        $gvredeem = $this->create('GVRedeem');
        $gvredeem->populate();
        $this->exportGlobal("zm_gvredeem", $gvredeem);

        if (!zm_is_empty($gvredeem->getCode())) {
            // only try to redeem if code given - people might browse the page without code parameter...
            $coupon = $zm_coupons->getCouponForCode($gvredeem->getCode());
            if (null != $coupon && ZM_COUPON_TYPPE_GV == $coupon->getType() && $zm_coupons->isCouponRedeemable($coupon->getId())) {
                // all good, set amount
                $gvredeem->setAmount($coupon->getAmount());
                // TODO: remote address
                $zm_coupons->redeemCoupon($coupon->getId(), $zm_request->getAccountId());
                $gvredeem->setRedeemed(true);
                // TODO: only for PHP4 and weak references
                $this->exportGlobal("zm_gvredeem", $gvredeem);
            } else {
                // not redeemable
                $zm_messages->error('The provided gift voucher code seems to be invalid!');
            }
        }

        return $this->findView();
    }

    /**
     * Redeem the given info.
     *
     * @param ZMGVRedeem redeem The redeem info.
     * @return ZMGVRedeem The updated redeem info.
     */
    function gvRedeem(&$gvredeem) {
        return $gvredeem;
    }
}

?>
