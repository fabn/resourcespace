<?php
# Swedish
# Language File for ResourceSpace
# -------
# Översättningsfil för själva applikationen.
#
# Tilläggsprogram översätts i plugins/*/languages/se.php
# Webbplatsens innehåll såsom instruktioner och andra skräddarsydda texter är översatta i dbstruct/data_site_text.txt - se även 'Hantera webbplatsens innehåll' (Manage Content)
# Fältvärden översätts (än så länge) i 'Hantera fältinnehåll' (Manage Field Options)
# Komponenter som t.ex. JUpload översätts inom respektive projekt
#
# Fraserna har översatts för hand, med hjälp av: 
# En befintlig svensk maskinöversättning
# Den norska översättningen (den danska var maskinöversatt)
# Computer Swedens språkwebb: http://cstjanster.idg.se/sprakwebben/
# Svenska datatermgruppen: http://www.datatermgruppen.se/
# Språkrådet: http://www.sprakradet.se/frågelådan
# Norstedts stora engelsk-svenska ordbok
# Nationalencyklopedins ordbok
#
# En första version av översättningen skapades av Henrik Frizén (förnamn.efternamn utan accenttecken i e-postboxen.Sveriges landskod) 20110124 för version 2295.
#
# Senast uppdaterad av [Namn] [Datum] för version [svn-version], [kommentar]
# Senast uppdaterad av Henrik Frizén 20110504 för version 2671+.
#
#
# User group names (for the default user groups)
$lang["usergroup-administrators"]="Administratörer";
$lang["usergroup-general_users"]="Vanliga användare";
$lang["usergroup-super_admin"]="Systemadministratör";
$lang["usergroup-archivists"]="Arkivarier";
$lang["usergroup-restricted_user_-_requests_emailed"]="Begränsade - begäranden: e-post";
$lang["usergroup-restricted_user_-_requests_managed"]="Begränsade - begäranden: hanterade";
$lang["usergroup-restricted_user_-_payment_immediate"]="Begränsade - direktbetalning";
$lang["usergroup-restricted_user_-_payment_invoice"]="Begränsade - fakturabetalning";

# Resource type names (for the default resource types)
$lang["resourcetype-photo"]="Fotografi";
$lang["resourcetype-document"]="Dokument";
$lang["resourcetype-video"]="Video";
$lang["resourcetype-audio"]="Audio";
$lang["resourcetype-global_fields"]="Globala fält";
$lang["resourcetype-archive_only"]="Arkiverat material";

# Image size names (for the default image sizes)
$lang["imagesize-thumbnail"]="Miniatyrbild";
$lang["imagesize-preview"]="Förhandsgranskning";
$lang["imagesize-screen"]="Bildskärm";
$lang["imagesize-low_resolution_print"]="Lågupplöst utskrift";
$lang["imagesize-high_resolution_print"]="Högupplöst utskrift";
$lang["imagesize-collection"]="Samling";

# Field titles (for the default fields)
$lang["fieldtitle-keywords"]="Nyckelord";
$lang["fieldtitle-country"]="Land";
$lang["fieldtitle-title"]="Titel";
$lang["fieldtitle-story_extract"]=$lang["storyextract"]="Sammanfattning";
$lang["fieldtitle-credit"]="Skapare";
$lang["fieldtitle-date"]=$lang["date"]="Datum";
$lang["fieldtitle-expiry_date"]="Utgångsdatum";
$lang["fieldtitle-caption"]="Beskrivning";
$lang["fieldtitle-notes"]="Anteckningar";
$lang["fieldtitle-named_persons"]="Namngivna personer";
$lang["fieldtitle-camera_make_and_model"]="Kameratillverkare/modell";
$lang["fieldtitle-original_filename"]="Ursprungligt filnamn";
$lang["fieldtitle-video_contents_list"]="Videoinnehållslista";
$lang["fieldtitle-source"]="Källa";
$lang["fieldtitle-website"]="Webbplats";
$lang["fieldtitle-artist"]="Artist";
$lang["fieldtitle-album"]="Album";
$lang["fieldtitle-track"]="Spår";
$lang["fieldtitle-year"]="Årtal";
$lang["fieldtitle-genre"]="Genre";
$lang["fieldtitle-duration"]="Längd";
$lang["fieldtitle-channel_mode"]="Ljudkanaler";
$lang["fieldtitle-sample_rate"]="Samplingsfrekvens";
$lang["fieldtitle-audio_bitrate"]="Bithastighet, ljud";
$lang["fieldtitle-frame_rate"]="Bildfrekvens";
$lang["fieldtitle-video_bitrate"]="Bithastighet, video";
$lang["fieldtitle-aspect_ratio"]="Bildformat";
$lang["fieldtitle-video_size"]="Bildstorlek";
$lang["fieldtitle-image_size"]="Bildstorlek";
$lang["fieldtitle-extracted_text"]="Automatiskt utdrag";
$lang["fieldtitle-file_size"]=$lang["filesize"]="Filstorlek";
$lang["fieldtitle-category"]="Kategori";
$lang["fieldtitle-subject"]="Ämne";
$lang["fieldtitle-author"]="Författare";

# Top navigation bar (also reused for page titles)
$lang["logout"]="Logga ut";
$lang["contactus"]="Kontakta oss";
# next line
$lang["home"]="Startsida";
$lang["searchresults"]="Sökresultat";
$lang["themes"]="Teman";
$lang["mycollections"]="Mina samlingar";
$lang["myrequests"]="Mina begäranden/beställningar";
$lang["collections"]="Samlingar";
$lang["mycontributions"]="Mina bidrag";
$lang["researchrequest"]="Researchförfrågan";
$lang["helpandadvice"]="Hjälp";
$lang["teamcentre"]="Administration";
# footer link
$lang["aboutus"]="Om oss";
$lang["interface"]="Gränssnitt";

# Search bar
$lang["simplesearch"]="Enkel sökning";
$lang["searchbutton"]="Sök";
$lang["clearbutton"]="Töm";
$lang["bycountry"]="Efter land";
$lang["bydate"]="Efter datum";
$lang["anyyear"]="Alla år";
$lang["anymonth"]="Alla månader";
$lang["anyday"]="Alla dagar";
$lang["anycountry"]="Alla länder";
$lang["resultsdisplay"]="Resultatvisning";
$lang["xlthumbs"]="Extrastora";
$lang["largethumbs"]="Stora";
$lang["smallthumbs"]="Små";
$lang["list"]="Lista";
$lang["perpage"]="per sida";

$lang["gotoadvancedsearch"]="Gå till avancerad sökning";
$lang["viewnewmaterial"]="Visa nytt material";
$lang["researchrequestservice"]="Researchförfrågan";

# Team Centre
$lang["manageresources"]="Hantera material";
$lang["overquota"]="Lagringskvoten överskriden, uppladdning inte möjlig";
$lang["managearchiveresources"]="Hantera arkivmaterial";
$lang["managethemes"]="Hantera teman";
$lang["manageresearchrequests"]="Hantera researchförfrågningar";
$lang["manageusers"]="Hantera användare";
$lang["managecontent"]="Hantera webbplatsens innehåll";
$lang["viewstatistics"]="Visa statistik";
$lang["viewreports"]="Visa rapporter";
$lang["viewreport"]="Visa rapport";
$lang["treeobjecttype-report"]=$lang["report"]="Rapport";
$lang["sendbulkmail"]="Gör massutskick";
$lang["systemsetup"]="Systemkonfiguration";
$lang["usersonline"]="Inloggade användare (passiv tid i minuter)";
$lang["diskusage"]="Använt lagringsutrymme";
$lang["available"]="tillgängligt";
$lang["used"]="använt";
$lang["free"]="ledigt";
$lang["editresearch"]="Redigera researchförfrågan";
$lang["editproperties"]="Redigera egenskaper";
$lang["selectfiles"]="Välj filer";
$lang["searchcontent"]="Sök innehåll";
$lang["ticktodeletehelp"]="Markera kryssrutan och klicka på 'Spara' för att radera textavsnittet (på alla språk)";
$lang["createnewhelp"]="Skapa ett nytt hjälpavsnitt";
$lang["searchcontenteg"]="(sida, namn eller text)";
$lang["copyresource"]="Kopiera material";
$lang["resourceidnotfound"]="Materialnumret hittades inte";
$lang["inclusive"]="(inklusive)";
$lang["pluginssetup"]="Hantera tillägg";
$lang["pluginmanager"]="Tilläggshanterare";
$lang["users"]="användare";


# Team Centre - Bulk E-mails
$lang["emailrecipients"]="Mottagare";
$lang["emailsubject"]="Ämne";
$lang["emailtext"]="Meddelande";
$lang["send"]="Skicka";
$lang["emailsent"]="E-postmeddelandet har skickats.";
$lang["mustspecifyoneuser"]="Du måste ange minst en användare";
$lang["couldnotmatchusers"]="Ett eller flera användarnamn är felaktigt eller dubblerat";

# Team Centre - User management
$lang["comments"]="Kommentarer";

# Team Centre - Resource management
$lang["viewuserpending"]="Visa material som är redo för granskning";
$lang["userpending"]="Material som är redo för granskning";
$lang["viewuserpendingsubmission"]="Visa material som är under registrering";
$lang["userpendingsubmission"]="Material som är under registrering";
$lang["searcharchivedresources"]="Sök i arkiverat material";
$lang["viewresourcespendingarchive"]="Visa material som är redo för arkivering";
$lang["resourcespendingarchive"]="Material som är redo för arkivering";
$lang["uploadresourcebatch"]="Ladda upp material";
$lang["uploadinprogress"]="Uppladdning och skalning pågår";
$lang["transferringfiles"]="Överför filer, var god vänta.";
$lang["donotmoveaway"]="VIKTIGT: Lämna inte den här sidan innan uppladdningen har slutförts!";
$lang["pleaseselectfiles"]="Välj en eller flera filer att ladda upp.";
$lang["resizingimage"]="Skalar bilden";
$lang["uploaded"]="Uppladdad(e)";
$lang["andresized"]="och skalad(e)";
$lang["uploadfailedfor"]="Uppladdningen misslyckades för"; # E.g. upload failed for abc123.jpg
$lang["uploadcomplete"]="Uppladdningen slutförd.";
$lang["resourcesuploadedok"]="filer korrekt uppladdade"; # E.g. 17 resources uploaded OK
$lang["failed"]="misslyckades";
$lang["clickviewnewmaterial"]="Klicka på 'Visa nytt material' för att se uppladdat material.";
$lang["specifyftpserver"]="Ange FTP-server";
$lang["ftpserver"]="FTP-server";
$lang["ftpusername"]="Användarnamn (FTP)";
$lang["ftppassword"]="Lösenord (FTP)";
$lang["ftpfolder"]="Mapp (FTP)";
$lang["connect"]="Anslut";
$lang["uselocalupload"]="ELLER: Använd en lokal 'uppladdningsmapp' i stället för FTP-server";

# User contributions
$lang["contributenewresource"]="Bidra med nytt material";
$lang["viewcontributedps"]="Visa mina bidrag - under registrering";
$lang["viewcontributedpr"]="Visa mina bidrag - redo för granskning";
$lang["viewcontributedsubittedl"]="Visa mina bidrag - aktiva";
$lang["contributedps"]="Mina bidrag - under registrering";
$lang["contributedpr"]="Mina bidrag - redo för granskning";
$lang["contributedsubittedl"]="Mina bidrag - aktiva";

