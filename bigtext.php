<?php
include('bigt.php');
if (isset($argv))
	echo chr(27)."[0;$argv[1]m";
function createlines($string) {
	global $c;
	$line = array('','','','','');
	for ($j = 0; $j < strlen($string); $j++)
		for ($i = 0; $i < 5; $i++)
			$line[$i] = $line[$i].str_replace('-', ' ',$c[ord($string[$j])][$i]).' ';
	return $line;
}
$str = (isset($_GET['string']) ? $_GET['string'] : 'WHAT');
$line = createlines($str);
echo '<pre>';
for ($i = 0; $i < 5; $i++)
	echo $line[$i]."\n";
if (isset($argv))
	echo chr(27).'[0m';
?>
