<?php
define('URLFORMAT', 'http://warehouse23.com/basement/box/index.html?level=%d');
define('DATABASE', 'warehouse.db');
define('REGEX', '|<h3>You open one of the (?P<numboxes>\d*) boxes on this floor and find...</h3>\n\n<p>\n(?P<boxtext>.*)\n</p>|');
$useragent = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:8.0a1) Gecko/20110815 Firefox/8.0a1';
ini_set('user_agent', $useragent);
function fix_http_response($http_response_header) {
	if (!isset($http_response_header))
		return NULL;
	$output = array();
	foreach($http_response_header as $header) {
		$exploded = explode(':', $header);
		$key = array_shift($exploded);
		$output[$key] = substr(implode(':', $exploded),1);
	}
	return $output;
}
function getfile($url) {
	$limit = 10;
	$file = @file_get_contents($url);
	while ((!$file) && ($limit-- > 0)) {
		if (isset($http_response_header[0]) && (substr($http_response_header[0], 9, 3) == '404'))
			trigger_error('404! Check parsing!', E_USER_ERROR);
		$file = @file_get_contents($url);
		$v = fix_http_response($http_response_header);
		if (isset($v['Content-Length']))
			if ($v['Content-Length'] > strlen($file))
				$file = null;
	}
	if (!isset($http_response_header))
		return $file;
	return $file;
}
function countboxes($level) {
	global $database;
	$count = 0;
	$results = $database->query("SELECT * from boxes where level=$level");
	while ($results->fetchArray(SQLITE3_ASSOC))
		$count++;
	return $count;
}
function find_duplicate_box($md5) {
	global $database;
	$results = $database->query("SELECT * from boxes where md5=\"$md5\"");
	$output = $results->fetchArray(SQLITE3_ASSOC);
	if (is_array($output))
		return true;
	return false;
}
function addbox($id, $level, $box) {
	global $database;
	$md5 = md5($box);
	if (find_duplicate_box($md5))
		return false;
	$box = $database->escapeString($box);
	$database->exec("INSERT INTO boxes VALUES ($id,$level,'$box','$md5')");
	return true;

}
function openrandombox($level) {
	global $database;
	$results = $database->query("SELECT * from boxes where level=$level ORDER BY RANDOM() LIMIT 1");
	$output = $results->fetchArray(SQLITE3_ASSOC);
	return $output['text'];
}
function openbox($id, $level) {
	global $database;
	$results = $database->query("SELECT * from boxes where level=$level AND ID=$id ORDER BY RANDOM() LIMIT 1");
	$output = $results->fetchArray(SQLITE3_ASSOC);
	return $output['text'];
}
function getLevel($level) {
	$i = 1;
	$numboxes = 1;
	$tries = 0;
	while ($i <= $numboxes) {
		$tries++;
		$page = getfile(sprintf(URLFORMAT, $level));
		preg_match(REGEX, $page, $results);
		$numboxes = $results['numboxes']-2;
		if(addbox($i, $level, $results['boxtext'])) {
			printf('Adding box %04d of %04d (%d tries)'.PHP_EOL, $i, $numboxes, $tries);
			$i++;
			$tries = 0;
		}
	}
}

if (!file_exists(DATABASE)) {
	$database = new SQLite3(DATABASE);
	$database->exec('CREATE TABLE boxes (ID,level,text,md5)');
} else {
	$database = new SQLite3(DATABASE);
}
$level = rand(1,5);
$boxes = countboxes($level);
printf('You open one of %d boxes on this floor and find...'.PHP_EOL, $boxes, $level);
printf('%s'.PHP_EOL, openrandombox($level));