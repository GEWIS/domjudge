<?php
/**
 * View/download a specific problem text. This page could later be
 * extended to provide more details, like sample test cases.
 *
 * Part of the DOMjudge Programming Contest Jury System and licenced
 * under the GNU GPL. See README and COPYING for details.
 */

require('init.php');

$id = getRequestID();
if ( empty($id) ) error("Missing problem id");
$prob = $DB->q("MAYBETUPLE SELECT * FROM problem
                INNER JOIN contestproblem USING (probid)
                WHERE cid = %i AND probid = %i", $cid, $id);

$title = 'Problem description - Problem ' . htmlspecialchars($prob['shortname']) . ': ' . htmlspecialchars($prob['name']);
require(LIBWWWDIR . '/header.php');

echo "<h1>" . $title . "</h1>\n\n";

// Show a given problem statement
putProblemText($id);

require(LIBWWWDIR . '/footer.php');
