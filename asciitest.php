<?php
echo <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
 <head>
  <title>ASCII Test</title>
<style type="text/css"><!--
body {
font-size: 1px;
line-height: 1px;
}
--></style>
 </head>
 <body><div>
EOT;
$file = (isset($_GET['file']) ? $_GET['file'] : 'bouffarus.png');
$im = imagecreatefrompng($file);
$color = 'INVALID';
list($width, $height) = getimagesize($file);
$data = array();
$pos = 0;
for ($i = 0; $i < $height*$height; $i++) {
	$prevcolor = $color;
	$color = imagecolorat($im, $i%$width, $i/$width);
	if (($prevcolor != $color) || ($i%$width == 0))
		$data[$pos++] = array('value' => imagecolorsforindex($im, $color), 'num' => 0);
	$data[$pos-1]['num']++;
}
$i = 0;
foreach ($data as $color) {
	$i += $color['num'];
	printf('<span style="color: rgba(%d,%d,%d,%F);">%s</span>%s', $color['value']['red'], $color['value']['green'], $color['value']['blue'], (256-$color['value']['alpha'])/256, str_repeat('█', $color['num']), (($i % $width) == 0) ? '<br>' : '');
}
echo '</div></body></html>';
?>
