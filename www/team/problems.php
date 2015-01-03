<?php
/**
 * View/download problem texts
 *
 * Part of the DOMjudge Programming Contest Jury System and licenced
 * under the GNU GPL. See README and COPYING for details.
 */

require('init.php');

$title = $cdata['scoreboardhideproblems'] ? 'Problems' : 'Problem statements';
$sorttable = true;
require(LIBWWWDIR . '/header.php');

echo "<h1>$title</h1>\n\n";

if ( $cdata['scoreboardhideproblems'] ) {
	putProblemTextTable($teamid);
} else {
	putProblemTextList();
}

require(LIBWWWDIR . '/footer.php');
