<?php
$handle = fopen($argv[1], 'r');
switch (fread($handle, 0x1A)) {
	case 'ZSNES Save State File V0.6':
	case 'ZSNES Save State File V143':
		$offset = 0xC13; break;
	default:
		die('Unsure if will work on this file!');
}

fseek($handle, $offset);
$data = fread($handle, 0x20000);
file_put_contents($argv[1].'.ram', $data);

?>