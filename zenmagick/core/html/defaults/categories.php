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
 *
 * $Id$
 */
?>
<?php

    /**
     * Build a nested unordered list from the given categories.
     *
     * <p>Supports show category count and use category page.</p>
     *
     * <p>Links in the active path (&lt;a&gt;) will have a class named <code>act</code>,
     * empty categories will have a class <code>empty</code>. Note that both can occur
     * at the same time.</p>
     *
     * <p>Uses output buffering for increased performance.</p>
     *
     * @package net.radebatz.zenmagick.html.defaults
     * @param array categories An <code>array</code> of <code>ZMCategory</code> instances.
     * @param boolean showProductCount If true, show the product count per category.
     * @param boolean $useCategoryPage If true, create links for empty categories.
     * @param boolean activeParent If true, the parent category is considered in the current category path.
     * @param boolean root Flag to indicate the start of the recursion (not required to set, as defaults to <code>true</code>).
     * @return string The given categories as nested unordered list.
     */
    function zm_build_category_tree_list($categories, $showProductCount=false, $useCategoryPage=false, $activeParent=false, $root=true) {
    global $zm_products;

        if ($root) { ob_start(); }
        echo '<ul' . ($activeParent ? ' class="act"' : '') . '>';
$ii=0;        foreach ($categories as $category) {$ii++;
            if (!$category) { echo $ii."<br>";echo count($categories); echo $categories[17]->id_;}
            $active = $category->isActive();
            $noOfProducts = $showProductCount ? count($zm_products->getProductIdsForCategoryId($category->getId())) : 0;
            $empty = 0 == $noOfProducts;
            echo '<li>';
            $class = '';
            $class = $active ? 'act' : '';
            $class .= $empty ? ' empty' : '';
            $class .= ($active && !$category->hasChildren()) ? ' curr' : '';
            $class = trim($class);
            $onclick = $empty ? ($useCategoryPage ? '' : ' onclick="return catclick(this);"') : '';
            echo '<a' . ('' != $class ? ' class="'.$class.'"' : '') . $onclick . ' href="' .
                        zm_href(FILENAME_DEFAULT, '&'.$category->getPath(), '', false, false) .
                        '">'.zm_htmlencode($category->getName(), false).'</a>';
            if (0 < $noOfProducts) {
                echo '('.$noOfProducts.')';
            }
            if ($category->hasChildren()) {
                echo '&gt;';
            }
            if ($category->hasChildren()) { // && $active) {
                zm_build_category_tree_list($category->getChildren(), $showProductCount, $useCategoryPage, $active, false);
            }
            echo '</li>';
        }
        echo '</ul>';

        $html = $root ? ob_get_clean() : '';
        return $html;
    }

?>