# Collections
$lang["editcollection"]="Redigera samling";
$lang["access"]="Åtkomst";
$lang["private"]="Privat";
$lang["public"]="Publik";
$lang["attachedusers"]="Tillknutna användare";
$lang["themecategory"]="Temakategori";
$lang["theme"]="Tema";
$lang["newcategoryname"]="ELLER: Ange ett nytt temakategorinamn...";
$lang["allowothersaddremove"]="Tillåt andra användare att lägga till/ta bort material";
$lang["resetarchivestatus"]="Uppdatera status för allt material i samlingen";
$lang["editallresources"]="Redigera allt material i samlingen";
$lang["editresources"]="Redigera material";
$lang["multieditnotallowed"]="Materialet är inte möjligt att redigera i grupp - allt material har inte samma status eller är av samma typ.";
$lang["emailcollection"]="Dela ut samling via e-post";
$lang["collectionname"]="Samlingsnamn";
$lang["collectionid"]="Samlingsnummer";
$lang["collectionidprefix"]="Saml_nr";
$lang["emailtousers"]="Mottagare<br><br><b>För mottagare med användarkonto (intern):</b> Skriv några bokstäver i användarens namn för att söka, klicka sen på det funna namnet och därefter på plus.<br><br><b>För mottagare  utan användarkonto (extern):</b> Skriv en e-postadress och klicka på plus.";
$lang["removecollectionareyousure"]="Är du säker på att du vill ta bort den här samlingen från listan?";
$lang["managemycollections"]="Hantera 'Mina samlingar'";
$lang["createnewcollection"]="Skapa ny samling";
$lang["findpubliccollection"]="Hitta en publik samling";
$lang["searchpubliccollections"]="Sök publika samlingar";
$lang["addtomycollections"]="Lägg till i mina samlingar";
$lang["action-addtocollection"]="Lägg till i samling";
$lang["action-removefromcollection"]="Ta bort från samling";
$lang["addtocollection"]="Lägg till i samling";
$lang["cantmodifycollection"]="Du kan inte ändra denna samling.";
$lang["currentcollection"]="Aktiv samling";
$lang["viewcollection"]="Visa samling";
$lang["viewall"]="Visa alla";
$lang["action-editall"]="Redigera alla";
$lang["hidethumbnails"]="Dölj miniatyrbilder";
$lang["showthumbnails"]="Visa miniatyrbilder";
$lang["contactsheet"]="Kontaktkopia";
$lang["mycollection"]="Min samling";
$lang["editresearchrequests"]="Redigera researchförfrågan";
$lang["research"]="Research";
$lang["savedsearch"]="Sparad sökning";
$lang["mustspecifyoneusername"]="Du måste ange minst ett användarnamn";
$lang["couldnotmatchallusernames"]="Ett användarnamn är felaktigt";
$lang["emailcollectionmessage"]="har skickat en samling till dig från $applicationname."; # suffixed to user name e.g. "Fred has e-mailed you a collection.."
$lang["emailcollectionmessageexternal"]="har skickat en samling till dig från $applicationname."; # suffixed to user name e.g. "Fred has e-mailed you a collection.."
$lang["clicklinkviewcollection"]="Klicka på länken nedan för att visa samlingen.";
$lang["zippedcollectiontextfile"]="Inkludera textfil med information om material/samling.";
$lang["copycollectionremoveall"]="Ta bort allt nuvarande material innan kopiering";
$lang["purgeanddelete"]="Rensa ut";
$lang["purgecollectionareyousure"]="Är du säker på att du vill radera denna samling OCH RADERA allt material i den?";
$lang["collectionsdeleteempty"]="Radera tomma samlingar";
$lang["collectionsdeleteemptyareyousure"]="Är du säker på att du vill radera alla dina tomma samlingar?";
$lang["collectionsnothemeselected"]="Du måste välja en befintlig, eller namnge en ny temakategori.";
$lang["downloaded"]="Nedladdad";
$lang["contents"]="Innehåll";
$lang["forthispackage"]="för det här paketet";
$lang["didnotinclude"]="Utelämnades";
$lang["selectcollection"]="Välj samlingen";
$lang["total"]="Totalt";
$lang["ownedbyyou"]="ägda av dig";

# Resource create / edit / view
$lang["createnewresource"]="Skapa nytt material";
$lang["treeobjecttype-resource_type"]=$lang["resourcetype"]="Materialtyp";
$lang["resourcetypes"]="Materialtyper";
$lang["deleteresource"]="Radera material";
$lang["downloadresource"]="Ladda ned material";
$lang["rightclicktodownload"]="Högerklicka på denna länk och välj 'Spara mål som...' för att ladda ned materialet..."; # For Opera/IE browsers only
$lang["downloadinprogress"]="Nedladdning pågår";
$lang["editmultipleresources"]="Redigera material i grupp";
$lang["editresource"]="Redigera material";
$lang["resources_selected-1"]="1 material valt"; # 1 resource selected
$lang["resources_selected-2"]="%number material valda"; # e.g. 17 resources selected
$lang["image"]="Bild";
$lang["previewimage"]="Förhandsgranska bild";
$lang["file"]="Fil";
$lang["upload"]="Uppladdning";
$lang["action-upload"]="Ladda upp";
$lang["uploadafile"]="Ladda upp fil";
$lang["replacefile"]="Ersätt fil";
$lang["imagecorrection"]="Bildkorrigering";
$lang["previewthumbonly"]="(endast förhandsgranskning/miniatyrbild)";
$lang["rotateclockwise"]="Rotera medurs";
$lang["rotateanticlockwise"]="Rotera moturs";
$lang["increasegamma"]="Öka gamma (ljusare)";
$lang["decreasegamma"]="Minska gamma (mörkare)";
$lang["restoreoriginal"]="Återställ original";
$lang["recreatepreviews"]="Återskapa förhandsgranskningar";
$lang["retrypreviews"]="Försök skapa förhandsgranskningar igen";
$lang["specifydefaultcontent"]="Ange standardinnehåll för nytt material";
$lang["properties"]="- typspecifika egenskaper";
$lang["relatedresources"]="Relaterat material";
$lang["indexedsearchable"]="Indexerade, sökbara fält";
$lang["clearform"]="Töm formulär";
$lang["similarresources"]="liknande material"; # e.g. 17 similar resources
$lang["similarresource"]="liknande material"; # e.g. 1 similar resource
$lang["nosimilarresources"]="Inget liknande material";
$lang["emailresource"]="Dela ut via e-post";
$lang["resourcetitle"]="Materialtitel";
$lang["requestresource"]="Begär material";
$lang["action-viewmatchingresources"]="Visa matchande material";
$lang["nomatchingresources"]="Inget matchande material";
$lang["matchingresources"]="matchande material"; # e.g. 17 matching resources
$lang["advancedsearch"]="Avancerad sökning";
$lang["archiveonlysearch"]="Sökning endast i arkiverat material";
$lang["allfields"]="Alla fält";
$lang["typespecific"]="Typspecifika";
$lang["youfound"]="Du hittade"; # e.g. you found 17 resources
$lang["youfoundresources"]="material"; # e.g. you found 17 resources
$lang["youfoundresource"]="material"; # e.g. you found 1 resource
$lang["display"]="Visning"; # e.g. Display: thumbnails / list
$lang["sortorder"]="Sorteringsordning";
$lang["relevance"]="Relevans";
$lang["asadded"]="Tilläggsdatum";
$lang["popularity"]="Popularitet";
$lang["rating"]="Betyg";
$lang["colour"]="Färg";
$lang["jumptopage"]="Gå till sida";
$lang["jump"]="Gå";
$lang["titleandcountry"]="Titel / Land";
$lang["torefineyourresults"]="För att förfina dina resultat, försök";
$lang["verybestresources"]="Vårt bästa material";
$lang["addtocurrentcollection"]="Lägg till i aktiv samling";
$lang["addresource"]="Lägg till ett material";
$lang["addresourcebatch"]="Lägg till material i grupp";
$lang["fileupload"]="Ladda upp fil";
$lang["clickbrowsetolocate"]="Klicka på 'Bläddra...' för att leta upp en fil";
$lang["resourcetools"]="Materialverktyg";
$lang["fileinformation"]="Filinformation";
$lang["options"]="Alternativ";
$lang["previousresult"]="Föregående resultat";
$lang["viewallresults"]="Visa alla resultat";
$lang["nextresult"]="Nästa resultat";
$lang["pixels"]="pixlar";
$lang["download"]="Ladda ned";
$lang["preview"]="Förhandsgranskning";
$lang["fullscreenpreview"]="Förhandsgranska på bildskärm";
$lang["originalfileoftype"]="Originalfil - ?"; # ? will be replaced, e.g. "Original PDF File"
$lang["fileoftype"]="?-fil"; # ? will be replaced, e.g. "MP4 File"
$lang["log"]="Logg";
$lang["resourcedetails"]="Egenskaper för material";
$lang["offlineresource"]="Nedkopplat material";
$lang["request"]="Begär";
$lang["searchforsimilarresources"]="Sök efter liknande material";
$lang["clicktoviewasresultset"]="Klicka för att se detta material som ett resultatsätt";
$lang["searchnomatches"]="Din sökning matchade inget material.";
$lang["try"]="Prova";
$lang["tryselectingallcountries"]="Prova att välja <b>Alla länder</b> i fältet 'Efter land', eller";
$lang["tryselectinganyyear"]="Prova att välja <b>Alla år</b> i fältet 'Efter år', eller";
$lang["tryselectinganymonth"]="Prova att välja <b>Alla månader</b> i fältet 'Efter månad', eller";
$lang["trybeinglessspecific"]="Prova att vara mindre specifik genom att";
$lang["enteringfewerkeywords"]="ange färre sökord."; # Suffixed to any of the above 4 items e.g. "Try being less specific by entering fewer search keywords"
$lang["match"]="träff";
$lang["matches"]="träffar";
$lang["inthearchive"]="i arkivet";
$lang["nomatchesinthearchive"]="Inga träffar i arkivet";
$lang["savethissearchtocollection"]="Lägg till denna sökfråga i aktiv samling";
$lang["mustspecifyonekeyword"]="Du måste ange minst ett sökord.";
$lang["hasemailedyouaresource"]="har skickat ett material till dig per e-post."; # Suffixed to user name, e.g. Fred has e-mailed you a resource
$lang["clicktoviewresource"]="Klicka på länken nedan för att visa materialet.";
$lang["statuscode"]="Statuskod";

# Resource log - actions
$lang["resourcelog"]="Materiallogg";
$lang["log-u"]="Laddade upp fil";
$lang["log-c"]="Skapade material";
$lang["log-d"]="Laddade ned fil";
$lang["log-e"]="Redigerade fält";
$lang["log-m"]="Redigerade fält (gruppredigering)";
$lang["log-E"]="Delade ut material (via e-post) till";//  + notes field
$lang["log-v"]="Visade material";
$lang["log-x"]="Raderade material";
$lang["log-l"]="Loggade in"; # For user entries only.
$lang["log-t"]="Transformerade fil";
$lang["log-s"]="Ändrade status";
$lang["log-a"]="Ändrade åtkomst";

