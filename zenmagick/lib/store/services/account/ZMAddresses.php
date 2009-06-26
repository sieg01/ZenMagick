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
 * Addresses.
 *
 * @author DerManoMann
 * @package org.zenmagick.store.services.account
 * @version $Id: ZMAddresses.php 2233 2009-05-21 10:26:30Z dermanomann $
 */
class ZMAddresses extends ZMObject {

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
     * Get instance.
     */
    public static function instance() {
        return ZMObject::singleton('Addresses');
    }


    /**
     * Get the address for the given id.
     *
     * @param int addressId The address id.
     * @param int accountId Optional account id to make it easy to verify access; default is <code>null</code>.
     * @return ZMAddress The address or <code>null</code>.
     */
    public function getAddressForId($addressId, $accountId=null) {
        $sql = "SELECT *
                FROM " . TABLE_ADDRESS_BOOK . "
                WHERE address_book_id = :id";
        if (null !== $accountId) {
            $sql .= " AND customers_id = :accountId";
        }
        $address = Runtime::getDatabase()->querySingle($sql, array('id' => $addressId, 'accountId' => $accountId), TABLE_ADDRESS_BOOK, 'Address');
        if (null != $address) {
            $defaultAddressId = $this->getDefaultAddressId($address->getAccountId());
            $address->setPrimary($address->getId() == $defaultAddressId);
        }

        return $address;
    }


    /**
     * Get all addresses for the given account id.
     *
     * @param int accountId The account id.
     * @return array A list of <code>ZMAddress</code> instances.
     */
    public function getAddressesForAccountId($accountId) {
        $sql = "SELECT *
                FROM " . TABLE_ADDRESS_BOOK . "
                WHERE customers_id = :accountId";
        $addresses = Runtime::getDatabase()->query($sql, array('accountId' => $accountId), TABLE_ADDRESS_BOOK, 'Address');

        $defaultAddressId = $this->getDefaultAddressId($accountId);
        foreach ($addresses as $address) {
            $address->setPrimary($address->getId() == $defaultAddressId);
        }

        return $addresses;
    }


    /**
     * Update the given address.
     *
     * @param ZMAddress account The address.
     * @return ZMAddress The updated address.
     */
    public function updateAddress($address) {
        Runtime::getDatabase()->updateModel(TABLE_ADDRESS_BOOK, $address);
        return $address;
    }


    /**
     * Create a new address.
     *
     * @param ZMAddress The new address.
     * @return ZMAddress The created address incl. the new address id.
     */
    public function createAddress($address) {
        Runtime::getDatabase()->createModel(TABLE_ADDRESS_BOOK, $address);
        return $address;
    }


    /**
     * Delte an address.
     *
     * @param int addressId The address id.
     * @param boolean <code>true</code>.
     */
    public function deleteAddressForId($addressId) {
        $sql = "DELETE FROM " . TABLE_ADDRESS_BOOK . "
                WHERE  address_book_id = :id"; 
        Runtime::getDatabase()->update($sql, array('id' => $addressId), TABLE_ADDRESS_BOOK);
        return true;
    }


    /**
     * Get the default address id for the given account.
     *
     * @param int accountId The account id.
     */
    private function getDefaultAddressId($accountId) {
        $account = ZMAccounts::instance()->getAccountForId($accountId);
        return null != $account ? $account->getDefaultAddressId() : 0;
    }


    /**
     * Get the address format for the given address format id.
     *
     * @param int addressFormatId The address format id.
     * @return string The address format.
     */
    public function getAddressFormatForId($addressFormatId) {
        $sql = "SELECT address_format
                FROM " . TABLE_ADDRESS_FORMAT . "
                WHERE address_format_id = :id";
        $result = Runtime::getDatabase()->querySingle($sql, array('id' => $addressFormatId), TABLE_ADDRESS_FORMAT);
        return $result['format'];
    }

}

?>
