<?php
/*
 * ZenMagick - Smart e-commerce
 * Copyright (C) 2006-2012 zenmagick.org
 *
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

<script type="text/javascript">
    var all_zones = new Array();
    <?php
        foreach ($view->container->get('countryService')->getCountries() as $country) {
            $zones = $view->container->get('countryService')->getZonesForCountryId($country->getId());
            if (0 < count($zones)) {
                echo 'all_zones['.$country->getId() . '] = new Array();';
                foreach ($zones as $zone) {
                    echo "all_zones[".$country->getId()."][".$zone->getId()."] = '" . $zone->getName() ."';";
                }
            }
        }
    ?>
</script>
<?php /*=== include to allow PHP execution in ZM context ==*/ ?>
<script type="text/javascript"><?php echo $this->render('StorefrontBundle::dynamicState.js.php') ?></script>

<?php $countryId = 0 != $address->getCountryId() ? $address->getCountryId() : $view['settings']->get('storeCountry'); ?>
<fieldset>
    <legend><?php _vzm("Address") ?></legend>
    <table cellspacing="0" cellpadding="0" id="newaddress">
        <thead>
            <tr>
               <th id="label"></th>
               <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($customFields)) { ?>
                <?php foreach ($customFields as $fieldInfo) { ?>
                    <tr>
                        <td><?php echo $fieldInfo['label'] ?></td>
                        <td><?php echo $fieldInfo['field'] ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>
            <?php if ($view['settings']->get('isAccountGender')) { ?>
                <tr>
                    <td><?php _vzm("Title") ?><span>*</span></td>
                    <td>
                        <input type="radio" id="male" name="gender" value="m"<?php $form->checked('m'==$address->getGender()) ?> />
                        <label for="male"><?php _vzm("Mr.") ?></label>
                        <input type="radio" id="female" name="gender" value="f"<?php $form->checked('f', $address->getGender()) ?> />
                        <label for="female"><?php _vzm("Ms.") ?></label>
                    </td>
                </tr>
            <?php } ?>
            <tr>
                <td><?php _vzm("First Name") ?><span>*</span></td>
                <td><input type="text" id="firstName" name="firstName" value="<?php echo $view->escape($address->getFirstName()) ?>" /></td>
            </tr>
            <tr>
                <td><?php _vzm("Last Name") ?><span>*</span></td>
                <td><input type="text" id="lastName" name="lastName" value="<?php echo $view->escape($address->getLastName()) ?>" /></td>
            </tr>
            <?php if ($view['settings']->get('isAccountCompany')) { ?>
                <tr>
                    <td><?php _vzm("Company Name") ?></td>
                    <td><input type="text" id="companyName" name="companyName" value="<?php echo $view->escape($address->getCompanyName()) ?>" /></td>
                </tr>
            <?php } ?>
            <tr>
                <td><?php _vzm("Street Address") ?><span>*</span></td>
                <td><input type="text" id="addressLine1" name="addressLine1" value="<?php echo $view->escape($address->getAddressLine1()) ?>" <?php echo $form->fieldLength('address_book', 'entry_street_address') ?> /></td>
            </tr>
            <tr>
                <td><?php _vzm("Suburb") ?></td>
                <td><input type="text" id="suburb" name="suburb" value="<?php echo $view->escape($address->getSuburb()) ?>" <?php echo $form->fieldLength('address_book', 'entry_suburb') ?> /></td>
            </tr>
            <tr>
                <td><?php _vzm("City") ?><span>*</span></td>
                <td><input type="text" id="city" name="city" value="<?php echo $view->escape($address->getCity()) ?>" <?php echo $form->fieldLength('address_book', 'entry_city') ?> /></td>
            </tr>
            <tr>
                <td><?php _vzm("Post Code") ?><span>*</span></td>
                <td><input type="text" id="postcode" name="postcode" value="<?php echo $view->escape($address->getPostcode()) ?>" <?php echo $form->fieldLength('address_book', 'entry_postcode') ?> /></td>
            </tr>
             <tr>
                <td><?php _vzm("Country") ?><span>*</span></td>
                <td><?php echo $form->idpSelect('countryId', array_merge(array(new ZMIdNamePair("", _zm("Select Country"))), $view->container->get('countryService')->getCountries()), $countryId) ?></td>
            </tr>
            <?php if ($view['settings']->get('isAccountState')) { ?>
                <?php $zones = $view->container->get('countryService')->getZonesForCountryId($countryId); ?>
                <tr>
                    <td><?php _vzm("State/Province") ?><span>*</span></td>
                    <td>
                        <?php if (0 < count($zones)) { ?>
                            <?php echo $form->idpSelect('zoneId', $zones, $address->getZoneId()) ?>
                        <?php } else { ?>
                            <input type="text" id="state" name="state" value="<?php echo $view->escape($address->getState()) ?>" />
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
            <?php if (!$address->isPrimary()) { ?>
                 <tr>
                    <td></td>
                    <td>
                        <input type="hidden" name="_primary" value="<?php echo $address->isPrimary() ?>" />
                        <input type="checkbox" id="primary" name="primary" value="on" <?php $form->checked($address->isPrimary()) ?> />
                        <label for="primary"><?php _vzm("Use as primary address") ?></label>
                    </td>
                </tr>
            <?php } ?>
            <tr class="legend">
                <td colspan="2">
                    <input type="hidden" name="id" value="<?php echo $address->getId() ?>" />
                    <?php _vzm("<span>*</span> Mandatory fields") ?>
                </td>
            </tr>
        </tbody>
    </table>
</fieldset>