<?php
/*
 * ZenMagick - Extensions for zen-cart
 * Copyright (C) 2006-2008 ZenMagick
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
 * All stuff related to product prices and offers.
 *
 * @author DerManoMann
 * @package org.zenmagick.model.catalog
 * @version $Id$
 */
class ZMOffers extends ZMObject {
    private $product_;
    private $basePrice_;
    private $specialPrice_;
    private $salePrice_;
    private $taxRate_;
    private $discountPercent_;


    /**
     * Create new instance.
     *
     * @param ZMProduct product The product.
     */
    function __construct($product) {
        parent::__construct();
        $this->product_ = $product;
        $this->basePrice_ = null;
        $this->specialPrice_ = null;
        $this->salePrice_ = null;
        $this->discountPercent_ = 0;
        $this->taxRate_ = null;
        $this->calculatePrice();
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * Checks if there are attribute prices that will affect the final price.
     *
     * @return boolean <code>true</code> if attribute prices exist.
     */
    function isAttributePrice() { 
        foreach ($this->product_->getAttributes() as $attribute) {
            foreach ($attribute->getValues() as $value) {
                if (0 < $value->getPrice()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Set the product.
     *
     * @param ZMProduct product The product.
     */
    public function setProduct($product) { $this->product_ = $product; }

    /**
     * Get the product price.
     *
     * <p>This is the price as configured in the database.</p>
     *
     * @param boolean tax Set to <code>true</code> to include tax (if applicable); default is <code>true</code>.
     * @return float The product price.
     */
    public function getProductPrice($tax=true) {
        return $tax ? $this->getTaxRate()->addTax($this->product_->getProductPrice()) : $this->product_->getProductPrice();
    }

    /**
     * Get the base price; this is the lowest possible product price.
     *
     * <p>The base price consists of the product price plus the lowest attribute price (if any).</p>
     *
     * @param boolean tax Set to <code>true</code> to include tax (if applicable); default is <code>true</code>.
     * @return float The base price.
     */
    public function getBasePrice($tax=true) {
        if (null === $this->basePrice_) {
            $this->basePrice_ = $this->doGetBasePrice();
        }

        return $tax ? $this->getTaxRate()->addTax($this->basePrice_) : $this->basePrice_;
    }

    /**
     * Calculate the base price.
     */
    protected function doGetBasePrice() {
        $basePrice = 0;

        $attributes = $this->product_->getAttributes();
        if ($this->product_->isPricedByAttributes() && 0 < count($attributes)) {
            // add minimum attributes price to price
            foreach ($attributes as $attribute) {
                $lowest = null;
                foreach ($attribute->getValues() as $value) {
                    if (!$value->isDisplayOnly() && $value->isIncludeInBasePrice()) {
                        if (null == $lowest || $lowest->getValuePrice(false) > $value->getValuePrice(false)) {
                            $lowest = $value;
                        }
                    }
                }
                if (null != $lowest) {
                    $basePrice += $lowest->getValuePrice(false);
                }
            }
        }

        // this is for price factor based attributes (the lower limit is the set price [even though priced by attr])
        $basePrice += $this->product_->getProductPrice();

        return $basePrice;
    }

    /**
     * Get the special price.
     *
     * <p>Special price as configured.</p>
     *
     * @param boolean tax Set to <code>true</code> to include tax (if applicable); default is <code>true</code>.
     * @return float The special price.
     */
    public function getSpecialPrice($tax=true) {
        if (null === $this->specialPrice_) {
            $this->specialPrice_ = $this->product_->getSpecialPrice();
        }

        return $tax ? $this->getTaxRate()->addTax($this->specialPrice_) : $this->specialPrice_;
    }

    /**
     * Get the discount price.
     *
     * <p>This price is the price as set up with the sales maker in the admin interface.</p>
     *
     * @param boolean tax Set to <code>true</code> to include tax (if applicable); default is <code>true</code>.
     * @return float The discount price.
     */
    public function getSalePrice($tax=true) {
        if (null === $this->salePrice_) {
            $this->salePrice_ = $this->doGetSalePrice();
        }

        return $tax ? $this->getTaxRate()->addTax($this->salePrice_) : $this->salePrice_;
    }

    /**
     * Calculate the discount price.
     */
    protected function doGetSalePrice() {
  	    $basePrice = $this->getBasePrice(false);
  	    $specialPrice = $this->getSpecialPrice(false);

        // get available sales
        $sql = "SELECT sale_specials_condition, sale_deduction_value, sale_deduction_type
                FROM " . TABLE_SALEMAKER_SALES . "
                WHERE sale_categories_all LIKE '%," . $this->product_->getMasterCategoryId() . ",%' AND sale_status = '1'
                AND (sale_date_start <= now() OR sale_date_start = '0001-01-01')
                AND (sale_date_end >= now() OR sale_date_end = '0001-01-01')
                AND (sale_pricerange_from <= :priceFrom  OR sale_pricerange_from = '0')
                AND (sale_pricerange_to >= :priceFrom OR sale_pricerange_to = '0')";
        $args = array('priceFrom' => $basePrice, 'categoriesAll' => '%,'.$this->product_->getMasterCategoryId().',%');
        $results = ZMRuntime::getDatabase()->query($sql, $args, TABLE_SALEMAKER_SALES);

        if (0 == count($results)) {
            return $specialPrice;
        }

        // read first result
        $saleType = $results[0]['deductionType'];
        $saleValue = $results[0]['deductionValue'];
        $saleCondition = $results[0]['specialsCondition'];

        // best special price available
        $bestSpecialPrice = $specialPrice ? $specialPrice : $basePrice;

        switch ($saleType) {
            case ZM_SALE_TYPE_AMOUNT:
                $saleBasePrice = $basePrice - $saleValue;
                $saleSpecialPrice = $bestSpecialPrice - $saleValue;
                break;
            case ZM_SALE_TYPE_PERCENT:
                $saleBasePrice = $basePrice - (($basePrice * $saleValue) / 100);
                $saleSpecialPrice = $bestSpecialPrice - (($bestSpecialPrice * $saleValue) / 100);
                break;
            case ZM_SALE_TYPE_PRICE:
                $saleBasePrice = $saleValue;
                $saleSpecialPrice = $saleValue;
                break;
            default:
                // gosh, how'd we get here??
                return $bestSpecialPrice;
        }

        $calculationDecimals = ZMSettings::get('calculationDecimals');

        // sanitize
        $saleBasePrice = $saleBasePrice < 0 ? 0 : $saleBasePrice;
        $saleSpecialPrice = $saleSpecialPrice < 0 ? 0 : $saleSpecialPrice;

        if (!$specialPrice) {
            return number_format($saleBasePrice, $calculationDecimals, '.', '');
        } else {
            switch($saleCondition){
                case 0:
                    return number_format($saleBasePrice, $calculationDecimals, '.', '');
                    break;
                case 1:
                    return number_format($specialPrice, $calculationDecimals, '.', '');
                    break;
                case 2:
                    return number_format($saleSpecialPrice, $calculationDecimals, '.', '');
                    break;
                default:
                    return number_format($specialPrice, $calculationDecimals, '.', '');
            }
        }
    }


    /**
     * Calculate the (best) price.
     */
    protected function calculatePrice() {
        $basePrice = $this->getBasePrice(false);
        $specialPrice = $this->getSpecialPrice(false);
        $salePrice = $this->getSalePrice(false);

        // calculate discount
        $this->discountPercent_ = 0;
        if ((0 != $specialPrice || 0 != $salePrice) && 0 != $basePrice) {
            if (0 != $salePrice) {
                $this->discountPercent_ = number_format(100 - (($salePrice / $basePrice) * 100), ZMSettings::get('discountDecimals'));
            } else {
                $this->discountPercent_ = number_format(100 - (($specialPrice / $basePrice) * 100), ZMSettings::get('discountDecimals'));
            }
        }
    }

    /**
     * Get the discount as percent value.
     *
     * @return float The discount in percent.
     */
    public function getDiscountPercent() { return $this->discountPercent_; }

    /**
     * Get the discount amount.
     *
     * @return float The discount amount.
     */
    public function getDiscountAmount() {
        $save = 0;
        if (!$this->product_->isFree() && ($this->isSpecial() || $this->isSale())) {
          if ($this->isSpecial())  {
              $save = $this->getBasePrice() - $this->getSpecialPrice();
          } else if ($this->isSale()) {
              $save = $this->getBasePrice() - $this->getSalePrice();
          }
        }
        return $save;
    }

    /**
     * Get the tax rate for the product.
     *
     * @return float The tax rate.
     */
    public function getTaxRate() { 
        if (null == $this->taxRate_) {
            $this->taxRate_ = $this->product_->getTaxRate();
        }

        return $this->taxRate_; 
    }

    /**
     * Checks if a special price is available.
     *
     * @return boolean <code>true</code> if a special price is available.
     */
    public function isSpecial() { return 0 != $this->specialPrice_ && $this->specialPrice_ != $this->basePrice_ && !$this->isSale(); }

    /**
     * Checks if a sale price is available.
     *
     * @return boolean <code>true</code> if a sale price is available.
     */
    public function isSale() { return 0 != $this->salePrice_; }

    /**
     * Get the calculated price.
     *
     * <p>This is the actual price, taking into account if sale or discount are available.</p>
     *
     * @param boolean tax Set to <code>true</code> to include tax (if applicable); default is <code>true</code>.
     * @return float The calculated price.
     */
    public function getCalculatedPrice($tax=true) { 
        if ($this->product_->isFree()) {
            return 0;
        } else if (0 != ($salePrice = $this->getSalePrice($tax))) {
            return $salePrice;
        } else if (0 != ($specialPrice = $this->getSpecialPrice($tax))) {
            return $specialPrice;
        } else {
            return $this->getBasePrice($tax); 
        }
    }

    /**
     * Get quantity discounts, if any.
     *
     * @param boolean tax Set to <code>true</code> to include tax (if applicable); default is <code>true</code>.
     * @return array A list of <code>ZMQuantityDiscount</code> instances.
     */
    public function getQuantityDiscounts($tax=true) {
        $discounts = array();
        $sql = "SELECT * FROM " . TABLE_PRODUCTS_DISCOUNT_QUANTITY . "
                WHERE products_id = :productId
                  AND discount_qty != 0
                ORDER BY discount_qty";

        $args = array('productId' => $this->product_->getId());
        $discounts = ZMRuntime::getDatabase()->query($sql, $args, TABLE_PRODUCTS_DISCOUNT_QUANTITY, 'QuantityDiscount');

        if (0 < count($discounts)) {
            $product = $this->product_;
            $basePrice = $this->getBasePrice($tax);
            if (ZM_DISCOUNT_FROM_SPECIAL_PRICE == $product->getDiscountTypeFrom() && $this->isSpecial()) {
                $basePrice = $this->getSpecialPrice($tax);
            }

            foreach ($discounts as $discount) {
                $price = 0;
                switch ($product->getDiscountType()) {
                    case ZM_DISCOUNT_TYPE_NONE:
                        $price = 0; // WTF???
                        break;
                    case ZM_DISCOUNT_TYPE_PERCENT:
                        $price = $basePrice - ($basePrice * ($discount->getValue() / 100));
                        break;
                    case ZM_DISCOUNT_TYPE_PRICE:
                        $price = $discount->getValue();
                        break;
                    case ZM_DISCOUNT_TYPE_AMOUNT:
                        $price = $basePrice - $discount->getValue();
                        break;
                    default:
                        throw ZMLoader::make('ZMException', ' invalid discount type: '.$product->getDiscountType());
                        break;
                }
                $discount->setPrice($price);
            }
        }

        return $discounts;
    }

}

?>
