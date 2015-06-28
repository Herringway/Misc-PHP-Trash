<?php
$file = fopen($argv[1], 'r');
function printStats($file) {
	fseek($file, 0xD433B);
	$output = array();
	for ($i = 0; $i < 0x14B; $i++) {
		$data = fread($file, 0x2F);
		$output[] = unpack('cFamily/C5Unknown/CMaxLevel/CGrowth/C3Ability/CUnknown6/CHP/CMP/CAttack/CDefense/CAgility/CIntelligence/C27Resistance/CUnknown7/CUnknown8', $data);
	}
	echo 'ID  | FM ML GR HP MP AT DF AG IN | A1 A2 A3 | U1 U2 U3 U4 U5 U6 U7 U8 | R1 R2 R3 R4 R5'.PHP_EOL;
	echo '--------------------------------------------------------------------------------------'.PHP_EOL;
	foreach ($output as $i=>$statblock) {
		printf('%03d | %02d %02d %02d %02d %02d %02d %02d %02d %02d | %02X %02X %02X | %02X %02X %02X %02X %02X %02X %02X %02X | %02d %02d %02d %02d %02d'.PHP_EOL, $i, $statblock['Family'], $statblock['MaxLevel'], $statblock['Growth'], $statblock['HP'], $statblock['MP'], $statblock['Attack'], $statblock['Defense'], $statblock['Agility'], $statblock['Intelligence'], $statblock['Ability1'], $statblock['Ability2'], $statblock['Ability3'], $statblock['Unknown1'], $statblock['Unknown2'], $statblock['Unknown3'], $statblock['Unknown4'], $statblock['Unknown5'], $statblock['Unknown6'], $statblock['Unknown7'], $statblock['Unknown8'], $statblock['Resistance1'], $statblock['Resistance2'], $statblock['Resistance3'], $statblock['Resistance4'], $statblock['Resistance5']);
	}
}
function printMoveReqs($file) {
	fseek($file, 0x68FF8);
	$output = array();
	for ($i = 0; $i < 169; $i++) {
		$data = fread($file, 0x12);
		$output[] = unpack('cLevel/vHP/vMP/vAttack/vDefense/vAgility/vIntelligence/C5ComboAbility', $data);
	}
	echo 'ID | LV HP  MP  ATK DEF AGI INT | C1 C2 C3 C4 C5'.PHP_EOL;
	echo '------------------------------------------------'.PHP_EOL;
	foreach ($output as $i=>$statblock) {
		printf('%02X | %02d %03d %03d %03d %03d %03d %03d | %02X %02X %02X %02X %02X'.PHP_EOL, $i, $statblock['Level'], $statblock['HP'], $statblock['MP'], $statblock['Attack'], $statblock['Defense'], $statblock['Agility'], $statblock['Intelligence'], $statblock['ComboAbility1'], $statblock['ComboAbility2'], $statblock['ComboAbility3'], $statblock['ComboAbility4'], $statblock['ComboAbility5']);
	}
}
function printUnknown($file) {
	fseek($file, 0x6A616);
	$output = [];
	$totals = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
	for ($i = 0; $i < 32; $i++)
		for ($j = 1; $j < 100; $j++)
			$output[$i][$j] = ord(fgetc($file));
	echo 'LV | 00  01  02  03  04  05  06  07  08  09  10  11  12  13  14  15  16  17  18  19  20  21  22  23  24  25  26  27  28  29  30  31 '.PHP_EOL;
	echo '------------------------------------------------------------------------------------------------------------------------------------'.PHP_EOL;
	for ($i = 1; $i < 100; $i++) {
		printf('%02d | ', $i);
		foreach ($output as $k=>$levelarray) {
			$totals[$k] += $levelarray[$i];
			$totals[$k] = min($totals[$k], 999);
			printf ('%02d  ', $levelarray[$i]);
		}
		echo PHP_EOL;
	}
	echo '------------------------------------------------------------------------------------------------------------------------------------'.PHP_EOL;
	vprintf('TOT| %03d %03d %03d %03d %03d %03d %03d %03d %03d %03d %03d %03d %03d %03d %03d %03d %03d %03d %03d %03d %03d %03d %03d %03d %03d %03d %03d %03d %03d %03d %03d %03d', $totals);
}
printStats($file);
printMoveReqs($file);
printUnknown($file);
?>