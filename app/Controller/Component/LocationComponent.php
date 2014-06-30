<?php

App::uses('DBComponent', 'Controller/Component');
App::uses('APIComponent', 'Controller/Component');

class LocationComponent extends APIComponent {

    public function openHours($params) {
        $data = $this->api->internalCall('location', 'data', $params);
        $return = $this->weekdays;
        foreach ($return as &$v) {
            $v = array(
                $v => array(
                    'open' => $data['data'][$v . '_open'],
                    'close' => $data['data'][$v . '_close']
                )
            );
        }
        $result = array('data' => $return);
        $result['options'] = array(
            'endpoint'  => 'location/openHours',
            'location_id' => $params['location_id'],
            'start_date'=> $params['start_date'],
            'end_date'  => $params['end_date'],
        );
        return $result;
    }

    public function totals($params) {        
        $rules = array(
            'location_id' => array('required', 'int'),
            'start_date' => array('required', 'date'),
            'end_date' => array('required', 'date')
        );
        $this->validate($params, $rules);
        $data = $this->api->internalCall('location', 'data', array('location_id' => $params['location_id']));
        $while_closed = $data['data']['transactions_while_closed'];
        $open_total = $while_closed == 'no' ? 'open' : 'total';
        $result = array();
        $aPath = array();
        $aPostfields = array();
        $metrics = [
            ['location', 'walkbys', 'open'],
            ['location', 'transactions', $open_total],
            ['location', 'dwell', 'open'],
            ['location', 'windowConversion', 'open'],
            ['location', 'returning', 'total'],
            ['location', 'footTraffic', 'total'],
            ['location', 'revenue', $open_total],
            ['location', 'avgTicket', $open_total],
            ['location', 'totalItems', $open_total],
            ['location', 'conversionRate', $open_total],
            ['location', 'itemsPerTransaction', $open_total],
            
            ['location', 'traffic', 'total'],
            ['location', 'devices', 'total'],
            ['location', 'timeInShop', 'total'],
            ['location', 'totalItems', $open_total],
            
        ];
        if ($params['start_date'] != $params['end_date']) {
            $result = $this->iterativeTotals('location', __FUNCTION__, $params);
            $result['dwell'] = round($result['timeInShop'] / coalesce($result['traffic'],1),2);
            $result['windowConversion'] = round(($result['traffic'] / coalesce($result['devices'],coalesce($result['traffic'], 1)))*100,2);
            $result['conversionRate'] = round(($result['transactions'] / coalesce($result['footTraffic'],  coalesce($result['transactions'], 1)))*100,2);
            $result['itemsPerTransaction'] = round($result['totalItems'] / coalesce($result['transactions'],1),2);
            $result['avgTicket'] = round($result['revenue'] / coalesce($result['transactions'],1),2);
            return $result;
        } else {
            $weekday = strtolower(date('l', strtotime($params['start_date'])));
            $isOpen = $data['data'][$weekday . '_open'] != 0 && $data['data'][$weekday . '_close'] != 0;
            foreach ($metrics as $k => $v) {
                if ($isOpen) {
                    $tmp = $this->api->internalCall($v[0], $v[1], $params);
                    $result[$v[1]] = $tmp['data']['totals'][$v[2]];
                } else {
                    $result[$v[1]] = 0;
                }
            }
            return $result;
        }
    }

