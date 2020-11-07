<?php
define('STEAMURL', 'http://steamcommunity.com/app/%d');
require_once('simple_html_dom.php');
function getName($id) {
	$file = file_get_html(sprintf(STEAMURL, $id));
	$title = $file->find('title', 0);
	if (substr($title->plaintext, 0, 15) != 'Steam Community')
		return '';
	return str_replace('Steam Community :: ', '', $title->plaintext);
}
//echo getName($argv[1]);
$datafile = fopen('valvetestapps.txt', 'r');
while (!feof($datafile)) {
	$testid = fgets($datafile);
	echo $testid;
}
?>