<?php

require('init.php');

$id = getRequestID();
if ( empty($id) ) error("Missing problem id");

putProblemTextFrame($id);
exit;
