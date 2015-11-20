<?php
require_once('config.php');

function getDir(&$link, $path){
	if($link !== null)
		return true;

	$link = opendir($path);
	if($link === false){
		if(VERBOSE)
			printf("Unable to open %s\n", $path);
		return false;
	}

	return true;
}

function listFile(&$result, &$name, $path){
	if(!getDir($link, $path)){
		return false;
	}

	while (($entry = readdir($link)) !== false)
	{
		if($entry == "." || $entry == "..")
			continue;

		if(is_dir($path . '\\' . $entry)){
			listFile($result, $name, $path . '\\' . $entry);
		}
		else{
			if(is_file($path .'\\' . $entry)){
				if(preg_match('/.*\.avi$/', $entry)){
					$name[] = $entry;
					$result[] = $path . '\\' . $entry;
				}
			}
			continue;
		}
    }
}

$result=array();
$name=array();
$matches=array();
listFile($result, $name, PATH_TEST);

foreach($name as $row){
	preg_match('/^(\[.*\])?[_\.\s]?(([a-zA-Z0-9éèàê]{1,})([_\.\s]([A-Z]?([a-zéèàê]{1,}|[I]{1,})?|[0-9]{1,3}|\%\![0-9]{1,}))*)([_\.\s]((\(?([0-9]){4}\)?|[A-Z]{4,}|(([sSeE](aison|eason|pisode)?)[_\s]?[0-9]{1,})[\s-]*(([sSeE](aison|eason|pisode)?)[_\s]?[0-9]{1,})?).*))?\.(avi|mp4|mov|mpg|mpa|wma)$/', $row, $matches);
	print_r($matches);
	print_r($row);
	echo "<br/><br/>";
}
?>