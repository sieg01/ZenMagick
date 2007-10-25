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
 */
?>
<?php


// some default defaults ;)
define ('_ZM_WIKI_BOXEX_PREFIX', 'zm_wiki');
define ('ZM_FILENAME_WIKI', 'wiki');


/**
 * Plugin adding a simple wiki.
 *
 * <p>This plugin is based on pawfaliki (http://www.pawfal.org/pawfaliki).</p>
 *
 * <p>Edit permission is based on the setting <em>plugin.zm_wiki.access.modify</em>.
 * Possible values are:</p>
 * <dl>
 *   <dt>ALL</dt><dd>Everyone is allowed to edit pages.</dd>
 *   <dt>REGISTERED</dt><dd>Only registered (and logged in) users are allowed to edit pages.</dd>
 *   <dt>ADMIN</dt><dd>Only admin can edit pages</dd>
 *   <dt>NONE</dt><dd>Read only for all.</dd>
 * </dl>
 *
 * @author mano
 * @package org.zenmagick.plugins.zm_wiki
 * @version $Id$
 */
class zm_wiki extends ZMBoxPlugin {

    /**
     * Default c'tor.
     */
    function zm_wiki() {
        parent::__construct('Pawfaliki Wiki', 'Adds a Wiki.', '${plugin.version}');
        $this->setLoaderSupport('ALL');
    }

    /**
     * Default c'tor.
     */
    function __construct() {
        $this->zm_wiki();
    }

    /**
     * Default d'tor.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * Install this plugin.
     */
    function install() {
        parent::install();

        $filesDir = str_replace('/', DIRECTORY_SEPARATOR, DIR_FS_CATALOG."wiki/files/");
        zm_mkdir($filesDir);
        zm_mkdir(str_replace('/', DIRECTORY_SEPARATOR, DIR_FS_CATALOG."wiki/tmp/"));

        // create WikiNav default
        $wikiNav = $filesDir.'WikiNav';
        if (!file_exists($wikiNav)) {
            $handle = fopen($wikiNav, 'ab');
            fwrite($handle, 'zm_wiki sidebox nav');
            fclose($handle);
        }
    }

    /**
     * Init this plugin.
     */
    function init() {
        parent::init();

        // remember last strategy
        zm_set_setting('plugin.zm_wiki.last-page-caching-strategy', zm_setting('pageCacheStrategyCallback'));
        // replace with own implementation
        zm_set_setting('pageCacheStrategyCallback', 'zm_wiki_is_page_cacheable');
        $this->addMenuItem('wiki', zm_l10n_get('Manage Wiki'), 'zm_wiki_admin');
        zm_set_pretty_link_mapping('wiki', null, array('page'), array('page'));
    }

    /**
     * Get the ids/names of the boxes supported by this plugin.
     *
     * @return array List of box names.
     */
    function getBoxNames() {
        return array(_ZM_WIKI_BOXEX_PREFIX);
    }

    /**
     * Get the contents for the given box id.
     *
     * @return string Contents for the box implementation.
     */
    function getBoxContents($id) {
        $contents = <<<EOT
<h3><a href="<?php zm_href(ZM_FILENAME_WIKI) ?>">[<?php zm_l10n("Home")?>]</a><?php zm_l10n("Wiki Content") ?></h3>
<div id="sb_zm_wiki" class="box">
    <?php zm_wiki_display_page('WikiNav'); ?>
</div>
EOT;
        return $contents;
    }

}

?>
