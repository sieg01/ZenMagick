<?php
/*
 * ZenMagick - Smart e-commerce
 * Copyright (C) 2006-2012 zenmagick.org
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
namespace ZenMagick\StorefrontBundle\Controller;

use ZenMagick\Base\Beans;
use ZenMagick\Base\Toolbox;
use ZenMagick\ZenMagickBundle\Controller\DefaultController;
use ZenMagick\StoreBundle\Entity\Coupons\Coupon;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Request controller for account creation page.
 *
 * <p>The <em>createDefaultAddress</em> property can be used to control whether or not
 * to create a default address entry in the address book. Obviously, the validation rules
 * for the registration form need to be adjusted accordingly.</p>
 *
 * @author DerManoMann <mano@zenmagick.org>
 */
class CreateAccountController extends DefaultController
{
    private $createDefaultAddress;

    /**
     * Create new instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->createDefaultAddress = true;
    }

    /**
     * Set create default address flag.
     *
     * @param boolean value The new value.
     */
    public function setCreateDefaultAddress($value)
    {
        // make sure we convert to boolean; typically this would be set via a bean definition
        $this->createDefaultAddress = Toolbox::asBoolean($value);
        $this->container->get('logger')->debug('createDefaultAddress set to: '.$this->createDefaultAddress);
    }

    /**
     * {@inheritDoc}
     */
    public function processPost($request)
    {
        $registration = $this->getFormData($request);

        $settingsService = $this->container->get('settingsService');
        $translator = $this->get('translator');
        $clearPassword = $registration->getPassword();
        $account = $registration->getAccount();
        $encoder = $this->get('security.encoder_factory')->getEncoder($account);
        $account->setPassword($encoder->encodePassword($clearPassword));
        $account = $this->container->get('accountService')->createAccount($account);

        $address = null;
        $addressService = $this->container->get('addressService');
        if ($this->createDefaultAddress) {
            // account and address refer to each other...
            $address = $registration->getAddress();
            $address->setPrimary(true);
            $address->setAccountId($account->getId());
            $address = $addressService->createAddress($address);
            $account->setDefaultAddressId($address->getId());
            $this->container->get('accountService')->updateAccount($account);
        }

        // here we have a proper account, so time to let other know about it
        $args = array('request' => $request, 'controller' => $this, 'account' => $account, 'address' => $address, 'clearPassword' => $clearPassword);
        $this->container->get('event_dispatcher')->dispatch('create_account', new GenericEvent($this, $args));

        // in case it got changed
        $this->container->get('accountService')->updateAccount($account);
        if (null != $address) {
            $addressService->updateAddress($address);
        }

        $session = $request->getSession();
        $session->migrate();
        $session->setAccount($account);
        $session->restoreCart();

        $couponService = $this->container->get('couponService');
        $discountCoupon = null;
        if (null != ($newAccountDiscountCouponId = $settingsService->get('apps.store.newAccountDiscountCouponId'))) {
            $discountCoupon = $couponService->getCouponForId($newAccountDiscountCouponId, $session->getLanguageId());
        }
        $newAccountGVAmountCoupon = null;
        if (null != ($newAccountGVAmount = $settingsService->get('apps.store.newAccountGVAmount'))) {
            // set up coupon
            $couponCode = $couponService->createCouponCode($account->getEmail());
            $coupon = $couponService->createCoupon($couponCode, $newAccountGVAmount, Coupon::TYPPE_GV);
            // the receiver of the gv
            $gvReceiver = Beans::getBean('ZMGVReceiver');
            $gvReceiver->setEmail($account->getEmail());
            // the sender
            $senderAccount = Beans::getBean('ZenMagick\StoreBundle\Entity\Account');
            $senderAccount->setFirstName($settingsService->get('storeName'));
            $couponService->createCouponTracker($coupon, $senderAccount, $gvReceiver);
            $newAccountGVAmountCoupon = $coupon;
        }

        // account email
        $context = array(
            'currentAccount' => $account,
            'office_only_html' => '', 'office_only_text' => '',
            'newAccountDiscountCoupon' => $discountCoupon,
            'newAccountGVAmountCoupon' => $newAccountGVAmountCoupon
        );
        $translator = $this->container->get('translator');
        $message = $this->container->get('messageBuilder')->createMessage('welcome', $account->isHtmlEmail(), $request, $context);
        $message->setSubject($translator->trans('Welcome to %store_name%', array('%store_name%' => $settingsService->get('storeName'))))
                 ->setTo($account->getEmail(), $account->getFullName())
                 ->setFrom($settingsService->get('storeEmail'));

        $this->container->get('mailer')->send($message);

        if ($settingsService->get('isEmailAdminCreateAccount')) {
            // store copy
            $context = $this->get('macroTool')->officeOnlyEmailFooter($account->getFullName(), $account->getEmail(), $session);
            $context['currentAccount'] = $account;

            $message = $this->container->get('messageBuilder')->createMessage('welcome', $settingsService->get('isEmailAdminExtraHtml', false), $request, $context);
            $message->setSubject($translator->trans('[CREATE ACCOUNT] Welcome to %store_name%', array('%store_name%' => $settingsService->get('storeName')))
                    ->setFrom($settingsService->get('storeEmail')));
            foreach ($settingsService->get('emailAdminCreateAccount') as $email => $name) {
                $message->addTo($email, $name);
            }
            $this->container->get('mailer')->send($message);
        }

        $this->get('session.flash_bag')->success($translator->trans('Thank you for signing up'));

        $stickyUrl = $request->getFollowUpUrl();

        return $this->findView('success', array('currentAccount' => $account), array('url' => $stickyUrl));
    }

}
