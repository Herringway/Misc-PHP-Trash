<?php
header('Content-type: text/plain');
ini_set('memory_limit', '64M');
set_time_limit(60*60*24*2);
$combocounter = 0;
$counter = 0;
$combo = array();
$titles = array(
array('Heir', 'Breath'),
array('Seer', 'Light'),
array('Knight', 'Time'),
array('Witch', 'Space'),
array('Maid', 'Life'),
array('Rogue', 'Void'),
array('Prince', 'Heart'),
array('Page', 'Hope'),
array('Maid', 'Time'),
array('Page', 'Breath'),
array('Mage', 'Doom'),
array('Knight', 'Blood'),
array('Rogue', 'Heart'),
array('Sylph', 'Space'),
array('Seer', 'Mind'),
array('Thief', 'Light'),
array('Heir', 'Void'),
array('Bard', 'Rage'),
array('Prince', 'Hope'),
array('Witch', 'Life'));
$titlesdead = array(
array('Heir', 'Breath'),
array('Seer', 'Light'),
array('Knight', 'Time'),
array('Witch', 'Space'),
array('Maid', 'Life'),
array('Page', 'Hope'),
array('Maid', 'Time'),
array('Page', 'Breath'),
array('Mage', 'Doom'),
array('Knight', 'Blood'),
array('Rogue', 'Heart'),
array('Sylph', 'Space'),
array('Seer', 'Mind'),
array('Heir', 'Void'),
array('Bard', 'Rage'),
array('Witch', 'Life'));
function printcombo($combo) {
	global $titles;
	foreach ($combo as $c)
		vprintf('%s of %s'.PHP_EOL, $titles[$c]);
	echo PHP_EOL;
}
function check($combo, $value) {
	global $titles;
	foreach ($combo as $c)
		if (($titles[$c][0] == $value[0]) || ($titles[$c][1] == $value[1]))
			return true;
	return false;
}
function validate($combos) {
	global $titles,$combocounter, $combo;
	$t1 = array(); $t2 = array();
	foreach ($combos as $c) {
		$t1[] = $titles[$c][0];
		$t2[] = $titles[$c][1];
	}
	if ((count(array_unique($t1)) == count($t1)) && (count(array_unique($t2)) == count($t2))) {
		sort($combos);
		if (!isset($combo[implode(',',$combos)])) {
			$combo[implode(',',$combos)] = true;
			printcombo($combos);
			$combocounter++;
		}
	}
}
function permute($array,$limit, $combo = array()) {
	global $counter;
	if ($limit == 0) {
		validate($combo);
		return;
	}
	foreach ($array as $k=>$v) {
		$curcombo = $combo;
		$tmp = $array;
		unset($tmp[$k]);
		if (check($curcombo, $v))
			continue;
		$curcombo[] = $k;
		permute($tmp,$limit-1, $curcombo);
	}
}
//$titles = $titlesdead;
permute($titles,12);
echo $combocounter.PHP_EOL;
echo 'Done!';
?>