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
 * A form field for a payment type;
 *
 * @author mano
 * @package org.zenmagick.model.order
 * @version $Id$
 */
class ZMPaymentField extends ZMModel {
    var $label_;
    var $html_;


    /**
     * Create new payment (input) field.
     *
     * @param string label The field label.
     * @param string html The (input) field HTML.
     */
    function ZMPaymentField($label, $html) {
        parent::__construct();

        $this->label_ = $label;
        $this->html_ = $html;
    }

    /**
     * Create new payment (input) field.
     *
     * @param string label The field label.
     * @param string html The (input) field HTML.
     */
    function __construct($label, $html) {
        $this->ZMPaymentField($label, $html);
    }

    /**
     * Default d'tor.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * Get the field name/label.
     *
     * @return string The field name/label.
     */
    function getLabel() { return $this->label_; }

    /**
     * Get the field HTML.
     *
     * @return string The field HTML.
     */
    function getHTML() { return $this->html_; }

}


?>