$lang["backtoresourceview"]="Tillbaka till att visa material";

# Resource status
$lang["status"]="Status"; # Ska kunna inleda med "Materialet är..." följt av statusen.
$lang["status-2"]="Under registrering";
$lang["status-1"]="Redo för granskning";
$lang["status0"]="Aktivt";
$lang["status1"]="Redo för arkivering";
$lang["status2"]="Arkiverat";
$lang["status3"]="Raderat";

# Charts
$lang["activity"]="Aktivitet";
$lang["summary"]="- sammanfattning";
$lang["mostinaday"]="Störst antal på en dag";
$lang["totalfortheyear"]="Totalt antal i år";
$lang["totalforthemonth"]="Totalt antal under innevarande månad";
$lang["dailyaverage"]="Dagligt genomsnittligt antal för denna period";
$lang["nodata"]="Inga uppgifter för denna period.";
$lang["max"]="Max"; # i.e. maximum
$lang["statisticsfor"]="Statistik för"; # e.g. Statistics for 2007
$lang["printallforyear"]="Skriv ut all statistik för året";

# Log in / user account
$lang["nopassword"]="Klicka här för att ansöka om ett användarkonto";
$lang["forgottenpassword"]="Klicka här om du har glömt ditt lösenord";
$lang["keepmeloggedin"]="Håll mig inloggad på den här datorn";
$lang["columnheader-username"]=$lang["username"]="Användarnamn";
$lang["password"]="Lösenord";
$lang["login"]="Logga in";
$lang["loginincorrect"]="Felaktigt användarnamn eller lösenord.<br/><br/>Klicka på länken ovan, <br/>för att begära ett nytt lösenord.";
$lang["accountexpired"]="Ditt användarkontos utgångsdatum har passerats. Kontakta systemets administratör.";
$lang["useralreadyexists"]="Ett användarkonto med samma e-postadress eller användarnamn existerar redan, ändringarna har inte sparats";
$lang["useremailalreadyexists"]="Ett användarkonto med samma e-postadress existerar redan.";
$lang["ticktoemail"]="Skicka användarnamnet och ett nytt lösenord till den här användaren";
$lang["ticktodelete"]="Markera kryssrutan och klicka på 'Spara' för att radera användaren";
$lang["edituser"]="Redigera användare";
$lang["columnheader-full_name"]=$lang["fullname"]="Fullständigt namn";
$lang["email"]="E-post";
$lang["columnheader-e-mail_address"]=$lang["emailaddress"]="E-postadress";
$lang["suggest"]="Föreslå";
$lang["accountexpiresoptional"]="Användarkontot går ut (ej obligatoriskt)";
$lang["lastactive"]="Senast aktiv";
$lang["lastbrowser"]="Senaste använd webbläsare";
$lang["searchusers"]="Sök användare";
$lang["createuserwithusername"]="Skapa användare med användarnamn...";
$lang["emailnotfound"]="E-postadressen kunde inte hittas";
$lang["yourname"]="Ditt fullständiga namn";
$lang["youremailaddress"]="Din e-postadress";
$lang["sendreminder"]="Skicka påminnelse";
$lang["sendnewpassword"]="Skicka nytt lösenord";
$lang["requestuserlogin"]="Ansök om ett användarkonto";

# Research request
$lang["nameofproject"]="Projektets namn";
$lang["descriptionofproject"]="Beskrivning av projektet";
$lang["descriptionofprojecteg"]="(t.ex. målgrupp, stil, ämne eller geografiskt område)";
$lang["deadline"]="Tidsfrist";
$lang["nodeadline"]="Ingen tidsfrist";
$lang["noprojectname"]="Du måste ange ett namn på projektet";
$lang["noprojectdescription"]="Du måste ange en beskrivning av projektet";
$lang["contacttelephone"]="Kontakttelefon";
$lang["finaluse"]="Slutanvändning";
$lang["finaluseeg"]="(t.ex. PowerPoint, broschyr eller affisch)";
$lang["noresourcesrequired"]="Mängd material som krävs för den färdiga produkten?";
$lang["shaperequired"]="Önskad bildorientering";
$lang["portrait"]="Porträtt";
$lang["landscape"]="Landskap";
$lang["square"]="Kvadratisk";
$lang["either"]="Valfri";
$lang["sendrequest"]="Skicka förfrågan";
$lang["editresearchrequest"]="Redigera researchförfrågan";
$lang["requeststatus0"]=$lang["unassigned"]="Ej tilldelad";
$lang["requeststatus1"]="Under behandling";
$lang["requeststatus2"]="Besvarad";
$lang["copyexistingresources"]="Kopiera materialet i en befintlig samling till denna research";
$lang["deletethisrequest"]="Markera kryssrutan och klicka på 'Spara' för att radera begäran/beställningen";
$lang["requestedby"]="Inskickad av";
$lang["requesteditems"]="Förfrågade poster";
$lang["assignedtoteammember"]="Tilldelad gruppmedlem";
$lang["typecollectionid"]="(Skriv samlingsnumret nedan)";
$lang["researchid"]="Researchförfrågenummer";
$lang["assignedto"]="Tilldelad";
$lang["createresearchforuser"]="Skapa researchförfrågan för användare";
$lang["searchresearchrequests"]="Sök researchförfrågan";
$lang["requestasuser"]="Förfråga som användare";
$lang["haspostedresearchrequest"]="har postat en researchförfrågan"; # username is suffixed to this
$lang["newresearchrequestwaiting"]="Ny researchförfrågan väntar";
$lang["researchrequestassignedmessage"]="Din researchförfrågan har tilldelats en medlem i teamet. När vi har slutfört researchen kommer du att få ett e-postmeddelande med en länk till allt det material som vi rekommenderar.";
$lang["researchrequestassigned"]="Researchförfrågan är tilldelad";
$lang["researchrequestcompletemessage"]="Din researchförfrågan är besvarad och har lagts till i 'Mina samlingar'.";
$lang["researchrequestcomplete"]="Researchförfrågan besvarad";


# Misc / global
$lang["selectgroupuser"]="Välj grupp/användare...";
$lang["select"]="Välj...";
$lang["add"]="Lägg till";
$lang["create"]="Skapa";
$lang["treeobjecttype-group"]=$lang["group"]="Grupp";
$lang["confirmaddgroup"]="Är du säker på att du vill lägga till alla medlemmar i denna grupp?";
$lang["backtoteamhome"]="Tillbaka till huvudsidan för administration";
$lang["columnheader-resource_id"]=$lang["resourceid"]="Materialnummer";
$lang["id"]="Nr";
$lang["todate"]="Till datum";
$lang["fromdate"]="Från datum";
$lang["day"]="Dag";
$lang["month"]="Månad";
$lang["year"]="År";
$lang["hour-abbreviated"]="TT";
$lang["minute-abbreviated"]="MM";
$lang["itemstitle"]="Poster";
$lang["tools"]="Verktyg";
$lang["created"]="Skapad";
$lang["user"]="Användare";
$lang["owner"]="Ägare";
$lang["message"]="Meddelande";
$lang["name"]="Namn";
$lang["action"]="Handling";
$lang["treeobjecttype-field"]=$lang["field"]="Fält";
$lang["save"]="Spara";
$lang["revert"]="Återställ";
$lang["cancel"]="Avbryt";
$lang["view"]="Visa";
$lang["type"]="Typ";
$lang["text"]="Text";
$lang["yes"]="Ja";
$lang["no"]="Nej";
$lang["key"]="Symbolförklaring"; # e.g. explanation of icons on search page
$lang["languageselection"]="Språkval";
$lang["language"]="Språk";
$lang["changeyourpassword"]="Byt lösenord";
$lang["yourpassword"]="Ditt lösenord";
$lang["newpassword"]="Nytt lösenord";
$lang["newpasswordretype"]="Nytt lösenord (repetera)";
$lang["passwordnotvalid"]="Detta är inte ett giltigt lösenord";
$lang["passwordnotmatch"]="Du har inte skrivit samma lösenord båda gångerna";
$lang["wrongpassword"]="Felaktigt lösenord, försök igen";
$lang["action-view"]="Visa";
$lang["action-preview"]="Förhandsgranska";
$lang["action-viewmatchingresources"]="Visa matchande material";
$lang["action-expand"]="Expandera";
$lang["action-select"]="Välj";
$lang["action-download"]="Ladda ned";
$lang["action-email"]="E-posta";
$lang["action-edit"]="Redigera";
$lang["action-delete"]="Radera";
$lang["action-revertmetadata"]="Återställ metadata";
$lang["confirm-revertmetadata"]="Är du säker på att du vill återhämta den ursprungliga metadatan från den här filen? Den här åtgärden simulerar en återuppladdningen av filen, och du kommer att förlora all ändrad metadata.";
$lang["action-remove"]="Ta bort";
$lang["complete"]="Slutförd";
$lang["backtohome"]="Tillbaka till startsidan";
$lang["backtohelphome"]="Tillbaka till huvudsidan för hjälp";
$lang["backtosearch"]="Tillbaka till sökresultatet";
$lang["backtoview"]="Visa material";
$lang["backtoeditresource"]="Tillbaka till att redigera material";
$lang["backtouser"]="Tillbaka till inloggningssidan";
$lang["termsandconditions"]="Användningsvillkor";
$lang["iaccept"]="Jag accepterar";
$lang["contributedby"]="Tillagt av";
$lang["format"]="Format";
$lang["notavailableshort"]="-";
$lang["allmonths"]="Alla månader";
$lang["allgroups"]="Alla grupper";
$lang["status-ok"]="Okej";
$lang["status-fail"]="MISSLYCKADES";
$lang["status-warning"]="VARNING";
$lang["status-notinstalled"]="Ej installerad";
$lang["status-never"]="Aldrig";
$lang["softwareversion"]="?-version"; # E.g. "PHP version"
$lang["softwarebuild"]="?-bygge"; # E.g. "ResourceSpace Build"
$lang["softwarenotfound"]="Programmet '?' hittades inte."; # ? will be replaced.
$lang["client-encoding"]="(klientkodning: %encoding)"; # %encoding will be replaced, e.g. client-encoding: utf8
$lang["browseruseragent"]="Webbläsare";
$lang['serverplatform']="Serverplattform";
$lang["are_available-0"]="är tillgängliga";
$lang["are_available-1"]="är tillgängligt";
$lang["are_available-2"]="är tillgängliga";
$lang["were_available-0"]="var tillgängliga";
$lang["were_available-1"]="var tillgängligt";
$lang["were_available-2"]="var tillgängliga";
$lang["resource-0"]="material";
$lang["resource-1"]="material";
$lang["resource-2"]="material";
$lang["status-note"]="OBSERVERA";
$lang["action-changelanguage"]="Byt språk";

# Pager
$lang["next"]="Nästa";
$lang["previous"]="Föregående";
$lang["page"]="Sida";
$lang["of"]="av"; # e.g. page 1 of 2
$lang["items"]="poster"; # e.g. 17 items
$lang["item"]="post"; # e.g. 1 item