    public function walkbys($params) {
        $rules = array(
            'location_id' => array('required', 'int'),
            'start_date' => array('required', 'date'),
            'end_date' => array('required', 'date')
        );
        $this->validate($params, $rules);
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('location', __FUNCTION__, $params);
        } else {
            $data = $this->api->internalCall('location', 'data', array('location_id' => $params['location_id']));
            $ap_id = (!empty($data['data']['ap_id'])) ? $data['data']['ap_id'] : 0;
            $timezone = $data['data']['timezone'];
            $factor = $data['data']['traffic_factor'];
            $factor = 1 + ((empty($factor) ? 0 : $factor / 100));
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $table = $this->getSessionsTableName($start_date, $end_date, $params['location_id'], $ap_id);
            $oDb = DBComponent::getInstance($table, 'swarmdataRead');
            $sSQL = <<<SQL
SELECT 
    ROUND(COUNT(walkbys)*$factor) as value, 
    hour, 
    date
FROM(
    SELECT 
        DISTINCT ses1.mac_id as walkbys,        
        DATE_FORMAT(convert_tz(time_login,'GMT', '$timezone'), '%Y-%m-%d') AS date,
        DATE_FORMAT(convert_tz(time_login,'GMT', '$timezone'), '%k') AS hour
    FROM $table ses1
    INNER JOIN mac_address 
        ON ses1.mac_id = mac_address.id
    WHERE ( status !='noise' AND NOISE is false) 
      AND (network_id= $ap_id) 
      AND (sessionid='passerby') 
      AND time_login BETWEEN '$start_date' AND '$end_date'
    GROUP BY ses1.mac_id
) as t2 GROUP BY date ASC, hour ASC             
SQL;
            $aRes = $oDb->fetchAll($sSQL);
            return $this->format($aRes, $data, $params, '/location/' . __FUNCTION__, 0, 't2');
        }
    }

    /**
     * API Method to load traffic data from door sensors
     * @param $params Array containing location_id, start_date and end_date
     * @return array Array of results formatted for display in the dashboard
     */
    public function sensorTraffic($params) {
        // Set validation rules and validate parameters
        $rules = array(
            'location_id' => array('required', 'int'),
            'start_date'  => array('required', 'date'),
            'end_date'    => array('required', 'date')
        );
        $this->validate($params, $rules);

        // Pass method and parameters to iteration function if the dates are different
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('location', __FUNCTION__, $params);
        }

        // Get location data for location id including location's timezone and traffic factor
        $location_id = $params['location_id'];
        $data = $this->api->internalCall('location', 'data', array('location_id' => $location_id));
        $timezone = $data['data']['timezone'];

        // apply timezone to dates entered and query for sensor detections
        list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
        $table = 'visitorEvent';
        $oDb = DBComponent::getInstance($table, 'portal');
        $sSQL = <<<SQL
SELECT
    ROUND(COUNT(*)) AS detect_count,
    DATE_FORMAT(convert_tz(ts,'GMT', '$timezone'), '%Y-%m-%d') AS date,
    DATE_FORMAT(convert_tz(ts,'GMT','$timezone'), '%k') AS hour
FROM visitorEvent
WHERE
    entered = 1 AND         
    location_id=$location_id AND
    ts BETWEEN '$start_date' AND '$end_date'
