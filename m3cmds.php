<?php
$json = json_decode(file_get_contents('m3cmds.json'), true);
//var_dump($json);
foreach ($json as $id=>$val) {
	printf('[04 00 %02X 00]'.PHP_EOL, $id);
	while ($val[0]--)
		printf('	%02d'.PHP_EOL, $val[0]+1);
}
?>