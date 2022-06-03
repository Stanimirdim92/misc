<?php

//SELECT
//    PC1.name AS L1,
//    PC2.name AS L2,
//    PC3.name AS L3,
//    PC4.name AS L4,
//    PC5.name AS L5,
//    CONCAT_WS(' > ',
//            PC2.name,
//            PC3.name,
//            PC4.name,
//            PC5.name) AS category_path
//FROM
//    product_categories AS PC1
//        LEFT JOIN
//    product_categories AS PC2 ON PC2.`parent_id` = PC1.id
//        LEFT JOIN
//    product_categories AS PC3 ON PC3.`parent_id` = PC2.id
//        LEFT JOIN
//    product_categories AS PC4 ON PC4.`parent_id` = PC3.id
//        LEFT JOIN
//    product_categories AS PC5 ON PC5.`parent_id` = PC4.id
//WHERE
//    PC1.`parent_id` = 0
//ORDER BY category_path ASC;

//    SET @MY_SCHEMA = "";
//
//-- tables
//SELECT DISTINCT
//    CONCAT("ALTER TABLE ", concat('`',TABLE_NAME,'`')," CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;") as queries
//FROM INFORMATION_SCHEMA.TABLES
//WHERE TABLE_SCHEMA=@MY_SCHEMA
//AND TABLE_TYPE="BASE TABLE"
//
//UNION
//
//-- table columns
//SELECT DISTINCT
//    CONCAT("ALTER TABLE ", concat('`',C.TABLE_NAME,'`'), " CHANGE ", concat('`',C.COLUMN_NAME,'`'), " ", concat('`',C.COLUMN_NAME,'`'), " ", C.COLUMN_TYPE, " CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;") as queries
//FROM INFORMATION_SCHEMA.COLUMNS as C
//    LEFT JOIN INFORMATION_SCHEMA.TABLES as T
//        ON C.TABLE_NAME = T.TABLE_NAME
//WHERE C.COLLATION_NAME is not null
//AND C.TABLE_SCHEMA=@MY_SCHEMA
//AND T.TABLE_TYPE="BASE TABLE"


//SELECT
//    table_schema,
//    table_name,
//    column_name
//FROM
//    information_schema.columns
//WHERE
//    column_key = 'PRI'
//    AND extra <> 'auto_increment'
//    AND data_type = 'int'
//    SELECT DISTINCT  table_nameFROM  INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_NAME = 'inventory_id';


$second = 1;
$minute = 60;
$hour = 3600;
$day = 86400;
$week = 604800;
$month = 2592000;
$year = 31536000;


function getWebsitePage($url) {
    $ch = curl_init();
    $timeout = 10;
    //$userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';
    //$userAgent = 'Googlebot/2.1 (+http://www.google.com/bot.html)';
    $userAgent = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';
    $ip = "64.233.160.100";
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array("REMOTE_ADDR: {$ip}", "HTTP_X_FORWARDED_FOR: {$ip}"));
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

$sites = array(

);

foreach ($sites as $site) {
    $returned_content = getWebsitePage($site);
    echo "<pre>".print_r($returned_content, true)."</pre>";
}

    function weeks($month, $year){
        $firstday = date("w", mktime(0, 0, 0, $month, 1, $year));
        $lastday = date("t", mktime(0, 0, 0, $month, 1, $year));
        if ($firstday!=0) $count_weeks = 1 + ceil(($lastday-8+$firstday)/7);
        else $count_weeks = 1 + ceil(($lastday-1)/7);
        return $count_weeks;
    }

// function merge($sheetProducts = [], $quotesProducts = [])
// {
//     if (!count($sheetProducts) && !count($quotesProducts)) {
//         return [];
//     }

