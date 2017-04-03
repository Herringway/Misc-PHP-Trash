<?php
include 'decompress.php';
function flipImage($image, $vertical, $horizontal) {
	if (!$vertical && !$horizontal) return $image;
	$flipped = imagecreatetruecolor(8, 8);
	if ($vertical)
		for ($y = 0; $y < 8; $y++)
			imagecopy($flipped, $image, 0, $y, 0, 7-$y, 8, 1);
	if ($horizontal) {
		if ($vertical) {
			return imagerotate($image, 180, 0);
		}
		for ($x = 0; $x < 8; $x++)
			imagecopy($flipped, $image, $x, 0, 7-$x, 0, 1, 8);
	}
	return $flipped;
}
function getpalette($file, $offset, $bpp, $pals = 5) {
	for ($palette = 0; $palette <= $pals; $palette++) {
		for ($i = 0; $i < pow(2,$bpp); $i++) {
			$snespal = (ord($file[$offset+1+$i*2+$i2*pow(2,$bpp)*2])<<8)+ord($file[$offset+$i*2+$i2*pow(2,$bpp)*2]);
			$colour[$palette][$i] = ((($snespal%32)*8)<<16)+(((($snespal>>5)%32)*8)<<8)+((($snespal>>10)%32)*8);
		}
	}
	return $colour;
}
function drawtile(&$img, &$file, &$tiledata, &$colour, $offx, $offy, &$imgx, $bpp = 4) {
	$offset = $tiledata[tile];
	@$offsetb = $offset/($bpp*8);
	if (!$imgx[$offsetb]) {
		$imgx[$offsetb] = imagecreatetruecolor(8,8);
		$pal = ($tiledata[flags]&0x1C)>>3;
		for ($x = 0; $x < 8; $x++)
			for ($y = 0; $y < 8; $y++) {
				for ($bitplane = 0; $bitplane < $bpp; $bitplane++)
					$tile[$x][$y] += ((ord($file[$offset+$y*2+(floor($bitplane/2)*16+($bitplane&1))])    & (1 << 7-$x)) >> 7-$x) << $bitplane;
				if ($tile[$x][$y] != 0)
					imagesetpixel($imgx[$offsetb],$x,$y,$colour[$pal][$tile[$x][$y]]);
			}
		}
	imagecopymerge($img, flipImage($imgx[$offsetb], $tiledata[flags] & 128, $tiledata[flags] & 64), $offx, $offy, 0, 0, 8, 8, 100);
}
function drawframe($begin, $frame, &$img, &$imgx, &$gfx, &$tile, &$colour, $bpp, $height = 32) {
	for ($i = 0; $i < 32; $i++)
		for ($i2 = $begin; $i2 < $height-$begin; $i2++)
			drawtile($img, $gfx, $tile[$i2+$frame*$height][$i], $colour, $i*8, ($i2-$begin)*8, $imgx, $bpp);
}
?>