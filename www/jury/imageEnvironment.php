<?php
/**
 * View the problems
 *
 * Part of the DOMjudge Programming Contest Jury System and licenced
 * under the GNU GPL. See README and COPYING for details.
 */

require('init.php');
$title = 'Image Environment';

require(LIBWWWDIR . '/header.php');

function parseValue($type, $value) {
	switch($type){
		case 'bool':
			return $value == '1';
		case 'int':
			return $value * 1;
		case 'string':
			return "".$value;
	}
	return $value;
}


function getGroup($group){
	global $options;
	if(!isset($options[$group]))
		return "";

	$toParse = $options[$group];

	$toRet = "";
	foreach($toParse as $k => $a) {
		switch(gettype($a)) {
			case 'boolean':
				$toRet .= '<input type="checkbox" '.($a?"checked":"").'/> '.$k;
			break;
			case 'string':

			break;
			case 'int':

			break;
		}
		$toRet .= "<br />";	
	}

	return $toRet;
}


$settingsRows = $DB->q("SELECT * FROM
			gewis_image_options WHERE 1");

$options = [];
while($row = $settingsRows->next()){
	if(!isset($options[$row['group_name']]))
		$options[$row['group_name']] = [];

	$options[$row['group_name']][$row['setting_name']] = parseValue($row['type'], $row['value']);
}

?>
<h1>Image Environment</h1>

<h3>Desktop environments</h3>
<?php
	echo getGroup("desktop_environment");
?>

<h3>Programs to install</h3>
<?php
	echo getGroup("programs");
?>

<h3>Compilers</h3>
<?php
	echo getGroup("compilers");
?>

<h3>Generated installscript</h3>
<textarea cols=75 rows=25><?php

$types = array('desktop_environment', 'compilers', 'programs');

foreach($types as $type) {
	$res = $DB->q("SELECT image_setting, setting_name
                       FROM gewis_image_options
                       WHERE group_name='$type' AND `type`='bool' AND value='1' AND image_setting<>''");

        while($row = $res->next())
                echo "#".$row['setting_name']."\n".$row['image_setting']."\n\n";
}

?></textarea>

<h3>Commands</h3>
<a href="">Shutdown contestant laptops</a><br />
<a href="">Backup contestants home dirs</a><br />
<a href="">Return to login screen</a><br />


<?php
require(LIBWWWDIR . '/footer.php');
