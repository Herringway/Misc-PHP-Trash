<?php
header('Content-Type: text/html; charset=UTF-8');
require_once '/home/penguin/public_html/id3/getid3/getid3.php';
//$base = str_replace(basename($_SERVER['SCRIPT_FILENAME']),'', __FILE__);
$base = '.';
$request = ($_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : '/');
$request = explode('?', urldecode($request));
$dir = $request[0];
$gallery = 0;
if (is_file($base.$dir.'/.gallery'))
	$gallery = 1;
if (array_key_exists('info', $_GET)) {
	$getid3 = new getID3;
	$getid3->encoding = 'UTF-8';
	$getid3->Analyze("$base/$_GET[info]");
	echo '<pre>';
	var_dump($getid3->info);
	echo '</pre>';
	die();
}

echo <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
 <head>
  <title>Index of $dir</title>
  <link rel="stylesheet" type="text/css" href="/.style.css" title="Default" />
 </head>
 <body>
<h1>Index of $dir</h1>
EOT;
if (!$gallery) {
echo <<<EOT
<table><tr><th style="width: 20px;"></th><th>Name</th><th style="width: 8em;">Last modified</th><th style="width: 4em;">Size</th><th style="width: 15em;">Description</th></tr>
<tr><td valign="top"><img src="/icons/back.png" alt="[DIR]"></td><td><a href="..">Parent Directory</a>       </td><td>&nbsp;</td><td align="right">  - </td><td></td></tr>
EOT;
}
function check($file_name) {
	global $base, $dir;
	if ($file_name[0] == '.')
		return 'hide';
	if ($file_name == 'index.php')
		return 'hide';
	if (is_dir("$base$dir$file_name"))
		return 'dir';
	$file = explode('.', $file_name);
	return strtolower($file[count($file)-1]);
}
function getsize($bytes) {
	if ($bytes > 1024) {
		if ($bytes > 1024*1024) {
			if ($bytes > 1024*1024*1024) {
				return round($bytes/1024/1024/1024, 1).'GB';
			}
			return round($bytes/1024/1024, 1).'MB';
		}
		return round($bytes/1024, 1).'KB';
	}
	return $bytes.'B';
}
function reading ($dir) {
	global $base,$descriptions;
	$handle = opendir("$base$dir");
	while (($file_name = readdir($handle))) {
	$filestuff = check($file_name);
		switch ($filestuff) {
			case 'dir':
				$entry[$file_name]['name'] = htmlentities($file_name);
				$entry[$file_name]['icon'] = '<img src="/icons/folder.png" alt="DIR"/>';
				break;
			case 'mp3':
			case 'ogg':
			case 'mid':
			case 'spc':
			case 'gsf':
			case 'minigsf':
			case 'psf':
			case 'psf2':
			case 'minipsf':
			case 'snsf':
			case 'minisnsf':
			case 'usf':
			case 'miniusf':
			case 'm4a':
			case 'mp4':
				$getid3 = new getID3;
				$getid3->encoding = 'UTF-8';
				$getid3->Analyze("$base$dir$file_name");
				$album = (array_key_exists('album', $getid3->info['comments']) ? ($getid3->info['comments']['album'][1] ? $getid3->info['comments']['album'][1] : $getid3->info['comments']['album'][0]).' - ' : null);
				$artist = (array_key_exists('artist', $getid3->info['comments']) ? ($getid3->info['comments']['artist'][1] ? $getid3->info['comments']['artist'][1] : $getid3->info['comments']['artist'][0]).' - ' : null);
				$tracknum = (array_key_exists('tracknumber', $getid3->info['comments']) ? ($getid3->info['comments']['tracknumber'][1] ? $getid3->info['comments']['tracknumber'][1] : $getid3->info['comments']['tracknumber'][0]).' - ' : null);
				$title = (array_key_exists('title', $getid3->info['comments']) ? ($getid3->info['comments']['title'][1] ? $getid3->info['comments']['title'][1] : $getid3->info['comments']['title'][0]) : null);
				$string = htmlentities(($album || $artist || $title ? "$album $artist $tracknum $title" : $file_name), ENT_QUOTES, 'UTF-8', 0);
				$entry[$file_name]['name'] = $string;
				$entry[$file_name]['icon'] = '<img src="/icons/sound2.png" alt="SND"/>';
				break;
			case 'gif':
			case 'jpeg':
			case 'jpg':
			case 'png':
			case 'bmp':
				$string = htmlentities($file_name, ENT_QUOTES, 'UTF-8', 0);
				$entry[$file_name]['name'] = $string;
				$entry[$file_name]['icon'] = '<img src="/icons/image2.png" alt="IMG"/>';
				break;
			case 'swf':
			case 'avi':
			case 'mpg':
				$string = htmlentities($file_name, ENT_QUOTES, 'UTF-8', 0);
				$entry[$file_name]['name'] = $string;
				$entry[$file_name]['icon'] = '<img src="/icons/movie.png" alt="MOV"/>';
				break;
			case 'txt':
				$string = htmlentities($file_name, ENT_QUOTES, 'UTF-8', 0);
				$entry[$file_name]['name'] = $string;
				$entry[$file_name]['icon'] = '<img src="/icons/text.png" alt="TXT"/>';
				break;
			case 'html':
			case 'htm':
			case 'php':
				$string = htmlentities($file_name, ENT_QUOTES, 'UTF-8', 0);
				$entry[$file_name]['name'] = $string;
				$entry[$file_name]['icon'] = '<img src="/icons/layout.png" alt="HTML"/>';
				break;
			case 'zip':
			case '7z':
			case 'rar':
				$string = htmlentities($file_name, ENT_QUOTES, 'UTF-8', 0);
				$entry[$file_name]['name'] = $string;
				$entry[$file_name]['icon'] = '<img src="/icons/compressed.png" alt="ARC"/>';
				break;
			case 'hide':
				break;
			default:
				$entry[$file_name]['alt'] = '   ';
				$entry[$file_name]['icon'] = 'unknown';
				$entry[$file_name]['name'] = htmlentities($file_name, ENT_QUOTES, 'UTF-8', 0);
				break;
		}
		if ((substr($file_name, 0, 1) != '.') && ($file_name != 'index.php')) {
			$entry[$file_name]['size'] = filesize("$base$dir$file_name");
			$entry[$file_name]['time'] = filemtime("$base$dir$file_name");
			$entry[$file_name]['href'] = "$dir$file_name";
			$entry[$file_name]['desc'] = $descriptions[$entry[$file_name]['href']];
		}
	}
	return $entry;
}
function compare($a, $b) {
	global $base;
	if (array_key_exists('href', $a) && array_key_exists('href', $b)) {
		if (is_dir($base.$a['href']) && is_dir($base.$b['href']))
			return (substr(strtoupper($a['name']), 0, -1) < substr(strtoupper($b['name']), 0, -1) ? -1 : 1);
		else if (is_dir($base.$a['href']))
			return -1;
		else if (is_dir($base.$b['href']))
			return 1;
	}
	if (!$a[$_GET['sort']])
		return 0;
	if (substr(strtoupper($a[$_GET['sort']]), 0, -1) == substr(strtoupper($b[$_GET['sort']]), 0, -1))
		return 0;
	if (!array_key_exists('desc', $_GET))
		return (substr(strtoupper($a[$_GET['sort']]), 0, -1) < substr(strtoupper($b[$_GET['sort']]), 0, -1) ? -1 : 1);
	return (substr(strtoupper($a[$_GET['sort']]), 0, -1) > substr(strtoupper($b[$_GET['sort']]), 0, -1) ? -1 : 1);
}
function mkthumbnail($href, $uri, $time) {
	global $base, $dir;
	if (!is_dir($base.$dir.'.thumbs'))
		mkdir($base.$dir.'.thumbs');
	if (is_file($base.$dir.'.thumbs/'.$time.$uri))
		return;
	list($width, $height) = getimagesize($href);
	if ($width > $height) {
		$fheight = ($height/$width)*175;
		$fwidth = 175;
	} else if ($width < $height) {
		$fwidth = ($width/$height)*175;
		$fheight = 175;
	} else {
		$fheight = 175;
		$fwidth = 175;
	}
	$img2 = imagecreatetruecolor($fwidth, $fheight);
	if (check($uri) == 'gif') {
		$img = imagecreatefromgif($href);
		imagecopyresampled($img2, $img, 0, 0, 0, 0, $fwidth, $fheight, $width, $height);
		imagegif($img2, $base.$dir.'.thumbs/'.$time.$uri);
	}
	else if ((check($uri) == 'jpg') || (check($uri) == 'jpeg')) {
		$img = imagecreatefromjpeg($href);
		imagecopyresampled($img2, $img, 0, 0, 0, 0, $fwidth, $fheight, $width, $height);
		imagejpeg($img2, $base.$dir.'.thumbs/'.$time.$uri);
	}
	else if (check($uri) == 'png') {
		$img = imagecreatefrompng($href);
		imagecopyresampled($img2, $img, 0, 0, 0, 0, $fwidth, $fheight, $width, $height);
		imagepng($img2, $base.$dir.'.thumbs/'.$time.$uri);
	}
}
$file = reading($dir);
if ($file && !$gallery) {
	if (!array_key_exists('sort', $_GET))
		$_GET['sort'] = 'name';
	usort($file, 'compare');
	foreach ($file as $key => $val)
			$files[$key] = $val;
	if ($files)
		foreach ($files as $key => $v)
			printf ("<tr><td>%s</td><td><a href=\"%s\">%s</a></td><td>%s</td><td>%s</td><td>%s</td></tr>\r\n", sprintf($v['icon'], $v['name']), $v['href'], $v['name'], strftime('%d-%b-%Y %H:%M',$v['time']), is_dir($base.$v['href']) ? ' - ' : getsize($v['size']), $v['desc']);
	echo '</table>';
}
elseif ($file && $gallery) {
	echo '<table>';
	echo '<div class="img"><a style="text-decoration: none;" href=".."><img src="/icons/folder.png" width="40" height="44"><br/>Parent Directory</a></div>'."\r\n";
	if (!array_key_exists('sort', $_GET))
		$_GET['sort'] = 'name';
	usort($file, 'compare');
	foreach ($file as $key => $val)
			$files[$key] = $val;
	if ($files) {
		foreach ($files as $key => $val) {
			if (@$val['alt'] == 'IMG') mkthumbnail($base.$val['href'], $val['name'], $val['time']);
			@printf('<div class="img"><a href="%s">%s</a></div>'."\r\n", 'http://'.$_SERVER['SERVER_NAME'].$val['href'],(($val['alt'] == 'IMG') ? '.thumbs/'.$val['time'].$val['name'] : $val['icon'].'.png'), ($val['alt'] != 'IMG') ? '' : '');
		}
	}
	echo '</table>';
}
echo <<<EOT
</body></html>
EOT;
?>