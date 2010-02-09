<?php

/**
 * Test url manager.
 *
 * @package org.zenmagick.plugins.unitTests.tests
 * @author DerManoMann
 * @version $Id$
 */
class TestZMUrlManager extends ZMTestCase {

    /**
     * Test global.
     */
    public function testGlobal() {
        $manager = new ZMUrlManager();
        $manager->setMapping(null, array('error' => array('template' => 'error', 'layout' => 'foo')));
        $mapping = $manager->findMapping('foo', 'error');
        $this->assertEqual(array('controller'=>null,'formId'=>null,'form'=>null,'view'=>null,'template'=>'error', 'layout' => 'foo'), $mapping);

        // test store mapping
        $mapping = ZMUrlManager::instance()->findMapping('foo', 'empty_cart');
        $this->assertEqual(array('controller'=>null,'formId'=>null,'form'=>null,'view'=>'RedirectView','template'=>'shopping_cart', 'layout' => null), $mapping);

        // test store mapping
        $mapping = ZMUrlManager::instance()->findMapping(null, 'low_stock');
        $this->assertEqual(array('controller'=>null,'formId'=>null,'form'=>null,'view'=>'RedirectView','template'=>'shopping_cart', 'layout' => null), $mapping);
    }

}

?>
