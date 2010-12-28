<?php
//single upload progress meter
//http://www.ultramegatech.com/blog/2010/10/create-an-upload-progress-bar-with-php-and-jquery/
include('../../include/db.php');
 
    // Fetch the upload progress data
    $status = uploadprogress_get_info(getval('uid','test'));
    
    if ($status) {
 
        // Calculate the current percentage
        echo round($status['bytes_uploaded']/$status['bytes_total']*100);
 
    }
    else {
 
        // If there is no data, assume it's done
        echo 100;
 
    }

    

