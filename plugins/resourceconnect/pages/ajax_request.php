<?php
include "../../../include/db.php";
include "../../../include/general.php";
include "../../../include/authenticate.php";
include "../../../include/search_functions.php";

# This basically acts as a proxy to fetch the remote results, because AJAX is unable to make requests directly to remote servers for security reasons.

$affiliate=$resourceconnect_affiliates[getval("affiliate","")];

$abaseurl=$affiliate["baseurl"];

$search=getval("search","");
$offset=getval("offset","");
$pagesize=getval("pagesize","");
$restypes=getval("restypes","");

# Sign this request.
$access_key=$affiliate["accesskey"];
$sign=md5($access_key . $search);

echo file_get_contents($abaseurl . "/plugins/resourceconnect/pages/remote_results.php?search=" . urlencode($search) . "&pagesize=" . $pagesize . "&offset=" . $offset . "&sign=" . urlencode($sign) . "&language_set="  . urlencode($language) . "&affiliatename=" . urlencode(getval("affiliatename","")) . "&restypes=" . urlencode($restypes) . "&resourceconnect_source=" . urlencode($baseurl));


?>