GROUP BY date ASC, hour ASC
SQL;
        $aRes = $oDb->fetchAll($sSQL);
        // return formatted result
        return $this->format($aRes, $data, $params, '/location/' . __FUNCTION__, 0, 0, 'detect_count');
    }

    public function purchaseInfo($params) {
        $rules = array(
            'location_id' => array('required', 'int'),
            'start_date'  => array('required', 'date'),
            'end_date'    => array('required', 'date')
        );
        $this->validate($params, $rules);
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeQuery('location', __FUNCTION__, $params);
        } else {
            $data = $this->api->internalCall('location', 'data', array('location_id' => $params['location_id']));
            $timezone = $data['data']['timezone'];

            $register_filter = @$data['data']['register_filter'];
            $register_filter = (!empty($register_filter)) ? " AND i.register_id = $register_filter " : '';
            $outlet_filter = @$data['data']['outlet_filter'];
            $outlet_filter = (!empty($outlet_filter)) ? " AND i.outlet_id = $outlet_filter " : '';

            $lightspeed_id = (empty($data['data']['lightspeed_id'])) ? 0 : $data['data']['lightspeed_id'];
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $table = 'invoices';
            $oDb = DBComponent::getInstance($table, 'pos');
            $aRes = array();
            if (!empty($lightspeed_id)) {
                $sSQL = <<<SQL
SELECT 
    COUNT(*) as transactions,
    SUM(revenue) as revenue,
    SUM(total_items) as total_items,
    date,
    hour
FROM (
    SELECT
            i.invoice_id as transactions,
        i.total as revenue,
        SUM(il.quantity) as total_items,
        DATE_FORMAT(CONVERT_TZ(i.ts,'GMT','$timezone'),'%Y-%m-%d' ) AS date,
        DATE_FORMAT(CONVERT_TZ(i.ts,'GMT','$timezone'), '%k') AS hour
        FROM invoices i
    LEFT JOIN invoice_lines il ON i.invoice_id = il.invoice_id
    WHERE i.store_id= $lightspeed_id
        AND i.completed 
        AND i.total != 0 
            $register_filter
            $outlet_filter
        AND i.ts BETWEEN '$start_date' AND '$end_date'
    GROUP BY i.invoice_id
) t2
GROUP BY date ASC, hour ASC             
SQL;
                $aRes = $oDb->fetchAll($sSQL);
            }
            return $aRes;
        }
    }

    public function transactions($params) {
        $rules = array(
            'location_id' => array('required', 'int'),
            'start_date'  => array('required', 'date'),
            'end_date'    => array('required', 'date')
        );
        $this->validate($params, $rules);
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('location', __FUNCTION__, $params);
        } else {
            $data = $this->api->internalCall('location', 'data', array('location_id' => $params['location_id']));
            $aRes = $this->api->internalCall('location', 'purchaseInfo', $params);
            $timezone = $data['data']['timezone'];
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            return $this->format($aRes, $data, $params, '/location/' . __FUNCTION__, 0, 't2', __FUNCTION__);
        }
    }

    public function revenue($params) {
        $rules = array(
            'location_id' => array('required', 'int'),
            'start_date'  => array('required', 'date'),
            'end_date'    => array('required', 'date')
        );
        $this->validate($params, $rules);
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('location', __FUNCTION__, $params);
        } else {
            $data = $this->api->internalCall('location', 'data', array('location_id' => $params['location_id']));
            $aRes = $this->api->internalCall('location', 'purchaseInfo', $params);
            $timezone = $data['data']['timezone'];
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            return $this->format($aRes, $data, $params, '/location/' . __FUNCTION__, 0, 't2', __FUNCTION__);
        }
    }

    public function totalItems($params) {
        $rules = array(
            'location_id' => array('required', 'int'),
            'start_date'  => array('required', 'date'),
            'end_date'    => array('required', 'date')
        );
        $this->validate($params, $rules);
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('location', __FUNCTION__, $params);
        } else {
            $data = $this->api->internalCall('location', 'data', array('location_id' => $params['location_id']));
            $aRes = $this->api->internalCall('location', 'purchaseInfo', $params);
            $timezone = $data['data']['timezone'];
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            return $this->format($aRes, $data, $params, '/location/' . __FUNCTION__, 0, 't2', 'total_items');
        }
    }

    private function returningByHour($start_date, $end_date, $timezone, $location_id, $ap_id, $factor) {
        $table = $this->getSessionsTableName($start_date, $end_date, $location_id, $ap_id);
        $sSQL = <<<SQL
SELECT
    x.hour,
    ROUND(COUNT(z.login)*$factor) as value 
FROM(
    SELECT 0 as hour 
    UNION SELECT 1   UNION SELECT 2     UNION SELECT 3  
    UNION SELECT 4   UNION SELECT 5 UNION SELECT 6  
    UNION SELECT 7   UNION SELECT 8 UNION SELECT 9  
    UNION SELECT 10  UNION SELECT 11    UNION SELECT 12 
    UNION SELECT 13  UNION SELECT 14    UNION SELECT 15 
    UNION SELECT 16  UNION SELECT 17    UNION SELECT 18 
    UNION SELECT 19  UNION SELECT 20    UNION SELECT 21 
    UNION SELECT 22  UNION SELECT 23
) x
INNER JOIN (
    SELECT login,logout
    FROM (
      SELECT 
        DISTINCT ses1.mac_id as unique_mac,(CONVERT_TZ(time_login,'GMT','$timezone')) as max_login,
        DATE_FORMAT((CONVERT_TZ(time_login,'GMT','$timezone')),'%H') as login,
        DATE_FORMAT((CONVERT_TZ(time_logout,'GMT','$timezone')),'%H') as logout
      FROM $table  ses1
      INNER JOIN mac_address 
        ON ses1.mac_id = mac_address.id
      WHERE (mac_address.status<>'noise') 
        AND (sessionid='instore' OR sessionid='passive' OR sessionid='active' OR sessionid='login') 
        AND time_logout IS NOT NULL 
        AND (network_id= $ap_id)
        AND time_login BETWEEN '$start_date' AND '$end_date'
      GROUP BY ses1.mac_id
    ) y
    INNER JOIN network_mac_logins nml
        ON nml.first_logout   < y.max_login 
       AND nml.mac_id=y.unique_mac
    WHERE nml.network_id= $ap_id
      AND nml.first_logout IS NOT NULL
      AND y.max_login IS NOT NULL
) z ON x.hour BETWEEN z.login AND z.logout
GROUP BY x.hour      
ORDER BY x.hour ASC
SQL;
        $oDb = DBComponent::getInstance($table, 'swarmdataRead');
        return $oDb->fetchAll($sSQL);
    }

    private function returningByDate($date, $data, $timezone, $location_id, $ap_id, $factor) {
        list($start_date, $end_date) = $this->getOpenCloseTimes($date, $data, $timezone);
        $table = $this->getSessionsTableName($start_date, $end_date, $location_id, $ap_id);
        $sSQL = <<<SQL
SELECT  
    date(y.max_login) as date,
    ROUND(COUNT(distinct y.unique_mac)*$factor) as value
FROM (
    SELECT 
    DISTINCT ses1.mac_id as unique_mac,
    date((CONVERT_TZ(time_login,'GMT','$timezone'))) as max_login 
    FROM $table  ses1
    INNER JOIN mac_address 
    ON ses1.mac_id = mac_address.id
    WHERE (mac_address.status<>'noise')
      AND (sessionid='instore' OR sessionid='passive' OR sessionid='active' OR sessionid='login') 
      AND time_login IS NOT NULL
      AND (network_id = $ap_id) 
      AND time_login BETWEEN '$start_date' AND '$end_date'  
) y
INNER JOIN network_mac_logins nml
    ON  nml.first_logout < y.max_login 
    AND nml.mac_id=y.unique_mac
WHERE nml.first_logout IS NOT NULL
  AND nml.network_id = $ap_id
  AND y.max_login IS NOT NULL
GROUP BY date       
SQL;
        $oDb = DBComponent::getInstance($table, 'swarmdataRead');
        return $oDb->fetchAll($sSQL);
    }

    public function returning($params) {
        $rules = array(
            'location_id' => array('required', 'int'),
            'start_date'  => array('required', 'date'),
            'end_date'    => array('required', 'date')
        );
        $this->validate($params, $rules);
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('location', __FUNCTION__, $params);
        } else {
            $data = $this->api->internalCall('location', 'data', array('location_id' => $params['location_id']));
            $ap_id = (!empty($data['data']['ap_id'])) ? $data['data']['ap_id'] : 0;
            $timezone = $data['data']['timezone'];
            $factor   = $data['data']['traffic_factor'];
            $factor = 1 + ((empty($factor) ? 0 : $factor / 100));
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $aByHour = $this->returningByHour($start_date, $end_date, $timezone, $params['location_id'], $ap_id, $factor);
            $aByDate = $this->returningByDate($params['start_date'], $data, $timezone, $params['location_id'], $ap_id, $factor);
            return $this->hourlyDailyFormat($aByDate, $aByHour, $data, $params, '/location/' . __FUNCTION__, 0, 'x');
        }
    }

    private function footTrafficByHour($start_date, $end_date, $timezone, $location_id, $ap_id, $factor) {
        $table = $this->getSessionsTableName($start_date, $end_date, $location_id, $ap_id);
        $sSQL = <<<SQL
SELECT 
    x.hour,
    ROUND(COUNT(y.mac_id)*$factor) as value
FROM(
    SELECT 0 as hour 
    UNION SELECT 1   UNION SELECT 2     UNION SELECT 3  
    UNION SELECT 4   UNION SELECT 5 UNION SELECT 6  
    UNION SELECT 7   UNION SELECT 8 UNION SELECT 9  
    UNION SELECT 10  UNION SELECT 11    UNION SELECT 12 
    UNION SELECT 13  UNION SELECT 14    UNION SELECT 15 
    UNION SELECT 16  UNION SELECT 17    UNION SELECT 18 
    UNION SELECT 19  UNION SELECT 20    UNION SELECT 21 
    UNION SELECT 22  UNION SELECT 23
) x
LEFT JOIN
(
    SELECT 
        ses1.mac_id,DATE_FORMAT(MIN(convert_tz(time_login,'GMT','$timezone')), '%H') AS walk_in,
        DATE_FORMAT(max(convert_tz(time_logout,'GMT','$timezone')),'%H') AS walk_out
    FROM $table ses1
    INNER JOIN mac_address 
        ON ses1.mac_id = mac_address.id
    WHERE (mac_address.status<>'noise') 
      AND (sessionid='instore' OR sessionid='passive' OR sessionid='active' OR sessionid='login') 
      AND time_logout IS NOT NULL
      AND (network_id=$ap_id)
      AND time_login BETWEEN '$start_date' AND '$end_date'
    GROUP BY ses1.mac_id
) y ON x.hour between walk_in and walk_out 
GROUP BY x.hour
ORDER BY x.hour ASC
SQL;
        $oDb = DBComponent::getInstance($table, 'swarmdataRead');
        return $oDb->fetchAll($sSQL);
    }

    private function footTrafficByDate($date, $data, $timezone, $location_id, $ap_id, $factor) {
        list($start_date, $end_date) = $this->getOpenCloseTimes($date, $data, $timezone);
        $table = $this->getSessionsTableName($start_date, $end_date, $location_id, $ap_id);
        $sSQL = <<<SQL
SELECT 
    DATE(CONVERT_TZ(time_login,'GMT','$timezone')) as date,
    ROUND(COUNT(DISTINCT ses1.mac_id)*$factor) as value 
FROM $table ses1
INNER JOIN mac_address 
    ON ses1.mac_id = mac_address.id
WHERE (mac_address.status<>'noise')
 AND (sessionid='instore' OR sessionid='passive' OR sessionid='active' OR sessionid='login') 
  AND time_logout IS NOT NULL
 AND (network_id= $ap_id) 
 AND time_login BETWEEN '$start_date' AND '$end_date'  
GROUP BY date
SQL;
        $oDb = DBComponent::getInstance($table, 'swarmdataRead');
        return $oDb->fetchAll($sSQL);
    }

    public function footTraffic($params) {
        $rules = array(
            'location_id' => array('required', 'int'),
            'start_date'  => array('required', 'date'),
            'end_date'    => array('required', 'date')
        );
        $this->validate($params, $rules);
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('location', __FUNCTION__, $params);
        } else {
            $data = $this->api->internalCall('location', 'data', array('location_id' => $params['location_id']));
            $ap_id = (!empty($data['data']['ap_id'])) ? $data['data']['ap_id'] : 0;
            $timezone = $data['data']['timezone'];
            $factor = $data['data']['traffic_factor'];
            $factor = 1 + ((empty($factor) ? 0 : $factor / 100));
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $aByHour = $this->footTrafficByHour($start_date, $end_date, $timezone, $params['location_id'], $ap_id, $factor);
            $aByDate = $this->footTrafficByDate($params['start_date'], $data, $timezone, $params['location_id'], $ap_id, $factor);
            return $this->hourlyDailyFormat($aByDate, $aByHour, $data, $params, '/location/' . __FUNCTION__, 0, 'x');
        }
    }

    public function timeInShop($params) {
        $rules = array(
            'location_id' => array('required', 'int'),
            'start_date'  => array('required', 'date'),
            'end_date'    => array('required', 'date')
        );
        $this->validate($params, $rules);
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('location', __FUNCTION__, $params);
        } else {
            $data = $this->api->internalCall('location', 'data', array('location_id' => $params['location_id']));
            $ap_id = (!empty($data['data']['ap_id'])) ? $data['data']['ap_id'] : 0;
            $timezone = $data['data']['timezone'];
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $table = $this->getSessionsTableName($start_date, $end_date, $params['location_id'], $ap_id);
            $oDb = DBComponent::getInstance($table, 'swarmdataRead');
            $sSQL = <<<SQL
 SELECT 
     SUM(dwell_time) as value,
     date,
     hour
 FROM(
    SELECT 
       ses1.mac_id,
       (MAX(UNIX_TIMESTAMP(time_logout))-MIN(UNIX_TIMESTAMP(time_login))) as dwell_time,
       DATE_FORMAT(convert_tz(time_login,'GMT', '$timezone'), '%Y-%m-%d') AS date,
       DATE_FORMAT(convert_tz(time_login,'GMT', '$timezone'), '%k') AS hour
    FROM $table as ses1
    INNER JOIN mac_address 
            ON ses1.mac_id = mac_address.id
    WHERE status !='noise' 
       AND (         
         sessionid='instore' OR 
         sessionid='passive' OR 
         sessionid='active' OR 
         sessionid='login'
       )     
       AND noise IS false 
       AND time_logout IS NOT NULL 
       AND time_login != time_logout
       AND network_id= $ap_id
       AND time_login BETWEEN '$start_date' AND '$end_date'
    GROUP BY ses1.mac_id, date, hour
    HAVING 18000 > dwell_time
 ) t2
GROUP BY date, hour      
SQL;
            $aRes = $oDb->fetchAll($sSQL);
            return $this->format($aRes, $data, $params, '/location/' . __FUNCTION__, 0, 't2');
        }
    }

    public function traffic($params) {
        $rules = array(
            'location_id' => array('required', 'int'),
            'start_date'  => array('required', 'date'),
            'end_date'    => array('required', 'date')
        );
        $this->validate($params, $rules);
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('location', __FUNCTION__, $params);
        } else {
            $data = $this->api->internalCall('location', 'data', array('location_id' => $params['location_id']));
            $ap_id = (!empty($data['data']['ap_id'])) ? $data['data']['ap_id'] : 0;
            $timezone = $data['data']['timezone'];
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $table = $this->getSessionsTableName($start_date, $end_date, $params['location_id'], $ap_id);
            $oDb = DBComponent::getInstance($table, 'swarmdataRead');
            $sSQL = <<<SQL
    SELECT 
       COUNT(DISTINCT ses1.mac_id) as value,
       DATE_FORMAT(convert_tz(time_login,'GMT', '$timezone'), '%Y-%m-%d') AS date,
       DATE_FORMAT(convert_tz(time_login,'GMT', '$timezone'), '%k') AS hour
    FROM $table as ses1
    INNER JOIN mac_address 
            ON ses1.mac_id = mac_address.id
    WHERE status !='noise' 
       AND (         
         sessionid='instore' OR 
         sessionid='passive' OR 
         sessionid='active' OR 
         sessionid='login'
       )     
       AND noise IS false 
       AND time_logout IS NOT NULL 
       AND time_login != time_logout
       AND network_id= $ap_id
       AND time_login BETWEEN '$start_date' AND '$end_date'
    GROUP BY date, hour    
SQL;
            $aRes = $oDb->fetchAll($sSQL);
            return $this->format($aRes, $data, $params, '/location/' . __FUNCTION__, 0, 0);
        }
    }

    public function devices($params) {
        $rules = array(
            'location_id' => array('required', 'int'),
            'start_date'  => array('required', 'date'),
            'end_date'    => array('required', 'date')
        );
        $this->validate($params, $rules);
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('location', __FUNCTION__, $params);
        } else {
            $data = $this->api->internalCall('location', 'data', array('location_id' => $params['location_id']));
            $ap_id = (!empty($data['data']['ap_id'])) ? $data['data']['ap_id'] : 0;
            $timezone = $data['data']['timezone'];
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $table = $this->getSessionsTableName($start_date, $end_date, $params['location_id'], $ap_id);
            $oDb = DBComponent::getInstance($table, 'swarmdataRead');
            $sSQL = <<<SQL
    SELECT 
       COUNT(DISTINCT ses1.mac_id) as value,
       DATE_FORMAT(convert_tz(time_login,'GMT', '$timezone'), '%Y-%m-%d') AS date,
       DATE_FORMAT(convert_tz(time_login,'GMT', '$timezone'), '%k') AS hour
    FROM $table as ses1
    INNER JOIN mac_address 
            ON ses1.mac_id = mac_address.id
    WHERE status !='noise' 
       AND (         
         sessionid='passerby' OR 
         sessionid='instore' OR 
         sessionid='passive' OR 
         sessionid='active' OR 
         sessionid='login'
       )     
       AND noise IS false 
       AND time_logout IS NOT NULL         
       AND time_login != time_logout
       AND network_id= $ap_id
       AND time_login BETWEEN '$start_date' AND '$end_date'
    GROUP BY date, hour    
SQL;
            $aRes = $oDb->fetchAll($sSQL);
            return $this->format($aRes, $data, $params, '/location/' . __FUNCTION__, 0, 0);
        }
    }

    //Rates
    public function itemsPerTransaction($params) {
        $tt = $this->api->internalCall('location', 'totalItems', $params);
        $tr = $this->api->internalCall('location', 'transactions', $params);
        $result = $this->calculate($tt, $tr, true);
        $result['options'] = array(
            'endpoint' => '/location/' . __FUNCTION__,
            'location_id' => $params['location_id'],
            'start_date' => $params['start_date'],
            'end_date' => $params['end_date'],
        );
        return $result;
    }

    public function windowConversion($params) {
        $ft = $this->api->internalCall('location', 'traffic', $params);
        $nd = $this->api->internalCall('location', 'devices', $params);
        $result = $this->percentify($ft, $nd);
        $result['options'] = array(
            'endpoint'    => '/location/' . __FUNCTION__,
            'location_id' => $params['location_id'],
            'start_date'  => $params['start_date'],
            'end_date'    => $params['end_date'],
        );
        return $result;
    }

    public function avgTicket($params) {
        $re = $this->api->internalCall('location', 'revenue', $params);
        $tr = $this->api->internalCall('location', 'transactions', $params);
        $result = $this->calculate($re, $tr, true);
        $result['options'] = array(
            'endpoint' => '/location/' . __FUNCTION__,
            'location_id' => $params['location_id'],
            'start_date'  => $params['start_date'],
            'end_date'    => $params['end_date'],
        );
        return $result;
    }

    public function conversionRate($params) {
        $tr = $this->api->internalCall('location', 'transactions', $params);
        $ft = $this->api->internalCall('location', 'footTraffic', $params);
        $result = $this->percentify($tr, $ft);
        $result['options'] = array(
            'endpoint' => '/location/' . __FUNCTION__,
            'location_id' => $params['location_id'],
            'start_date'  => $params['start_date'],
            'end_date'    => $params['end_date'],
        );
        return $result;
    }

    public function dwell($params) {
        $ts = $this->api->internalCall('location', 'timeInShop', $params);
        $tr = $this->api->internalCall('location', 'traffic', $params);
        $result = $this->calculate($ts, $tr, true);
        $result['options'] = array(
            'endpoint' => '/location/' . __FUNCTION__,
            'location_id' => $params['location_id'],
            'start_date'  => $params['start_date'],
            'end_date'    => $params['end_date'],
        );
        return $result;
    }
    
    public function data($params) {
        $rules = array('location_id' => array('required', 'int'));
        $this->validate($params, $rules);
        $location_id = $params['location_id'];
        $oLocation = new Location();
        $oLocation = $oLocation->find('first', ['conditions' => ['Location.id' => $location_id]]);
        $aFields = [
            'ap_id' => 'network_id',
            'timezone' => 'timezone',
            'location_open' => 'location_open',
            'location_close' => 'location_close',
            'lightspeed_id' => 'pos_store_id',
            'monday_open' => 'monday_open',
            'monday_close' => 'monday_close',
            'tuesday_open' => 'tuesday_open',
            'tuesday_close' => 'tuesday_close',
            'wednesday_open' => 'wednesday_open',
            'wednesday_close' => 'wednesday_close',
            'thursday_open' => 'thursday_open',
            'thursday_close' => 'thursday_close',
            'friday_open' => 'friday_open',
            'friday_close' => 'friday_close',
            'saturday_open' => 'saturday_open',
            'saturday_close' => 'saturday_close',
            'sunday_open' => 'sunday_open',
            'sunday_close' => 'sunday_close',
            'network_provider' => 'network_provider',
            'register_filter' => 'register_filter',
            'outlet_filter' => 'outlet_filter',
            'country' => 'country',
            'nightclub_hours' => 'nightclub_hours',
            'traffic_factor' => 'traffic_factor',
            'no_rollups' => 'no_rollups',
            'no_cache' => 'no_cache',
            'nightclub_hours_location' => 'nightclub_hours_location',
            'transactions_while_closed' => 'transactions_while_closed'
        ];
        $tmp = array('data' => array());
        foreach ($aFields as $kOld => $kNew) {
            $tmp['data'][$kOld] = settVal($kNew, $oLocation['Setting']);
        }
        if (empty($tmp['data']['network_provider'])) {
            $tmp['data']['network_provider'] = 'gp';
        }
        if (empty($tmp['data']['timezone'])) {
            $tmp['data']['timezone'] = 'America/Los_Angeles';
        }
        foreach (array('open', 'close') as $state) {
            if (empty($tmp['data']['location_' . $state])) {
                $tmp['data']['location_' . $state] = $state == 'open' ? '09:00' : '21:00';
            }
            foreach ($this->weekdays as $day) {
                $daystate = $day . '_' . $state;
                $val = isset($tmp['data'][$daystate]) ? $tmp['data'][$daystate] : null;
                if (is_null($val) || trim($val) === '') {
                    $tmp['data'][$daystate] = $tmp['data']['location_' . $state];
                }
            }
        }
        return $tmp;
    } 
}
