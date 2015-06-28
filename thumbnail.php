<?php
function Dwoo_Plugin_thumbnail(Dwoo $dwoo, $file, $width = null, $height = null) {
    if (is_null($width) && is_null($height)) return $file; 
    foreach (pathinfo($_SERVER['DOCUMENT_ROOT'].$file) as $key => $value)
        $$key = $value;
    $cachePath = $dirname . '/.cache';
    $cacheFileName = $filename . '_' . implode('_', array($width, $height)) . '.png';
    if (!file_exists($cachePath))
        mkdir($cachePath);
    if (!file_exists($cachePath.'/'.$cacheFileName) || filectime($cachePath.'/'.$cacheFileName) <= filectime($dirname.'/'.$basename)) {        
		list($originalwidth, $originalheight) = getimagesize($dirname.'/'.$basename);
		if ($originalwidth > $originalheight) {
			$newheight = ($originalheight/$originalwidth)*$height;
			$newwidth = $width;
		} else if ($originalwidth < $originalheight) {
			$newheight = $height;
			$newwidth = ($originalwidth/$originalheight)*$width;
		} else {
			$newheight = $height;
			$newwidth = $width;
		}
		$canvas = imagecreatetruecolor($newwidth, $newheight);
		$type = explode('.', $basename);
		if ($type[count($type)-1] == 'gif')
			$img = imagecreatefromgif($dirname.'/'.$basename);
		else if (($type[count($type)-1] == 'jpg') || ($type[count($type)-1] == 'jpeg'))
			$img = imagecreatefromjpeg($dirname.'/'.$basename);
		else if ($type[count($type)-1] == 'png')
			$img = imagecreatefrompng($dirname.'/'.$basename);
		imagecopyresampled($canvas, $img, 0, 0, 0, 0, $newwidth, $newheight, $originalwidth, $originalheight);
		imagepng($canvas, $cachePath.'/'.$cacheFileName);
    }
    return dirname($file).'/.cache/'.$cacheFileName;
}
