#!/usr/bin/php
<?php
date_default_timezone_set('America/Halifax');
define('DATABASE', 'connections.db');
define('CONNECTION_REGEX_ZNC', '|\[(?<time>.*?)\] -.*?connecting.*?: (?<nick>.*?) \((?<host>.*?)\) \[(?<ip>.*?)\] \[(?<hostmask>.*?)\] \[Secure:.*?\] \[Gecos:(?<gecos>.*?)\]|');
define('CONNECTION_REGEX_MIRC', '/{(?<time>.*?)} .*?[cC]onnecting.*?: (?<nick>.*?) \((?<host>.*?)\) (\[(?<ip>.*?)\] \[(?<hostmask>.*?)\] \[Secure:.*?\] \[Gecos:|- )(?<gecos>.*?)\]?/');
define('CONNECTION_REGEX_ANNJO', '|(?<time>.*?) <DynStats> SIGNON: (?<nick>.*?)\((?<host>.*?)\) \[(?<gecos>.*?)?\]|');
define('CONNECTION_REGEX_ANNJO_ALT', '|(?<time>.*?) <DynStats> SIGNON (?<nick>.*?) \((?<host>.*?) - (?<gecos>.*?)\)|');
define('CONNECTION_REGEX_DAVE', '|{(?<date>.*?)¦(?<time>.*?)} .*?connecting: (?<nick>.*?) \((?<host>.*?)\) - (?<gecos>.*?)$|');
define('SESSION_START', '/Session (Time|Start): (?<date>.*)/');
function fixuparray($arr) {
	foreach ($arr as $key=>$val)
		if (is_int($key))
			unset($arr[$key]);
	return $arr;
}
function parsefile_znc($path, $statement) {
	$data = explode("\n", `xz -c -d $path`);
	$date = strtotime(substr($path, -15, 8));
	$success = 0;
	foreach ($data as $line) {
		if (preg_match(CONNECTION_REGEX_ZNC, $line, $result)) {
			$result['time'] = strtotime(date('F d Y ',$date).$result['time']);
			dbinsert($statement, $result);
			$success++;
		}
	}
	return $success;
}
function parsefile_dave($path, $statement) {
	$data = explode("\n", file_get_contents($path));
	$date = strtotime(substr($path, -12, 8));
	$success = 0;
	foreach ($data as $line) {
		if (preg_match(CONNECTION_REGEX_DAVE, $line, $result)) {
			if (strlen($result['date']) == 6)
				$d = sprintf('%2$02d-%1$02d-20%3$02d', substr($result['date'],0,2),substr($result['date'],2,2),substr($result['date'],4,2));
			else 
				$d = sprintf('%2$02d-%1$02d-20%3$02d', substr($result['date'],0,2),substr($result['date'],3,2),substr($result['date'],6,2));
			$result['time'] = strtotime($d.' '.substr($result['time'], 0, 8));
			dbinsert($statement, $result);
			$success++;
		}
	}
	return $success;
}
function parsefile_mirc($path, $statement) {
	$data = explode("\n", `xz -c -d $path`);
	$date = strtotime(substr($path, -15, 8));
	$success = 0;
	foreach ($data as $line) {
		if (preg_match(SESSION_START,$line,$result))
			$date = strtotime(date('F d Y', strtotime($result['date'])));
		if (preg_match(CONNECTION_REGEX_MIRC, $line, $result)) {
			$result['time'] = strtotime(date('F d Y ',$date).$result['time']);
			dbinsert($statement, $result);
			$success++;
		}
	}
	return $success;
}
function parsefile_old($path, $statement) {
	$data = explode("\n", file_get_contents($path));
	$date = strtotime(substr($path, -12, 8));
	$success = 0;
	foreach ($data as $line) {
		if (preg_match(SESSION_START,$line,$result))
			$date = strtotime(date('F d Y', strtotime($result['date'])));
		if (preg_match(CONNECTION_REGEX_MIRC, $line, $result)) {
			$result['time'] = strtotime(date('F d Y ',$date).$result['time']);
			dbinsert($statement, $result);
			$success++;
		}
	}
	return $success;
}
function parsefile_annjo($path,$statement) {
	$data = explode("\n", file_get_contents($path));
	$date = strtotime(substr($path, -12, 8));
	$success = 0;
	foreach ($data as $line) {
		if (preg_match(SESSION_START,$line,$result))
			$date = strtotime(date('F d Y', strtotime($result['date'])));
		if ((preg_match(CONNECTION_REGEX_ANNJO, $line, $result)) || (preg_match(CONNECTION_REGEX_ANNJO_ALT, $line, $result))) {
			$result['time'] = strtotime(date('F d Y ',$date).$result['time']);
			dbinsert($statement, $result);
			$success++;
		}
	}
	return $success;
}
function parsefile($dir, $file, $database) {
	$stmt = $database->prepare('INSERT INTO connects VALUES (?, ?, ?, ?, ?)');
	$database->beginTransaction();
	if ($dir == 'annjo')
		$r = parsefile_annjo($dir.'/'.$file, $stmt);
	else if ($dir == 'dave')
		$r = parsefile_dave($dir.'/'.$file, $stmt);
	else if ($dir == 'znc')
		$r = parsefile_znc($dir.'/'.$file, $stmt);
	else if ($dir == 'mirc')
		$r = parsefile_mirc($dir.'/'.$file, $stmt);
	else if ($dir == 'old')
		$r = parsefile_old($dir.'/'.$file, $stmt);
	$database->commit();
	return $r;
}
function dbinsert($statement, $data) {
	$statement->bindValue(1, $data['time'], PDO::PARAM_INT);
	$statement->bindValue(2, $data['nick']);
	$statement->bindValue(3, isset($data['host']) ? $data['host'] : '');
	$statement->bindValue(4, isset($data['hostmask']) ? $data['hostmask'] : '');
	$statement->bindValue(5, isset($data['gecos']) ? $data['gecos'] : '');
	$statement->execute();
}
function parse_files($dir) {
	if (!file_exists(DATABASE)) {
		$database = new PDO('sqlite:'.DATABASE);
		$database->exec('CREATE TABLE connects (time,nick,host,hostmask,gecos)');
	} else {
		$database = new PDO('sqlite:'.DATABASE);
	}
	$handle = opendir($dir);
	while ($filename = readdir($handle)) {
		if (!is_dir($dir.'/'.$filename))
			$filelist[] = $filename;
	}
	closedir($handle);
	$i = 1;
	foreach ($filelist as $file) {
		printf('Parsing %s (%d/%d)', $dir.'/'.$file, $i++, count($filelist));
		printf(' - %d records added'.PHP_EOL, parsefile($dir, $file,$database));
	}
}
parse_files('dave');
parse_files('annjo');
parse_files('old');
parse_files('mirc');
parse_files('znc');
?>
