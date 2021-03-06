<?php
/*
 * ZenMagick - Another PHP framework.
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

use ZenMagick\Base\Runtime;

class ZMRuntime
{
    private static $databaseMap = array();

    /**
     * Set database connection parameters for a connection name.
     *
     * @param string name name of connection
     * @param array database connection parameters
     */
    public static function setDatabase($name, $conf)
    {
        if (!isset($conf['wrapperClass'])) {
            $conf['wrapperClass'] = 'ZenMagick\\Base\\Database\\Connection';
        }
        $conf['driverOptions'] = array('table_prefix' => $conf['prefix']);
        $conf['driver'] = 'pdo_mysql';

        self::$databaseMap[$name] = $conf;
    }

    /**
     * Get a database connection by name.
     *
     * @param string name get default connection if null.
     * @return ZenMagick\Base\Database\Connection
     */
    public static function getDatabase($conf='default')
    {
        if (null !== Runtime::getContainer()) {
            return Runtime::getContainer()->get('doctrine.dbal.'.$conf.'_connection');
        }
        if (is_array(self::$databaseMap[$conf])) {
            self::$databaseMap[$conf] = Doctrine\DBAL\DriverManager::getConnection(self::$databaseMap[$conf]);
        }

        return self::$databaseMap[$conf];
    }

    public static function getDatabases()
    {
        return self::$databaseMap;
    }
}
