<?php
/*
 * ZenMagick - Another PHP framework.
 * Copyright (C) 2006-2011 zenmagick.org
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
namespace zenmagick\base\security\authentication\provider;

use Phpass\Hash;
use Phpass\Hash\Adapter\Portable;
use zenmagick\base\security\authentication\AuthenticationProvider;


/**
 * PhPass 2.0 authentication provider.
 *
 * <p>Uses <a href="https://github.com/rchouinard/phpass">Phpass</a>.</p>
 *
 * @author DerManoMann <mano@zenmagick.org>
 */
class PhPass2AuthenticationProvider implements AuthenticationProvider {
    private $hash;


    /**
     * Create instance.
     */
    public function __construct() {
        $this->hash = new Hash(new Portable(array('iterationCountLog2' => 8)));
    }

    /**
     * {@inheritDoc}
     */
    public function encryptPassword($plaintext, $salt=null) {
        return $this->hash->hashPassword($plaintext);
    }

    /**
     * {@inheritDoc}
     */
    public function validatePassword($plaintext, $encrypted) {
        return $this->hash->checkPassword($plaintext, $encrypted);
    }

}
