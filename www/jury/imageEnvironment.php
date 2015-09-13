<?php
/**
 * View the problems
 *
 * Part of the DOMjudge Programming Contest Jury System and licenced
 * under the GNU GPL. See README and COPYING for details.
 */

require('init.php');
include('imageHelper.php');
$title = 'Image Environment';

require(LIBWWWDIR . '/header.php');
$totCount = 0;
function getGroup($group){
	global $options, $totCount, $settingsSet;
	if(!isset($options[$group]))
		return "";

	$toParse = $options[$group];

	$toRet = "";
	foreach($toParse as $k => $a) {
		$toRet .= addCheckbox("data[0][mapping][0][items][$totCount]", in_array($a['optionid'], $settingsSet), $a['optionid']) . '<a href="imageOption.php?cmd=edit&amp;id='.$a['optionid'].'&amp;referrer=imageEnvironment.php"><img src="../images/edit.png" alt="edit" title="edit this image option" class="picto"></a> '.$k;
		$toRet .= "<br />";
		$totCount++;
	}

	return $toRet;
}


$settingsRows = $DB->q("SELECT * FROM
			gewis_image_options WHERE 1");

$settingsSet = $DB->q("TABLE SELECT optionid FROM gewis_image_options_contest WHERE cid=%i", $cid);
if( count($settingsSet) ){	
	$settingsSet = array_map('array_values', $settingsSet);
	$settingsSet = call_user_func_array('array_merge', $settingsSet);
}

$options = [];
while($row = $settingsRows->next()){
	if(!isset($options[$row['group_name']]))
		$options[$row['group_name']] = [];

	$options[$row['group_name']][$row['setting_name']] = array('optionid'=>$row['optionid'], 'value'=>$row['value']);
}

echo addForm("edit.php");

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
<?php

echo addHidden('data[0][mapping][0][fk][]', 'cid') .
     addHidden('data[0][mapping][0][fk][]', 'optionid') .
     addHidden('data[0][mapping][0][table]', 'gewis_image_options_contest');

echo addHidden('cmd', 'edit') .
	addHidden('keydata[0][cid]', $cid) .
	addHidden('table','contest') .
	addHidden('referrer', 'imageEnvironment.php' . ( $cmd == 'edit'?(strstr(@$_GET['referrer'],'?') === FALSE?'?edited=1':'&edited=1'):'')) .
	addHidden('ignore[tables][contest]', 1) .
	addSubmit('Save', null) .
	addSubmit('Cancel', 'cancel', null, true, 'formnovalidate' . (isset($_GET['referrer']) ? ' formaction="' . htmlspecialchars($_GET['referrer']) . '"':'')) .
	addEndForm();
?>
<h3>Generated installscript</h3>
<textarea cols=75 rows=25><?php

echo buildPreseed($cid);

/*
$types = array('desktop_environment', 'compilers', 'programs');

foreach($types as $type) {
	$res = $DB->q("SELECT image_setting, setting_name
                       FROM gewis_image_options
                       WHERE group_name='$type' AND `type`='bool' AND value='1' AND image_setting<>''");

        while($row = $res->next())
                echo "#".$row['setting_name']."\n".$row['image_setting']."\n\n";
}
*/
?></textarea>

<h3>Commands</h3>
<a href="">Shutdown contestant laptops</a><br />
<a href="">Backup contestants home dirs</a><br />
<a href="">Return to login screen</a><br />


<?php
require(LIBWWWDIR . '/footer.php');
