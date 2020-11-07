<?php

function readbytearray($handle, $size) {
	for ($i = 0; $i < $size; $i++)
		$output[] = ord(fgetc($handle));
	return $output;
}

function read_nullterminated_string($handle) {
	$output = '';
	while (true) {
		$b = fgetc($handle);
		if (ord($b) == 0) break;
		$output .= $b;
	}
	return $output;

}

if (array_key_exists('level', $_GET) && file_exists('meatboylevels/'.$_GET['level'])) {
	$handle = fopen('meatboylevels/'.$_GET['level'], 'r');

	$id = fread($handle, 4);
	printf('ID?: %s<br>',$id);
	
	$unknown = readbytearray($handle, 5);
	echo 'Unknown: ';
	foreach ($unknown as $b)
		printf('%02X ', $b);
	echo '<br>';
	printf('Level Palette?: %s<br>',read_nullterminated_string($handle));
} else {
	$dir = opendir('meatboylevels');

	while ($file = readdir($dir)) {
		if (($file == '.') || ($file == '..')) continue;
		else printf('Level: <a href="/meatboyleveltest.php?level=%s">%1$s</a><br>', $file);
	}

}

?>