<?php
if (!function_exists('custom_mysqli_result')) {
function custom_mysqli_result($res, $row=0, $col=0) {
    $numrows = mysqli_num_rows($res);
    if ($numrows && $row <= ($numrows - 1) && $row >=0) {
        mysqli_data_seek($res, $row);
        $response_row = (is_numeric($col)) ? mysqli_fetch_row($res) : mysqli_fetch_assoc($res);
        if (isset($response_row[$col])) {
            return $response_row[$col];
        }
    }
    return false;
}
}

if (!function_exists('qSELECT')) {
function qSELECT($query, $object = NULL) {
	global $link;
	$result = mysqli_query($link, $query);
	$return = [];
	if($result) {
		$num = mysqli_num_rows($result);
		for ($i=0; $i<$num; $i++) {
			if(!is_null($object)) {
				$row = mysqli_fetch_object($result, MYSQLI_ASSOC);
			}else{
				$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			}
			$return[$i]=$row;
		}
	}
	return $return;
}
}

if (!function_exists('counting')) {
function counting($table, $what) {
	global $link;
	$query = "SELECT COUNT(1) FROM ".$table;
	$result = mysqli_query($link, $query);
	$num = custom_mysqli_result($result, 0, 0);
	return $num;
}
}

if (!function_exists('countByAgent')) {
function countByAgent($table, $agentId) {
	global $link;
	$query = "SELECT COUNT(1) FROM ".$table." WHERE agentid=".intval($agentId);
	$result = mysqli_query($link, $query);
	$num = custom_mysqli_result($result, 0, 0);
	return $num;
}
}

if (!function_exists('getById')) {
function getById($table, $id) {
	$query = "SELECT * FROM ".$table." WHERE id=".$id." ";
	$result = qSELECT($query);
	if($result) return $result[0];
	else return $result;
}
}

if (!function_exists('getByIdd')) {
function getByIdd($table) {
	$id_get = 2;
	$query = "SELECT * FROM ".$table." WHERE agentid=".$id_get." ";
	$result = qSELECT($query);
	if($result) return $result[0];
	else return $result;
}
}


if (!function_exists('getByAg')) {
function getByAg($table) {
    $agentId = isset($_COOKIE['admin_id']) ? intval($_COOKIE['admin_id']) : 0;
    if ($agentId <= 0) return [];
    $query = "SELECT * FROM ".$table." WHERE agentid=".$agentId." ";
    $result = qSELECT($query);
    if($result) return $result[0];
    else return $result;
}
}

if (!function_exists('getAll')) {
function getAll($table) {
    $agentId = isset($_COOKIE['admin_id']) ? intval($_COOKIE['admin_id']) : 0;
    if ($agentId <= 0) return [];
    $query = "SELECT * FROM ".$table." WHERE agentid=".$agentId." ";
    $result = qSELECT($query);
    return $result;
}
}

if (!function_exists('getAG')) {
function getAG($table) {
    $agentId = isset($_COOKIE['admin_id']) ? intval($_COOKIE['admin_id']) : 0;
    if ($agentId <= 0) return [];
    $query = "SELECT * FROM ".$table." WHERE id=".$agentId." ";
    $result = qSELECT($query);
    return $result;
}
}



if (!function_exists('queryToSelect')) {
function queryToSelect($table, $where, $operator, $zero_value, $key, $value, $id) {
	$ul = '<option value="'.$zero_value.'">Please select</option>';

	$query = "SELECT * FROM ".$table." WHERE `".$where."` ".$operator." ".$zero_value." ";
	$result = qSELECT($query);
	foreach ($result as $row) {
		$ul .= '<option value="'.$row[$key].'" ';
		$ul .= $id == $row[$key] ? "selected" : "" ;
		$ul .= '>'.$row[$value].'</option>';
	}
	return $ul;
}
}

if (!function_exists('getSetting')) {
function getSetting($key) {
    global $link;
    $key = mysqli_real_escape_string($link, $key);
    $query = "SELECT setting_value FROM settings WHERE setting_key = '$key'";
    $result = mysqli_query($link, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['setting_value'];
    }
    return '';
}
}

if (!function_exists('updateSetting')) {
function updateSetting($key, $value) {
    global $link;
    $key = mysqli_real_escape_string($link, $key);
    $value = mysqli_real_escape_string($link, $value);
    $query = "INSERT INTO settings (setting_key, setting_value) VALUES ('$key', '$value') ON DUPLICATE KEY UPDATE setting_value = '$value'";
    return mysqli_query($link, $query);
}
}
?>