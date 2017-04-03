<?php
function getfile($id) {
	global $files, $handle;
	fseek($handle, $files[$id][0]);
	$buff = '';
	for ($i = 0; $i < $files[$id][1]; $i++)
		$buff .= fgetc($handle);
	return $buff;
}
$handle = fopen('gameaudio.dat', 'r');
fseek($handle, 0);
for ($i = 0; $i < 0x18; $i++)
	$header[] = ord(fgetc($handle));
$i = 0;
$entries = $header[20] + ($header[21]<<8);
for ($i = 0; $i < $entries; $i++) {
	for ($j = 0; $j < 12; $j++)
		$buff[$j] = ord(fgetc($handle));
	$files[] = array($buff[0] + ($buff[1]<<8) + ($buff[2]<<16) + ($buff[3]<<24),$buff[4] + ($buff[5]<<8) + ($buff[6]<<16) + ($buff[7]<<24));
}
for ($i = 0; $i < 0x18; $i++)
	$something[] = ord(fgetc($handle));
$i = 0;
for ($i = 0; $i < $entries+2; $i++) {
	$str = '';
	while($b = ord(fgetc($handle)))
		$str .= chr($b);
	$filelist[] = $str;
}
if (!array_key_exists('f', $_GET) && !isset($argv)) {
	for ($i = 0; $i < count($files); $i++)
		echo sprintf('%s - 0x%X (%u KB)<br>',$filelist[$i], $files[$i][0],$files[$i][1]/1024);
} elseif (!isset($argv)) {
	header('Content-type: application/octet-stream');
	header('Content-Disposition: attachment; filename="'.$_GET['f'].'"');
	echo getfile($_GET['f']);
} else {
	for ($i = 0; $i < $entries; $i++) {
		printf("Writing file %s...\n", $filelist[$i]);
		file_put_contents('meatboystuff/'.$filelist[$i],getfile($i));
	}
}
?>