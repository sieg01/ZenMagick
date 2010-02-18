<?php
/*
 * ZenMagick - Extensions for zen-cart
 * Copyright (C) 2006-2009 zenmagick.org
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
?><?php zm_l10n("Dear %s %s,", $currentAccount->getFirstName(), $currentAccount->getLastName()) ?>


<?php zm_l10n("This is to inform you that your order #%s has been updated.", $currentOrder->getId()) ?>

<?php if (ZMZenCartUserSacsHandler::REGISTERED == $currentAccount->getType()) { ?>
<?php zm_l10n("More details can be found at the following URL: %s", $net->url(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id='.$currentOrder->getId())) ?>
<?php } else { ?>
<?php zm_l10n("You can check the status of your order at: %s.", $net->url('guest_history')) ?>
<?php } ?>

<?php if ($newOrderStatus != $currentOrder->getStatusName()) { ?>
<?php zm_l10n("The new order status is: %s.", $newOrderStatus) ?>
<?php } ?>

<?php if (!empty($comment)) { ?>
<?php zm_l10n("The following comment has been added to your order:") ?>

<?php echo $comment ?>
<?php } ?>


<?php zm_l10n("Regards, %s", ZMSettings::get('storeName')) ?>

<?php echo strip_tags($utils->staticPageContent('email_advisory')) ?>
