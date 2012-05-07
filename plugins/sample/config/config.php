<?php
#
# Configuration file for ResourceSpace plugin "Sample"
#
# These are the default values. They can be overridden by using /plugins/sample/pages/setup.php
# which is invoked by choosing Team Centre > Manage Plugins and then clicking on Options for the
# sample plugin once it has been activated.

$sample_pets_owned = array(2);      // Indices to the list of pets type giving types owned
                                    // 0=Cat, 1=Bird, 2=Dog, 3=Fish, 4=Horse, 5=Lizard, 6=Monkey
$sample_favorite_pet_type = 2;      // Index in the list of pet types giving type of favorite pet
$sample_favorite_pet_name = "";     // Typed in text giving the name of the favorite pet
$sample_favorite_pet_living = -1;   // Boolean specifying whether favorite pet is alive or not
