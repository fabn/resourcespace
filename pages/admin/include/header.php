<html>
<head>
<link href="<?php echo $baseurl?>/css/Col-<?php echo (isset($userfixedtheme) && $userfixedtheme!="")?$userfixedtheme:getval("colourcss",$defaulttheme)?>.css" rel="stylesheet" type="text/css" media="screen,projection,print" id="colourcss" />
<link rel="stylesheet" type="text/css" href="<?php echo $baseurl?>/pages/admin/muse.css" />
<script src="../../lib/CodeMirror/js/codemirror.js" type="text/javascript"></script>
<script type="text/javascript" src="../../lib/js/jquery-1.6.1.min.js"></script>
<style type="text/css">
      .CodeMirror-line-numbers {
        width: 2.5em;
        color: #aaa;
        background-color: #eee;
        text-align: right;
        padding-right: .3em;
        font-size: 10pt;
        font-family: monospace;
        padding-top: .4em;
      }
    </style>
<style>
td a {color: black;}
body {color: black;}
</style>
<title>Administration</title>

</head>

