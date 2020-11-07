<?php
$file = 'access_log';
$handle = fopen($file, 'r');
ini_set('memory_limit', '1024M');
//20188649 lines?
$apacheregex = '/^(\S+) (\S+) (\S+) (\S+) \[(.+)\] "(.+)" (\S+) (\S+) "(.*?)"$/';
$GETregex = '/^\S+ (.*) HTTP\/\d\.\d$/';
$regex = $apacheregex;
$i = 0;
while (!feof($handle)) {
	$line = fgets($handle);
	preg_match($regex, $line, $match);
	preg_match($GETregex, $match[6], $request);
	$urls[$request[1]] = true;
	printf('%d/%d (%.2f)', $i++, 20188649, ($i-1) / 20188649 * 100);
	for ($k = 0; $k < 30; $k++)
		echo chr(8);
}
ksort($urls);
$outputfile = 'output.txt';
$handle = fopen($outputfile, 'w');
foreach ($urls as $url=>$v) {
	fwrite($handle, $url.PHP_EOL);
}
?>