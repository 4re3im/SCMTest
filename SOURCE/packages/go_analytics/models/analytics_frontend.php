<?php

/**
 * ANZGO-3452 Added by John Renzo Sunico, 11/08/2017
 * Model responsible in handling analytics related
 * to Cambridge Go Frontend.
 */

class AnalyticsFrontend
{
    public static function getGoButtonClickCountPerMonthYear($month, $year)
    {
        $db = Loader::db();

        $action = 'Click';
        $sql = <<<sql
            SELECT Info as Button, COUNT(*) as Clicks FROM CupGoLogUser
            WHERE MONTH(CreatedDate) = ? AND YEAR(CreatedDate) = ? AND Action = ?
            GROUP BY Info;
sql;

        $results =  $db->GetAll($sql, [$month, $year, $action]);

        $items = [];
        foreach ($results as $result) {
            $items[$result['Button']] = $result['Clicks'];
        }

        return $items;

    }
}
