<?php

/**
 * Test coupon service.
 *
 * @package org.zenmagick.plugins.zm_tests.tests
 * @author DerManoMann
 * @version $Id$
 */
class TestZMCoupons extends ZMTestCase {
    private $createdCouponIds_;

    
    /**
     * {@inheritDoc}
     */
    public function setUp() {
        $this->createdCouponIds_ = array();
        $this->accountIds_ = array($this->getAccountId());
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown() {
        $couponTables = array(TABLE_COUPONS, TABLE_COUPONS_DESCRIPTION, TABLE_COUPON_EMAIL_TRACK, TABLE_COUPON_REDEEM_TRACK, TABLE_COUPON_RESTRICT);
        $accountTables = array(TABLE_COUPON_GV_CUSTOMER, TABLE_COUPON_GV_QUEUE);

        foreach ($couponTables as $table) {
            $sql = "DELETE FROM " . $table . "
                    WHERE coupon_id = :couponId";
            foreach ($this->createdCouponIds_ as $couponId) {
                Runtime::getDatabase()->update($sql, array('couponId' => $couponId), $table);
            }
        }

        foreach ($accountTables as $table) {
            $sql = "DELETE FROM " . $table . "
                    WHERE customer_id = :accountId";
            foreach ($this->accountIds_ as $accountId) {
                Runtime::getDatabase()->update($sql, array('accountId' => $accountId), $table);
            }
        }
    }


    /**
     * Get the test account id.
     *
     * @return int An account id.
     */
    protected function getAccountId() {
        $account = ZMAccounts::instance()->getAccountForEmailAddress('root@localhost');
        return null != $account ? $account->getId() : 1;
    }

    /**
     * Test create coupon code.
     */
    public function testCreateCouponCode() {
        $couponCode = ZMCoupons::instance()->createCouponCode('foo@bar.com');
        $this->assertNotNull($couponCode);
    }

    /**
     * Test create coupon.
     */
    public function testCreateCoupon() {
        $couponCode = ZMCoupons::instance()->createCouponCode('foo@bar.com');
        $this->assertNotNull($couponCode);
        $coupon = ZMCoupons::instance()->createCoupon($couponCode, 5, ZMCoupons::TYPPE_GV);
        $this->createdCouponIds_[] = $coupon->getId();
        $this->assertNotNull($coupon);
        $this->assertEqual($couponCode, $coupon->getCode());
        $this->assertEqual(5, $coupon->getAmount());
    }

    /**
     * Test get coupon for code.
     */
    public function testGetCouponForCode() {
        $couponCode = ZMCoupons::instance()->createCouponCode('foo@bar.com');
        $this->assertNotNull($couponCode);
        $coupon = ZMCoupons::instance()->createCoupon($couponCode, 5, ZMCoupons::TYPPE_GV);
        $this->createdCouponIds_[] = $coupon->getId();
        $loaded = ZMCoupons::instance()->getCouponForCode($couponCode);
        $this->assertEqual($coupon->getId(), $loaded->getId());
        $this->assertEqual($coupon->getCode(), $loaded->getCode());
        $this->assertEqual($coupon->getAmount(), $loaded->getAmount());
    }

    /**
     * Test get coupon for id.
     */
    public function testGetCouponForId() {
        $couponCode = ZMCoupons::instance()->createCouponCode('foo@bar.com');
        $this->assertNotNull($couponCode);
        $coupon = ZMCoupons::instance()->createCoupon($couponCode, 5, ZMCoupons::TYPPE_GV);
        $this->createdCouponIds_[] = $coupon->getId();
        $loaded = ZMCoupons::instance()->getCouponForId($coupon->getId());
        $this->assertEqual($coupon->getId(), $loaded->getId());
        $this->assertEqual($coupon->getCode(), $loaded->getCode());
        $this->assertEqual($coupon->getAmount(), $loaded->getAmount());
    }

    /**
     * Test get voucher balance for id.
     */
    public function testGetVoucherBalance() {
        ZMCoupons::instance()->setVoucherBalanceForAccountId($this->getAccountId(), 141);
        $balance = ZMCoupons::instance()->getVoucherBalanceForAccountId($this->getAccountId());
        $this->assertEqual(141, $balance);
    }

    /**
     * Test set voucher balance for id.
     */
    public function testSetVoucherBalance() {
        ZMCoupons::instance()->setVoucherBalanceForAccountId($this->getAccountId(), 39);
        $balance = ZMCoupons::instance()->getVoucherBalanceForAccountId($this->getAccountId());
        $this->assertEqual(39, $balance);
    }

    /**
     * Test restrictions.
     */
    public function testRestrictions() {
        $coupon = ZMCoupons::instance()->getCouponForId(9);
        if (null != $coupon) {
            $restrictions = $coupon->getRestrictions();
            $this->assertNotNull($restrictions);
            $direct = ZMCoupons::instance()->getRestrictionsForCouponId(9);
            $this->assertNotNull($direct);
        } else {
            $this->fail('test coupon not found');
        }
    }

    /**
     * Test is redeemable.
     */
    public function testIsCouponRedeemable() {
        $this->assertFalse(ZMCoupons::instance()->isCouponRedeemable(1));
        $this->assertTrue(ZMCoupons::instance()->isCouponRedeemable(99999));
    }

    /**
     * Test coupon tracker.
     */
    public function testCouponTracker() {
        $couponCode = ZMCoupons::instance()->createCouponCode('foo@bar.com');
        $coupon = ZMCoupons::instance()->createCoupon($couponCode, 5, ZMCoupons::TYPPE_GV);
        $this->createdCouponIds_[] = $coupon->getId();
        $account = ZMAccounts::instance()->getAccountForId($this->getAccountId());
        $gvReceiver = ZMLoader::make('GVReceiver');
        $gvReceiver->setEmail('foo@bar.com');

        ZMCoupons::instance()->createCouponTracker($coupon, $account, $gvReceiver);

        // manually check database
        $sql = "SELECT * FROM " . TABLE_COUPON_EMAIL_TRACK . "
                WHERE coupon_id = :couponId";
        $result = Runtime::getDatabase()->querySingle($sql, array('couponId' => $coupon->getId()), TABLE_COUPON_EMAIL_TRACK, 'ZMObject');
        $this->assertNotNull($result);
        $this->assertEqual('foo@bar.com', $result->getEmailTo());
    }

    /**
     * Test finalize coupon.
     */
    public function testFinalizeCoupon() {
        $couponCode = ZMCoupons::instance()->createCouponCode('foo@bar.com');
        $coupon = ZMCoupons::instance()->createCoupon($couponCode, 5, ZMCoupons::TYPPE_GV);
        $this->createdCouponIds_[] = $coupon->getId();
        ZMCoupons::instance()->finalizeCoupon($coupon->getId(), $this->getAccountId(), '127.0.0.1');

        // manually check database
        $sql = "SELECT * FROM " . TABLE_COUPON_REDEEM_TRACK . "
                WHERE coupon_id = :couponId";
        $result = Runtime::getDatabase()->querySingle($sql, array('couponId' => $coupon->getId()), TABLE_COUPON_REDEEM_TRACK, 'ZMObject');
        $this->assertNotNull($result);
        $this->assertEqual('127.0.0.1', $result->getRedeemIp());

        // check active flag
        $coupon = ZMCoupons::instance()->getCouponForCode($couponCode);
        $this->assertNotNull($coupon);
        $this->assertFalse($coupon->isActive());
    }

    /**
     * Test credit coupon.
     */
    public function testCreditCoupon() {
        // new coupon worth $5
        $couponCode = ZMCoupons::instance()->createCouponCode('foo@bar.com');
        $coupon = ZMCoupons::instance()->createCoupon($couponCode, 5, ZMCoupons::TYPPE_GV);
        $this->createdCouponIds_[] = $coupon->getId();

        ZMCoupons::instance()->creditCoupon($coupon->getId(), $this->getAccountId());
        $this->assertEqual(5, ZMCoupons::instance()->getVoucherBalanceForAccountId(1));

        // delete balance record to test create
        $sql = "DELETE FROM " . TABLE_COUPON_GV_CUSTOMER . "
                WHERE customer_id = :accountId";
        Runtime::getDatabase()->update($sql, array('accountId' => $this->getAccountId()), TABLE_COUPON_GV_CUSTOMER);

        // new coupon worth $5
        $couponCode = ZMCoupons::instance()->createCouponCode('foo@bar.com');
        $coupon = ZMCoupons::instance()->createCoupon($couponCode, 5, ZMCoupons::TYPPE_GV);
        $this->createdCouponIds_[] = $coupon->getId();

        ZMCoupons::instance()->creditCoupon($coupon->getId(), 1);
        $this->assertEqual(5, ZMCoupons::instance()->getVoucherBalanceForAccountId($this->getAccountId()));
    }
}

?>
