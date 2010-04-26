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
 *
 * $Id$
 */
?>

<?php if (0 != $request->getProductId() && isset($currentProduct)) { ?>
     <?php 
      $isSubscribed = false;
      if ($request->isRegistered()) {
          $account = $request->getAccount();
          if (null != $account) {
              $subscribedProducts = $account->getSubscribedProducts();
              $isSubscribed = in_array($request->getProductId(), $subscribedProducts);
          }
      }
    ?>
    <?php if ($request->isAnonymous() || !$isSubscribed) { ?>
        <h3><?php zm_l10n("Notifications") ?></h3>
        <div id="sb_product_notifications" class="box">
            <a href="<?php echo $net->url(null, 'action=notify') ?>"><img src="<?php echo $this->asUrl("images/big_tick.gif") ?>" alt="<?php zm_l10n("Notify me of updates to this product") ?>" title="<?php zm_l10n("Notify me of updates to this product") ?>" /><br /><?php zm_l10n("Notify me of updates to <strong>%s</strong>", $currentProduct->getName())?></a>
        </div>
    <?php } else if ($isSubscribed) { ?>
        <h3><?php zm_l10n("Notifications") ?></h3>
        <div id="sb_product_notifications" class="box">
            <a href="<?php echo $net->url(null, 'action=notify_remove') ?>"><img src="<?php echo $this->asUrl("images/big_remove.gif") ?>" alt="<?php zm_l10n("Remove product notification") ?>" title="<?php zm_l10n("Remove product notification") ?>" /><br /><?php zm_l10n("Do not notify me of updates to <strong>%s</strong>", $currentProduct->getName())?></a>
        </div>
    <?php } else if ($isSubscribed) { ?>
    <?php } ?>
<?php } ?>