//     $arr = [];
//     if (count($sheetProducts) && count($quotesProducts)) {
//         foreach ($quotesProducts as $key => $destination) {
//             if ($sheetProducts[$key]['inventory_id'] == $destination['inventory_id']) {
//                 $arr[] = array_merge($sheetProducts[$key], $destination);
//             } else {
//                 $arr[] = $destination;
//             }
//         }
//     } else if (!count($sheetProducts) && count($quotesProducts)) {
//         $arr = $quotesProducts;
//     } else if (count($sheetProducts) && !count($quotesProducts)) {
//         $arr = $sheetProducts;
//     }

//     return $arr;
// }


$protocols = array( 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet', 'mms', 'rtsp', 'svn', 'tel', 'fax', 'xmpp' );

$channel_id = 1;
$fixed_tables = implode(" ", $fixed_tables);
$file = "all_tables_for_channel_{$channel_id}.sql";
echo exec("C:\\xampp\mysql\bin\mysqldump.exe --opt -uusername -ppassword quickview {$fixed_tables} > {$file}");
echo exec("C:\\xampp\mysql\bin\mysql.exe -uusername -ppassword asddsa < {$file}");
unlink($file);

foreach ($other as $key => $table) {
    if($table == "products") {
        echo exec("C:\\xampp\mysql\bin\mysqldump.exe --opt -uusername -ppassword quickview {$table} --where=\"channels LIKE '%[{$channel_id}]%'\" > {$table}.sql");
    } else if($table == "product_categories") {
        echo exec("C:\\xampp\mysql\bin\mysqldump.exe --opt -uusername -ppassword quickview {$table} --where=\"channels LIKE '%[{$channel_id}]%'  OR name='ROOT' \" > {$table}.sql");
    }

    echo exec("C:\\xampp\mysql\bin\mysql.exe -uusername -ppassword asddsa < {$table}.sql");
    unlink("{$table}.sql");
}


    /**
     * Get the closest Saturday
     *
     * @param  string $dayName
     *
     * @return string
     */
    private function searchForSaturday($dayName = null)
    {
        global $db;
        $nonWorkingDays = $db->fetchAll("SELECT date FROM non_working_dates");

        $nonWorkingDay = array();
        foreach ($nonWorkingDays as $key => $newDay) {
            $nonWorkingDay[] = date('d/m/Y', $newDay['date']);
        }

        $isLeapYear = date('L');
        $totalDays = 365;
        if ($isLeapYear == 1) {
            $totalDays = 366;
        }

        $currentYearDay = date('z', time()) + 1;
        $dayPlus = 1;
        for ($i = $currentYearDay; $i <= $totalDays; $i++) {
            $d = mktime(0, 0, 0, 1, $i, date("Y"));
            $isThisTheDate = date("d/m/Y", $d);
            $dName = date('D', $d);

            if ($dName == 'Fri' && time() > strtotime('13:00:00')) {
                $dayPlus++;
            }

            if (time() > strtotime('13:00:00')) {
                do {
                    $dateTime = DateTime::createFromFormat('d/m/Y', $isThisTheDate);
                    $dateTime->modify("+{$dayPlus} days");
                    $dayPlus = 0;
                    $dayPlus++;
                    $dName = date('D', $dateTime->getTimestamp());
                    $isThisTheDate = date('d/m/Y', $dateTime->getTimestamp());

                } while ($dName != $dayName || in_array($isThisTheDate, $nonWorkingDay));

                return $isThisTheDate;
            } else if ($dayName == date('D', $d) && !in_array($isThisTheDate, $nonWorkingDay)) {
                return $isThisTheDate;
            }
        }

        return;
    }

    /**
     * Gte the closest working day
     *
     * @param  int    $dayName
     *
     * @return string
     */
    private function searchForNextWorkingDay($dayName = 0)
    {
        $dayName = (int) $dayName;
        $nonWorkingDays = $this->db->fetchAll("SELECT date FROM non_working_dates");
        $nonWorkingDay = array();
        if (count($nonWorkingDays) > 0) {
            foreach ($nonWorkingDays as $key => $newDay) {
                $nonWorkingDay[] = date('d/m/Y', $newDay['date']);
            }
        }

        $isLeapYear = date('L');
        $totalDays = 365;
        if ($isLeapYear == 1) {
            $totalDays = 366;
        }

        $currentYearDay = date('z', time()) + 1;
        $dayName = (int) $dayName;
        for ($i = $currentYearDay; $i <= $totalDays; $i++) {
            if ($dayName > 0) {
                $d = mktime(0, 0, 0, 1, $i, date("Y"));
                $isThisTheDate = date("d/m/Y", $d);
                if (time() > strtotime('13:00:00')) {
                    $dayName++;
                    return findDate($isThisTheDate, $dayName, $nonWorkingDay);
                }

                return findDate($isThisTheDate, $dayName, $nonWorkingDay);
            }
        }

        return '';
    }

    /**
     * @param  string $isThisTheDate
     * @param  int $dayName
     * @param  array $nonWorkingDay
     *
     * @return string
     */
    function findDate($isThisTheDate, $dayName = 1, $nonWorkingDay = array())
    {
        do {
            $dateTime = DateTime::createFromFormat('d/m/Y', $isThisTheDate);
            $dateTime->modify("+{$dayName} days");
            $dayName = 0;
            $dayName++;
            $dName = date('D', $dateTime->getTimestamp());
            $isThisTheDate = $dateTime->format('d/m/Y');
        } while (in_array(date("w", strtotime($dName)), array(0,6)) || in_array($isThisTheDate, $nonWorkingDay));

        return $isThisTheDate;
    }
    echo searchForSaturday('Sat');
    echo searchForNextWorkingDay('1');

    /**
     * Get a protected/private method reflection for testing.
     *
     * @param object $obj The instantiated instance of your class
     * @param string $name The name of your protected/private method
     * @param array $args Arguments for the protected/private method
     *
     * @return ReflectionClass The method you asked for
     */
    private static function getProtectedOrPrivateMethod($obj, $name, array $args) {
        $class = new ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method->invokeArgs($obj, $args);
    }

function prime($num) {
    $number = 2;
    $range = range(2, $num);
    $primes = array_combine($range, $range);

    while ($number*$number < $num) {
        for ($i = $number; $i <= $num; $i += $number) {
            if ($i == $number) {
                continue;
            }
            unset($primes[$i]);
        }
        $number = next($primes);
    }
    return $primes;
}

function bubbleSort(array $arr = [])
{
    $count = count($arr);
    if ($count <= 1) {
        return $arr;
    }

    for ($i = 0; $i < $count; $i++) {
        for ($j = $count - 1; $j > $i; $j--) {
            if ($arr[$j] < $arr[$j - 1]) {
                $tmp = $arr[$j];
                $arr[$j] = $arr[$j - 1];
                $arr[$j - 1] = $tmp;
            }
        }
    }

    return $arr;
}

function insertSort(array $arr = [])
{
    $count = count($arr);
    if ($count <= 1) {
        return $arr;
    }

    for ($i = 1; $i < $count; $i++) {
        $currentValue = $arr[$i];
        $j = $i - 1;

        while (isset($arr[$j]) && $arr[$j] > $currentValue) {
            $arr[$j + 1] = $arr[$j];
            $arr[$j] = $currentValue;
            $j--;
        }
    }

    return $arr;
}

function mergeSort(array $arr = [])
{
    $count = count($arr);
    if ($count <= 1) {
        return $arr;
    }

    $left  = array_slice($arr, 0, (int)($count/2));
    $right = array_slice($arr, (int)($count/2));

    $left = mergeSort($left);
    $right = mergeSort($right);

    return merge($left, $right);
}

function merge(array $left = [], array $right = [])
{
    $ret = array();
    if (count($left) <= 0) {
        return;
    }

    if (count($right) <= 0) {
        return;
    }

    while (count($left) > 0 && count($right) > 0) {
        if ($left[0] < $right[0]) {
            array_push($ret, array_shift($left));
        } else {
            array_push($ret, array_shift($right));
        }
    }

    array_splice($ret, count($ret), 0, $left);
    array_splice($ret, count($ret), 0, $right);

    return $ret;
}

//Quick Sorting
function quickSort(array $arr) {
    $count= count($arr);
    if ($count <= 1) {
        return $arr;
    }

    $first_val = $arr[0];
    $left_arr = array();
    $right_arr = array();

    for ($i = 1; $i < $count; $i++) {
        if ($arr[$i] <= $first_val) {
            $left_arr[] = $arr[$i];
        } else {
            $right_arr[] = $arr[$i];
        }
    }

    $left_arr = quickSort($left_arr);
    $right_arr = quickSort($right_arr);

    return array_merge($left_arr, array($first_val), $right_arr);
}

//Select Sorting
function selectSort(array $arr) {
    $count= count($arr);
    if ($count <= 1){
        return $arr;
    }

    for ($i = 0; $i < $count; $i++){
        $k = $i;

        for($j = $i + 1; $j < $count; $j++){
            if ($arr[$k] > $arr[$j]){
                $k = $j;
            }

            if ($k != $i){
                $tmp = $arr[$i];
                $arr[$i] = $arr[$k];
                $arr[$k] = $tmp;
            }
        }
    }

    return $arr;
}

//recursive menu with unknown depth and one query

/**
     * Initialize menus and their submenus. 1 query to rule them all!
     *
     * @return void
     */
    private function initMenus()
    {
        $menu = $this->getTable("Menu")->fetchList(false, ["id", "caption", "class", "menulink", "parent"], ["active" => 1, "language" => $this->language()], "AND", null, "id, menuOrder");
        if (count($menu) > 0) {
            $menus = ['menus' => [], 'submenus' => []];
            foreach ($menu as $submenus) {
                $menus['menus'][$submenus->getId()] = $submenus;
                $menus['submenus'][$submenus->getParent()][] = $submenus->getId();
            }

            $this->getView()->menu = $this->generateMenu(0, $menus);
        }
        return $this->getView();
    }


function calculate_score($votes, $item_hour_age, $gravity=1.8) {
    return ($votes - 1) / pow(($item_hour_age+2), $gravity);
  }

$excludeDays = array(0, 6);
$currentDay = new DateTime();
$currentDayName = $currentDay->format('D');
$yearLater = new DateTime('+1 year');

$workingDays = array();
$nonWorkingDays = getNonWorkingDays();

for($date = clone $currentDay; $date < $yearLater; $date->modify('+1 day')) {
    if (!in_array($date->format('w'), $excludeDays)) {
        if (!in_array($date->format('d-m-Y'), $nonWorkingDays)) {
            $workingDays[] = $date->format('d-m-Y');
        }
    }
}

function showRating($rating) {
    $html = '';
    $k = 0;

    if (floor($rating) <= $rating) {
        $rating2 = floor($rating);
    } else {
        $rating2 = ceil($rating);
    }

    for($i = 1; $i <= $rating2; $i++) {
        $html .= "<i class='uk-icon-star rating-active f12 uk-margin-small-left'></i>";
        $k++;
    }


    if ($k < $rating) {
        $html .= "<i class='uk-icon-star-half-o f12 rating-active uk-margin-small-left'></i>";
    }

    for ($ii=ceil($rating); $ii < 5; $ii++) $html .= "<i class='uk-icon-star f12 rating-inactive uk-margin-small-left'></i>";

    return $html;
}

function showOverallRating($comment, $isNumber = true)
{
    $total = ($comment['design'] + $comment['timelasting'] + $comment['ease_of_use'] + $comment['maintenance'] + $comment['price_rat']) / 5;

    return ($isNumber ? number_format($total, 1) : showRating($total));
}


function nextWorkingDay($dateTime, $workingDays)
{
    while (!in_array($dateTime, $workingDays)) {
        $dateTime = date('d-m-Y', strtotime("+1 day", strtotime($dateTime)));
    }

    return $dateTime;
}

$currTime = $currentDay->getTimestamp();

if ($currTime < strtotime('13:00:00')) {
    $orderDeadline = date('d-m-Y', strtotime('13:00:00'));
} else {
    $orderDeadline = nextWorkingDay(date('d-m-Y', strtotime("+1 day", $currTime)), $workingDays);
}

if ($currTime < strtotime('13:00:00')) {
    $nextWorkingDay = nextWorkingDay($currentDay->format('d-m-Y'), $workingDays);
    $OrderDeliveryDate = nextWorkingDay(date('d-m-Y', strtotime("+1 day", strtotime($orderDeadline))), $workingDays);
} else {
    // tuk vliza sled 13 chasa
    $nextWorkingDay = nextWorkingDay($currentDay->modify('+1 day')->format('d-m-Y'), $workingDays);
    $OrderDeliveryDate = nextWorkingDay(date('d-m-Y', strtotime("+1 day", strtotime($orderDeadline))), $workingDays);
}

$orderDeadline = strtotime($orderDeadline. " 13:00:00");
$OrderDeliveryDateNew = date('D M d Y H:i:s O',$orderDeadline);
$diff = $orderDeadline - $currTime;
$deliveryDatename = date('l', strtotime($OrderDeliveryDate));

/**
     * Prints OPTIONS List for Select
     *
     * @param  array $data
     * @param  mixed $selected
     * @param  bool   $strict
     *
     * @return string
     */
    function arrayOptions($data = array(), $selected, $strict = false) {
        $result = "";
        if (count($data) > 0) {

            foreach($data as $k => $v) {
                $sel = "";
                if($strict) {
                    if((string)$k === (string)$selected)
                        $sel = "selected";
                }
                if(!$strict) {
                    if($k == $selected) {
                        $sel = "selected";
                    }
                }

                $result .= "<option value=\"{$k}\" {$sel}>{$v}</option>\n";
            }
        }

        return $result;
    }


    /**
     * @param string $column
     * @param array $requestData
     *
     * @return string
     */
    public function generateSearchByParam($column, $requestData)
    {
        $srch_by_param = '';
        if(is_array($requestData) && count($requestData) > 0) {
            $srch_position = array();
            foreach($requestData as $key => $value) {
                if($value) {
                    $srch_position[] = "'".$value."'";
                }
            }
            if(count($srch_position) > 0) {
                $srch_by_param = implode(",", $srch_position);
                $srch_by_param = " AND {$column} IN ({$srch_by_param}) ";
            }
        }

        return $srch_by_param;
    }

    function generateSearch($fields, $str, $how = 'AND') {
        $s = explode(" ", $str);
        $result = "";
        $flds = array();
        foreach($fields as $fld) {
            $tmp = array();
            foreach($s as $k => $v) {
                $tmp[] = " {$fld} LIKE '%{$v}%' ";
            }
            $field_str = implode(" ".$how." ", $tmp);
            $field_str = "(" . $field_str . ")";
            $flds[] = $field_str;
        }

        $result = "( " . implode(" OR ", $flds) . " )";

        return $result;
    }
    function url_origin($use_forwarded_host = false )
    {
        $ssl      = ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' );
        $sp       = strtolower( $_SERVER['SERVER_PROTOCOL'] );
        $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
        $port     = $_SERVER['SERVER_PORT'];
        $port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
        $host     = ( $use_forwarded_host && isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : ( isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : null );
        $host     = isset( $host ) ? $host : $_SERVER['SERVER_NAME'] . $port;
        return $protocol . '://' . $host;
    }

    function full_url($use_forwarded_host = false )
    {
        return url_origin($use_forwarded_host ) . $_SERVER['REQUEST_URI'];
    }

    /**
     * Scan headers and find user real ip even if it's behind proxy
     *
     * @return string
     */
    function getIP()
    {
        $ipAddress = 'UNKNOWN';

        if(getenv('HTTP_CLIENT_IP'))
            $ipAddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipAddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipAddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipAddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipAddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipAddress = getenv('REMOTE_ADDR');

        return $ipAddress;
    }

