

function updateThemeLevels(changedlevel){
		// used to compute theme string and update dynamic theme levels on edit.php page
		//
		// how many instances are currently being displayed?
		var levelcount = 0;
		var o = document.getElementById("themelevellist").getElementsByTagName("div");
		for(var i=0;i < o.length;i++){
        if(o[i].className == "themelevelinstance")
                levelcount ++;
		}
		// first, if they've changed a value, we want to clear out all values higher than that, since they will need to reselect
		for (var k=changedlevel+1; k < levelcount; k++){
			var thisfield = "newtheme" + k;
			var thissel = "theme" + k;
			document.getElementById(thisfield).value = '';
			document.getElementById(thissel).selectedIndex = 0;
		}
		
		// ok, now piece together the string of theme titles to send to the server
		var themestring = '';
		var sep = '';
		for (i=0; i < levelcount+1; i++){
			thisfield = "newtheme" + i;
			thissel = "theme" + i;
			if (i > 0){
				sep = '||';
			}
			
			if (document.getElementById(thisfield) == null || (document.getElementById(thisfield).value == '' && document.getElementById(thissel)[document.getElementById(thissel).selectedIndex].value == '')){
				break;
			} else {
			thisselValue = document.getElementById(thissel)[document.getElementById(thissel).selectedIndex].value;
				if (document.getElementById(thisfield).value == '' ){
					themestring = themestring + sep + thisselValue;
				} else {
					themestring = themestring + sep + document.getElementById(thisfield).value;
				}
			}
		}
		document.getElementById('themestring').value = themestring;
		//alert(document.getElementById('themestring').value);
		//document.getElementById('submitform').submit();
		jQuery('#themeselect').load('ajax/themelevel_add.php?themestring='+encodeURIComponent(themestring),{async:true});
}

function resetThemeLevels(){
	jQuery('#themeselect').load('ajax/themelevel_add.php',{async:true});
}

function clearThemeLevels(){
	document.getElementById('themestring').value='';
}

