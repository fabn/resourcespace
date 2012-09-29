/**
	* LDAP Functions, this provides ajax and javascript
	* for the posixldapauth plugin.
	* @version 0.1
	* @author D.J.White <djwhite@mac.com>
	* @copyright 2012 D.J.White / Resourcespace
*/

// Basic xhtml handler for the clear directory routine
function getHTTPObject() {
  var xmlhttp;
  if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
    try {
      xmlhttp = new XMLHttpRequest();
    } catch (e) {
      xmlhttp = false;
    }
  }
  return xmlhttp;
}
var http = getHTTPObject(); // Create the HTTP Object


function getSelectedItemValue(selector)
{
	//This gets the value of the selected item in a drop down box...
	// This should be in a function library!
	//get the selector
	var selControl = document.getElementById(selector);
	var selOption = selControl.selectedIndex;
	// Options is an array that contains all the items in the select list.
	// We get to the value of the selected item by getting the array item index that is selected.
	return selControl.options[selOption].value;

}

function hideObject(selector) 
{ 
	document.getElementById(selector).style.display = 'none'; 
	//hide label
	//document.getElementById("l" + selector).style.visibility = 'hidden'; 
} 

function showObject(selector) 
{ 
	document.getElementById(selector).style.display = ''; 
	//document.getElementById("l" + selector).style.visibility = 'visible';
} 


function ldapsetDisplayFields()
{
	/** This will change the fields that are visible depending auth to AD or OD
		It is triggered but the directory select drop down, and set on load.
	**/
	var ldapType = getSelectedItemValue('ldaptype');
	//alert (ldapType);
	
	if (ldapType == 0)
	{
		hideObject('trootdn');	
		hideObject('trootpass');
		hideObject('taddomain');
		showObject('tldapgroupcontainer');
		showObject('tloginfield');
	} else {
		showObject('trootdn');
		showObject('trootpass');
		showObject('taddomain');
		hideObject('tldapgroupcontainer');
		hideObject('tloginfield');
		
	}
	var tab=document.getElementById('tableldaptype');
	tab.refresh();
}

function testLdapConn()
{
	// This will test the LDAP connection:
	var ldapServer = document.getElementById('ldapserver').value;
	var ldapType = getSelectedItemValue('ldaptype');
	var basedn = document.getElementById('basedn').value;
	var ldapusercontainer = document.getElementById('ldapusercontainer').value;
	var addressurl = "ajax_test_login.php?type="+ldapType+"&server="+ldapServer+"&basedn="+basedn+"&usercont="+ldapusercontainer;
		
	//check LDAP type:
	if (ldapType == 0)
	{
		
		var loginField = document.getElementById('loginfield').value;
		var groupContainer = document.getElementById('ldapgroupcontainer').value;
		var addressurl = addressurl + "&loginfield="+loginField+"&groupcont="+groupContainer;
		
	} else { 
		// get add specific
		var rootdn = document.getElementById('rootdn').value;
		var rootpass = document.getElementById('rootpass').value;
		var addomain = document.getElementById('addomain').value;
		var addressurl = addressurl + "&rootdn="+rootdn+"&rootpass="+rootpass+"&addomain="+addomain;
	}
	
	// now we've built the address url we can call the ajax routine!
	http.open("GET",addressurl,true);
	
		// set the response function
	http.onreadystatechange = testLdapResponse;
	
	// send the request.
	http.send(null);
	
}


function testLdapResponse ()
{
	if (http.readyState == 4) 
	{
	    var response = http.responseText;
		//alert(response	);
		try 
		{
			if (http.status == 200)
			{
					alert(response);
					
			} else {
				alert("Status error in " + response);
			} // end status check
		}catch (e) {
            alert("Server Error" + e);
		} // end try
		
	}	
	
}
