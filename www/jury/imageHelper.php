<?php

function getPart($part, $singleLine, $prefix, $cid){
	global $DB;
	
	$part = $DB->q("TABLE SELECT value FROM gewis_image_options_part WHERE type=%s and optionid in (SELECT optionid FROM gewis_image_options_contest WHERE cid=%i)", $part, $cid);
	$part = array_map('array_values', $part);
	$part = call_user_func_array('array_merge', $part);
	
	if ( count($part) == 0 ){
		return;
	}
	
	if( $singleLine ) {
		echo $prefix . " " . implode(" ", $part) . "\n";
	} else {
		foreach($part as $p){
			echo  "$prefix $p\n";
		}
	}

}


function buildPreseed($cid){


	// Add all repositories first
	echo "apt-get install software-properties-common -y --force-yes\n";
	echo "apt-get update\n";

	getPart("repository", false, "add-apt-repository", $cid);
	getPart("preinstall", false, "", $cid);
	getPart("apt-get install --no-install-recommends", true, "apt-get install --no-install-recommends --force-yes -y", $cid);
	getPart("apt-get install", true, "apt-get install --no-install-recommends --force-yes -y", $cid);
	getPart("apt-get install", true, "apt-get install --no-install-recommends --force-yes -y", $cid);
}

?>
