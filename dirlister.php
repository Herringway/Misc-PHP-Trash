<?php
header('Content-Type: text/html; charset=UTF-8');
require_once 'getid3/getid3.php';
require_once 'Dwoo/dwooAutoload.php';
//$base = str_replace(basename($_SERVER['SCRIPT_FILENAME']),'', __FILE__);
date_default_timezone_set('America/Halifax');
$base = '.';
$request = ($_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : '/');
$request = explode('?', urldecode($request));
$dir = $request[0];
$gallery = is_file($base.$dir.'/.gallery');
if (array_key_exists('info', $_GET)) {
	$getid3 = new getID3;
	$getid3->encoding = 'UTF-8';
	$getid3->Analyze("$base/$_GET[info]");
	echo '<pre>';
	var_dump($getid3->info);
	echo '</pre>';
	die();
}
function check($file_name) {
	global $base, $dir;
	if ($file_name[0] == '.')
		return 'hide';
	if ($file_name == 'index.php')
		return 'hide';
	if ($file_name == 'cache')
		return 'hide';
	if (is_dir("$base$dir$file_name"))
		return 'dir';
	$file = explode('.', $file_name);
	return strtolower($file[count($file)-1]);
}
function reading ($dir) {
	global $base,$descriptions;
	$handle = opendir("$base$dir");
	while (($file_name = readdir($handle))) {
	$filestuff = check($file_name);
		switch ($filestuff) {
			case 'dir':
				$entry[$file_name]['name'] = htmlentities($file_name);
				$entry[$file_name]['alt'] = 'DIR';
				$entry[$file_name]['icon'] = 'folder';
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
				$album = ($getid3->info['comments']['album'][0] ? ($getid3->info['comments']['album'][1] ? $getid3->info['comments']['album'][1] : $getid3->info['comments']['album'][0]).' - ' : null);
				$artist = ($getid3->info['comments']['artist'][0] ? ($getid3->info['comments']['artist'][1] ? $getid3->info['comments']['artist'][1] : $getid3->info['comments']['artist'][0]).' - ' : null);
				$tracknum = ($getid3->info['comments']['tracknumber'][0] ? ($getid3->info['comments']['tracknumber'][1] ? $getid3->info['comments']['tracknumber'][1] : $getid3->info['comments']['tracknumber'][0]).' - ' : null);
				$title = ($getid3->info['comments']['title'][0] ? ($getid3->info['comments']['title'][1] ? $getid3->info['comments']['title'][1] : $getid3->info['comments']['title'][0]) : null);
				$string = htmlentities(($album || $artist || $title ? "$album $artist $tracknum $title" : $file_name), ENT_QUOTES, 'UTF-8', 0);
				$entry[$file_name]['name'] = $string;
				$entry[$file_name]['icon'] = 'sound2';
				$entry[$file_name]['alt'] = 'SND';
				break;
			case 'gif':
			case 'jpeg':
			case 'jpg':
			case 'png':
			case 'bmp':
				$string = htmlentities($file_name, ENT_QUOTES, 'UTF-8', 0);
				$entry[$file_name]['name'] = $string;
				$entry[$file_name]['icon'] = 'image2';
				$entry[$file_name]['alt'] = 'IMG';
				break;
			case 'swf':
			case 'avi':
			case 'mpg':
				$string = htmlentities($file_name, ENT_QUOTES, 'UTF-8', 0);
				$entry[$file_name]['name'] = $string;
				$entry[$file_name]['icon'] = 'movie';
				$entry[$file_name]['alt'] = 'MOV';
				break;
			case 'txt':
				$string = htmlentities($file_name, ENT_QUOTES, 'UTF-8', 0);
				$entry[$file_name]['name'] = $string;
				$entry[$file_name]['icon'] = 'text';
				$entry[$file_name]['alt'] = 'TXT';
				break;
			case 'html':
			case 'htm':
			case 'php':
				$string = htmlentities($file_name, ENT_QUOTES, 'UTF-8', 0);
				$entry[$file_name]['name'] = $string;
				$entry[$file_name]['icon'] = 'layout';
				$entry[$file_name]['alt'] = 'HTML';
				break;
			case 'zip':
			case '7z':
			case 'rar':
				$string = htmlentities($file_name, ENT_QUOTES, 'UTF-8', 0);
				$entry[$file_name]['name'] = $string;
				$entry[$file_name]['icon'] = 'compressed';
				$entry[$file_name]['alt'] = 'ARC';
				break;
			case 'hide':
				break;
			default:
				$entry[$file_name]['alt'] = '   ';
				$entry[$file_name]['icon'] = 'unknown';
				$entry[$file_name]['name'] = htmlentities($file_name, ENT_QUOTES, 'UTF-8', 0);
				break;
		}
		if ((substr($file_name, 0, 1) != '.') && ($file_name != 'index.php') && ($file_name != 'cache')) {
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
$file = reading($dir);
if (!array_key_exists('sort', $_GET))
	$_GET['sort'] = 'name';
usort($file, 'compare');
foreach ($file as $val)
		$filelist[] = array(
		'name' => $val['name'],
		'url' => 'http://'.$_SERVER['SERVER_NAME'].$val['href'],
		'path' => $val['href'],
		'time' => strftime('%d-%b-%Y %H:%M',$val['time']),
		'icon' => $val['icon'],
		'size' => (is_dir($base.$val['href']) ? ' - ' : $val['size']),
		'desc' => $val['desc'],
		'alt'  => $val['alt']);
$dwoo = new Dwoo();
$data = array('dir' => $dir, 'gallery' => $gallery, 'files' => $filelist);
$dwoo->output('./templates/dirlister.tpl', $data);
?>
