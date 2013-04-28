<?php
/*
 * ZenMagick - Smart e-commerce
 * Copyright (C) 2006-2012 zenmagick.org
 *
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

use ZenMagick\Base\Toolbox;

?>

<?php if (isset($currentProduct)) { ?>
        <?php $manufacturer = $currentProduct->getManufacturer(); ?>
        <?php if (null != $manufacturer) { ?>
            <h3><?php _vzm("Manufacturer Info") ?></h3>
            <div id="sb_manufacturer_info" class="box">
                <?php
                if ($manufacturer->hasImage()) {
                    $url = $view['router']->generate('manufacturer', array('manufacturers_id' => $manufacturer->getId()));
                    $class = '';
                    if (!Toolbox::isEmpty($manufacturer->getUrl())) {
                        $url = $net->trackLink('manufacturer', $manufacturer->getId());
                        $class = ' class="new-win" ';
                    }
                    ?><a href="<?php echo $url ?>"<?php echo $class ?>><?php echo $html->image($manufacturer->getImageInfo()) ?></a><?php
                    if (!Toolbox::isEmpty($manufacturer->getUrl())) {
                        $url = $view['router']->generate('manufacturer', array('manufacturers_id' => $manufacturer->getId()));
                        ?><a href="<?php echo $url ?>"<?php echo $target ?>><?php _vzm("Other Products") ?></a><?php
                    }
                } else {
                    $url = $view['router']->generate('manufacturer', array('manufacturers_id' => $manufacturer->getId()));
                    $class = '';
                    $text = _zm("Other Products");
                    if (!Toolbox::isEmpty($manufacturer->getUrl())) {
                        $url = $net->trackLink('manufacturer', $manufacturer->getId());
                        $class = ' class="new-win" ';
                        $text = _zm("Manufacturer Homepage");
                    }
                    ?><a href="<?php echo $url ?>"<?php echo $class ?>><?php echo $text ?></a><?php
                    if (!Toolbox::isEmpty($manufacturer->getUrl())) {
                        $url = $view['router']->generate('manufacturer', array('manufacturers_id' => $manufacturer->getId()));
                        ?><a href="<?php echo $url ?>"<?php echo $target ?>><?php _vzm("Other Products") ?></a><?php
                    }
                } ?>
            </div>
        <?php } ?>
<?php } ?>