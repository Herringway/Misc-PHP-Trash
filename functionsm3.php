<?php
function flipImage($image, $vertical, $horizontal) {
	if (!$vertical && !$horizontal) return $image;
	$flipped = imagecreatetruecolor(8, 8);
	if ($vertical)
		for ($y = 0; $y < 8; $y++)
			imagecopy($flipped, $image, 0, $y, 0, 8 - $y - 1, 8, 1);
	if ($horizontal) {
		if ($vertical) {
			$image = $flipped;
			$flipped = imagecreatetruecolor(8, 8);
		}
		for ($x = 0; $x < 8; $x++)
			imagecopy($flipped, $image, $x, 0, 8 - $x - 1, 0, 1, 8);
	}
	return $flipped;
}
function getpalette($file, $offset, $bpp) {
	for ($i = 0; $i < pow(2,$bpp); $i++) {
		$colour[$i] = (ord($file[$offset+1+$i*2])<<8)+ord($file[$offset+$i*2]);
		$colour['rgb'][$i] = ((($colour[$i]%32)*8)<<16)+(((($colour[$i]>>5)%32)*8)<<8)+((($colour[$i]>>10)%32)*8);
	}
	return $colour;
}
function drawtile($img, $file, $tiledata, $colour, $offx, $offy, $cachedtiles) {
	$offsetb = $offset = $tiledata['tile'];
	if (!$cachedtiles[$offsetb]) {
		$cachedtiles[$offsetb] = imagecreatetruecolor(8,8);
		for ($y = 0; $y < 8; $y++)
			for ($x = 0; $x < 8; $x += 2) {
				imagesetpixel($cachedtiles[$offsetb],$x,$y,$colour['rgb'][ord($file[$offset])&0x0f]);
				imagesetpixel($cachedtiles[$offsetb],$x+1,$y,$colour['rgb'][(ord($file[$offset++])&0xf0)/0x10]);
			}
	}
	$imgtmp = $cachedtiles[$offsetb];
	if ($tiledata['flags']&0x4)
		$imgtmp = flipImage($imgtmp, 0, 1);
	if ($tiledata['flags']&0x8)
		$imgtmp = flipImage($imgtmp, 1, 0);
	imagecopymerge($img, $imgtmp, $offx, $offy, 0, 0, 8, 8, 100);
}
function drawframe($img, $cachedtiles, $gfx, $tile, $colour) {
	for ($i = 0; $i < 32*32; $i++)
			drawtile($img, $gfx, $tile[$i%32][$i/32], $colour, ($i%32)*8, floor($i/32)*8, $cachedtiles);
}
?>