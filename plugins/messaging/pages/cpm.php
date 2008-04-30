<?

/***********************************************************
*
* Simple Private Messaging Tutorial Class
*
***********************************************************/
 
class cpm {
    var $userid = '';
    var $messages = array();
    var $dateformat = '';
 
    // Constructor gets initiated with userid
    function cpm($user,$date="d.m.Y - H:i") {
        // defining the given userid to the classuserid
        $this->userid = $user; 
        // Define that date_format
        $this->dateformat = $date;
    }
    
    // Fetch all messages from this user
    function getmessages($type=0) {
        // Specify what type of messages you want to fetch
        switch($type) {
            case "0": $sql = "SELECT * FROM messages WHERE `to` = '".$this->userid."' && `to_viewed` = '0' && `to_deleted` = '0' ORDER BY `created` DESC"; break; // New messages
            case "1": $sql = "SELECT * FROM messages WHERE `to` = '".$this->userid."' && `to_viewed` = '1' && `to_deleted` = '0' ORDER BY `to_vdate` DESC"; break; // Read messages
            case "2": $sql = "SELECT * FROM messages WHERE `from` = '".$this->userid."' ORDER BY `created` DESC"; break; // Send messages
            case "3": $sql = "SELECT * FROM messages WHERE `to` = '".$this->userid."' && `to_deleted` = '1' ORDER BY `to_ddate` DESC"; break; // Deleted messages
            default: $sql = "SELECT * FROM messages WHERE `to` = '".$this->userid."' && `to_viewed` = '0' ORDER BY `created` DESC"; break; // New messages
        }
        $result = mysql_query($sql) or die (mysql_error());
        
        // Check if there are any results
        if(mysql_num_rows($result)) {
            $i=0;
            // reset the array
            $this->messages = array();
            // if yes, fetch them!
            while($row = mysql_fetch_assoc($result)) {
                $this->messages[$i]['id'] = $row['id'];
                $this->messages[$i]['title'] = $row['title'];
                $this->messages[$i]['message'] = $row['message'];
                $this->messages[$i]['fromid'] = $row['from'];
                $this->messages[$i]['toid'] = $row['to'];
                $this->messages[$i]['from'] = $this->getusername($row['from']);
                $this->messages[$i]['to'] = $this->getusername($row['to']);
                $this->messages[$i]['from_viewed'] = $row['from_viewed'];
                $this->messages[$i]['to_viewed'] = $row['to_viewed'];
                $this->messages[$i]['from_deleted'] = $row['from_deleted'];
                $this->messages[$i]['to_deleted'] = $row['to_deleted'];
                $this->messages[$i]['from_vdate'] = date($this->dateformat, strtotime($row['from_vdate']));
                $this->messages[$i]['to_vdate'] = date($this->dateformat, strtotime($row['to_vdate']));
                $this->messages[$i]['from_ddate'] = date($this->dateformat, strtotime($row['from_ddate']));
                $this->messages[$i]['to_ddate'] = date($this->dateformat, strtotime($row['to_ddate']));
                $this->messages[$i]['created'] = date($this->dateformat, strtotime($row['created']));
                $i++;
            }
        } else {
            // If not return false
            return false;
        }
    }
    
    // Fetch the username from a userid, I made this function because I don't know how you did build your usersystem, that's why I also didn't use left join... this way you can easily edit it
    function getusername($userid) {
//CAM        $sql = "SELECT username FROM user WHERE `ref` = '".$userid."' LIMIT 1";
        $sql = "SELECT username FROM user WHERE `ref` = '".$userid."' LIMIT 1";
        
        $result = mysql_query($sql);
        // Check if there is someone with this id
        if(mysql_num_rows($result)) {
            // if yes get his username
            $row = mysql_fetch_row($result);
            return $row[0];
        } else {
            // if not, name him Unknown
            return "Unknown";
        }
    }
    
function getuseremail($userid) {
//CAM        $sql = "SELECT username FROM user WHERE `ref` = '".$userid."' LIMIT 1";
        $sql = "SELECT email FROM user WHERE `ref` = '".$userid."' LIMIT 1";
        
        $result = mysql_query($sql);
        // Check if there is someone with this id
        if(mysql_num_rows($result)) {
            // if yes get his username
            $row = mysql_fetch_row($result);
            return $row[0];
        } else {
            // if not, name him Unknown
            return "Unknown";
        }
    }    
    
    // Fetch a specific message
    function getmessage($message) {
        $sql = "SELECT * FROM messages WHERE `id` = '".$message."' && (`from` = '".$this->userid."' || `to` = '".$this->userid."') LIMIT 1";
        $result = mysql_query($sql);
        if(mysql_num_rows($result)) {
            // reset the array
            $this->messages = array();
            // fetch the data
            $row = mysql_fetch_assoc($result);
            $this->messages[0]['id'] = $row['id'];
            $this->messages[0]['title'] = $row['title'];
            $this->messages[0]['message'] = $row['message'];
            $this->messages[0]['fromid'] = $row['from'];
            $this->messages[0]['toid'] = $row['to'];
            $this->messages[0]['from'] = $this->getusername($row['from']);
            $this->messages[0]['to'] = $this->getusername($row['to']);
            $this->messages[0]['from_viewed'] = $row['from_viewed'];
            $this->messages[0]['to_viewed'] = $row['to_viewed'];
            $this->messages[0]['from_deleted'] = $row['from_deleted'];
            $this->messages[0]['to_deleted'] = $row['to_deleted'];
            $this->messages[0]['from_vdate'] = date($this->dateformat, strtotime($row['from_vdate']));
            $this->messages[0]['to_vdate'] = date($this->dateformat, strtotime($row['to_vdate']));
            $this->messages[0]['from_ddate'] = date($this->dateformat, strtotime($row['from_ddate']));
            $this->messages[0]['to_ddate'] = date($this->dateformat, strtotime($row['to_ddate']));
            $this->messages[0]['created'] = date($this->dateformat, strtotime($row['created']));
        } else {
            return false;
        }
    }
    
    // We need the userid for pms, but we only let users input usernames, so we need to get the userid of the username :)
    function getuserid($username) {
        //$sql = "SELECT id FROM user WHERE `username` = '".$username."' LIMIT 1";
//CAMILLO        
		$sql = "SELECT ref FROM user WHERE `username` = '".$username."' LIMIT 1";
		
        $result = mysql_query($sql);
        if(mysql_num_rows($result)) {
            $row = mysql_fetch_row($result);
            return $row[0];
        } else {
            return false;
        }
    }
    
    // Flag a message as viewed
    function viewed($message) {
        $sql = "UPDATE messages SET `to_viewed` = '1', `to_vdate` = NOW() WHERE `id` = '".$message."' LIMIT 1";
        return (@mysql_query($sql)) ? true:false;
    }
    
    // Flag a message as deleted
    function deleted($message) {
        $sql = "UPDATE messages SET `to_deleted` = '1', `to_ddate` = NOW() WHERE `id` = '".$message."' LIMIT 1";
        return (@mysql_query($sql)) ? true:false;
    }
    
    // Add a new personal message
    function sendmessage($to,$title,$message) {
        $to = $this->getuserid($to);
        $sql = "INSERT INTO messages SET `to` = '".$to."', `from` = '".$this->userid."', `title` = '".$title."', `message` = '".$message."', `created` = NOW()";
        return (@mysql_query($sql)) ? true:false;
    }
    
    // Render the text (in here you can easily add bbcode for example)
    function render($message) {
        $message = strip_tags($message, '<br>');
        $message = stripslashes($message); 
        $message = nl2br($message);
        return $message;
    }
 
}

?>