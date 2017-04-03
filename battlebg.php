<?php
include 'decompress.php';	//Provides a decomp() function, which decompresses data. wow.
$file = $ebrom;			//$ebrom is an array containing an earthbound rom. No, I'm not telling you where it is. PS: Headerless.
function flipImage($image, $vertical, $horizontal) { 							//Flipping and rotating. Returns an image resource.
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
function getpalette($file, $offset, $bpp, $pals = 5) {							//Returns $pals arrays of RGB colours.
	for ($palette = 0; $palette <= $pals; $palette++) {
		for ($i = 0; $i < pow(2,$bpp); $i++) {
			$snespal = (ord($file[$offset+1+$i*2+$i2*pow(2,$bpp)*2])<<8)+ord($file[$offset+$i*2+$i2*pow(2,$bpp)*2]);
			$colour[$palette][$i] = ((($snespal%32)*8)<<16)+(((($snespal>>5)%32)*8)<<8)+((($snespal>>10)%32)*8);
		}
	}
	return $colour;
}
function drawtile(&$img, &$file, &$tiledata, &$colour, $offx, $offy, &$imgx, $bpp = 4) { 		//Void: Draw a single tile.
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
function drawframe($begin, $frame, &$img, &$imgx, &$gfx, &$tile, &$colour, $bpp, $height = 32) {	//Void: Draws a frame. I don't think all these arguments are necessary, but...
	for ($i = 0; $i < 32; $i++)
		for ($i2 = $begin; $i2 < $height-$begin; $i2++)
			drawtile($img, $gfx, $tile[$i2+$frame*$height][$i], $colour, $i*8, ($i2-$begin)*8, $imgx, $bpp);
}
function getpointer(&$file, $offset) {	//Returns an integer that should represent an offset.
	return (ord($file[$offset+2])<<16)+(ord($file[$offset+1])<<8)+ord($file[$offset])-0xC00000;
}
$num = ($_GET[num] ? $_GET[num] : 1);	//Defaults to arrangement 1.
if (($num > 326) || ($num < 0))
	die('WTF ARE YOU DOING');	//don't try going out of range. bad stuff happens.
for ($i = 0; $i < 17; $i++)
	$arrparam[$i] = ord($file[0x0ADCA1+$num*17+$i]);
$arr = decomp(getpointer($file, 0xAD93D+$arrparam[0]*4));
$gfx = decomp(getpointer($file, 0xAD7A1+$arrparam[0]*4));
for ($i = 0; $i < strlen($arr); $i += 2)								//Create $tile[x][y] with flags and tile for that coordinate. Essential!
	$tile[floor(($i/2)/32)][($i/2)%32] = array('tile' => (((ord($arr[$i+1])&3)<<8)+ord($arr[$i]))*($arrparam[2]*8), 'flags' => ord($arr[$i+1])&0xFC); 
$colour = getpalette($file, getpointer($file, 0xADAD9+$arrparam[1]*4), $arrparam[2]);			//Get Palette
$img = imagecreatetruecolor(256,272);									//Create the image to work with.
drawframe(0, $_GET[frame], $img, $imgx, $gfx, $tile, $colour, $arrparam[2], 32);			//Draw the frame, darn it.
header("Content-Type: image/png");									//so the browser displays it properly.
imagettftext($img, 7, 0, 1, 266, 0xFFFFFF, '../fonts/arial.ttf',"Effects: ".$arrparam[13].' '.$arrparam[14].' '.$arrparam[15].' '.$arrparam[16].' Movements: '.$arrparam[9].' '.$arrparam[10].' '.$arrparam[11].' '.$arrparam[12]);
imagepng($img);												//Output the image.
imagedestroy($img);											//Free memory.
?>