# Statistics
$lang["stat-addpubliccollection"]="Tillägg av publika samlingar"; # Man ska kunna sätta "Antal" framför alla aktiviteter.
$lang["stat-addresourcetocollection"]="Tillägg av material i samlingar";
$lang["stat-addsavedsearchtocollection"]="Tillägg av sparade sökningar i samlingar";
$lang["stat-addsavedsearchitemstocollection"]="Tillägg av poster från sparade sökningar i samlingar";
$lang["stat-advancedsearch"]="Avancerade sökningar";
$lang["stat-archivesearch"]="Arkivsökningar";
$lang["stat-assignedresearchrequest"]="Tilldelade researchförfrågningar";
$lang["stat-createresource"]="Skapade material";
$lang["stat-e-mailedcollection"]="E-postutskick av samlingar";
$lang["stat-e-mailedresource"]="E-postutskick av material";
$lang["stat-keywordaddedtoresource"]="Tillägg av nyckelord till material";
$lang["stat-keywordusage"]="Användningar av nyckelord";
$lang["stat-newcollection"]="Nya samlingar";
$lang["stat-newresearchrequest"]="Nya researchförfrågningar";
$lang["stat-printstory"]="Utskrifter av sammanfattningar";
$lang["stat-processedresearchrequest"]="Bearbetade sökningar";
$lang["stat-resourcedownload"]="Nedladdningar av material";
$lang["stat-resourceedit"]="Redigeringar av material";
$lang["stat-resourceupload"]="Uppladdningar av material";
$lang["stat-resourceview"]="Visningar av material";
$lang["stat-search"]="Sökningar";
$lang["stat-usersession"]="Användarsessioner";
$lang["stat-addedsmartcollection"]="Tillägg av smarta samlingar";

# Access
$lang["access0"]="Öppen";
$lang["access1"]="Begränsad";
$lang["access2"]="Konfidentiell";
$lang["access3"]="Anpassad";
$lang["statusandrelationships"]="Status och relationer";

# Lists
$lang["months"]=array("januari","februari","mars","april","maj","juni","juli","augusti","september","oktober","november","december");

# New for 1.3
$lang["savesearchitemstocollection"]="Lägg till funna poster i aktiv samling";
$lang["removeallresourcesfromcollection"]="Markera kryssrutan och klicka på 'Spara' för att ta bort allt material från den här samlingen";
$lang["deleteallresourcesfromcollection"]="Markera kryssrutan och klicka på 'Spara' för att radera själva materialet i den här samlingen";
$lang["deleteallsure"]="Är du säker på att du vill RADERA detta material? Detta kommer att radera själva materialet, inte bara ta bort det från denna samling.";
$lang["batchdonotaddcollection"]="(Lägg inte till i en samling)";
$lang["collectionsthemes"]="Relaterade teman och publika samlingar";
$lang["recent"]="Nyaste";
$lang["batchcopyfrom"]="Kopiera metadata från material nr";
$lang["copy"]="Kopiera";
$lang["zipall"]="Zippa alla";
$lang["downloadzip"]="Ladda ned samlingen som en zip-fil";
$lang["downloadsize"]="Nedladdningsstorlek";
$lang["tagging"]="Etikettering";
$lang["speedtagging"]="Snabbetikettering";
$lang["existingkeywords"]="Befintliga nyckelord:";
$lang["extrakeywords"]="Extra nyckelord";
$lang["leaderboard"]="Tabell";
$lang["confirmeditall"]="Är du säker på att du vill spara? Detta kommer att skriva över existerande värden för de valda fälten för allt material i din aktiva samling.";
$lang["confirmsubmitall"]="Är du säker på att du vill sända allt material för granskning? Detta kommer att skriva över existerande värden för de valda fälten för allt material i din aktiva samling och sända det till granskning.";
$lang["confirmunsubmitall"]="Är du säker på att du vill dra tillbaka allt material från granskningsprocessen? Detta kommer att skriva över existerande värden för de valda fälten för allt material i din aktiva samling och dra tillbaka det från granskningsprocessen.";
$lang["confirmpublishall"]="Är du säker på att du vill publicera materialet? Detta kommer skriva över existerande värden för de valda fälten för allt material i din aktiva samling och publicera allt för publik visning.";
$lang["confirmunpublishall"]="Är du säker på att du vill dra tillbaka publiceringen? Detta kommer skriva över existerande värden för de valda fälten för allt material i din aktiva samling och dra tillbaka det från publik visning.";
$lang["collectiondeleteconfirm"]="Är du säker på att du vill radera den här samlingen?";
$lang["hidden"]="(dolt)";
$lang["requestnewpassword"]="Begär nytt lösenord";

# New for 1.4
$lang["reorderresources"]="Ändra ordningen på materialet inom samlingen (klicka och dra)";
$lang["addorviewcomments"]="Skriv eller visa kommentarer";
$lang["collectioncomments"]="Samlingskommentarer";
$lang["collectioncommentsinfo"]="Skriv en kommentar till materialet. Kommentaren gäller bara i den här samlingen.";
$lang["comment"]="Kommentar";
$lang["warningexpired"]="Materialets utgångsdatum har passerats";
$lang["warningexpiredtext"]="Varning! Materialets utgångsdatum har passerats. Du måste klicka på länken nedan för att aktivera filnedladdning.";
$lang["warningexpiredok"]="&gt; Aktivera materialnedladdning";
$lang["userrequestcomment"]="Meddelande";
$lang["addresourcebatchbrowser"]="Lägg till material i grupp - i webbläsare (Flash)";
$lang["addresourcebatchbrowserjava"]="Lägg till material i grupp - i webbläsare (Java - rekommenderas)";

$lang["addresourcebatchftp"]="Lägg till material i grupp - hämta från FTP-server";
$lang["replaceresourcebatch"]="Ersätt material i grupp";
$lang["editmode"]="Redigeringsläge";
$lang["replacealltext"]="Ersätt befintlig text med texten nedan";
$lang["findandreplace"]="Sök och ersätt";
$lang["appendtext"]="Lägg till texten nedan";
$lang["removetext"]="Ange text att radera från befintlig text";
$lang["find"]="Sök";
$lang["andreplacewith"]="... och ersätt med...";
$lang["relateallresources"]="Skapa relationer mellan allt material i den här samlingen";

# New for 1.5
$lang["columns"]="Kolumner";
$lang["contactsheetconfiguration"]="Inställningar för kontaktkopia";
$lang["thumbnails"]="Miniatyrbilder";
$lang["contactsheetintrotext"]="Välj arkstorlek och antal kolumner för din kontaktkopia.";
$lang["size"]="Storlek";
$lang["orientation"]="Orientering";
$lang["requiredfield"]="Obligatoriskt fält";
$lang["requiredfields"]="Alla obligatoriska fält är inte ifyllda. Gå igenom formuläret och försök sen igen.";
$lang["viewduplicates"]="Visa dubbletter av material";
$lang["duplicateresources"]="Dubbletter av material";
$lang["userlog"]="Användarlogg";
$lang["ipaddressrestriction"]="Begränsa IP-adress (frivilligt)";
$lang["wildcardpermittedeg"]="Jokertecken tillåtna, t.ex.";

# New for 1.6
$lang["collection_download_original"]="Originalfil";
$lang["newflag"]="NY!";
$lang["link"]="Länk";
$lang["uploadpreview"]="Ladda upp en bild som enbart förhandsgranskning";
$lang["starttypingusername"]="Användarnamn/namn/gruppnamn...";
$lang["requestfeedback"]="Be om respons<br/>(svar sänds per e-post)";
$lang["sendfeedback"]="Skicka respons";
$lang["feedbacknocomments"]="Du har inte gett någon respons på materialet i samlingen.<br/>Klicka på pratbubblorna bredvid materialet för att ge respons.";
$lang["collectionfeedback"]="Respons på samlingen";
$lang["collectionfeedbackemail"]="Du har fått följande respons:";
$lang["feedbacksent"]="Din respons har skickats.";
$lang["newarchiveresource"]="Lägg till ett arkiverat material";
$lang["nocategoriesselected"]="Inga kategorier valda";
$lang["showhidetree"]="Visa/dölj träd";
$lang["clearall"]="Töm allt";
$lang["clearcategoriesareyousure"]="Är du säker på att du vill ta bort alla gjorda val?";
$lang["share"]="Dela ut";
$lang["sharecollection"]="Dela ut samling";
$lang["generateurl"]="Generera webbadress";
$lang["generateurlinternal"]="Nedanstående webbadress gäller bara för inloggade användare.";
$lang["generateurlexternal"]="Nedanstående webbadress kommer att fungera för alla och kräver inte inloggning.";
$lang["archive"]="Arkiv";
$lang["collectionviewhover"]="Klicka för att se materialet i den här samlingen.";
$lang["collectioncontacthover"]="Skapa en kontaktkopia med materialet i den här samlingen.";
$lang["original"]="Original";

$lang["password_not_min_length"]="Lösenordet måste innehålla minst ? tecken";
$lang["password_not_min_alpha"]="Lösenordet måste innehålla minst ? bokstäver (a-z, A-Z)";
$lang["password_not_min_uppercase"]="Lösenordet måste innehålla minst ? versaler (A-Z)";
$lang["password_not_min_numeric"]="Lösenordet måste innehålla minst ? siffror (0-9)";
$lang["password_not_min_special"]="Lösenordet måste innehålla minst ? icke alfanumeriska tecken (!@$%&amp;* etc.)";
$lang["password_matches_existing"]="Det föreslagna lösenordet är samma som ditt befintliga lösenord";
$lang["password_expired"]="Ditt lösenords utgångsdatum har passerats och du måste nu ange ett nytt lösenord";
$lang["max_login_attempts_exceeded"]="Du har överskridit det maximalt tillåtna antalet inloggningsförsök. Du måste nu vänta ? minuter innan du kan försöka logga in igen.";

$lang["newlogindetails"]="Du hittar dina nya inloggningsuppgifter nedan."; # For new password mail
$lang["youraccountdetails"]="Dina kontouppgifter"; # Subject of mail sent to user on user details save

$lang["copyfromcollection"]="Kopiera från samling";
$lang["donotcopycollection"]="Kopiera inte från en samling";

$lang["resourcesincollection"]="material i den här samlingen"; # E.g. 3 resources in this collection
$lang["removefromcurrentcollection"]="Ta bort från aktiv samling";
$lang["showtranslations"]="+ Visa översättningar";
$lang["hidetranslations"]="- Dölj översättningar";
$lang["archivedresource"]="Arkiverat material";

$lang["managerelatedkeywords"]="Hantera relaterade nyckelord";
$lang["keyword"]="Nyckelord";
$lang["relatedkeywords"]="Relaterade nyckelord";
$lang["matchingrelatedkeywords"]="Matchande relaterade nyckelord";
$lang["newkeywordrelationship"]="Skapa ny relation för nyckelord...";
$lang["searchkeyword"]="Sök nyckelord";

$lang["exportdata"]="Exportera data";
$lang["exporttype"]="Exportformat";

$lang["managealternativefiles"]="Hantera alternativa filer";
$lang["managealternativefilestitle"]="Hantera alternativa filer";
$lang["alternativefiles"]="Alternativa filer";
$lang["filetype"]="Filtyp";
$lang["filedeleteconfirm"]="Är du säker på att du vill radera denna fil?";
$lang["addalternativefile"]="Lägg till alternativ fil";
$lang["editalternativefile"]="Redigera alternativ fil";
$lang["description"]="Beskrivning";
$lang["notuploaded"]="Inte uppladdade";
$lang["uploadreplacementfile"]="Ladda upp ersättningsfil";
$lang["backtomanagealternativefiles"]="Tillbaka till att hantera alternativa filer";


