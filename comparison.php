<html>
<head>
<title>Comparison Test</title>
<style type="text/css">
body { font-family: sans-serif; }
thead td { background: #9999CC; color:black; }
td {background: #F0F0F0; width: 55px; height: 25px; font-weight: bold; text-align: center; }
</style>
</head>
<body>
<?php

$comparables = array('TRUE', 'FALSE', '1', '0', '-1', '"1"', '"0"', '"-1"', 'NULL', 'array()', '"php"', '""', '"FALSE"', '"TRUE"', '999999');

function create_table($comparables, $operator) {
	echo 'Comparisons with '.htmlentities($operator);
	echo '<table><thead><tr><td></td>';
	foreach ($comparables as $str)
		echo '<td>'.$str.'</td>';
	echo '</thead>';

	foreach ($comparables as $val1) {
		echo '<tr><td>'.$val1.'</td>';
		foreach ($comparables as $val2)
			echo '<td>'.(eval('return '.$val1.$operator.$val2.';') ? 'TRUE' : 'FALSE').'</td>';
		echo '</tr>';
	}
	echo '</table>';
}
create_table($comparables, '==');
create_table($comparables, '>');
create_table($comparables, '<');
create_table($comparables, '===');
?>
</body></html>
