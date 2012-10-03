<?php 
function HookMagictouchAllAdditionalheaderjs(){

global $magictouch_account_id,$magictouch_secure;
if ($magictouch_account_id!=""){
    ?>
    <script src="<?php echo $magictouch_secure ?>://www.magictoolbox.com/mt/<?php echo $magictouch_account_id ?>/magictouch.js" type="text/javascript" defer="defer"></script>
    <?php
    }
}

