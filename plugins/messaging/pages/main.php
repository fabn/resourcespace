<?
 
    /*************************************************************************
    *
    * Simple Private Messaging Tutorial for Pixel2Life Community
    * 
    * Features:
    * 
    * - Messaging using Usernames 
    * - No HTML allowed (bbcode can simply be included) 
    * - You can see if somebody has deleted or read the pm 
    * - On reply, the old mail will be quoted
    *
    * by Christian Weber
    * 
    * 
    *************************************************************************/
    
    // Load the config file!
    //include('config.php');
    
	include(dirname(__FILE__)."/../../../include/db.php");
	include(dirname(__FILE__)."/../../../include/authenticate.php");
	include(dirname(__FILE__)."/../../../include/general.php");
    include(dirname(__FILE__)."/../../../include/header.php");

    // Load the class
//    require('pm.php');
    if (!class_exists("cpm")) {
		include(dirname(__FILE__)."/../pages/cpm.php");
	}

    // Set the userid to 2 for testing purposes... you should have your own usersystem, so this should contain the userid
    $userid=$userref;

    // initiate a new pm class
    $pm = new cpm($userref);
    
    // check if a new message had been send
    if(isset($_POST['newmessage'])) {
        // check if there is an error while sending the message (beware, the input hasn't been checked, you should never trust users input!)
        if($pm->sendmessage($_POST['to'],$_POST['subject'],$_POST['message'])) {
            // Tell the user it was successful
            echo "Message successfully sent!<br />";
            
            //CAM Notify
            $notify = "MAM Mediaset\r\n\r\nTi è arrivato un nuovo messaggio dal titolo '".$_POST['subject']."'.\r\nPer leggerlo vai su: ".$baseurl."/plugins/messaging/pages/main.php\r\n\r\nQuesta è una notifica automatica.";
            $toemail = $pm->getuseremail($pm->getuserid($_POST['to']));
            send_mail($toemail,"Messaggio automatico da MAM Mediaset (non rispondere)",$notify);
            
        } else {
            // Tell user something went wrong it the return was false
            echo "Error, couldn't send PM. Maybe wrong user.<br />";
        }
    }
    
    // check if a message had been deleted
    if(isset($_POST['delete'])) {
        // check if there is an error during deletion of the message
        if($pm->deleted($_POST['did'])) {
            echo "Message successfully deleted!<br />";
        } else {
            echo "Error, couldn't delete PM!<br />";
        }
    }
    
// In this switch we check what page has to be loaded, this way we just load the messages we want using numbers from 0 to 3 (0 is standart, so we don't need to type this)
if(isset($_GET['p'])) {
    switch($_GET['p']) {
        // get all new / unread messages
        case 'new': $pm->getmessages(); break;
        // get all send messages
        case 'send': $pm->getmessages(2); break;
        // get all read messages
        case 'read': $pm->getmessages(1); break;
        // get all deleted messages
        case 'deleted': $pm->getmessages(3); break;
        // get a specific message
        case 'view': $pm->getmessage($_GET['mid']); break;
        // get all new / unread messages
        default: $pm->getmessages(); break;
    }
} else {
    // get all new / unread messages
    $pm->getmessages();
}
// Standard links

function Icon($text)
{
	global $baseurl;
?>
<img src="<?=$baseurl?>/plugins/messaging/static/<?=$text?>.gif">&nbsp;
<?
}

