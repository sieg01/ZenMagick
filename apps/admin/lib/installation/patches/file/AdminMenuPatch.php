<?php
/*
 * ZenMagick - Smart e-commerce
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
namespace zenmagick\apps\store\admin\installation\patches\file;

use zenmagick\base\Runtime;
use zenmagick\apps\store\admin\installation\patches\FilePatch;

/**
 * Admin menu patch.
 *
 * @author DerManoMann <mano@zenmagick.org>
 */
class AdminMenuPatch extends FilePatch {
    protected $extrasBoxFile;
    /**
     * Create new instance.
     */
    public function __construct() {
        parent::__construct('adminMenu');
        $this->label_ = 'Install ZenMagick admin menu';
        $this->extrasBoxFile = $this->getZcAdminPath().'/includes/boxes/extras_dhtml.php';
    }


    /**
     * Checks if this patch can still be applied.
     *
     * @return boolean <code>true</code> if this patch can still be applied.
     */
    function isOpen() {
        $contents = file_get_contents($this->extrasBoxFile);
        return false === strpos($contents, "zenmagick_dhtml.php");
    }

    /**
     * Checks if this patch is ready to be applied.
     *
     * @return boolean <code>true</code> if this patch is ready and all preconditions are met.
     */
    function isReady() {
        return is_writeable($this->extrasBoxFile);
    }

    /**
     * Get the precondition message.
     *
     * <p>This will return an empty string when <code>isReady()</code> returns <code>true</code>.</p>
     *
     * @return string The preconditions message or an empty string.
     */
    function getPreconditionsMessage() {
        return $this->isReady() ? "" : "Need permission to write " . $this->extrasBoxFile;
    }

    /**
     * Execute this patch.
     *
     * @param boolean force If set to <code>true</code> it will force patching even if
     *  disabled as per settings.
     * @return boolean <code>true</code> if patching was successful, <code>false</code> if not.
     */
    function patch($force=false) {
        if ($this->isOpen() && $this->isReady()) {
            Runtime::getLogging()->error("** ZenMagick: patching zen-cart admin to auto-enable ZenMagick admin menu");
            $handle = fopen($this->extrasBoxFile, "ab");
            fwrite($handle, "<?php require('includes/boxes/zenmagick_dhtml.php'); /* added by ZenMagick installation patcher */ ?>\n");
            fclose($handle);
            \ZMFileUtils::setFilePerms($this->extrasBoxFile);
            return true;
        } else {
            Runtime::getLogging()->error("** ZenMagick: no permission to patch zen-cart admin extras_dhtml.php");
            return false;
        }

        return true;
    }

    /**
     * Revert the patch.
     *
     * @return boolean <code>true</code> if patching was successful, <code>false</code> if not.
     */
    function undo() {
        if ($this->isOpen()) {
            return true;
        }

        if (is_writeable($this->extrasBoxFile)) {
            $lines = $this->getFileLines($this->extrasBoxFile);
            $unpatchedLines = array();
            foreach ($lines as $line) {
                if (false !== strpos($line, "zenmagick_dhtml")) {
                    continue;
                }
                array_push($unpatchedLines, $line);
            }

            return $this->putFileLines($this->extrasBoxFile, $unpatchedLines);
        } else {
            Runtime::getLogging()->error("** ZenMagick: no permission to patch index.php for uninstall");
            return false;
        }
    }

}
