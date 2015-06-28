<?php
function Dwoo_Plugin_sanesize(Dwoo $dwoo, $bytes, $fullunits = false) {
	$prefixes = array('Kilo', 'Mega', 'Giga', 'Tera', 'Peta', 'Exa', 'Zetta', 'Yotta');
	if ($bytes < 1024)
		return $bytes.($fullunits ? 'Bytes' : 'b');
	for ($i = 0; $i < count($prefixes); $i++)
		if ($bytes/pow(1024,$i+1) < 1024)
			return round($bytes/pow(1024,$i+1),1).($fullunits ? ' '.$prefixes[$i].'bytes' : $prefixes[$i][0].'iB');
	return 'Huge';
}
?>