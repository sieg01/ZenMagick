<?php
/*
 * ZenMagick - Extensions for zen-cart
 * Copyright (C) 2006-2010 zenmagick.org
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
 * Page meta tag data generator.
 *
 * <p>Based on zen-carts <code>../modules/meta_tags.php</code>.</p>
 *
 * <p>In contrast to the zen-cart implementation, however, keywords are build
 * entirely by looking at category and product id in the request.</p>
 *
 * <p>All other pages will get served default values base on the store configuration.
 * Only exception is the homepage where the keywords will include the top categories.</p>
 *
 * @author DerManoMann
 * @package org.zenmagick.store.mvc.tools
 * @version $Id$
 */
class ZMToolboxMetaTags extends ZMToolboxTool {
    private $topCategories_ = null;
    private $crumbtrail_ = null;
    private $product_ = null;
    private $productName_ = null;
    private $category_ = null;
    private $keywordDelimiter_;


    /**
     * Create new instance.
     *
     * @param string delimiter Optional keyword delimiter.
     */
    function __construct($delimiter=null) {
        parent::__construct();
        $this->keywordDelimiter_ = null != $delimiter ? $delimiter : ZMSettings::get('metaTagKeywordDelimiter');
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * Returns/echo'es the page title.
     *
     * @return string The page title.
     */
    public function getTitle() {
        $this->initMetaTags();

        // default to page name
        $title = $this->getToolbox()->utils->getTitle();
        // remove popup prefix
        $title = str_replace('Popup ', '', $title);

        // lookup localized page title
        $requestId = $this->getRequest()->getRequestId();
        $pageTitleKey = ZMSettings::get('metaTitlePrefix').$requestId;
        if (null != _zm_l10n_lookup($pageTitleKey, null)) {
            $title = _zm($pageTitleKey);
        }

        // special handling for categories, manufacturers
        $controller = $this->getRequest()->getController();
        $view = $controller->getView();
        if ('index' == $requestId) {
            $title = ZMSettings::get('storeName');
        } else if (ZMLangUtils::startsWith($requestId, 'product_')) {
            if (null != $this->product_) {
                $languageId = $this->getRequest()->getSession()->getLanguageId();
                if (null != ($details =  $this->product_->getMetaTagDetails($languageId)) && !ZMLangUtils::isEmpty($details->getTitle())) {
                    // got meta tags
                    $title = $details->getTitle();
                } else {
                    $title = $this->productName_;
                }
            } else {
                $title = $this->productName_;
            }
        } else if ('manufacturer' == $requestId) {
            $title = $this->category_;
        } else if ('category' == $requestId || 'category_list' == $requestId) {
            if (null != ($category = ZMCategories::instance()->getCategoryForId($this->getRequest()->getCategoryId(), $this->getRequest()->getSession()->getLanguageId()))) {
                $languageId = $this->getRequest()->getSession()->getLanguageId();
                if (null != ($details = $category->getMetaTagDetails($languageId))) {
                    $title = ZMHtmlUtils::encode($details->getTitle());
                } else {
                    $title = $this->category_;
                }
            } else {
                $title = $this->category_;
            }
        } else if ('page' == $requestId) {
            $vars = $controller->getView()->getVars();
            $ezpage = $vars['ezPage'];
            if (null != $ezpage) {
                $title = $ezpage->getTitle();
            }
        }

        if (ZMSettings::get('isStoreNameInTitle') && 'index' != $requestId) {
            if (0 < strlen($title)) $title .= ZMSettings::get('metaTitleDelimiter');
            $title .= ZMSettings::get('storeName');
        }

        $title = ZMHtmlUtils::encode($title);

        return $title;
    }

    /**
     * Returns/echo'es the keywords meta tag value for the current request.
     *
     * @return string The meta tag value.
     */
    public function getKeywords() {
        $this->initMetaTags();
        $value = '';
        $addTopCats = true;
        if (null != $this->product_) {
            $languageId = $this->getRequest()->getSession()->getLanguageId();
            if (null != ($details =  $this->product_->getMetaTagDetails($languageId)) && !ZMLangUtils::isEmpty($details->getKeywords())) {
                // got meta tags
                $value .= $details->getKeywords();
                $value .= $this->keywordDelimiter_;
                $addTopCats = false;
            }
            $value .= $this->productName_;
        } else if (0 != $this->getRequest()->getCategoryId()) {
            if (null != ($category = ZMCategories::instance()->getCategoryForId($this->getRequest()->getCategoryId(), $this->getRequest()->getSession()->getLanguageId()))) {
                $languageId = $this->getRequest()->getSession()->getLanguageId();
                if (null != ($details = $category->getMetaTagDetails($languageId))) {
                    $value = $details->getKeywords();
                    $addTopCats = false;
                }
            }
        }

        if ($addTopCats) {
            if (!empty($value)) {
                $value .= $this->keywordDelimiter_;
            }
            $value .= $this->topCategories_;
        }

        $value = ZMHtmlUtils::encode(trim($value));

        return $value;
    }

    /**
     * Returns/echo'es the description meta tag value value for the current request.
     *
     * @return string The meta tag value.
     */
    public function getDescription() {
        $this->initMetaTags();
        $value = ZMSettings::get('storeName');
        if (0 < strlen($this->formatCrumbtrail())) {
            $value .= ZMSettings::get('metaTagCrumbtrailDelimiter');
            $value .= $this->formatCrumbtrail();
        }

        // special handling for home
        if (null != $this->product_) {
            $languageId = $this->getRequest()->getSession()->getLanguageId();
            if (null != ($details =  $this->product_->getMetaTagDetails($languageId)) && !ZMLangUtils::isEmpty($details->getKeywords())) {
                // got meta tags
                $value = $details->getDescription();
            } else {
                $value .= ZMSettings::get('metaTagCrumbtrailDelimiter');
                $value .= $this->topCategories_;
            }
        } else if (0 != $this->getRequest()->getCategoryId()) {
            if (null != ($category = ZMCategories::instance()->getCategoryForId($this->getRequest()->getCategoryId(), $this->getRequest()->getSession()->getLanguageId()))) {
                $languageId = $this->getRequest()->getSession()->getLanguageId();
                if (null != ($details = $category->getMetaTagDetails($languageId))) {
                    $value = $details->getDescription();
                }
            }
        }

        $value = ZMHtmlUtils::encode(trim($value));

        return $value;
    }


    /**
     * Set up all required internal structures.
     */
    protected function initMetaTags() {
        $this->loadTopCategories();
        $this->loadCrumbtrail();
        $this->loadProduct();
        $this->loadCategory();
    }


    /**
     * Load top categories.
     */
    protected function loadTopCategories() {
        if (null != $this->topCategories_)
            return;

        $topCategories = ZMCategories::instance()->getRootCategories($this->getRequest()->getSession()->getLanguageId());

        $first = true;
        foreach ($topCategories as $category) {
            if (!$first) $this->topCategories_ .= $this->keywordDelimiter_;
            $first = false; 
            $this->topCategories_ .= $category->getName();
        }
    }


    /**
     * Load category crumbtrail.
     */
    protected function loadCrumbtrail() {
        if (null != $this->crumbtrail_)
            return;

        $this->crumbtrail_ = $this->getToolbox()->crumbtrail;
        // it's the controllers responsibility to set up the crumbtrail..
        return;
        $this->crumbtrail_->addCategoryPath($this->getRequest()->getCategoryPathArray());
        $this->crumbtrail_->addManufacturer($this->getRequest()->getManufacturerId());
        $this->crumbtrail_->addProduct($this->getRequest()->getProductId());
    }


    /*
     * Format the current crumbtrail.
     */
    protected function formatCrumbtrail() {
        if (null == $this->crumbtrail_)
            return null;

        $crumbs = $this->crumbtrail_->getCrumbs();
        array_shift($crumbs);
        $first = true;
        $value = '';
        foreach ($crumbs as $crumb) {
            if (!$first) $value .= ZMSettings::get('metaTagCrumbtrailDelimiter');
            $first = false;
            $value .= $crumb->getName();
        }

        return $value;
    }


    /**
     * Load product info.
     */
    protected function loadProduct() {
        if (null == $this->getRequest()->getProductId() || null != $this->productName_)
            return;

        if (null != ($this->product_ = ZMProducts::instance()->getProductForId($this->getRequest()->getProductId()))) {
            $this->productName_ = $this->product_->getName();
            if (!ZMLangUtils::isEmpty($this->product_->getModel())) {
                $this->productName_ .= ' [' . $this->product_->getModel() . ']';
            }
        }
    }

    /**
     * Load category info.
     */
    protected function loadCategory() {
        if (null != $this->getRequest()->getCategoryPath()) {
            if (null != ($category = ZMCategories::instance()->getCategoryForId($this->getRequest()->getCategoryId(), $this->getRequest()->getSession()->getLanguageId()))) {
                $this->category_ = $category->getName();
            }
        } else if (null != $this->getRequest()->getManufacturerId()) {
            if (null != ($manufacturer = ZMManufacturers::instance()->getManufacturerForId($this->getRequest()->getManufacturerId(), $this->getRequest()->getSession()->getLanguageId()))) {
                $this->category_ = $manufacturer->getName();
            }
        }
    }

}
