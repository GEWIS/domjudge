<?php
/**
 * View and edit an Image Part
 *
 * Part of the DOMjudge Programming Contest Jury System and licenced
 * under the GNU GPL. See README and COPYING for details.
 */

require('init.php');
$title = 'Edit Image Option';

require(LIBWWWDIR . '/header.php');

$options = array('apt-get install','apt-get install --no-install-recommends','preinstall','repository','pre-repository-add command','pre-install command','post-install command');

$template = "<div><select><option>" . implode($options, "</option><option>") . "</option></select> <input type='text' />&nbsp;<a href=\"#\" onclick=\"this.parentNode.parentNode.removeChild(this.parentNode)\"><img src=\"../images/delete.png\" alt=\"delete\" title=\"delete this contest\" class=\"picto\"></a></div>";


$optionid = isset($_GET['id'])?$_GET['id'] : null;

$optionPart = $DB->q("MAYBETUPLE SELECT * FROM gewis_image_options WHERE optionid=%i", $optionid);

if ( is_null($optionid) || is_null($optionPart) ) {
	// Error somthing
}

$title = ucwords(str_replace('_',' ',$optionPart['group_name']) . "&nbsp;-&nbsp;" . $optionPart['setting_name']);

$rows = $DB->q("SELECT * FROM gewis_image_options_part WHERE optionid=%i and optionid in (SELECT optionid FROM gewis_image_options WHERE defaultContest=0 or defaultContest=%i)", $optionid, $cid);

echo addForm('edit.php');

?>

<h1>Edit imageoption: <?php echo $title; ?></h1>

<form>
<div id='elementInsert'>
<?php

$templateIndex = 0;
function fillTemplate($info = null) {
	global $options, $templateIndex, $optionid;

	$index = $templateIndex;	

	if(is_null($info)) {
		$index = "{index}";
		$info = array_fill_keys(array('type', 'value'), array());
	}

	echo "<div>" .
             addSelect("data[0][mapping][0][extra][$index][type]", $options, $info['type']) .
             addInput("data[0][mapping][0][extra][$index][value]", $info['value']) .
	     " delete</div>";

	$templateIndex++;

}

while($row = $rows->next())
	fillTemplate($row); 

?>
<script type="text/javascript">

templateIndex = <?php echo $templateIndex; ?>;

function addTemplate(templateId, elementId){
	var text = document.getElementById(templateId).innerText+"";
	document.getElementById(elementId).innerHTML += (text.replace(/{index}/g, templateIndex++));
}

</script>
</div>
<?php

echo addHidden('data[0][mapping][0][fk][0]', 'optionid') .
//     addHidden('data[0][mapping][0][fk][1]', 'type') .
     addHidden('data[0][mapping][0][table]', 'gewis_image_options_part');

echo addHidden('cmd', 'edit') .
	addHidden('keydata[0][optionid]', $optionid) .
	addHidden('table','gewis_image_options') .
	addHidden('referrer', @$_GET['referrer'] . ( $cmd == 'edit'?(strstr(@$_GET['referrer'],'?') === FALSE?'?edited=1':'&edited=1'):'')) .
	addInputField('button', 'newButton', 'New step', "onclick=\"addTemplate('imageOptionPart', 'elementInsert')\"") .
	addSubmit('Save', null, 'clearTeamsOnPublic()') .
	addSubmit('Cancel', 'cancel', null, true, 'formnovalidate' . (isset($_GET['referrer']) ? ' formaction="' . htmlspecialchars($_GET['referrer']) . '"':'')) .
	addEndForm();

?>

<script type="text/template" id="imageOptionPart">
	<?php echo fillTemplate(); ?>
</script>


<?php
require(LIBWWWDIR . '/footer.php');
