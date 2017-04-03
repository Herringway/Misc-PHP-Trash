<?php
$json = json_decode(file_get_contents('tf2.json'), true);
$data = $json['result']['items'];
foreach ($data as $id=>$item) { 
	$name = $item['name'];
	if (!isset($item['used_by_classes']))
		$classlist = 'TF2Item.ALLCLASSES';
	else {
		foreach ($item['used_by_classes'] as &$class)
			$class = 'TF2Item.'.strtoupper($class);
		$classlist = implode(' + ', $item['used_by_classes']);
		if ($classlist == '')
			$classlist = '(char) TF2Item.NONE';
	}
	if (!isset($item['item_slot']))
		$region = 'NONE';
	else
		$region = strtoupper($item['item_slot']);
	printf('this.data.add(new TF2Item("%s", %s, TF2Item.%s));'.PHP_EOL, $name, $classlist, $region);
}
?>