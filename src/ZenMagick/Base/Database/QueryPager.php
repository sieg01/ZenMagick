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
namespace ZenMagick\Base\Database;

use ZenMagick\Base\ZMObject;

/**
 * Paginate a query.
 *
 * @author DerManoMann <mano@zenmagick.org> <mano@zenmagick.org>
 */
class QueryPager extends ZMObject
{
    private $queryDetails;
    private $orderBy;
    private $filters;
    private $totalResultsCount;

    /**
     * Create new instance for the given query.
     *
     * @param ZenMagick\Base\Database\QueryDetails queryDetails The query details; default is <code>null</code>.
     */
    public function __construct($queryDetails=null)
    {
        $this->queryDetails = $queryDetails;
        $this->orderBy = '';
        $this->totalResultsCount = -1;
        $this->filters = array();
    }

    /**
     * Set query details.
     *
     * @param ZenMagick\Base\Database\QueryDetails queryDetails The query details.
     */
    public function setQueryDetails($queryDetails)
    {
        $this->queryDetails = $queryDetails;
    }

    /**
     * Set order by clause(s).
     *
     * @param string orderBy The order by condition(s).
     */
    public function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;
    }

    /**
     * Add a filter clause.
     *
     * @param string filter The filter condition.
     */
    public function addFilter($filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * Get the filter conditions.
     *
     * @return string The sql.
     */
    protected function getFilterSQL()
    {
        $sql = '';
        foreach ($this->filters as $filter) {
            if (!empty($sql)) {
                $sql .= ' AND ';
            }
            $sql .= $filter;
        }

        return $sql;
    }

    /**
     * Get total number of results.
     *
     * @return int The total number of results available.
     */
    public function getTotalNumberOfResults()
    {
        if (0 > $this->totalResultsCount) {
            $queryDetails = $this->queryDetails;
            $sql = $queryDetails->getSql();

            // split SQL
            $lcSql = strtolower($sql);
            // length of SQL to process
            $posTo = strlen($sql);

            // get some positions
            $posSelect = strpos($lcSql, 'select ', 0);
            $posFrom = strpos($lcSql, ' from', $posSelect);
            $posWhere = strpos($lcSql, ' where', $posFrom);
            $posGroupBy = strpos($lcSql, ' group by', $posFrom);

            // reduce to essential query

            // strip group by
            if ($posGroupBy < $posTo && $posGroupBy !== false) {
                $posTo = $posGroupBy;
            }

            // strip having
            $posHaving = strpos($lcSql, ' having', $posFrom);
            if ($posHaving < $posTo && $posHaving !== false) {
                $posTo = $posHaving;
            }

            $posOrderBy = strpos($lcSql, ' order by', $posFrom);
            if ($posOrderBy < $posTo && $posOrderBy !== false) {
                $posTo = $posOrderBy;
            }

            // create count over selected data
            if (null == ($countSql = $queryDetails->getCountCol())) {
                $countSql = trim(preg_replace('/distinct/i', '', substr($sql, $posSelect+7, $posFrom-6)));
            }
            if (strpos($lcSql, 'distinct') || strpos($lcSql, 'group by')) {
                $countSql = 'distinct '.$countSql;
            }

            // count results
            $getTotalSql = "select count(" . $countSql . ") as total " . substr($sql, $posFrom, ($posTo - $posFrom));

            // apply filters (if any)
            $filter = $this->getFilterSQL();
            if (!empty($filter)) {
                if (false === $posWhere) {
                      $getTotalSql .= ' where '.$filter;
                } else {
                      $getTotalSql .= ' and '.$filter;
                }
            }

            $result = $queryDetails->getDatabase()->querySingle($getTotalSql, $queryDetails->getArgs(), $queryDetails->getMapping(), \ZenMagick\Base\Database\Connection::MODEL_RAW);
            $this->totalResultsCount = (int) $result['total'];
        }

        return $this->totalResultsCount;
    }

    /**
     * Get results.
     *
     * @param int page The page number to retreive.
     * @param int pagination The number of results per page.
     * @return array A list of results.
     */
    public function getResults($page, $pagination)
    {
        $sql = $this->queryDetails->getSql();
        $lcSql = strtolower($sql);

        if (!empty($this->orderBy)) {
            if (false !== ($pos = strpos($lcSql, 'order by'))) {
                // keep original order also
                $sql = preg_replace('/order by /i', 'order by '.$this->orderBy.',', $sql);
            } else {
                $sql .= ' order by '.$this->orderBy;
            }
        }

        // apply filter
        $filter = $this->getFilterSQL();
        if (!empty($filter)) {
            $posFrom = strpos($lcSql, ' from', 0);
            $posWhere = strpos($lcSql, ' where', $posFrom);
            if (false !== $posWhere) {
                $sql = preg_replace('/ where /i', ' where '.$filter.' and ', $sql);
            } else {
                $posInsert = strlen($sql);
                if (false !== ($posGroupBy = strpos($lcSql, ' group by', $posFrom))) {
                    $posInsert = $posGroupBy;
                } elseif (false !== ($posHaving = strpos($lcSql, ' having', $posFrom))) {
                    $posInsert = $posHaving;
                } elseif (false !== ($posOrderBy = strpos($lcSql, ' order by', $posFrom))) {
                    $posInsert = $posOrderBy;
                }
                $sql = substr($sql, 0, $posInsert) . ' where ' . $filter . substr($sql, $posInsert);
            }
        }

        if (0 != $pagination) {
            // calcualte offset
            $offset = ($pagination * ($page - 1));
            if ($offset < 0) {
                // just in case
                $offset = 0;
            }
        } else {
            $offset = 0;
            $pagination = $this->getTotalNumberOfResults();
        }

        // limit to current page
        $sql .= " limit " . $offset . ", " . $pagination;

        return $this->queryDetails->query($sql);
    }

}
