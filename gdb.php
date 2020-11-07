<?php
function nothing($input) {
	return $input;
}
function boolean($input) {
	if (($input === "No") || ($input === "no"))
		return false;
	else if (($input === "Yes") || ($input === "yes"))
		return true;
}
function html_fix($input) {
	if ($input === '&nbsp;')
		return '';
	return trim(preg_replace('/\s\s+/', ' ', $input));
}
$source = 'sheet001.htm';
require_once('simple_html_dom.php');
$file = file_get_html($source);
$i = 0;
$processing = array('intval', 'nothing', 'intval', 'intval', 'intval', 'nothing', 'strtotime', 'nothing', 'nothing', 'boolean', 'intval', 'intval', 'strtotime', 'nothing', 'intval', 'nothing', 'intval', 'nothing', 'intval', 'nothing');
foreach ($file->find('tr') as $tr) {
	$j = 0;
	foreach ($tr->find('td') as $td) {
		if ($i == 0)
			$headers[] = $td->plaintext;
		else
			$output[$i-1][$headers[$j++]] = html_fix(call_user_func($processing[$j-1], $td->plaintext));
	}
	$i++;
}
$database = new PDO('sqlite:releases.db');
$database->exec('CREATE TABLE releases (ApplicationId,ApplicationName,TitlePriority,DevtechPriority,PhysXPriority,Grade,ReleaseDate,Flagship,TWIMTBP,Confidential,NeedBuild,Shipped,DateChanged,UserChanged,ApplicationType,ApplicationTypeName,Enabled,Checksum)');
$statement = $database->prepare('INSERT INTO releases VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
$database->beginTransaction();
foreach ($output as $title) {
	if (!$title['ApplicationId'])
		continue;
	$statement->bindValue(1, $title['ApplicationId'], 			PDO::PARAM_INT);
	$statement->bindValue(2, $title['ApplicationName'], 		PDO::PARAM_STR);
	$statement->bindValue(3, $title['Title Priority'],			PDO::PARAM_INT);
	$statement->bindValue(4, $title['Devtech Priority'], 		PDO::PARAM_INT);
	$statement->bindValue(5, $title['PhysX Priority'], 			PDO::PARAM_INT);
	$statement->bindValue(6, $title['Grade'], 					PDO::PARAM_STR);
	$statement->bindValue(7, $title['Release Date'], 			PDO::PARAM_INT);
	$statement->bindValue(8, $title['Flagship'], 				PDO::PARAM_STR);
	$statement->bindValue(9, $title['TWIMTBP'], 				PDO::PARAM_STR);
	$statement->bindValue(10, $title['Confidential'], 			PDO::PARAM_INT);
	$statement->bindValue(11, $title['Need Build'], 			PDO::PARAM_INT);
	$statement->bindValue(12, $title['Shipped'], 				PDO::PARAM_INT);
	$statement->bindValue(13, $title['DateChanged'], 			PDO::PARAM_INT);
	$statement->bindValue(14, $title['UserChanged'], 			PDO::PARAM_STR);
	$statement->bindValue(15, $title['ApplicationType'], 		PDO::PARAM_INT);
	$statement->bindValue(16, $title['ApplicationTypeName'], 	PDO::PARAM_STR);
	$statement->bindValue(17, $title['Enabled'], 				PDO::PARAM_INT);
	$statement->bindValue(18, $title['Checksum'], 				PDO::PARAM_STR);
	$statement->execute();
}
$database->commit();
?>