$lang["resourceistranscoding"]="Materialet kodas just nu om";
$lang["cantdeletewhiletranscoding"]="Du kan inte radera material medan det kodas om";

$lang["maxcollectionthumbsreached"]="Det finns för mycket material i den här samlingen för att kunna visa miniatyrbilder. Miniatyrbilder kommer nu att döljas.";

$lang["ratethisresource"]="Vilket betyg ger du materialet?";
$lang["ratingthankyou"]="Tack för ditt betyg!";
$lang["ratings"]="betyg";
$lang["rating_lowercase"]="betyg";
$lang["cannotemailpassword"]="Du kan inte skicka användarna deras existerande lösenord eftersom lösenorden är lagrade i krypterad form.<br/><br/>Du måste använda knappen 'Föreslå' ovanför som genererar ett nytt lösenord för att sen kunna skicka det per e-post.";

$lang["userrequestnotification1"]="Användarformuläret har fyllts i med följande uppgifter:";
$lang["userrequestnotification2"]="Om du godtar denna ansökan kan du gå till webbadressen nedan och skapa ett användarkonto för den här användaren.";
$lang["ipaddress"]="IP-adress";
$lang["userresourcessubmitted"]="Följande användarbidrag har lagts fram för granskning:";
$lang["userresourcesunsubmitted"]="Följande användarbidrag har dragits tillbaka, och kräver inte längre granskning:";
$lang["viewalluserpending"]="Se alla användarbidrag som väntar på granskning:";

# New for 1.7
$lang["installationcheck"]="Installationskontroll";
$lang["managefieldoptions"]="Hantera fältinnehåll";
$lang["matchingresourcesheading"]="Matchande material";
$lang["backtofieldlist"]="Tillbaka till fältlistan";
$lang["rename"]="Byt namn";
$lang["showalllanguages"]="Visa alla språk";
$lang["hidealllanguages"]="Dölj alla språk";
$lang["clicktologinasthisuser"]="Klicka här för att logga in som denna användare";
$lang["addkeyword"]="Lägg till nyckelord";
$lang["selectedresources"]="Valt material";

$lang["internalusersharing"]="Dela ut till en intern användare";
$lang["externalusersharing"]="Dela ut till en extern användare";
$lang["accesskey"]="Åtkomstnyckel";
$lang["sharedby"]="Utdelad av";
$lang["sharedwith"]="Utdelad till";
$lang["lastupdated"]="Senast uppdaterad";
$lang["lastused"]="Senast använd";
$lang["noattachedusers"]="Ingen tillknuten användare.";
$lang["confirmdeleteaccess"]="Är du säker på att du vill radera denna åtkomstnyckel? Användare som har fått tillgång till denna samling med hjälp av denna nyckel kommer inte längre att kunna komma åt samlingen.";
$lang["noexternalsharing"]="Ingen extern utdelning.";
$lang["sharedcollectionaddwarning"]="Varning: Den här samlingen har delats ut till externa användare. Materialet du har lagt till har därmed gjorts tillgängligt för dessa användare. Klicka på 'Dela ut' för att hantera extern åtkomst för denna samling.";
$lang["addresourcebatchlocalfolder"]="Lägg till material i grupp - hämta från lokal mapp";

# Setup Script
$lang["setup-alreadyconfigured"]="Din installation av ResourceSpace är redan konfigurerad. För att göra om konfigurationen kan du radera <pre>include/config.php</pre> och peka webbläsaren till den här sidan igen.";
$lang["setup-successheader"]="Gratulerar!";
$lang["setup-successdetails"]="Den grundläggande delen av installationen av ResourceSpace är klar. Gå igenom filen 'include/default.config.php' för att hitta fler konfigurationsmöjligheter.";
$lang["setup-successnextsteps"]="Nästa steg:";
$lang["setup-successremovewrite"]="Du bör nu avlägsna skrivrättigheten till mappen 'include/'.";
$lang["setup-visitwiki"]='Besök <a href="http://rswiki.montala.net/index.php/Main_Page">ResourceSpace Documentation Wiki</a> (engelskspråkig wiki) för att hitta mer information om hur du skräddarsyr din installation.';
$lang["setup-checkconfigwrite"]="Skrivrättighet till konfigurationsmapp:";
$lang["setup-checkstoragewrite"]="Skrivrättighet till lagringsmapp:";
$lang["setup-welcome"]="Välkommen till ResourceSpace";
$lang["setup-introtext"]="Tack för att du väljer ResourceSpace. Detta konfigurationsskript hjälper dig att installera ResourceSpace. Detta behöver endast göras en gång.";
$lang["setup-checkerrors"]="Fel upptäcktes i din systemkonfiguration.<br/> Var vänlig åtgärda dessa fel och peka sen webbläsaren till den här sidan igen för att fortsätta.";
$lang["setup-errorheader"]="Fel upptäcktes i din konfiguration. Se detaljerade felmeddelanden nedan.";
$lang["setup-warnheader"]="Några av dina inställningar genererade varningsmeddelanden, se nedan. Detta betyder inte nödvändigtvis att det är ett problem med din konfiguration.";
$lang["setup-basicsettings"]="Grundläggande inställningar";
$lang["setup-basicsettingsdetails"]="Här gör du de grundläggande inställningarna för din installation av ResourceSpace.<br><strong>*</strong>Obligatoriskt fält";
$lang["setup-dbaseconfig"]="Databaskonfiguration";
$lang["setup-mysqlerror"]="Det finns ett fel i dina MySQL-inställningar:";
$lang["setup-mysqlerrorversion"]="MySQL-versionen måste vara 5 eller senare.";
$lang["setup-mysqlerrorserver"]="Kunde inte ansluta till servern.";
$lang["setup-mysqlerrorlogin"]="Inloggningen misslyckades. (Kontrollera användarnamn och lösenord.)";
$lang["setup-mysqlerrordbase"]="Kunde inte att ansluta till databasen.";
$lang["setup-mysqlerrorperns"]="Kontrollera databasanvändarens behörigheter. Kunde inte skapa tabeller.";
$lang["setup-mysqltestfailed"]="Testet misslyckades (kunde inte verifiera MySQL)";
$lang["setup-mysqlserver"]="MySQL-server:";
$lang["setup-mysqlusername"]="Användarnamn (MySQL):";
$lang["setup-mysqlpassword"]="Lösenord (MySQL):";
$lang["setup-mysqldb"]="Databasnamn (MySQL):";
$lang["setup-mysqlbinpath"]="Programsökväg (MySQL):";
$lang["setup-generalsettings"]="Allmänna inställningar";
$lang["setup-baseurl"]="Baswebbadress:";
$lang["setup-emailfrom"]="E-post skickas från adress:";
$lang["setup-emailnotify"]="E-post skickas till adress:";
$lang["setup-spiderpassword"]="Spindellösenord:";
$lang["setup-scramblekey"]="Krypteringsnyckel:";
$lang["setup-apiscramblekey"]="Krypteringsnyckel för API:et:";
$lang["setup-paths"]="Sökvägar";
$lang["setup-pathsdetail"]="Ange sökväg utan efterföljande snedstreck för varje program. Lämna sökvägen tom för att inaktivera ett program. En del sökvägar upptäcktes och fylldes i automatiskt.";
$lang["setup-applicationname"]="Webbplatsens/installationens namn:";
$lang["setup-basicsettingsfooter"]="OBS! Alla <strong>obligatoriska</strong> inställningar är alla samlade på den här sidan. Om du inte är intresserad av att kontrollera de avancerade inställningarna kan du klicka nedan för att starta installationen.";
$lang["setup-if_mysqlserver"]='IP-adress eller <abbr title="Fullständigt kvalificerat domännamn">FQDN</abbr> för din MySQL-server. Ange "localhost" om MySQL är installerad på samma server som din webbserver.';
$lang["setup-if_mysqlusername"]="Användarnamnet som ska användas för att ansluta till MySQL-servern. Användaren måste ha rättighet att skapa tabeller i databasen nedan.";
$lang["setup-if_mysqlpassword"]="Lösenordet för MySQL-användaren ovan.";
$lang["setup-if_mysqldb"]="Namnet på MySQL-databasen som ResourceSpace ska använda. Databasen måste redan existera.";
$lang["setup-if_mysqlbinpath"]="Sökvägen till MySQL-klientens programfiler - t.ex. mysqldump. OBS! Detta behövs bara om du avser att använda exportverktyg.";
$lang["setup-if_baseurl"]="Baswebbadressen för den här installationen. OBS! Utan efterföljande snedstreck.";
$lang["setup-if_emailfrom"]="Den adress som e-post från ResourceSpace tycks komma ifrån.";
$lang["setup-if_emailnotify"]="E-postadressen som ansökningar och förfrågningar ska skickas till.";
$lang["setup-if_spiderpassword"]="Spindellösenordet är ett obligatoriskt fält.";
$lang["setup-if_scramblekey"]="Ange en sträng (svår att gissa) som krypteringsnyckel för att aktivera kryptering av materialsökvägar. Om detta är en installation nåbar från Internet rekommenderas kryptering. Lämna fältet tomt för att inaktivera kryptering. Innehållet i fältet har redan slumpats fram för dig, men du kan ändra det så att det motsvarar en befintlig installation, om det behövs.";
$lang["setup-if_apiscramblekey"]="Ange en sträng (svår att gissa) som krypteringsnyckel för API:et. Om du planerar att använda API:er rekommenderas detta i högsta grad.";
$lang["setup-if_applicationname"]="Namnet på webbplatsen/installationen (ex. 'MittFöretags mediaarkiv').";
$lang["setup-err_mysqlbinpath"]="Det går inte att verifiera sökvägen. Lämna tomt för att inaktivera.";
$lang["setup-err_baseurl"]="Baswebbadressen är ett obligatoriskt fält.";
$lang["setup-err_baseurlverify"]="Baswebbadressen verkar inte vara korrekt (kunde inte ladda license.txt).";
$lang["setup-err_spiderpassword"]="Lösenordet som krävs för spider.php. VIKTIGT! Slumpa fram detta för varje ny installation. Ditt material kommer att kunna läsas av den som kan detta lösenord. Innehållet i fältet har redan slumpats fram för dig, men du kan ändra det så att det motsvarar en befintlig installation, om det behövs.";
$lang["setup-err_scramblekey"]="Om detta är en installation nåbar från Internet rekommenderas kryptering";
$lang["setup-err_apiscramblekey"]="Om detta är en installation nåbar från Internet rekommenderas användning av krypteringsnyckel för API:et.";
$lang["setup-err_path"]="Det går inte att verifiera sökvägen för";
$lang["setup-emailerr"]="Ogiltig e-postadress.";
$lang["setup-rs_initial_configuration"]="ResourceSpace: Inledande konfiguration";
$lang["setup-include_not_writable"]="Skrivrättighet till mappen '/include' saknas. Krävs bara under installationen.";
$lang["setup-override_location_in_advanced"]="Sökvägen kan åsidosättas i 'Avancerade inställningar'.";
$lang["setup-advancedsettings"]="Avancerade inställningar";
$lang["setup-binpath"]="Sökväg till %bin"; #%bin will be replaced, e.g. "Imagemagick Path"
$lang["setup-begin_installation"]="Starta installation";
$lang["setup-generaloptions"]="Allmänna val";
$lang["setup-allow_password_change"]="Tillåt byte av lösenord?";
$lang["setup-enable_remote_apis"]="Tillåt API-anrop utifrån?";
$lang["setup-if_allowpasswordchange"]="Tillåt användarna att byta sina egna lösenord.";
$lang["setup-if_enableremoteapis"]="Tillåt fjärråtkomst till API-tilläggen.";
$lang["setup-allow_account_requests"]="Tillåt ansökningar om användarkonton?";
$lang["setup-display_research_request"]="Visa funktionen researchfrågan?";
$lang["setup-if_displayresearchrequest"]="Tillåt användarna att skicka in researchförfrågningar via ett formulär, som sen skickas per e-post.";
$lang["setup-themes_as_home"]="Använd sidan Teman som startsida?";
$lang["setup-remote_storage_locations"]="Platser för fjärrlagring";
$lang["setup-use_remote_storage"]="Använd fjärrlagring?";
$lang["setup-if_useremotestorage"]="Markera den här kryssrutan för att konfigurera fjärrlagring för ResourceSpace. (För att placera lagringsmappen på en annan server.)";
$lang["setup-storage_directory"]="Lagringsmapp";
$lang["setup-if_storagedirectory"]="Var materialfilerna lagras. Kan vara en absolut sökväg (/var/www/blah/blah) eller relativ till installationen. OBS! Inget efterföljande snedstreck.";
$lang["setup-storage_url"]="Lagringsmappens webbadress";
$lang["setup-if_storageurl"]="Var lagringsmappen finns tillgänglig. Kan vara absolut (http://filer.exempel.se) eller relativ till installationen. OBS! Inget efterföljande snedstreck.";
$lang["setup-ftp_settings"]="FTP-inställningar";
$lang["setup-if_ftpserver"]="Krävs endast om du planerar att hämta material från en FTP-server.";
$lang["setup-login_to"]="Logga in på";
$lang["setup-configuration_file_output"]="Utmatning till konfigurationsfilen";

