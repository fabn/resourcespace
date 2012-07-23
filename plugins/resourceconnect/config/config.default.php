<?php

# Important - turn on global config option that enables empty collections to be shared, so that collections containing only external resources can also be shared.
$collection_allow_empty_share=true;

$resourceconnect_link_name="View matches in the Affiliate Network";
$resourceconnect_title="Search the Affiliate Network";
$resourceconnect_user=1;
$resourceconnect_pagesize=8;
$resourceconnect_pagesize_expanded=32;
$resourceconnect_treat_local_system_as_affiliate=false; # For testing - causes the local system itself to work like an external affiliate

# Affiliate list
$resourceconnect_affiliates=array
        (
        array
                (
                "name"=>"This System",
                "baseurl"=>"http://my.system",
                "accesskey"=>"x0x0x0x0x0x0x0x0x0x0x0x0x0x0x0x0" # From external system's plugin setup page
                ),
        array
                (
                "name"=>"Remote System A",
                "baseurl"=>"http://remote.system.a",
                "accesskey"=>"x0x0x0x0x0x0x0x0x0x0x0x0x0x0x0x0"
                ),
        array
                (
                "name"=>"Remote System B",
                "baseurl"=>"http://remote.system.b",
                "accesskey"=>"x0x0x0x0x0x0x0x0x0x0x0x0x0x0x0x0"
                )
        );
        
