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
 * A validator.
 *
 * @author mano
 * @package net.radebatz.zenmagick.validations
 * @version $Id$
 */
class ZMValidator {
    // each set contains rules for a single form
    var $sets_;
    var $messages_;


    // create new instance
    function ZMValidator() {
        $this->sets_ = array();
        $this->messages_ = array();
    }

    // create new instance
    function __construct() {
        $this->ZMValidator();
    }

    function __destruct() {
    }


    /**
     * Add a new <code>ZMRuleSet</code>.
     *
     * @param ZMRuleSet set A new rule set.
     */
    function addRuleSet($set) {
        $this->sets_[$set->getId()] = $set;
    }


    /**
     * Get a <code>ZMRuleSet</code> for the given id/name.
     *
     * @param string id The id/name of the set.
     * @return ZMRuleSet A <code>ZMRuleSet</code> instance or <code>null</code>.
     */
    function getRulSet($id) {
        return array_key_exists($id, $this->sets_) ? $this->sets_[$id] : null;
    }


    /**
     * If a validation was not successful, corresponding error messages
     * will be available here.
     *
     * @return array A list of localized messages.
     */
    function getMessages() {
        return $this->messages_;
    }

    /**
     * Validate the given request using the named (id) rule set.
     *
     * @param array req The request.
     * @param string id The ruls set id.
     * @return bool <code>true</code> if the validation was successful, <code>false</code> if not.
     */
    function validate($req, $id) {
        $this->messages_ = array();

        $set = array_key_exists($id, $this->sets_) ? $this->sets_[$id] : null;
        if (null == $set) {
            return true;
        }

        // iterate over rules
        $status = true;
        foreach ($set->getRules() as $rule) {
            if (!$rule->validate($req)) {
                $status = false;
                $msgList = array();
                if (array_key_exists($rule->getName(), $this->messages_)) {
                    $msgList = $this->messages_[$rule->getName()];
                }
                array_push($msgList, $rule->getErrorMsg());
                $this->messages_[$rule->getName()] = $msgList;
            }
        }

        return $status;
    }


    /**
     * Create JS validation call.
     *
     * @param string id The id of the form to validate.
     * @param bool echo If <code>true</code>, the JavaScript will be echo'ed as well as returned.
     * @return string Formatted JavaScript .
     */
    function toJSString($id, $echo=true) {
        $set = array_key_exists($id, $this->sets_) ? $this->sets_[$id] : null;

        if (null == $set) {
            return '';
        }

        $n = "\n";
        $js = '';
        $js .= '<script type="text/javascript">'.$n;
        $js .= '  var ' . $id . '_rules = new Array('.$n;
        $first = true;
        foreach ($set->getRules() as $rule) {
            if (!$first) $js .= ','.$n;
            $first = false;
            $js .= $rule->toJSString();
        }
        $js .= $n.'  );'.$n;
        $js .= '</script>'.$n;

        if ($echo) echo $js;
        return $js;
    }

}

?>
