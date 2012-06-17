<?php
/**
 * autoloader array for catalog application_top.php
 * see  {@link  http://www.zen-cart.com/wiki/index.php/Developers_API_Tutorials#InitSystem wikitutorials} for more details.
 *
 * @package initSystem
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: config.core.php 4271 2006-08-26 01:21:02Z drbyte $
 */

  $autoLoadConfig[0][] = array('autoType'=>'init_script',
                                'loadFile'=> 'init_begin.php');

  $autoLoadConfig[10][] = array('autoType'=>'include_glob',
                                'loadFile'=>'includes/extra_configures/*.php');

  $autoLoadConfig[110][] = array('autoType'=>'include',
                                'loadFile'=>'includes/classes/db/mysql/define_queries.php');
  $autoLoadConfig[110][] = array('autoType'=>'classInstantiate',
                                 'className'=> 'language',
                                 'objectName'=>'lng');
  $autoLoadConfig[110][] = array('autoType'=>'service',
                                'name'=>'themeService',
                                'method'=>'getActiveThemeId',
                                'resultVar'=>'template_dir');
  $autoLoadConfig[110][] = array('autoType'=>'include',
                                 'once'=>true,
                                 'loadFile'=>'includes/languages/%template_dir%/%language%.php');
  $autoLoadConfig[110][] = array('autoType'=>'include',
                                 'once'=>true,
                                 'loadFile'=>'includes/languages/%language%.php');
  $autoLoadConfig[110][] = array('autoType'=>'include_glob',
                                 'loadFile'=> array(
                                              'includes/languages/%language%/extra_definitions/%template_dir%/*.php',
                                              'includes/languages/%language%/extra_definitions/*.php'));

  $autoLoadConfig[130][] = array('autoType'=>'init_script',
                                 'loadFile'=> 'init_customer_auth.php',
                                 'loaderPrefix'=>'config');

  $autoLoadConfig[160][] = array('autoType'=>'classInstantiate',
                                 'className'=>'breadcrumb',
                                 'objectName'=>'breadcrumb');
  $autoLoadConfig[160][] = array('autoType'=>'init_script',
                                 'loadFile'=> 'init_category_path.php');

  $autoLoadConfig[170][] = array('autoType'=>'init_script',
                                 'loadFile'=> 'init_add_crumbs.php',
                                 'loaderPrefix'=>'config');


