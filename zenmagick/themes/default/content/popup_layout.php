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
?>
<?php
    // allow for custom layout settings without having to copy the whole file every time...
    $pageLayout = "layout/".$request->getRequestId().".php";
    if ($this->exists($pageLayout)) {
        echo $this->fetch($pageLayout);
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
  <head>
    <title><?php $metaTags->getTitle()?></title>
    <base href="<?php echo $request->getPageBase() ?>" />
    <meta http-equiv="content-type" content="text/html; charset=<?php echo zm_i18n('HTML_CHARSET') ?>" />
    <?php $utils->cssFile('popup.css') ?>
    <?php /* give other themes the chance to add to the default CSS without having to copy everything */ ?>
    <?php if ($this->exists("theme.css")) { ?>
        <?php $utils->cssFile('theme.css') ?>
    <?php } ?>
    <?php $pageCSS = "css/".$request->getRequestId().".css"; ?>
    <?php /* page specific CSS */ ?>
    <?php if ($this->exists($pageCSS)) { ?>
        <?php $utils->cssFile($pageCSS) ?>
    <?php } ?>
  </head>

  <body id="pb_<?php echo $request->getRequestId() ?>">
    <?php echo $this->fetch($viewTemplate); ?>
  </body>
</html>
