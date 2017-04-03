<?php
include 'functionsm3.php';
$file = $mo3rom;
$num = (array_key_exists('num', $_GET) ? $_GET['num'] : 1);
$bg['gfx'] = (ord($file[0x1d0bc9F+$num*0x90])<<8)+ord($file[0x1d0bcA0+$num*0x90]);
$bg['arr'] = (ord($file[0x1d0bcA1+$num*0x90])<<8)+ord($file[0x1d0bcA2+$num*0x90]);
$colour = getpalette($file, 0x1d0bcA4+($num*0x90), 4);
$height = 32;
$arr = substr($file, 0x1D1FB30+(ord($file[0x1D1FB32+$bg['arr']*8])<<16)+(ord($file[0x1D1FB31+$bg['arr']*8])<<8)+ord($file[0x1D1FB30+$bg['arr']*8])-8, 2048);
$gfx = substr($file, 0x1D1FB30+(ord($file[0x1D1FB32+$bg['gfx']*8])<<16)+(ord($file[0x1D1FB31+$bg['gfx']*8])<<8)+ord($file[0x1D1FB30+$bg['gfx']*8])-8,(ord($file[0x1D1FB35+$bg['gfx']*8])<<8)+ord($file[0x1D1FB34+$bg['gfx']*8]));
for ($i = 0; $i < strlen($arr); $i += 2)
	$tile[($i/2)%32][floor(($i/2)/32)] = array('tile' => ord($arr[$i])*4*8, 'flags' => ord($arr[$i+1]));
$img = imagecreatetruecolor(256,256);
if (array_key_exists('debug', $_GET)) {
	echo '<pre>';
	for ($i = 0; $i < strlen($arr); $i++) {
		printf('%03X ', $tile[floor($i/32)][$i%32]['tile']/64);
		if ($i%32 == 31)
			echo '<br>';
	}
	echo '</pre>';
} else if (array_key_exists('dumptiles', $_GET)) {
	for ($i = 0; $i < 256*256; $i++)
		$tile[($i)%32][floor(($i)/32)] = array('tile' => $i*64, 'flags' => 0);
	drawframe($img, $cachedtiles, $gfx, $tile, $colour);
	header("Content-Type: image/png");
	imagepng($img);
	imagedestroy($img);
} else {
	$cachedtiles = null;
	drawframe($img, $cachedtiles, $gfx, $tile, $colour);
	header("Content-Type: image/png");
	imagepng($img);
	if (array_key_exists('dump',$_GET)) {
		imagepng($img, 'bgs/'.$num.'.png');
		$imgb = imagecreatetruecolor(256,256);
		imagecopymerge($imgb, $img, $offx, $offy, 0, 0, 256, 256, 100);
		imagepng($imgb, 'bgs/'.$num.'clean.png');
		imagedestroy($imgb);
	}
	imagedestroy($img);
}
?>