# Collection log - actions
$lang["collectionlog"]="Samlingslogg";
$lang["collectionlog-r"]="Tog bort material";
$lang["collectionlog-R"]="Tog bort allt material";
$lang["collectionlog-D"]="Raderade allt material";
$lang["collectionlog-d"]="Raderade material"; // this shows external deletion of any resources related to the collection.
$lang["collectionlog-a"]="La till material";
$lang["collectionlog-c"]="La till material (kopierade)";
$lang["collectionlog-m"]="La till materialkommentar";
$lang["collectionlog-*"]="La till materialbetyg";
$lang["collectionlog-S"]="Delade ut samlingen till "; //  + notes field
$lang["collectionlog-E"]="Skickade samlingen per e-post till ";//  + notes field
$lang["collectionlog-s"]="Delade ut material till ";//  + notes field
$lang["collectionlog-T"]="Återtog utdelningen av samlingen till ";//  + notes field
$lang["collectionlog-t"]="Återtog åtkomst till material för ";//  + notes field
$lang["collectionlog-X"]="Raderade samlingen";


$lang["viewuncollectedresources"]="Visa material som inte ingår i samlingar";

# Collection requesting
$lang["requestcollection"]="Begär samling";

# Metadata report
$lang["metadata-report"]="Detaljerad metadata";

# Video Playlist
$lang["videoplaylist"]="Videospellista";

$lang["restrictedsharecollection"]="Du har begränsad tillgång till minst ett material i den här samlingen och därför är utdelning inte tillåten.";

$lang["collection"]="Samling";
$lang["idecline"]="Jag accepterar inte"; # For terms and conditions

$lang["mycollection_notpublic"]="Du kan inte göra samlingen 'Min samling' till en publik samling eller ett tema. Skapa en ny samling för detta ändamål.";

$lang["resourcemetadata"]="Metadata för material";

$lang["selectgenerateurlexternal"]="Välj behörighetsnivå för den externa webbadressen (för användare utan konto).";

$lang["externalselectresourceaccess"]="Välj en behörighetsnivå du finner lämplig, om du delar ut material till en extern användare.";

$lang["externalselectresourceexpires"]="Välj ett utgångsdatum för den genererade webbadressen, om du delar ut material till en extern användare.";

$lang["externalshareexpired"]="Utgångsdatumet har passerats och därför är utdelningen tyvärr inte längre tillgänglig.";

$lang["expires"]="Utgår";
$lang["never"]="Aldrig";

$lang["approved"]="Godkänd";
$lang["notapproved"]="Ej godkänd";

$lang["userrequestnotification3"]="Klicka på länken nedan för att se över detaljerna och godkänna användarkontot, om du godtar denna ansökan.";

$lang["ticktoapproveuser"]="Du måste markera kryssrutan för att godkänna användaren om du vill aktivera kontot";

$lang["managerequestsorders"]="Hantera begäranden / beställningar";
$lang["editrequestorder"]="Redigera begäran / beställning";
$lang["requestorderid"]="Begäran / Beställning";
$lang["viewrequesturl"]="Klicka på länken nedan för att visa denna begäran:";
$lang["requestreason"]="Anledning till begäran";

$lang["resourcerequeststatus0"]="Obesvarad";
$lang["resourcerequeststatus1"]="Bifallen";
$lang["resourcerequeststatus2"]="Avslagen";

$lang["ppi"]="PPI"; # (Pixels Per Inch - used on the resource download options list).

$lang["useasthemethumbnail"]="Vill du använda detta material som miniatyrbild för temakategorin?";
$lang["sessionexpired"]="Du har blivit utloggad eftersom du var inaktiv i mer än 30 minuter. Var god och logga in igen för att fortsätta.";

$lang["resourcenotinresults"]="Detta material ingår inte längre i ditt sökresultat, så navigering mellan nästa/föregående är inte längre möjligt.";
$lang["publishstatus"]="Spara med publiceringsstatus:";
$lang["addnewcontent"]="Nytt innehåll (sida, namn)";
$lang["hitcount"]="Antal träffar";
$lang["downloads"]="Nedladdningar";

$lang["addremove"]="";

##  Translations for standard log entries
$lang["all_users"]="alla användare";
$lang["new_resource"]="nytt material";

$lang["invalidextension_mustbe"]="Ogiltigt filnamnstillägg, måste vara";
$lang["allowedextensions"]="Giltiga filnamnstillägg";

$lang["alternativebatchupload"]="Ladda upp alternativa filer i grupp (Java)";

$lang["confirmdeletefieldoption"]="Är du säker på att du vill RADERA och TA BORT detta fält?";

$lang["cannotshareemptycollection"]="Denna samling är tom och kan inte delas ut.";

$lang["requestall"]="Begär alla";
$lang["requesttype-email_only"]=$lang["resourcerequesttype0"]="E-post";
$lang["requesttype-managed"]=$lang["resourcerequesttype1"]="Hanterad";
$lang["requesttype-payment_-_immediate"]=$lang["resourcerequesttype2"]="Direktbetalning";
$lang["requesttype-payment_-_invoice"]=$lang["resourcerequesttype3"]="Fakturabetalning";

$lang["requestapprovedmail"]="Din begäran har blivit godkänd. Klicka på länken nedanför för att visa och ladda ned materialet.";
$lang["requestdeclinedmail"]="Beklagar, din begäran har blivit avslagen för materialet i samlingen nedan.";

$lang["resourceexpirymail"]="Följande material har ett utgångsdatum som passerats:";
$lang["resourceexpiry"]="Materialets utgångsdatum";

$lang["requestapprovedexpires"]="Din åtkomst till detta material går ut den";

$lang["pleasewaitsmall"]="(vänligen vänta)";
$lang["removethisfilter"]="(ta bort detta filter)";

$lang["no_exif"]="Importera inte EXIF-, IPTC- eller XMP-metadata vid denna uppladdning";
$lang["difference"]="Ändring";
$lang["viewdeletedresources"]="Visa raderat material";
$lang["finaldeletion"]="Detta material är redan markerat som raderat. Denna handling kommer att radera materialet permanent.";

$lang["nocookies"]="En kaka kunde inte sparas korrekt. Kontrollera att din webbläsare tillåter kakor.";

$lang["selectedresourceslightroom"]="Valt material (lista kompatibel med Adobe Lightroom):";

# Plugins Manager
$lang['plugins-noneinstalled'] = "Inga tillägg aktiverade.";
$lang['plugins-noneavailable'] = "Inga tillägg tillgängliga.";
$lang['plugins-availableheader'] = 'Tillgängliga tillägg';
$lang['plugins-installedheader'] = 'Aktiverade tillägg';
$lang['plugins-author'] = 'Upphovsman';
$lang['plugins-version'] = 'Version';
$lang['plugins-instversion'] = 'Installerad version';
$lang['plugins-uploadheader'] = 'Ladda upp tillägg';
$lang['plugins-uploadtext'] = 'Välj en .rsp-fil att ladda upp.';
$lang['plugins-deactivate'] = 'Inaktivera';
$lang['plugins-moreinfo'] = 'Mer information';
$lang['plugins-activate'] = 'Aktivera';
$lang['plugins-purge'] = 'Nollställ konfiguration';
$lang['plugins-rejmultpath'] = 'Arkivet innehåller flera sökvägar. (Säkerhetsrisk)';
$lang['plugins-rejrootpath'] = 'Arkivet innehåller absoluta sökvägar. (Säkerhetsrisk)';
$lang['plugins-rejparentpath'] = 'Arkivet innehåller överliggande sökvägar (../). (Säkerhetsrisk)';
$lang['plugins-rejmetadata'] = 'Arkivets dokumentationsfil hittades inte.';
$lang['plugins-rejarchprob'] = 'Det uppstod ett problem under uppackningen:';
$lang['plugins-rejfileprob'] = 'Tillägget måste vara en .rsp-fil.';
$lang['plugins-rejremedy'] = 'Om du litar på detta tillägg kan du installera det manuellt genom att packa upp arkivet direkt i din tilläggsmapp.';
$lang['plugins-uploadsuccess'] = 'Uppladdningen av tillägget slutfördes korrekt';
$lang['plugins-headertext'] = 'Tillägg utvidgar funktionerna för ResourceSpace.';
$lang['plugins-legacyinst'] = 'Aktiverat via config.php';
$lang['plugins-uploadbutton'] = 'Ladda upp tillägg';

#Location Data
$lang['location-title'] = 'Platsinformation';
$lang['location-add'] = 'Lägg till plats';
$lang['location-edit'] = 'Redigera plats';
$lang['location-details'] = 'Dubbelklicka på kartan för att placera nålen. Du kan dra i nålen för att justera placeringen i efterhand.';
$lang['location-noneselected']="Ingen plats vald";
$lang['location'] = 'Plats';

$lang["publiccollections"]="Publika samlingar";
$lang["viewmygroupsonly"]="Visa bara mina grupper";
$lang["usemetadatatemplate"]="Använd metadatamall";
$lang["undometadatatemplate"]="(ångra val av metadatamall)";

$lang["accountemailalreadyexists"]="Ett användarkonto med samma e-postadress existerar redan";

