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
 *
 * $Id$
 */
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="<?php echo $language->getCode() ?>">
<head>
<title><?php zm_l10n("Product recommendation from %s at %s", $zm_emailMessage->getFromName(), ZMSettings::get('storeName')) ?></title>
</head>
<body>
<body>
<div style="font-family:Verdana,Arial,Helvetica,sans-serif;font-size:10pt;">
<p><?php zm_l10n("Hi %s,", $zm_emailMessage->getToName()) ?></p>

<p><?php zm_l10n("Your friend, %s, thought that you would be interested in %s from %s.", $zm_emailMessage->getFromName(), $zm_product->getName(), ZMSettings::get('storeName')) ?></p>

<?php if ($zm_emailMessage->hasMessage()) { ?>
<p>
<?php zm_l10n("%s also sent a note saying:", $zm_emailMessage->getFromName()) ?><br>
<?php echo $html->text2html($zm_emailMessage->getMessage()) ?>
</p>
<?php } ?>

<?php $href = '<a href="'.$net->product($zm_product->getId(), null, false).'">'.$html->encode($zm_product->getName(), false).'</a>'; ?>
<p><?php zm_l10n("To view the product, click on the following link or copy and paste the link into your web browser: %s", $href) ?></p>

<p><?php zm_l10n("Regards, %s", ZMSettings::get('storeOwner')) ?></p>

<?php echo zm_l10n_chunk_get('email_advisory', ZMSettings::get('storeEmail')) ?>
</div>
</body>
</html>
