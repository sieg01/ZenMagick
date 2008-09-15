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
?>
<?php

    /**
     * Dispatch the current request.
     *
     * @package org.zenmagick
     * @return boolean Always <code>true</code>.
     */
    function zm_dispatch() {
        $controller = ZMLoader::make(ZMLoader::makeClassname(ZMRequest::getPageName().'Controller'));
        if (null == $controller) {
            $controller = ZMLoader::make("DefaultController");
        }
        ZMRequest::setController($controller);

        if (ZMSettings::get('isLegacyAPI')) { eval(zm_globals()); }

        try {
            // execute controller
            $view = $controller->process();
        } catch (Exception $e) {
            // TODO: extract somewhere into method/function??
            ZMLogging::instance()->dump($e, ZMLogging::WARN);
            $controller = ZMLoader::make("DefaultController");
            $controller->exportGlobal('exception', $e);
            $view = $controller->findView('error');
            // uguu!
            $view->setController($controller);
            $controller->setView($view);
            ZMRequest::setController($controller);
        }

        // generate response
        if (null != $view) {
            // make header match the template
            header('Content-Type: text/html; charset='.zm_i18n('HTML_CHARSET'));

            // common view variables
            $controller->exportGlobal('zm_view', $view);
            $controller->exportGlobal('zm_theme', ZMRuntime::getTheme());

            ZMEvents::instance()->fireEvent(null, ZM_EVENT_VIEW_START, array('view' => $view));
            // TODO: catch exceptions
            $view->generate();
            ZMEvents::instance()->fireEvent(null, ZM_EVENT_VIEW_DONE, array('view' => $view));
        }

        return true;
    }

?>
