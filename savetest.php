<?php
require 'saveinc.php';
$filename = 'save';
if (isset($_GET['file']))
	$filename = $_GET['file'];
$file = file_get_contents($filename);
$i = $_GET['offset'];
function readname($stats, $x) {
	global $text;
	for ($i = $x; $stats[$i] != 255; $i++) {
		if (($stats[$i] >= 0x25) && ($stats[$i] < 0x45))
			$output .= chr($stats[$i]+60);
		else if (($stats[$i] < 0x25) && ($stats[$i] >= 0xB))
			$output .= chr($stats[$i]+54);
		else
			$output .= ($text[$stats[$i]] ? $text[$stats[$i]] : '('.dechex($stats[$i]).')');
	}
	return $output;
}
if ($_GET['list']) {
	while ($i < 0x30F8) {
		if (($_GET['offset']-$i)%$_GET['len'] == 0)
			echo '<hr>'.dechex($i).': ';
		$chrlow = ord($file[$i])+60;
		$chrhigh = ord($file[$i])+54;
		if ((ord($file[$i]) >= 0x25) && (ord($file[$i]) < 0x45))
			echo chr($chrlow);
		else if ((ord($file[$i]) < 0x25) && (ord($file[$i]) >= 0xB))
			echo chr($chrhigh);
		else
			echo ' '.str_pad(dechex(ord($file[$i])), 2, '0', STR_PAD_LEFT);
		$i++;
	}
} else {
	$offset = ($_GET['num']*404)+0x65C;
	for ($i = 0; $i <= 404; $i++)
		$stats[$i] = ord($file[$i+$offset]);
	echo readname($stats, 0).'(+'.$stats[20].')<br>';
	echo 'Level: '.($stats[114]).'<br>';
	echo 'HP: '.($stats[116]+($stats[117]<<8)).'<br>';
	echo 'MP: '.($stats[118]+($stats[119]<<8)).'<br>';
	echo 'Attack: '.($stats[120]+($stats[121]<<8)).'('.($stats[136]+($stats[137]<<8)).')<br>';
	echo 'Defence: '.($stats[122]+($stats[123]<<8)).'<br>';
	echo 'Agility: '.($stats[124]+($stats[125]<<8)).'<br>';
	echo 'Wisdom: '.($stats[126]+($stats[127]<<8)).'<br>';
	echo 'Unknown: '.($stats[128]+($stats[129]<<8)).'<br>';
	echo 'Unknown: '.($stats[130]+($stats[131]<<8)).'<br>';
	echo 'Unknown: '.($stats[132]+($stats[133]<<8)).'<br>';
	echo 'Unknown: '.($stats[134]+($stats[135]<<8)).'<br>';
	echo 'Unknown: '.($stats[138]+($stats[139]<<8)).'<br>';
	echo 'Unknown: '.($stats[140]+($stats[141]<<8)).'<br>';
	echo 'Unknown: '.($stats[142]+($stats[143]<<8)).'<br>';
	echo 'EXP: '.($stats[144]+($stats[145]<<8)+($stats[146]<<16)).'<br>';
	echo 'Skills: '.($skills[$stats[160]] ? $skills[$stats[160]] : $stats[160]).'('.$stats[163].')/'.($skills[$stats[161]] ? $skills[$stats[161]] : $stats[161]).'('.$stats[164].')/'.($skills[$stats[162]] ? $skills[$stats[162]] : $stats[162]).'('.$stats[165].')<br>';
	echo 'Parents: '.readname($stats, 262).'('.readname($stats, 273).') & '.readname($stats, 286).'('.readname($stats, 297).')';
	if ($_GET['dump'])
		echo '<pre>'.var_export($stats, 1).'</pre>';

}
?>
