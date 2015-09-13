<?php
/**
 * DOMjudge REST API
 *
 * Part of the DOMjudge Programming Contest Jury System and licenced
 * under the GNU GPL. See README and COPYING for details.
 */

require_once('../configure.php');

require_once(LIBDIR . '/init.php');

setup_database_connection();

require_once(LIBWWWDIR . '/common.php');
require_once(LIBWWWDIR . '/print.php');
require_once(LIBWWWDIR . '/scoreboard.php');
require_once(LIBWWWDIR . '/auth.php');
require_once(LIBWWWDIR . '/restapi.php');

$cdatas = getCurContests(TRUE, -1);
$cids = array_keys($cdatas);

if ( ! logged_in() &&
     isset($_SERVER['PHP_AUTH_USER']) &&
     isset($_SERVER['PHP_AUTH_PW']) ) {
	do_login_native($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
	$userdata['roles'] = get_user_roles($userdata['userid']);
} elseif ( isset($_SERVER['HTTP_X_CONTEST_HASH']) ){
	global $DB;

	$cdatas = ($DB->q("KEYTABLE SELECT cid AS ARRAYKEY,contest.* FROM contest where cid in (SELECT cid FROM gewis_contest_meta where metaName='contestHash' AND metaValue= %s)", $_SERVER['HTTP_X_CONTEST_HASH']));
	$cids = array_keys($cdatas);
	$cid = $cids[0];
	$cdata = $cdatas[$cid];
}