?>
<?=Icon("unread")?><a href='<?php echo $_SERVER['PHP_SELF']; ?>?p=new'>New Messages</a> -
<?=Icon("sent")?><a href='<?php echo $_SERVER['PHP_SELF']; ?>?p=send'>Sent Messages</a> -
<?=Icon("read")?><a href='<?php echo $_SERVER['PHP_SELF']; ?>?p=read'>Read Messages</a> -
<?=Icon("deleted")?><a href='<?php echo $_SERVER['PHP_SELF']; ?>?p=deleted'>Deleted Messages</a><br/><br/><br/><br/>
<?=Icon("new")?><a href='<?php echo $_SERVER['PHP_SELF']; ?>?p=sendnew'>New message...</a><br/><br/>
<?
// if it's the standart startpage or the page new, then show all new messages
if(!isset($_GET['p']) || $_GET['p'] == 'new') {
?>
<table class="msg">
    <tr>
        <td>From</td>
        <td>Title</td>
        <td>Date</td>
    </tr>
    <?php
        // If there are messages, show them
        if(count($pm->messages)) {
            // message loop
            for($i=0;$i<count($pm->messages);$i++) {
                ?>
                <tr>
                    <td><?php echo $pm->messages[$i]['from']; ?></td>
                    <td><a href='<?php echo $_SERVER['PHP_SELF']; ?>?p=view&mid=<?php echo $pm->messages[$i]['id']; ?>'><?php echo $pm->messages[$i]['title'] ?></a></td>
                    <td><?php echo $pm->messages[$i]['created']; ?></td>
                </tr>
                <?php
            }
        } else {
            // else... tell the user that there are no new messages
            echo "<tr><td colspan='3'><strong>No new messages found</strong></td></tr>";
        }
    ?>
</table>
<?php
// check if the user wants send messages
} elseif($_GET['p'] == 'send') {
?>
 
<table class="msg">
    <tr>
        <td>To</td>
        <td>Title</td>
        <td>Status</td>
        <td>Date</td>
    </tr>
    <?php
        // if there are messages, show them
        if(count($pm->messages)) {
            // message loop
            for($i=0;$i<count($pm->messages);$i++) {
                ?>
                <tr>
                    <td><?php echo $pm->messages[$i]['to']; ?></td>
                    <td><a href='<?php echo $_SERVER['PHP_SELF']; ?>?p=view&mid=<?php echo $pm->messages[$i]['id']; ?>'><?php echo $pm->messages[$i]['title'] ?></a></td>
                    <td>
                    <?php  
                        // If a message is deleted and not viewed
                        if($pm->messages[$i]['to_deleted'] && !$pm->messages[$i]['to_viewed']) {
                            echo "Deleted without reading";
                        // if a message got deleted AND viewed
                        } elseif($pm->messages[$i]['to_deleted'] && $pm->messages[$i]['to_viewed']) {
                            echo "Deleted after reading";
                        // if a message got not deleted but viewed
                        } elseif(!$pm->messages[$i]['to_deleted'] && $pm->messages[$i]['to_viewed']) {
                            echo "Read";
                        } else {
                        // not viewed and not deleted
                            echo "Not read yet";
                        }
                    ?>
                    </td>
                    <td><?php echo $pm->messages[$i]['created']; ?></td>
                </tr>
                <?php
            }
        } else {
            // else... tell the user that there are no new messages
            echo "<tr><td colspan='4'><strong>No send messages found</strong></td></tr>";
        }
    ?>
</table>
 
<?php
// check if the user wants the read messages
} elseif($_GET['p'] == 'read') {
?>
    <table class="msg">
    <tr>
        <td>From</td>
        <td>Title</td>
        <td>Date</td>
    </tr>
    <?php
        // if there are messages, show them
        if(count($pm->messages)) {
            // message loop
            for($i=0;$i<count($pm->messages);$i++) {
                ?>
                <tr>
                    <td><?php echo $pm->messages[$i]['from']; ?></td>
                    <td><a href='<?php echo $_SERVER['PHP_SELF']; ?>?p=view&mid=<?php echo $pm->messages[$i]['id']; ?>'><?php echo $pm->messages[$i]['title'] ?></a></td>
                    <td><?php echo $pm->messages[$i]['to_vdate']; ?></td>
                </tr>
                <?php
            }
        } else {
            // else... tell the user that there are no new messages
            echo "<tr><td colspan='4'><strong>No read messages found</strong></td></tr>";
        }
    ?>
    </table>
 
<?php
// check if the user wants the deleted messages
} elseif($_GET['p'] == 'deleted') {
?>
    <table class="msg">
    <tr>
        <td>From</td>
        <td>Title</td>
        <td>Date</td>
    </tr>
    <?php
        // if there are messages, show them
        if(count($pm->messages)) {
            // message loop
            for($i=0;$i<count($pm->messages);$i++) {
                ?>
                <tr>
                    <td><?php echo $pm->messages[$i]['from']; ?></td>
                    <td><a href='<?php echo $_SERVER['PHP_SELF']; ?>?p=view&mid=<?php echo $pm->messages[$i]['id']; ?>'><?php echo $pm->messages[$i]['title'] ?></a></td>
                    <td><?php echo $pm->messages[$i]['to_ddate']; ?></td>
                </tr>
                <?php
            }
        } else {
            // else... tell the user that there are no new messages
            echo "<tr><td colspan='4'><strong>No deleted messages found</strong></td></tr>";
        }
    ?>
</table>
<?php
// if the user wants a detail view and the message id is set...
} elseif($_GET['p'] == 'view' && isset($_GET['mid'])) {
    // if the users id is the recipients id and the message hadn't been viewed yet
    if($userid == $pm->messages[0]['toid'] && !$pm->messages[0]['to_viewed']) {
        // set the messages flag to viewed
        $pm->viewed($pm->messages[0]['id']);
    }
?>
    <table class="msg">
        <tr>
            <td>From:</td>
            <td><?php echo $pm->messages[0]['from']; ?></td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td>Date:</td>
            <td><?php echo $pm->messages[0]['created']; ?></td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td>Subject:</td>
            <td colspan="3"><?php echo $pm->messages[0]['title']; ?></td>
        </tr>
        <tr>
            <td colspan="4"><?php echo $pm->render($pm->messages[0]['message']); ?></td>
        </tr>
    </table>
    <form name='reply' method='post' action='<?php echo $_SERVER['PHP_SELF']; ?>'>
        <input type='hidden' name='rfrom' value='<?php echo $pm->messages[0]['from']; ?>' />
        <input type='hidden' name='rsubject' value='Re: <?php echo $pm->messages[0]['title']; ?>' />
        <input type='hidden' name='rmessage' value='[quote]<?php echo $pm->messages[0]['message']; ?>[/quote]' />
        <input type='submit' name='reply' value='Reply' />
    </form>
    <form name='delete' method='post' action='<?php echo $_SERVER['PHP_SELF']; ?>'>
        <input type='hidden' name='did' value='<?php echo $pm->messages[0]['id']; ?>' />
        <input type='submit' name='delete' value='Delete' />
    </form>
<?php
} elseif($_GET['p'] == 'sendnew') {
?>
<form name="new" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<strong>To:</strong><br />
<input style="background-color: #aaaaaa" type='text' id="to" name='to' readonly="true" value='<?php if(isset($_POST['reply'])) { echo $_POST['rfrom']; } ?>' /> --> <a href="#" onclick="javascript:window.open('getuserlist.php','userlist','width=250,height=500,status=0,scrollbars=1'); return false;">Select user</a><br /><br /><br />
<strong>Subject:</strong><br />
<input type='text' name='subject' size="60" value='<?php if(isset($_POST['reply'])) { echo $_POST['rsubject']; } ?>' /><br />
<strong>Message:</strong><br  />
<textarea name='message' rows="6" cols="80"><?php if(isset($_POST['reply'])) { echo $_POST['rmessage']; } ?></textarea><br />
<input type='submit' name='newmessage' value='Send' />
</form>
<?
}

include(dirname(__FILE__)."/../../../include/footer.php");
?>
</body>
</html>