$lang["backtothemes"]="Tillbaka till teman";
$lang["downloadreport"]="Ladda ned rapport";

#Bug Report Page
$lang['reportbug']="Förbered buggrapport till utvecklarna av ResourceSpace";
$lang['reportbug-detail']="Följande information har sammanställts till buggrapporten.  Du kommer att kunna redigera all data innan du skickar iväg rapporten.";
$lang['reportbug-login']="OBS! Klicka här för att logga in till bugghanteringssystemet INNAN du klickar på 'Förbered buggrapport'.";
$lang['reportbug-preparebutton']="Förbered buggrapport";

$lang["enterantispamcode"]="<strong>Inloggningstest</strong> <sup>*</sup><br /> Var god fyll i koden:";

$lang["groupaccess"]="Gruppåtkomst";
$lang["plugin-groupsallaccess"]="Det här tillägget är aktiverat för alla grupper";
$lang["plugin-groupsspecific"]="Det här tillägget är endast aktiverat för markerade grupper";


$lang["associatedcollections"]="Samlingar materialet ingår i";
$lang["emailfromuser"]="Skicka e-postmeddelandet från ";
$lang["emailfromsystem"]="Om du avmarkerar kryssrutan skickas e-postmeddelandet från systemets e-postadress: ";



$lang["previewpage"]="Förhandsgranska sida";
$lang["nodownloads"]="Inga nedladdningar";
$lang["uncollectedresources"]="Material som inte ingår i samlingar";
$lang["nowritewillbeattempted"]="Exiftool kommer inte att försöka skriva metadata.";
$lang["notallfileformatsarewritable"]="Exiftool kan dock inte skriva i alla filtyper.";
$lang["filetypenotsupported"]="Filtypen %filetype stöds inte";
$lang["exiftoolprocessingdisabledforfiletype"]="Exiftool är inaktiverad för filtypen %filetype"; # %filetype will be replaced, e.g. Exiftool processing disabled for file type JPG
$lang["nometadatareport"]="Ingen metadatarapport";
$lang["metadatawritewillbeattempted"]="Exiftool kommer att försöka skriva nedanstående metadata.";
$lang["embeddedvalue"]="Inbäddat värde";
$lang["exiftooltag"]="Exiftool-fält";
$lang["error"]="Fel";
$lang["exiftoolnotfound"]="Kunde inte hitta Exiftool";

$lang["indicateusage"]="Beskriv hur du planerar att använda detta material.";
$lang["usage"]="Användning";
$lang["indicateusagemedium"]="Användningsmedia";
$lang["usageincorrect"]="Du måste ange hur du planerar att använda materialet och välja ett media";

$lang["savesearchassmartcollection"]="Spara sökning som en 'Smart samling'";
$lang["smartcollection"]="Smart samling";


$lang["uploadertryflash"]="Om du har problem med den här uppladdaren, prova <strong>Flash-uppladdaren</strong>.";
$lang["uploadertryjava"]="Om du har problem med den här uppladdaren, eller om du <strong>laddar upp stora filer</strong>, prova <strong>Java-uppladdaren</strong>.";
$lang["getjava"]="Besök Javas webbplats för att säkerställa att du har den senaste Java-versionen installerad.";
$lang["getflash"]="Besök Flash-spelarens webbplats för att säkerställa att du har den senaste Flash-spelaren installerad.";

$lang["all"]="Alla";
$lang["backtoresults"]="Tillbaka till sökresultatet";

$lang["preview_all"]="Förhandsgranska alla";

$lang["usagehistory"]="Användningshistorik";
$lang["usagebreakdown"]="Detaljerad användningshistorik";
$lang["usagetotal"]="Totalt nedladdat";
$lang["usagetotalno"]="Totalt antal nedladdningar";
$lang["ok"]="OK";

$lang["random"]="Slumpmässig";
$lang["userratingstatsforresource"]="Användarbetyg för material";
$lang["average"]="Medel";
$lang["popupblocked"]="Popup-fönstret har blockerats av din webbläsare.";
$lang["closethiswindow"]="Stäng fönstret";

$lang["requestaddedtocollection"]="Det här materialet har lagts till i din aktiva samling. Du kan begära alla poster i samlingen genom att klicka på \'Begär alla\' i panelen \'Mina samlingar\' i nederkant av skärmen";

# E-commerce text
$lang["buynow"]="Köp nu";
$lang["yourbasket"]="Din varukorg";
$lang["addtobasket"]="Lägg i varukorg";
$lang["yourbasketcontains"]="Din varukorg innehåller ? artiklar.";
$lang["yourbasketisempty"]="Din varukorg är tom.";
$lang["buy"]="Köp";
$lang["buyitemaddedtocollection"]="Det här materialet har lagts i din varukorg. Du kan köpa alla artiklar i din varukorg genom att klicka på \'Köp nu\' nedan.";
$lang["buynowintro"]="Välj de storlekar du önskar.";
$lang["nodownloadsavailable"]="Tyvärr finns det inga nedladdningar tillgängliga för det här materialet.";
$lang["proceedtocheckout"]="Gå till kassan";
$lang["totalprice"]="Totalsumma";
$lang["price"]="Pris";
$lang["waitingforpaymentauthorisation"]="Tyvärr har vi inte fått betalningsuppdraget. Vänta en kort stund och klicka sen på 'Ladda om' nedan.";
$lang["reload"]="Ladda om";
$lang["downloadpurchaseitems"]="Ladda ned köpta artiklar";
$lang["downloadpurchaseitemsnow"]="Använd länkarna nedan för att ladda ned dina köpta artiklar direkt.<br><br>Lämna inte den här sidan innan du har laddat ned alla artiklar.";
$lang["alternatetype"]="Alternativ typ";


$lang["subcategories"]="Underkategorier";
$lang["back"]="Tillbaka";

$lang["pleasewait"]="Vänligen vänta...";

$lang["autorotate"]="Rotera bilder automatiskt?";

# Reports
# Report names (for the default reports)
$lang["report-keywords_used_in_resource_edits"]="Nyckelord använda i material";
$lang["report-keywords_used_in_searches"]="Nyckelord använda i sökningar";
$lang["report-resource_download_summary"]="Materialnedladdningar - sammanställning";
$lang["report-resource_views"]="Materialvisningar";
$lang["report-resources_sent_via_e-mail"]="Material skickat per e-post";
$lang["report-resources_added_to_collection"]="Material tillagt i samling";
$lang["report-resources_created"]="Material skapat";
$lang["report-resources_with_zero_downloads"]="Material utan nedladdningar";
$lang["report-resources_with_zero_views"]="Material utan visningar";
$lang["report-resource_downloads_by_group"]="Materialnedladdningar per grupp";
$lang["report-resource_download_detail"]="Materialnedladdningar - detaljerad lista";
$lang["report-user_details_including_group_allocation"]="Användaruppgifter inklusive grupptillhörighet";

#Column headers (for the default reports)
$lang["columnheader-keyword"]="Nyckelord";
$lang["columnheader-entered_count"]="Antal förekomster";
$lang["columnheader-searches"]="Sökningar";
$lang["columnheader-date_and_time"]="Datum / Tid";
$lang["columnheader-downloaded_by_user"]="Nedladdat av användare";
$lang["columnheader-user_group"]="Grupp";
$lang["columnheader-resource_title"]="Materialtitel";
$lang["columnheader-title"]="Titel";
$lang["columnheader-downloads"]="Nedladdningar";
$lang["columnheader-group_name"]="Gruppnamn";
$lang["columnheader-resource_downloads"]="Nedladdningar";
$lang["columnheader-views"]="Visningar";
$lang["columnheader-added"]="Tillagt";
$lang["columnheader-creation_date"]="Skapat";
$lang["columnheader-sent"]="Skickat";
$lang["columnheader-last_seen"]="Senast inloggad";

$lang["period"]="Period";
$lang["lastndays"]="Senaste ? dagarna"; # ? is replaced by the system with the number of days, for example "Last 100 days".
$lang["specificdays"]="Specifikt antal dagar";
$lang["specificdaterange"]="Specifik period";
$lang["to"]="till";

$lang["emailperiodically"]="Skapa ett nytt periodiskt återkommande e-postutskick";
$lang["emaileveryndays"]="Skicka mig denna rapport per e-post var ? dag";
$lang["newemailreportcreated"]="Ett nytt periodiskt återkommande e-postutskick har skapats. Du kan avbryta utskicken genom att klicka på webblänken nederst i meddelandena.";
$lang["unsubscribereport"]="Klicka på webblänken nedan för att avbryta prenumerationen på den här rapporten:";
$lang["unsubscribed"]="Prenumerationen avbruten";
$lang["youhaveunsubscribedreport"]="Du har avbrutit prenumerationen på det periodiskt återkommande e-postutskicket med rapporter.";
$lang["sendingreportto"]="Skickar rapporten till";
$lang["reportempty"]="Ingen matchande data hittades för vald rapport och period.";

$lang["purchaseonaccount"]="Debitera konto";
$lang["areyousurepayaccount"]="Är du säker på att du vill debitera ditt konto för detta köp?";
$lang["accountholderpayment"]="Kontobetalning";
$lang["subtotal"]="Delsumma";
$lang["discountsapplied"]="Totala rabatter";
$lang["log-p"]="Inköpt material";
$lang["viauser"]="via användare";
$lang["close"]="Stäng";

# Installation Check
$lang["repeatinstallationcheck"]="Repetera installationskontroll";
$lang["shouldbeversion"]="Ska vara version ? eller högre"; # E.g. "should be 4.4 or greater"
$lang["phpinivalue"]="PHP.INI-värde för '?'"; # E.g. "PHP.INI value for 'memory_limit'"
$lang["writeaccesstofilestore"]="Skrivrättighet till mappen $storagedir finns?";
$lang["nowriteaccesstofilestore"]="Skrivrättighet till mappen $storagedir saknas.";
$lang["writeaccesstohomeanim"]="Skrivrättighet till mappen $homeanim_folder finns?";
$lang["nowriteaccesstohomeanim"]="Skrivrättighet till mappen $homeanim_folder saknas. Skrivrättighet måste finnas för att tillägget 'transform' ska kunna infoga bilder i startsidans bildspel.";
$lang["blockedbrowsingoffilestore"]="Åtkomsten till mappen 'filestore' är blockerad för webbläsare?";
$lang["noblockedbrowsingoffilestore"]="Mappen 'filestore' är inte blockerad för webbläsare. Ta bort 'Indexes' från 'Options' i Apache.";
$lang["executionofconvertfailed"]="Exekveringen misslyckades. Oväntat svar när kommandot exekverades. Svaret var '?'.<br>I Windows och IIS 6 måste åtkomst ges för kommandon i kommandotolken. Se installationsinstruktionerna i wikin."; # ? will be replaced.
$lang["lastscheduledtaskexection"]="Senaste schemalagda uppgiftskörning (dagar)";
$lang["executecronphp"]="Sökningar efter liknande material kommer inte att fungera och schemalagda e-postrapporter kommer inte att skickas. Se till att <a href='../batch/cron.php'>batch/cron.php</a> körs åtminstone en gång per dag som ett cronjobb eller liknande.";
$lang["shouldbeormore"]="Bör vara ? eller mer."; # E.g. should be 200M or greater

$lang["generateexternalurl"]="Generera extern webbadress";

