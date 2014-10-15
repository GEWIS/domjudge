<?php

/**
 * Recalculate all cached data in DOMjudge:
 * - The scoreboard.
 * Use this sparingly since it requires
 * (3 x #teams x #problems) queries.
 *
 * Part of the DOMjudge Programming Contest Jury System and licenced
 * under the GNU GPL. See README and COPYING for details.
 */

require('init.php');
$title = 'Refresh Cache';
require(LIBWWWDIR . '/header.php');
require(LIBWWWDIR . '/scoreboard.php');

echo "<h1>Refresh Cache</h1>\n\n";

requireAdmin();

if ( ! isset($_REQUEST['refresh']) ) {
	echo addForm($pagename);
	echo msgbox('Significant database impact',
	       'Refreshing the scoreboard cache can have a significant impact on the database load, ' .
	       'and is not necessary in normal operating circumstances.<br /><br />Refresh scoreboard cache now?' .
	       '<br /><br />' .
               addSubmit(" Refresh now! ", 'refresh') );
        echo addEndForm();

	require(LIBWWWDIR . '/footer.php');
	exit;
}

$time_start = microtime(TRUE);

auditlog('scoreboard', null, 'refresh cache');

// no output buffering... we want to see what's going on real-time
ob_implicit_flush();

$contests = getCurContests(TRUE);

foreach ($contests as $contest) {
	// get the contest, teams and problems
	$teams = $DB->q('TABLE SELECT t.teamid FROM team t INNER JOIN gewis_contestteam g USING (teamid) WHERE g.cid = %i ORDER BY teamid',
	                $contest['cid']);
	$probs = $DB->q('TABLE SELECT probid, gewis_contestproblem.cid FROM problem
                 INNER JOIN gewis_contestproblem USING (probid)
                 WHERE gewis_contestproblem.cid = %i ORDER BY shortname',
	                $contest['cid']);

	echo "<p>Recalculating all values for the scoreboard cache for contest c${contest['cid']} (" .
	     count($teams) . " teams, " . count($probs) . " problems)...</p>\n\n<pre>\n";

	if ( count($teams) == 0 ) {
		echo "No teams defined, doing nothing.</pre>\n\n";
		continue;
	}
	if ( count($probs) == 0 ) {
		echo "No problems defined, doing nothing.</pre>\n\n";
		continue;
	}

// for each team, fetch the status of each problem
	foreach ($teams as $team) {

		echo "Team t" . htmlspecialchars($team['teamid']) . ":";

		// for each problem fetch the result
		foreach ($probs as $pr) {
			echo " p" . htmlspecialchars($pr['probid']);
			calcScoreRow($pr['cid'], $team['teamid'], $pr['probid']);
		}

		// Now recompute the rank for both jury and public
		echo " rankcache";
		updateRankCache($contest['cid'], $team['teamid'], true);
		updateRankCache($contest['cid'], $team['teamid'], false);

		echo "\n";
		ob_flush();
	}

	echo "</pre>\n\n";
}

echo "<p>Deleting irrelevant data...</p>\n\n";

// drop all contests that are not current, teams and problems that do not exist
$DB->q('DELETE FROM scorecache_jury
	        WHERE cid NOT IN (%Ai)',
       $cids);
$DB->q('DELETE FROM scorecache_public
	        WHERE cid NOT IN (%Ai)',
       $cids);

foreach ($contests as $contest) {
	$probids = $DB->q('COLUMN SELECT probid FROM problem
                 INNER JOIN gewis_contestproblem USING (probid)
                 WHERE gewis_contestproblem.cid = %i ORDER BY shortname', $contest['cid']);
	$teamids = $DB->q('COLUMN SELECT t.teamid FROM team t INNER JOIN gewis_contestteam g USING (teamid) WHERE g.cid = %i ORDER BY teamid',
	                  $contest['cid']);
	// probid -1 will never happen, but otherwise the array is empty and that is not supported
	if ( empty($probids) ) {
		$probids = array(-1);
	}
	// Same for teamids
	if ( empty($teamids) ) {
		$teamids = array(-1);
	}
	// drop all contests that are not current, teams and problems that do not exist
	$DB->q('DELETE FROM scorecache_jury
	        WHERE cid = %i AND probid NOT IN (%Ai)',
	        $contest['cid'], $probids);
	$DB->q('DELETE FROM scorecache_public
	        WHERE cid = %i AND probid NOT IN (%Ai)',
	        $contest['cid'], $probids);
	$DB->q('DELETE FROM scorecache_jury
	        WHERE cid = %i AND teamid NOT IN (%Ai)',
	       $contest['cid'], $teamids);
	$DB->q('DELETE FROM scorecache_public
	        WHERE cid = %i AND teamid NOT IN (%Ai)',
	       $contest['cid'], $teamids);

	$DB->q('DELETE FROM rankcache_jury
        WHERE cid = %i AND teamid NOT IN (%Ai)', $contest['cid'], $teamids);
	$DB->q('DELETE FROM rankcache_public
        WHERE cid = %i AND teamid NOT IN (%Ai)', $contest['cid'], $teamids);
}

$time_end = microtime(TRUE);

echo "<p>Scoreboard cache refresh completed in ".round($time_end - $time_start,2)." seconds.</p>\n\n";

require(LIBWWWDIR . '/footer.php');
