<?php
/**
 * Download problem as zip archive.
 *
 * Part of the DOMjudge Programming Contest Jury System and licenced
 * under the GNU GPL. See README and COPYING for details.
 */

require('init.php');

$id = getRequestID();
if ( !isset($id) ) {
	error("No problem id given.");
}

$problem = $DB->q('MAYBETUPLE SELECT * FROM problem p
                   LEFT JOIN contestproblem cp USING (probid)
                   WHERE probid = %i LIMIT 1', $id);
if ( empty($problem) ) error ("Problem p$id not found");

$zip = new ZipArchive;
if ( !($tmpfname = tempnam(TMPDIR, "export-")) ) {
	error("Could not create temporary file.");
}

$res = $zip->open($tmpfname, ZipArchive::OVERWRITE);
if ( $res !== TRUE ) {
	error("Could not create temporary zip file.");
}
$testcases = $DB->q('SELECT description, testcaseid, rank FROM testcase
		     WHERE probid = %i AND sample = 1 ORDER BY rank', $id);
while ($tc = $testcases->next()) {
	$fname = $id . "_" . $tc['rank'] .
		(empty($tc['description'])?"":"_".$tc['description']);
	foreach(array('in','out') as $inout) {
		$content = $DB->q("VALUE SELECT SQL_NO_CACHE " . $inout . "put FROM testcase
				   WHERE testcaseid = %i", $tc['testcaseid']);
		$curfname = preg_replace('/[^A-Za-z0-9]/', '_', $fname) .  '.' . $inout;
		$zip->addFromString($curfname, $content);
		unset($content);
	}
}
$zip->close();

$filename = 'p' . $id . '-' . $problem['shortname'] . '-samples.zip';

header("Content-Description: File Transfer");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Type: application/zip; name=\"$filename\"");
header("Content-Length: " . filesize($tmpfname) . "\n\n");
header("Content-Transfer-Encoding: binary");

readfile($tmpfname);
unlink($tmpfname);
