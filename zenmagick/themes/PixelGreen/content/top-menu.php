<ul>
  <?php
    $menu = array();
    $menu[] = array($net->url(FILENAME_DEFAULT, '', false, false), zm_l10n_get("Home"));
    if ($request->isAnonymous()) {
        $menu[] = array($net->url(FILENAME_LOGIN, '', true, false), zm_l10n_get("Login"));
    }
    if ($request->isRegistered()) {
        $menu[] = array($net->url(FILENAME_ACCOUNT, '', true, false), zm_l10n_get("Account"));
    }
    if (!$request->isAnonymous()) {
        $menu[] = array($net->url(FILENAME_LOGOFF, '', true, false), zm_l10n_get("Logoff"));
    }
    if (!$request->getShoppingCart()->isEmpty() && !$request->isCheckout()) {
        $menu[] = array($net->url(FILENAME_SHOPPING_CART, '', true, false), zm_l10n_get("Cart"));
        $menu[] = array($net->url(FILENAME_CHECKOUT_SHIPPING, '', true, false), zm_l10n_get("Checkout"));
    }
    foreach (ZMEZPages::instance()->getPagesForHeader($session->getLanguageId()) as $page) {
        $menu[] = array($net->ezpage($page, false), $page, false);
    }
    foreach ($menu as $item) {
        if (3 == count($item)) {
          // url, page, false
          $page = $item[1];
          $current = ZMTools::compareStoreUrl($item[0]) ? ' id="current"' : '';
          ?><li<?php echo $current ?>><?php $html->ezpageLink($page->getId(), '<span>'.$html->encode($page->getTitle(), false).'</span>') ?></li><?php 
        $menu[] = array($html->ezpageLink($page->getId(), '<span>'.$html->encode($page->getTitle(), false).'</span>', array(), false));
        } else {
          // url, title
          $current = ZMTools::compareStoreUrl($item[0]) ? ' id="current"' : '';
          ?><li<?php echo $current ?>><a href="<?php echo $item[0] ?>"><span><?php echo $item[1] ?></span></a></li><?php
        }
    }
  ?>
</ul>	