$lang["starsminsearch"]="Antal stjärnor (minimum)";
$lang["anynumberofstars"]="Valfritt antal stjärnor";

$lang["noupload"]="Ingen uppladdning";

# System Setup
# System Setup Tree Nodes (for the default setup tree)
$lang["treenode-root"]="Rot";
$lang["treenode-group_management"]="Grupphanteraren";
$lang["treenode-new_group"]="Ny grupp";
$lang["treenode-new_subgroup"]="Ny undergrupp";
$lang["treenode-resource_types_and_fields"]="Materialtyper / -fält";
$lang["treenode-new_resource_type"]="Ny materialtyp";
$lang["treenode-new_field"]="Nytt fält";
$lang["treenode-reports"]="Rapporter";
$lang["treenode-new_report"]="Ny rapport";
$lang["treenode-downloads_and_preview_sizes"]="Storlekar för nedladdning / förhandsgranskning";
$lang["treenode-new_download_and_preview_size"]="Ny storlek";
$lang["treenode-database_statistics"]="Databasstatistik";
$lang["treenode-permissions_search"]="Behörighetssökning";
$lang["treenode-no_name"]="Namnlös";

$lang["treeobjecttype-preview_size"]="Storlek";

$lang["permissions"]="Behörigheter";

# System Setup File Editor
$lang["configdefault-title"]="(Kopiera och klistra in inställningarna härifrån.)";
$lang["config-title"]="(Var NOGA med att undvika syntaxfel. Om du skapar en fil med syntaxfel kan felen inte korrigeras inifrån ResourceSpace!)";

# System Setup Properties Pane
$lang["file_too_large"]="Filen är för stor";
$lang["field_updated"]="Fältet uppdaterat";
$lang["zoom"]="Förstoring";
$lang["deletion_instruction"]="Lämna tomt och klicka på 'Spara' för att radera filen";
$lang["upload_file"]="Ladda upp fil";
$lang["item_deleted"]="Posten raderad";
$lang["viewing_version_created_by"]="Visar versionen skapad av";
$lang["on_date"]="den";
$lang["launchpermissionsmanager"]="Starta Behörighetshanteraren";
$lang["confirm-deletion"]="Är du säker?";

# Permissions Manager
$lang["permissionsmanager"]="Behörighetshanteraren";
$lang["backtogroupmanagement"]="Tillbaka till grupphanteraren";
$lang["searching_and_access"]="Sökning/Åtkomst";
$lang["metadatafields"]="Metadatafält";
$lang["resource_creation_and_management"]="Skapande/hantering av material";
$lang["themes_and_collections"]="Teman/Samlingar";
$lang["administration"]="Administration";
$lang["other"]="Övrigt";
$lang["custompermissions"]="Anpassade behörigheter";
$lang["searchcapability"]="Kan söka efter material";
$lang["access_to_restricted_and_confidential_resources"]="Kan se konfidentiellt material, kan ladda ned material med 'begränsad' åtkomst<br>(normalt endast för administratörer)";
$lang["restrict_access_to_all_available_resources"]="Tillåts åtkomst endast till tillgängligt material";
$lang["can_make_resource_requests"]="Kan begära material";
$lang["show_watermarked_previews_and_thumbnails"]="Ser förhandsgranskningar/miniatyrbilder vattenstämplade";
$lang["can_see_all_fields"]="Kan se alla fält";
$lang["can_see_field"]="Kan se fältet";
$lang["can_edit_all_fields"]="Kan skriva i alla fält<br>(för skrivbara material)";
$lang["can_edit_field"]="Kan skriva i fältet";
$lang["can_see_resource_type"]="Kan se material av typen";
$lang["restricted_access_only_to_resource_type"]="Tillåts åtkomst endast till material av typen";
$lang["edit_access_to_workflow_state"]="Kan redigera material med statusen";
$lang["can_create_resources_and_upload_files-admins"]="Kan skapa material / ladda upp filer<br>(administratörer; materialet får statusen 'Aktivt')";
$lang["can_create_resources_and_upload_files-general_users"]="Kan skapa material / ladda upp filer<br>(vanliga användare; materialet får statusen 'Redo för granskning'";
$lang["can_delete_resources"]="Kan radera material<br>(till vilket användaren har skrivrättighet)";
$lang["can_manage_archive_resources"]="Kan hantera arkivmaterial";
$lang["can_tag_resources_using_speed_tagging"]="Kan etikettera material med 'Snabbetikettering'<br>(måste vara aktiverat i config.php)";
$lang["enable_bottom_collection_bar"]="Aktivera samlingsfältet i nederkant av skärmen";
$lang["can_publish_collections_as_themes"]="Kan publicera samlingar som teman";
$lang["can_see_all_theme_categories"]="Kan se alla temakategorier";
$lang["can_see_theme_category"]="Kan se temakategori";
$lang["display_only_resources_within_accessible_themes"]="Kan endast söka efter material som hör till teman som användaren har åtkomst till";
$lang["can_access_team_centre"]="Kan nå sidan 'Administration'";
$lang["can_manage_research_requests"]="Kan hantera researchförfrågningar";
$lang["can_manage_resource_requests"]="Kan hantera begäranden/beställningar av material";
$lang["can_manage_content"]="Kan hantera webbplatsinnehåll";
$lang["can_bulk-mail_users"]="Kan göra massutskick";
$lang["can_manage_users"]="Kan hantera användare";
$lang["can_manage_keywords"]="Kan hantera nyckelord";
$lang["can_access_system_setup"]="Kan nå sidan 'Systemkonfiguration'";
$lang["can_change_own_password"]="Kan ändra lösenordet till det egna användarkontot";
$lang["can_manage_users_in_children_groups"]="Kan hantera användare endast i grupper som är barn till användarens egen grupp.";
$lang["can_email_resources_to_own_and_children_and_parent_groups"]="Kan skicka material per e-post endast till användare i användarens egen grupp och till användare i grupper som är barn eller förälder till användarens grupp";

$lang["nodownloadcollection"]="Du har inte behörighet att ladda ned material från den här samlingen.";

$lang["progress"]="Förlopp";
$lang["ticktodeletethisresearchrequest"]="Markera kryssrutan och klicka på 'Spara' för att radera förfrågan";

# SWFUpload
$lang["queued_too_many_files"]="Du har försökt att köa för många filer.";
$lang["creatingthumbnail"]="Skapar miniatyrbild...";
$lang["uploading"]="Laddar upp...";
$lang["thumbnailcreated"]="En miniatyrbild är skapad.";
$lang["done"]="Slutförd.";
$lang["stopped"]="Stoppad."; 

$lang["latlong"]="Latitud, longitud";
$lang["geographicsearch"]="Geografisk sökning";

$lang["geographicsearch_help"]="Klicka och dra för att välja ett sökområde.";

$lang["purge"]="Rensa ut";
$lang["purgeuserstitle"]="Rensa ut användare";
$lang["purgeusers"]="Rensa ut användare";
$lang["purgeuserscommand"]="Radera användarkonton som inte har varit aktiva de senaste % månaderna, men som skapades före den perioden.";
$lang["purgeusersconfirm"]="Är du säker på att du vill radera % användarkonton?";
$lang["pleaseenteravalidnumber"]="Var vänlig att ange ett korrekt nummer";
$lang["purgeusersnousers"]="Det finns inga användare att rensa ut.";

$lang["editallresourcetypewarning"]="Varning: Om du ändrar materialtypen kommer eventuell redan lagrad typspecifik metadata för materialet att raderas.";

$lang["geodragmode"]="Dragläge";
$lang["geodragmodearea"]="Områdesval";
$lang["geodragmodepan"]="Panorering";

$lang["substituted_original"] = "ersattes av original";
$lang["use_original_if_size"] = "Använd original om vald storlek är otillgänglig?";

$lang["originals-available-0"] = "tillgängliga"; # 0 (originals) available
$lang["originals-available-1"] = "tillgängligt"; # 1 (original) available
$lang["originals-available-2"] = "tillgängliga"; # 2+ (originals) available

$lang["inch-short"] = "tum";
$lang["centimetre-short"] = "cm";
$lang["megapixel-short"]="MP";
$lang["at-resolution"] = "i"; # E.g. 5.9 in x 4.4 in @ 144 PPI

$lang["deletedresource"] = "Raderat material";
$lang["deletedresources"] = "Raderat material";
$lang["action-delete_permanently"] = "Radera permanent";

$lang["horizontal"] = "Horisontellt";
$lang["vertical"] = "Vertikalt";

$lang["cc-emailaddress"] = "Kopia till %emailaddress"; # %emailaddress will be replaced, e.g. CC [your email address]

$lang["sort"] = "Sortera";
$lang["sortcollection"] = "Sortera samling";
$lang["emptycollection"] = "Töm samling";
$lang["emptycollectionareyousure"]="Är du säker på att du vill ta bort allt material från den här samlingen?";

$lang["error-cannoteditemptycollection"]="Du kan inte redigera en tom samling.";
$lang["error-permissiondenied"]="Tillåtelse nekades.";
$lang["error-collectionnotfound"]="Samlingen hittades inte.";

$lang["header-upload-subtitle"] = "Steg %number: %subtitle"; # %number, %subtitle will be replaced, e.g. Step 1: Specify Default Content For New Resources
$lang["local_upload_path"] = "Lokal uppladdningsmapp";
$lang["ftp_upload_path"] = "FTP-mapp";
$lang["foldercontent"] = "Mappinnehåll";
$lang["intro-local_upload"] = "Välj en eller flera filer från den lokala uppladdningsmappen och klicka på <b>Ladda upp</b>. När filerna är uppladdade kan de raderas från uppladdningsmappen.";
$lang["intro-ftp_upload"] = "Välj en eller flera filer från FTP-mappen och klicka på <b>Ladda upp</b>.";
$lang["intro-java_upload"] = "Klicka på <b>Bläddra</b> för att välja en eller flera filer och klicka sen på <b>Ladda upp</b>.";
$lang["intro-swf_upload"] = "Klicka på <b>Ladda upp</b> för att välja en eller flera filer som sen direkt laddas upp. Håll ner skift-tangenten för att välja flera filer samtidigt.";
$lang["intro-single_upload"] = "Klicka på <b>Bläddra</b> för att välja en fil och klicka sen på <b>Ladda upp</b>.";
$lang["intro-batch_edit"] = "Ange standardinställningar för uppladdning och standardvärden för metadata för materialet du kommer att ladda upp.";

$lang["collections-1"] = "(<strong>1</strong> samling)";
$lang["collections-2"] = "(<strong>%number</strong> samlingar)"; # %number will be replaced, e.g. 3 Collections
$lang["total-collections-0"] = "<strong>Totalt: 0</strong> samlingar";
$lang["total-collections-1"] = "<strong>Totalt: 1</strong> samling";
$lang["total-collections-2"] = "<strong>Totalt: %number</strong> samlingar"; # %number will be replaced, e.g. Total: 5 Collections
$lang["owned_by_you-0"] = "(<strong>0</strong> ägda av dig)";
$lang["owned_by_you-1"] = "(<strong>1</strong> ägd av dig)";
$lang["owned_by_you-2"] = "(<strong>%mynumber</strong> ägda av dig)"; # %mynumber will be replaced, e.g. (2 owned by you)
