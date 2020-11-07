<?php
require_once 'spyc.php';
$domains = array('Animal', 'Balance', 'Bestial', 'Chaos', 'Corrupt', 'Courage', 'Destruction', 'Drow', 'Hatred', 'Inquisition', 'Liberation', 'Luck', 'Mind', 'Nobility', 'Ocean', 'Orc', 'Sky', 'Strength', 'Suffering', 'Time', 'Truth', 'Tyranny', 'Wealth');
$classes = array('Artificer', 'Assassin', 'Bard', 'Beguiler', 'Blackguard', 'Cleric', 'Druid', 'Duskblade', 'Exalted Arcanist', 'Paladin', 'Ranger', 'Sorceror/Wizard', 'Wu Jen');
$classabbreviations = array('Asn' => 'Assassin', 'Blk' => 'Blackguard', 'Brd' => 'Bard', 'Clr' => 'Cleric', 'Drd' => 'Druid', 'Pal' => 'Paladin', 'Rgr' => 'Ranger', 'Sor/Wiz' => 'Sorceror/Wizard', 'Wuj' => 'Wu Jen');
$schoolabbreviations = array('Abj' => 'Abjuration', 'Conj' => 'Conjuration', 'Div' => 'Divination', 'Ench' => 'Enchantment', 'Evoc' => 'Evocation', 'Illu' => 'Illusion', 'Necr' => 'Necromancy', 'Tran' => 'Transmutation', 'Univ' => 'Universal');
$componentsabbr = array('M' => 'Material', 'S' => 'Somatic', 'V' => 'Verbal', 'XP' => 'Experience');
$books = array('BoEM' => 'The Book of Eldritch Might', 'DF' => 'Defenders of the Faith', 'Dragon 323' => 'Dragon Magazine #323', 'Dragon 333' => 'Dragon Magazine #333', 'FCotW' => 'Far Corners of the World', 'MoE' => 'Magic of Eberron', 'PH2' => 'Player\'s Handbook II','SPC' => 'Spell Compendium', 'SRD' => 'System Reference Document');
function remove_empty($array) {
	foreach ($array as $key=>&$val) {
		if ($val == null)
			unset($array[$key]);
		else if (is_array($val)) {
			$val = remove_empty($val);
			if (is_array($val) && empty($val))
				unset($array[$key]);
		} else
			$val = trim($val);
	}
	return $array;
}
function spell_fixup($array) {
	global $domains,$classes,$schoolabbreviations,$componentsabbr,$books;
	$array['Source'] = trim($books[$array['Source Book']].(isset($array['Src Page']) ? ' '.$array['Src Page']:''));
	$array['Type'] = trim($schoolabbreviations[$array['Schl']].(($array['Sub-school'] != null) ? ' ('.$array['Sub-school'].')':'').(($array['Descriptor'] != null) ? ' ['.$array['Descriptor'].']':''));
	/*$components = explode(',', $array['Components']);
	foreach ($components as &$com)
		$com = $componentsabbr[trim($com)];
	$array['Components'] = $components;*/
	foreach ($domains as $domain) {
		$array['Domains'][$domain] = $array[$domain];
		unset($array[$domain]);
	}
	foreach ($classes as $class) {
		$array['Classes'][$class] = $array[$class];
		unset($array[$class]);
	}
	unset($array['Source Book']);
	unset($array['Src Page']);
	unset($array['Schl']);
	unset($array['Sub-school']);
	unset($array['Descriptor']);
	return $array;
}
$file = 'AlexSpells.csv';
$handle = fopen($file, 'r');
$keys = fgetcsv($handle);
foreach ($keys as &$key)
	if (isset($classabbreviations[$key]))
		$key = $classabbreviations[$key];
while ($data = fgetcsv($handle)) {
	if (count($keys) != count($data))
		printf('%s: %d - %d'.PHP_EOL,$data[0],count($keys),count($data));
	$alldata[] = remove_empty(spell_fixup(array_combine($keys, $data)));
}
file_put_contents($file.'.yml', Spyc::YAMLDump($alldata));
?>