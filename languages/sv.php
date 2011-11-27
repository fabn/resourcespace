<?php
# Swedish
# Language File for ResourceSpace
# -------
# Översättningsfil för huvudprogrammet.
#
# Tilläggsprogram översätts i plugins/*/languages/sv.php
# Webbplatsens innehåll såsom instruktioner och andra skräddarsydda texter är översatta i dbstruct/data_site_text.txt - se även Hantera webbplatsens innehåll (Manage Content)
# Fältvärden översätts (än så länge) i Hantera fältalternativ (Manage Field Options)
# Komponenter som t.ex. JUpload översätts inom respektive projekt
#
# Fraserna har översatts för hand, med hjälp av:
# En befintlig svensk maskinöversättning
# Den norska översättningen (den danska var maskinöversatt)
# Computer Swedens språkwebb: http://cstjanster.idg.se/sprakwebben/
# Svenska datatermgruppen: http://www.datatermgruppen.se/
# Svensk översättning av Gnome: http://live.gnome.org/Swedish/GNOMEOrdlista
# Språkrådet: http://www.sprakradet.se/frågelådan
# Norstedts stora engelsk-svenska ordbok
# Nationalencyklopedins ordbok
#
# Mer information om den svenska översättningen finns på sidan:
# http://wiki.resourcespace.org/index.php/Swedish_Translation_-_svensk_%C3%B6vers%C3%A4ttning
# Där finns bland annat de skrivregler och den ordlista som används internt i ResourceSpace
#
# En första version av översättningen skapades av Henrik Frizén (förnamn.efternamn utan accenttecken i e-postboxen.Sveriges landskod) 20110124 för version 2295
#
# Senast uppdaterad av [Namn] [Datum] för version [svn-version], [kommentar]
# Senast uppdaterad av Henrik Frizén 20111127 för version 3074
#
#
# User group names (for the default user groups)
$lang["usergroup-administrators"]="Administratörer";
$lang["usergroup-general_users"]="Vanliga användare";
$lang["usergroup-super_admin"]="Systemadministratör";
$lang["usergroup-archivists"]="Arkivarier";
$lang["usergroup-restricted_user_-_requests_emailed"]="Begränsade &ndash; begäranden: e-post";
$lang["usergroup-restricted_user_-_requests_managed"]="Begränsade &ndash; begäranden: hanterade";
$lang["usergroup-restricted_user_-_payment_immediate"]="Begränsade &ndash; direktbetalning";
$lang["usergroup-restricted_user_-_payment_invoice"]="Begränsade &ndash; fakturabetalning";

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
$lang["imagesize-screen"]="Skärmbild";
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

# Field types
$lang["fieldtype-text_box_single_line"]="Textfält (enradigt)";
$lang["fieldtype-text_box_multi-line"]="Textfält (flerradigt)";
$lang["fieldtype-text_box_large_multi-line"]="Textfält (stort flerradigt)";
$lang["fieldtype-text_box_formatted_and_ckeditor"]="Textfält (formaterat)";
$lang["fieldtype-check_box_list"]="Kryssrutor (grupp)";
$lang["fieldtype-drop_down_list"]="Rullgardinslista";
$lang["fieldtype-date_and_time"]="Datum/tid";
$lang["fieldtype-expiry_date"]="Utgångsdatum";
$lang["fieldtype-category_tree"]="Kategoriträd";
$lang["fieldtype-dynamic_keywords_list"]="Dynamisk nyckelordslista";

# Property labels (for the default properties)
$lang["documentation-permissions"]="Se <a href=../../documentation/permissions_sv.txt target=_blank>hjälpfilen för behörigheter</a> om du behöver mer information.";
$lang["property-reference"]="Referensnr";
$lang["property-name"]="Namn";
$lang["property-permissions"]="Behörigheter";
$lang["information-permissions"]="Obs! Även eventuella globala behörigheter inställda i ’config.php’ gäller.";
$lang["property-fixed_theme"]="Fast tema";
$lang["property-parent"]="Överordnad";
$lang["property-search_filter"]="Sökfilter";
$lang["property-edit_filter"]="Redigeringsfilter";
$lang["property-resource_defaults"]="Förvald metadata för nytt material";
$lang["property-override_config_options"]="Åsidosätt inställningar i ’config.php’";
$lang["property-email_welcome_message"]="Välkomstmeddelande som skickas per e-post";
$lang["information-ip_address_restriction"]="Jokertecken kan användas i begränsningen av ip-adresser (t.ex. 128.124.*)";
$lang["property-ip_address_restriction"]="Begränsning av ip-adresser";
$lang["property-request_mode"]="Läge för begäranden/beställningar";
$lang["property-allow_registration_selection"]="Tillåt val av denna grupp vid registrering";

$lang["property-resource_type_id"]="Materialtypnr";
$lang["information-allowed_extensions"]="Om du vill begränsa vilka typer av filer som ska kunna överföras för denna materialtyp, anger du här de <i>tillåtna</i> filnamnstilläggen (t.ex. jpg, gif).";
$lang["property-allowed_extensions"]="Tillåtna filnamnstillägg";

$lang["property-field_id"]="Fältnr";
$lang["property-title"]="Titel";
$lang["property-resource_type"]="Materialtyp";
$lang["property-field_type"]="Fälttyp";

$lang["property-options"]="Alternativ";
$lang["property-required"]="Obligatoriskt";
$lang["property-order_by"]="Sorteringsnummer";
$lang["property-indexing"]="<b>Indexering</b>";
$lang["information-if_you_enable_indexing_below_and_the_field_already_contains_data-you_will_need_to_reindex_this_field"]="Om du aktiverar indexering nedan och fältet redan innehåller data måste du <a target=_blank href=../tools/reindex_field.php?field=%ref>återindexera detta fält.</a>"; # %ref will be replaced with the field id
$lang["property-index_this_field"]="Indexera detta fält";
$lang["information-enable_partial_indexing"]="Nyckelordsindexering av delar av ord (prefix + infix) bör användas sparsamt, då det ökar storleken på indexet betydligt. Du kan läsa mer om detta i wikin.";
$lang["property-enable_partial_indexing"]="Aktivera indexering av delar av ord";
$lang["information-shorthand_name"]="Obs! Fältet måste ha ett kortnamn för att det ska visas i Avancerad sökning. Kortnamnet får bara innehålla små bokstäver &ndash; inga mellanslag, siffror eller specialtecken.";
$lang["property-shorthand_name"]="Kortnamn";
$lang["property-display_field"]="Visa fält";
$lang["property-enable_advanced_search"]="Aktivera i Avancerad sökning";
$lang["property-enable_simple_search"]="Aktivera i Enkel sökning";
$lang["property-use_for_find_similar_searching"]="Använd vid sökning efter liknande material";
$lang["property-iptc_equiv"]="IPTC-motsv.";
$lang["property-display_template"]="Visningsmall";
$lang["property-value_filter"]="Värdefilter";
$lang["property-tab_name"]="Fliknamn";
$lang["property-smart_theme_name"]="Namn på smart tema";
$lang["property-exiftool_field"]="Exiftool-fält (tag name)";
$lang["property-exiftool_filter"]="Exiftool-filter";
$lang["property-help_text"]="Hjälptext";
$lang["information-display_as_dropdown"]="För kryssrutor och rullgardinslistor: Visa fältet som en rullgardinslista i Avancerad sökning? Den förvalda inställningen är att istället visa fält av denna typ som en grupp av kryssrutor för att möjliggöra ELLER-funktion vid sökning.";
$lang["property-display_as_dropdown"]="Visa som rullgardinslista";
$lang["property-external_user_access"]="Tillåt åtkomst för externa användare";
$lang["property-autocomplete_macro"]="Autoförslagsmakro";
$lang["property-hide_when_uploading"]="Dölj vid överföring";
$lang["property-hide_when_restricted"]="Dölj för användare med begränsad åtkomst";
$lang["property-omit_when_copying"]="Utelämna vid kopiering";

$lang["property-query"]="Fråga";

$lang["information-id"]="Obs! Fältet Id måste innehålla en unik kod bestående av tre bokstäver";
$lang["property-id"]="Id";
$lang["property-width"]="Bredd";
$lang["property-height"]="Höjd";
$lang["property-pad_to_size"]="Fyll ut till storlek";
$lang["property-internal"]="Intern";
$lang["property-allow_preview"]="Tillåt förhandsgranskning";
$lang["property-allow_restricted_download"]="Tillåt hämtning för användare med begränsad åtkomst";

$lang["property-total_resources"]="Totalt antal material";
$lang["property-total_keywords"]="Totalt antal nyckelord";
$lang["property-resource_keyword_relationships"]="Antal relationer material &ndash; nyckelord";
$lang["property-total_collections"]="Totalt antal samlingar";
$lang["property-collection_resource_relationships"]="Antal relationer samling &ndash; material";
$lang["property-total_users"]="Totalt antal användare";


# Top navigation bar (also reused for page titles)
$lang["logout"]="Logga ut";
$lang["contactus"]="Kontakta oss";
# next line
$lang["home"]="Startsida";
$lang["searchresults"]="Sökresultat";
$lang["themes"]="Teman";
$lang["mycollections"]="Mina samlingar";
$lang["myrequests"]="Mina begäranden";
$lang["collections"]="Samlingar";
$lang["mycontributions"]="Mina bidrag";
$lang["researchrequest"]="Researchförfrågning";
$lang["helpandadvice"]="Hjälp och tips";
$lang["teamcentre"]="Administration";
# footer link
$lang["aboutus"]="Om oss";
$lang["interface"]="Gränssnitt";

# Search bar
$lang["simplesearch"]="Enkel sökning";
$lang["searchbutton"]="Sök";
$lang["clearbutton"]="Rensa";
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

$lang["gotoadvancedsearch"]="Avancerad sökning";
$lang["viewnewmaterial"]="Visa nyaste materialet";
$lang["researchrequestservice"]="Researchförfrågan";

# Team Centre
$lang["manageresources"]="Hantera material";
$lang["overquota"]="Lagringskvoten är överskriden &ndash; du kan inte lägga till material";
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
$lang["usersonline"]="Uppkopplade användare (inaktiv tid i minuter)";
$lang["diskusage"]="Använt lagringsutrymme";
$lang["available"]="tillgängligt";
$lang["used"]="använt";
$lang["free"]="ledigt";
$lang["editresearch"]="Redigera researchförfrågan";
$lang["editproperties"]="Redigera egenskaper";
$lang["selectfiles"]="Välj filer";
$lang["searchcontent"]="Sök innehåll";
$lang["ticktodeletehelp"]="Om du vill ta bort detta textavsnitt (på alla språk) markerar du kryssrutan och klickar på <b>Spara</b>";
$lang["createnewhelp"]="Skapa ett nytt hjälpavsnitt";
$lang["searchcontenteg"]="(sida, namn, text)";
$lang["copyresource"]="Kopiera material";
$lang["resourceidnotfound"]="Materialnumret hittades inte";
$lang["inclusive"]="(inklusive)";
$lang["pluginssetup"]="Hantera tillägg";
$lang["pluginmanager"]="Tilläggshanteraren";
$lang["users"]="användare";


# Team Centre - Bulk E-mails
$lang["emailrecipients"]="Mottagare";
$lang["emailsubject"]="Ämne";
$lang["emailtext"]="Meddelande";
$lang["emailhtml"]="Aktivera stöd för html &ndash; meddelandet måste använda html-formatering";
$lang["send"]="Skicka";
$lang["emailsent"]="E-postmeddelandet har skickats.";
$lang["mustspecifyoneuser"]="Du måste ange minst en användare";
$lang["couldnotmatchusers"]="Ett eller flera användarnamn är felaktigt eller dubblerat";

# Team Centre - User management
$lang["comments"]="Kommentarer";

# Team Centre - Resource management
$lang["viewuserpending"]="Visa material som väntar på granskning";
$lang["userpending"]="Material som väntar på granskning";
$lang["viewuserpendingsubmission"]="Visa material som är under registrering";
$lang["userpendingsubmission"]="Material som är under registrering";
$lang["searcharchivedresources"]="Sök i arkiverat material";
$lang["viewresourcespendingarchive"]="Visa material som väntar på arkivering";
$lang["resourcespendingarchive"]="Material som väntar på arkivering";
$lang["uploadresourcebatch"]="Överför material";
$lang["uploadinprogress"]="Överföring och skalning pågår";
$lang["transferringfiles"]="Överför filer, vänta …";
$lang["donotmoveaway"]="OBS! Lämna inte den här sidan innan överföringen har slutförts.";
$lang["pleaseselectfiles"]="Välj en eller flera filer att överföra.";
$lang["resizingimage"]="Skalar bilden";
$lang["uploaded"]="Överförda";
$lang["andresized"]="och skalade";
$lang["uploadfailedfor"]="Överföringen misslyckades för"; # E.g. upload failed for abc123.jpg
$lang["uploadcomplete"]="Överföringen slutförd.";
$lang["resourcesuploadedok"]="filer korrekt överförda"; # E.g. 17 resources uploaded OK
$lang["failed"]="misslyckades";
$lang["clickviewnewmaterial"]="Klicka på <b>Visa nyaste materialet</b> för att se överfört material.";
$lang["specifyftpserver"]="Ange ftp-server";
$lang["ftpserver"]="Ftp-server";
$lang["ftpusername"]="Användarnamn (ftp)";
$lang["ftppassword"]="Lösenord (ftp)";
$lang["ftpfolder"]="Mapp (ftp)";
$lang["connect"]="Anslut";
$lang["uselocalupload"]="ELLER: Använd en lokal överföringsmapp i stället för ftp-server";

# User contributions
$lang["contributenewresource"]="Bidra med nytt material";
$lang["viewcontributedps"]="Visa mina bidrag &ndash; under registrering";
$lang["viewcontributedpr"]="Visa mina bidrag &ndash; väntande på granskning";
$lang["viewcontributedsubittedl"]="Visa mina bidrag &ndash; aktiva";
$lang["contributedps"]="Mina bidrag &ndash; under registrering";
$lang["contributedpr"]="Mina bidrag &ndash; väntande på granskning";
$lang["contributedsubittedl"]="Mina bidrag &ndash; aktiva";

# Collections
$lang["editcollection"]="Redigera samling";
$lang["editcollectionresources"]="Redigera samlingens förhandsgranskningar";
$lang["access"]="Åtkomst";
$lang["private"]="Privat";
$lang["public"]="Gemensam";
$lang["attachedusers"]="Tillknutna användare";
$lang["themecategory"]="Temakategori";
$lang["theme"]="Tema";
$lang["newcategoryname"]="… eller ange ett nytt temakategorinamn";
$lang["allowothersaddremove"]="Tillåt andra användare att lägga till/avlägsna material";
$lang["resetarchivestatus"]="Uppdatera status för alla material i samlingen";
$lang["editallresources"]="Redigera alla material i samlingen";
$lang["editresources"]="Redigera material";
$lang["multieditnotallowed"]="Materialen är inte möjliga att redigera i grupp &ndash; alla material har inte samma status eller är inte av samma typ.";
$lang["emailcollection"]="Dela samling via e-post";
$lang["collectionname"]="Samlingsnamn";
$lang["collectionid"]="Samlingsnr";
$lang["collectionidprefix"]="Saml_nr";
$lang["emailtousers"]="Mottagare<br><br><b>För mottagare med användarkonto:</b> Ange några bokstäver i användarens namn för att söka, klicka sedan på den hittade användaren och därefter på <b>+</b><br><br><b>För mottagare utan användarkonto:</b> Ange en e-postadress och klicka på <b>+</b>";
$lang["removecollectionareyousure"]="Vill du avlägsna den här samlingen från listan?";
$lang["managemycollections"]="Hantera Mina samlingar";
$lang["createnewcollection"]="Skapa ny samling";
$lang["findpubliccollection"]="Gemensamma samlingar";
$lang["searchpubliccollections"]="Sök gemensamma samlingar";
$lang["addtomycollections"]="Lägg till i Mina samlingar";
$lang["action-addtocollection"]="Lägg till i samling";
$lang["action-removefromcollection"]="Avlägsna från samling";
$lang["addtocollection"]="Lägg till i samling";
$lang["cantmodifycollection"]="Du kan inte ändra på denna samling.";
$lang["currentcollection"]="Aktuell samling";
$lang["viewcollection"]="Visa samling";
$lang["viewall"]="Visa alla";
$lang["action-editall"]="Redigera alla";
$lang["hidethumbnails"]="Dölj miniatyrbilder";
$lang["showthumbnails"]="Visa miniatyrbilder";
$lang["contactsheet"]="Kontaktkopia";
$lang["mycollection"]="Min samling";
$lang["editresearchrequests"]="Redigera researchförfrågningar";
$lang["research"]="Research";
$lang["savedsearch"]="Sparad sökning";
$lang["mustspecifyoneusername"]="Du måste ange minst ett användarnamn";
$lang["couldnotmatchallusernames"]="Ett användarnamn är felaktigt";
$lang["emailcollectionmessage"]="har skickat en samling med material till dig från $applicationname. Denna samling har lagts till i Mina samlingar."; # suffixed to user name e.g. "Fred has e-mailed you a collection..."
$lang["emailcollectionmessageexternal"]="har skickat en samling med material till dig från $applicationname."; # suffixed to user name e.g. "Fred has e-mailed you a collection..."
$lang["clicklinkviewcollection"]="Klicka på länken nedan om du vill visa samlingen.";
$lang["zippedcollectiontextfile"]="Inkludera textfil med information om material/samling";
$lang["copycollectionremoveall"]="Avlägsna alla material innan kopiering";
$lang["purgeanddelete"]="Rensa ut";
$lang["purgecollectionareyousure"]="Vill du ta bort både den här samlingen och alla material i den?";
$lang["collectionsdeleteempty"]="Ta bort tomma samlingar";
$lang["collectionsdeleteemptyareyousure"]="Vill du ta bort alla dina tomma samlingar?";
$lang["collectionsnothemeselected"]="Du måste antingen välja en befintlig temakategori eller namnge en ny.";
$lang["downloaded"]="Hämtad";
$lang["contents"]="Innehåll";
$lang["forthispackage"]="för det här paketet";
$lang["didnotinclude"]="Utelämnades";
$lang["selectcollection"]="Välj samling";
$lang["total"]="Totalt";
$lang["ownedbyyou"]="ägda av dig";

# Resource create / edit / view
$lang["createnewresource"]="Skapa nytt material";
$lang["treeobjecttype-resource_type"]=$lang["resourcetype"]="Materialtyp";
$lang["resourcetypes"]="Materialtyper";
$lang["deleteresource"]="Ta bort material";
$lang["downloadresource"]="Hämta material";
$lang["rightclicktodownload"]="Högerklicka på denna länk och välj <b>Spara mål som</b> för att hämta materialet."; # For Opera/IE browsers only
$lang["downloadinprogress"]="Hämtning pågår";
$lang["editmultipleresources"]="Redigera material i grupp";
$lang["editresource"]="Redigera material";
$lang["resources_selected-1"]="1 material valt"; # 1 resource selected
$lang["resources_selected-2"]="%number material valda"; # e.g. 17 resources selected
$lang["image"]="Bild";
$lang["previewimage"]="Förhandsgranska bild";
$lang["file"]="Fil";
$lang["upload"]="Överföring";
$lang["action-upload"]="Överför";
$lang["uploadafile"]="Överför fil";
$lang["replacefile"]="Ersätt fil";
$lang["imagecorrection"]="Redigera förhandsgranskningar";
$lang["previewthumbonly"]="(endast förhandsgranskning/miniatyrbild)";
$lang["rotateclockwise"]="Rotera medurs";
$lang["rotateanticlockwise"]="Rotera moturs";
$lang["increasegamma"]="Ljusa upp förhandsgranskningar";
$lang["decreasegamma"]="Mörka ner förhandsgranskningar";
$lang["restoreoriginal"]="Återställ original";
$lang["recreatepreviews"]="Återskapa förhandsgranskningar";
$lang["retrypreviews"]="Försök skapa förhandsgranskningar igen";
$lang["specifydefaultcontent"]="Ange den metadata som ska vara förvald för nya material";
$lang["properties"]="&ndash; typspecifika egenskaper";
$lang["relatedresources"]="Relaterade material";
$lang["indexedsearchable"]="Indexerade, sökbara fält";
$lang["clearform"]="Rensa formulär";
$lang["similarresources"]="liknande material"; # e.g. 17 similar resources
$lang["similarresource"]="liknande material"; # e.g. 1 similar resource
$lang["nosimilarresources"]="Inget liknande material";
$lang["emailresource"]="E-posta";
$lang["resourcetitle"]="Materialtitel";
$lang["requestresource"]="Begär material";
$lang["action-viewmatchingresources"]="Visa matchande material";
$lang["nomatchingresources"]="Inget matchande material";
$lang["matchingresources"]="matchande material"; # e.g. 17 matching resources
$lang["advancedsearch"]="Avancerad sökning";
$lang["archiveonlysearch"]="Sökning begränsad till arkiverat material";
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
$lang["titleandcountry"]="Titel/land";
$lang["torefineyourresults"]="För att förfina resultatet, prova";
$lang["verybestresources"]="Våra bästa material";
$lang["addtocurrentcollection"]="Lägg till i aktuell samling";
$lang["addresource"]="Lägg till ett material";
$lang["addresourcebatch"]="Lägg till material i grupp";
$lang["fileupload"]="Överför fil";
$lang["clickbrowsetolocate"]="Leta upp en fil genom att klicka på <b>Bläddra</b>";
$lang["resourcetools"]="Materialverktyg";
$lang["fileinformation"]="Filinformation";
$lang["options"]="Alternativ";
$lang["previousresult"]="Föregående resultat";
$lang["viewallresults"]="Visa alla resultat";
$lang["nextresult"]="Nästa resultat";
$lang["pixels"]="pixlar";
$lang["download"]="Hämtning";
$lang["preview"]="Förhandsgranskning";
$lang["fullscreenpreview"]="Förhandsgranska på bildskärm";
$lang["originalfileoftype"]="Originalfil &ndash; ?"; # ? will be replaced, e.g. "Original PDF File"
$lang["fileoftype"]="?-fil"; # ? will be replaced, e.g. "MP4 File"
$lang["log"]="Logg";
$lang["resourcedetails"]="Egenskaper för material";
$lang["offlineresource"]="Frånkopplat material";
$lang["action-request"]="Begär";
$lang["request"]="Begäran";
$lang["searchforsimilarresources"]="Sök efter liknande material";
$lang["clicktoviewasresultset"]="Klicka här om du vill visa dessa material som ett resultatsätt";
$lang["searchnomatches"]="Sökningen matchade inga material.";
$lang["try"]="Prova";
$lang["tryselectingallcountries"]="Prova att välja <i>Alla länder</i> i landsfältet eller";
$lang["tryselectinganyyear"]="Prova att välja <i>Alla år</i> i årsfältet eller";
$lang["tryselectinganymonth"]="Prova att välja <i>Alla månader</i> i månadsfältet eller";
$lang["trybeinglessspecific"]="Prova att vara mindre specifik genom";
$lang["enteringfewerkeywords"]="att ange färre sökord."; # Suffixed to any of the above 4 items e.g. "Try being less specific by entering fewer search keywords"
$lang["match"]="träff";
$lang["matches"]="träffar";
$lang["inthearchive"]="i arkivet";
$lang["nomatchesinthearchive"]="Inga träffar i arkivet";
$lang["savethissearchtocollection"]="Lägg till denna sökfråga i aktuell samling";
$lang["mustspecifyonekeyword"]="Du måste ange minst ett sökord.";
$lang["hasemailedyouaresource"]="har skickat ett material till dig per e-post."; # Suffixed to user name, e.g. Fred has e-mailed you a resource
$lang["clicktoviewresource"]="Klicka på länken nedan om du vill visa materialet.";
$lang["statuscode"]="Statuskod";

# Resource log - actions
$lang["resourcelog"]="Materiallogg";
$lang["log-u"]="Överförde fil";
$lang["log-c"]="Skapade material";
$lang["log-d"]="Hämtade fil";
$lang["log-e"]="Redigerade fält";
$lang["log-m"]="Redigerade fält (i grupp)";
$lang["log-E"]="Delade material via e-post till";//  + notes field
$lang["log-v"]="Visade material";
$lang["log-x"]="Tog bort material";
$lang["log-l"]="Loggade in"; # For user entries only.
$lang["log-t"]="Transformerade fil";
$lang["log-s"]="Ändrade status";
$lang["log-a"]="Ändrade åtkomst";
$lang["log-r"]="Återställde metadata";

$lang["backtoresourceview"]="Tillbaka: Visa material";

# Resource status
$lang["status"]="Status"; # Ska kunna inleda med "Materialet är" direkt följt av statusen.
$lang["status-2"]="Under registrering";
$lang["status-1"]="Väntande på granskning";
$lang["status0"]="Aktivt";
$lang["status1"]="Väntande på arkivering";
$lang["status2"]="Arkiverat";
$lang["status3"]="Borttaget";

# Charts
$lang["activity"]="Aktivitet";
$lang["summary"]="&ndash; sammanfattning";
$lang["mostinaday"]="Störst antal på en dag";
$lang["totalfortheyear"]="Totalt antal hittills i år";
$lang["totalforthemonth"]="Totalt antal under innevarande månad";
$lang["dailyaverage"]="Dagligt genomsnittligt antal för denna period";
$lang["nodata"]="Inga uppgifter för denna period.";
$lang["max"]="Max"; # i.e. maximum
$lang["statisticsfor"]="Statistik för"; # e.g. Statistics for 2007
$lang["printallforyear"]="Skriv ut all statistik för året";

# Log in / user account
$lang["nopassword"]="Klicka här om du vill ansöka om ett användarkonto";
$lang["forgottenpassword"]="Klicka här om du har glömt ditt lösenord";
$lang["keepmeloggedin"]="Håll mig inloggad på den här datorn";
$lang["columnheader-username"]=$lang["username"]="Användarnamn";
$lang["password"]="Lösenord";
$lang["login"]="Logga in";
$lang["loginincorrect"]="Det angivna användarnamnet eller lösenordet är fel.<br/><br/>Klicka på länken ovan<br/>om du vill begära ett nytt lösenord.";
$lang["accountexpired"]=">Användarkontots utgångsdatum har passerats. Kontakta systemets administratör.";
$lang["useralreadyexists"]="Ett användarkonto med samma e-postadress eller användarnamn existerar redan, ändringarna har inte sparats";
$lang["useremailalreadyexists"]="Ett användarkonto med samma e-postadress existerar redan.";
$lang["ticktoemail"]="Skicka användarnamnet och ett nytt lösenord till denna användare";
$lang["ticktodelete"]="Om du vill ta bort denna användare markerar du kryssrutan och klickar på <b>Spara</b>";
$lang["edituser"]="Redigera användare";
$lang["columnheader-full_name"]=$lang["fullname"]="Fullständigt namn";
$lang["email"]="E-post";
$lang["columnheader-e-mail_address"]=$lang["emailaddress"]="E-postadress";
$lang["suggest"]="Föreslå";
$lang["accountexpiresoptional"]="Användarkontot går ut<br/>(ej obligatoriskt)";
$lang["lastactive"]="Senast aktiv";
$lang["lastbrowser"]="Senaste använd webbläsare";
$lang["searchusers"]="Sök användare";
$lang["createuserwithusername"]="Skapa användare med användarnamn";
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
$lang["noresourcesrequired"]="Mängd material som krävs för den färdiga produkten";
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
$lang["copyexistingresources"]="Kopiera materialen i en befintlig samling till denna research";
$lang["deletethisrequest"]="Om du vill ta bort denna begäran markerar du kryssrutan och klickar på <b>Spara</b>";
$lang["requestedby"]="Inskickad av";
$lang["requesteditems"]="Begärt material";
$lang["assignedtoteammember"]="Tilldelad gruppmedlem";
$lang["typecollectionid"]="(ange samlingsnumret nedan)";
$lang["researchid"]="Researchförfrågenr";
$lang["assignedto"]="Tilldelad";
$lang["createresearchforuser"]="Skapa researchförfrågan för användare";
$lang["searchresearchrequests"]="Sök researchförfrågan";
$lang["requestasuser"]="Förfråga som användare";
$lang["haspostedresearchrequest"]="har postat en researchförfrågan"; # username is suffixed to this
$lang["newresearchrequestwaiting"]="Ny researchförfrågan väntar";
$lang["researchrequestassignedmessage"]="Din researchförfrågan har tilldelats en medlem i teamet. När vi har slutfört researchen kommer du att få ett e-postmeddelande med en länk till de material som vi rekommenderar.";
$lang["researchrequestassigned"]="Researchförfrågan är tilldelad";
$lang["researchrequestcompletemessage"]="Din researchförfrågan är besvarad och materialet har lagts till i Mina samlingar.";
$lang["researchrequestcomplete"]="Besvarad researchförfrågan";


# Misc / global
$lang["selectgroupuser"]="Välj grupp/användare…";
$lang["select"]="Välj…";
$lang["add"]="Lägg till";
$lang["create"]="Skapa";
$lang["treeobjecttype-group"]=$lang["group"]="Grupp";
$lang["confirmaddgroup"]="Vill du lägga till alla medlemmar i den här gruppen?";
$lang["backtoteamhome"]="Tillbaka: Administration, första sidan";
$lang["columnheader-resource_id"]=$lang["resourceid"]="Materialnr";
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
$lang["yourpassword"]="Lösenord";
$lang["newpassword"]="Nytt lösenord";
$lang["newpasswordretype"]="Nytt lösenord (repetera)";
$lang["passwordnotvalid"]="Detta är inte ett giltigt lösenord";
$lang["passwordnotmatch"]="Det upprepade lösenordet matchar inte lösenordet";
$lang["wrongpassword"]="Lösenordet är fel, försök igen";
$lang["action-view"]="Visa";
$lang["action-preview"]="Förhandsgranska";
$lang["action-viewmatchingresources"]="Visa matchande material";
$lang["action-expand"]="Expandera";
$lang["action-select"]="Välj";
$lang["action-download"]="Hämta";
$lang["action-email"]="E-posta";
$lang["action-edit"]="Redigera";
$lang["action-delete"]="Ta bort";
$lang["action-deletecollection"]="Ta bort samling";
$lang["action-revertmetadata"]="Återställ metadata";
$lang["confirm-revertmetadata"]="Vill du återextrahera den ursprungliga metadatan ur den här filen? Om du väljer att fortsätta simuleras en ny överföring av filen och därmed förloras all ändrad metadata.";
$lang["action-remove"]="Avlägsna";
$lang["complete"]="Slutförd";
$lang["backtohome"]="Tillbaka: Startsida";
$lang["backtohelphome"]="Tillbaka: Hjälp och tips, första sidan";
$lang["backtosearch"]="Tillbaka: Sökresultat";
$lang["backtoview"]="Tillbaka: Visa material";
$lang["backtoeditresource"]="Tillbaka: Redigera material";
$lang["backtouser"]="Tillbaka: Välkommen till ResourceSpace";
$lang["termsandconditions"]="Användningsvillkor";
$lang["iaccept"]="Jag accepterar";
$lang["contributedby"]="Tillagt av";
$lang["format"]="Format";
$lang["notavailableshort"]="&ndash;";
$lang["allmonths"]="Alla månader";
$lang["allgroups"]="Alla grupper";
$lang["status-ok"]="Okej";
$lang["status-fail"]="MISSLYCKADES";
$lang["status-warning"]="VARNING";
$lang["status-notinstalled"]="Ej installerad";
$lang["status-never"]="Aldrig";
$lang["softwareversion"]="?-version"; # E.g. "PHP version"
$lang["softwarebuild"]="?-bygge"; # E.g. "ResourceSpace Build"
$lang["softwarenotfound"]="Programmet ’?’ hittades inte."; # ? will be replaced.
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
$lang["stat-addpubliccollection"]="Tillägg av gemensamma samlingar"; # Det ska vara möjligt att sätta "Antal" framför alla aktiviteter.
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
$lang["stat-processedresearchrequest"]="Besvarade researchförfrågningar";
$lang["stat-resourcedownload"]="Hämtningar av material";
$lang["stat-resourceedit"]="Redigeringar av material";
$lang["stat-resourceupload"]="Överföringar av material";
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
$lang["savesearchitemstocollection"]="Lägg till hittade poster i aktuell samling";
$lang["removeallresourcesfromcollection"]="Om du vill avlägsna alla material från denna samling markerar du kryssrutan och klickar på <b>Spara</b>";
$lang["deleteallresourcesfromcollection"]="Om du vill ta bort själva materialen som ingår i denna samling markerar du kryssrutan och klickar på <b>Spara</b>";
$lang["deleteallsure"]="Vill du ta bort de här materialen? Om du väljer att fortsätta tas själva materialen bort, de  avlägsnas inte bara från denna samling.";
$lang["batchdonotaddcollection"]="(Lägg inte till i någon samling)";
$lang["collectionsthemes"]="Relaterade teman och gemensamma samlingar";
$lang["recent"]="Nyaste";
$lang["batchcopyfrom"]="Kopiera metadata från material med nummer";
$lang["copy"]="Kopiera";
$lang["zipall"]="Zippa alla";
$lang["downloadzip"]="Hämta samlingen som en zip-fil";
$lang["downloadsize"]="Hämtningsstorlek";
$lang["tagging"]="Taggning";
$lang["speedtagging"]="Snabbtaggning";
$lang["existingkeywords"]="Befintliga nyckelord:";
$lang["extrakeywords"]="Extra nyckelord";
$lang["leaderboard"]="Rankningstabell";
$lang["confirmeditall"]="Vill du spara? Om du väljer att fortsätta skrivs existerande värden över, för de valda fälten, i alla material i den aktuella samlingen.";
$lang["confirmsubmitall"]="Vill du sända alla material till granskning? Om du väljer att fortsätta skrivs existerande värden över, för de valda fälten, i alla material i den aktuella samlingen, därefter sänds materialen till granskning.";
$lang["confirmunsubmitall"]="Vill du dra tillbaka alla material från granskningsprocessen? Om du väljer att fortsätta skrivs existerande värden över, för de valda fälten, i alla material i den aktuella samlingen, därefter dras materialen tillbaka från granskningsprocessen.";
$lang["confirmpublishall"]="Vill du publicera materialen? Om du väljer att fortsätta skrivs existerande värden över, för de valda fälten, i alla material i den aktuella samlingen, därefter publiceras materialen för gemensam visning.";
$lang["confirmunpublishall"]="Vill du dra tillbaka publiceringen? Om du väljer att fortsätta skrivs existerande värden över, för de valda fälten, i alla material i den aktuella samlingen, därefter dras materialen tillbaka från gemensam visning.";
$lang["collectiondeleteconfirm"]="Vill du ta bort den här samlingen?";
$lang["hidden"]="(Dolt)";
$lang["requestnewpassword"]="Begär nytt lösenord";

# New for 1.4
$lang["reorderresources"]="Klicka och dra om du vill ändra ordningen på materialen inom samlingen";
$lang["addorviewcomments"]="Skriv eller visa kommentarer";
$lang["collectioncomments"]="Samlingskommentarer";
$lang["collectioncommentsinfo"]="Skriv en kommentar till materialet. Kommentaren gäller bara i den här samlingen.";
$lang["comment"]="Kommentar";
$lang["warningexpired"]="Materialets utgångsdatum har passerats";
$lang["warningexpiredtext"]="Varning! Materialets utgångsdatum har passerats. Du måste klicka på länken nedan för att aktivera hämtning av material.";
$lang["warningexpiredok"]="&gt; Aktivera hämtning av material";
$lang["userrequestcomment"]="Meddelande";
$lang["addresourcebatchbrowser"]="Lägg till material i grupp &ndash; i webbläsare (Flash)";
$lang["addresourcebatchbrowserjava"]="Lägg till material i grupp &ndash; i webbläsare (Java &ndash; rekommenderas)";

$lang["addresourcebatchftp"]="Lägg till material i grupp &ndash; överför från ftp-server";
$lang["replaceresourcebatch"]="Ersätt material i grupp";
$lang["editmode"]="Redigeringsläge";
$lang["replacealltext"]="Ersätt befintlig text med texten nedan";
$lang["findandreplace"]="Sök och ersätt";
$lang["appendtext"]="Lägg till texten nedan";
$lang["removetext"]="Ange text att ta bort från befintlig text";
$lang["find"]="Sök";
$lang["andreplacewith"]="… och ersätt med …";
$lang["relateallresources"]="Skapa relationer mellan alla material i den här samlingen";

# New for 1.5
$lang["columns"]="Kolumner";
$lang["contactsheetconfiguration"]="Inställningar för kontaktkopia";
$lang["thumbnails"]="Miniatyrbilder";
$lang["contactsheetintrotext"]="Välj arkstorlek och antal kolumner för kontaktkopian.";
$lang["size"]="Storlek";
$lang["orientation"]="Orientering";
$lang["requiredfield"]="Obligatoriskt fält";
$lang["requiredfields"]="Alla obligatoriska fält är inte ifyllda. Gå igenom formuläret och prova sedan igen.";
$lang["viewduplicates"]="Visa dubbletter av material";
$lang["duplicateresources"]="Dubbletter av material";
$lang["userlog"]="Användarlogg";
$lang["ipaddressrestriction"]="Begränsa tillåtna ip-adresser<br/>(ej obligatoriskt)";
$lang["wildcardpermittedeg"]="Jokertecken är tillåtna, t.ex.";

# New for 1.6
$lang["collection_download_original"]="Originalfil";
$lang["newflag"]="NY!";
$lang["link"]="Länk";
$lang["uploadpreview"]="Överför en bild som ny förhandsgranskning";
$lang["starttypingusername"]="Användarnamn, namn eller gruppnamn…";
$lang["requestfeedback"]="Begär respons<br/>(svar sänds per e-post)";
$lang["sendfeedback"]="Skicka respons";
$lang["feedbacknocomments"]="Du har inte gett någon respons på materialen i samlingen.<br/>Klicka på pratbubblorna bredvid materialen när du vill ge respons.";
$lang["collectionfeedback"]="Respons på samlingen";
$lang["collectionfeedbackemail"]="Du har fått följande respons:";
$lang["feedbacksent"]="Din respons har skickats.";
$lang["newarchiveresource"]="Lägg till ett arkiverat material";
$lang["nocategoriesselected"]="Inga kategorier valda";
$lang["showhidetree"]="Visa/dölj träd";
$lang["clearall"]="Rensa alla";
$lang["clearcategoriesareyousure"]="Vill du rensa alla valda alternativ?";
$lang["share"]="Dela";
$lang["sharecollection"]="Dela samling";
$lang["sharecollection-name"]="Dela samling &ndash; %collectionname"; # %collectionname will be replaced, e.g. Share Collection - Cars
$lang["generateurl"]="Generera webbadress";
$lang["generateurlinternal"]="Nedanstående webbadress fungerar bara för inloggade användare.";
$lang["generateurlexternal"]="Nedanstående webbadress fungerar för alla och kräver inte inloggning.";
$lang["archive"]="Arkiv";
$lang["collectionviewhover"]="Visa materialen som ingår i samlingen.";
$lang["collectioncontacthover"]="Skapa en kontaktkopia med de material som ingår i samlingen.";
$lang["original"]="Original";

$lang["password_not_min_length"]="Lösenordet måste innehålla minst ? tecken";
$lang["password_not_min_alpha"]="Lösenordet måste innehålla minst ? bokstäver (a&ndash;z, A&ndash;Z)";
$lang["password_not_min_uppercase"]="Lösenordet måste innehålla minst ? versaler (A&ndash;Z)";
$lang["password_not_min_numeric"]="Lösenordet måste innehålla minst ? siffror (0&ndash;9)";
$lang["password_not_min_special"]="Lösenordet måste innehålla minst ? icke alfanumeriska tecken (!@$%&amp;* etc.)";
$lang["password_matches_existing"]="Det föreslagna lösenordet är samma som det befintliga lösenordet";
$lang["password_expired"]="Ditt lösenords utgångsdatum har passerats och du måste nu ange ett nytt lösenord";
$lang["max_login_attempts_exceeded"]="Du har överskridit det maximalt tillåtna antalet inloggningsförsök. Du måste nu vänta ? minuter innan du kan försöka logga in igen.";

$lang["newlogindetails"]="Du hittar dina nya inloggningsuppgifter nedan."; # For new password mail
$lang["youraccountdetails"]="Dina kontouppgifter"; # Subject of mail sent to user on user details save

$lang["copyfromcollection"]="Kopiera från samling";
$lang["donotcopycollection"]="Kopiera inte från en samling";

$lang["resourcesincollection"]="material i den här samlingen"; # E.g. 3 resources in this collection
$lang["removefromcurrentcollection"]="Avlägsna från aktuell samling";
$lang["showtranslations"]="+ Visa översättningar";
$lang["hidetranslations"]="&minus; Dölj översättningar";
$lang["archivedresource"]="Arkiverat material";

$lang["managerelatedkeywords"]="Hantera relaterade nyckelord";
$lang["keyword"]="Nyckelord";
$lang["relatedkeywords"]="Relaterade nyckelord";
$lang["matchingrelatedkeywords"]="Matchande relaterade nyckelord";
$lang["newkeywordrelationship"]="Skapa ny relation för nyckelord";
$lang["searchkeyword"]="Sök nyckelord";

$lang["exportdata"]="Exportera data";
$lang["exporttype"]="Exportformat";

$lang["managealternativefiles"]="Hantera alternativa filer";
$lang["managealternativefilestitle"]="Hantera alternativa filer";
$lang["alternativefiles"]="Alternativa filer";
$lang["filetype"]="Filtyp";
$lang["filedeleteconfirm"]="Vill du ta bort den här filen?";
$lang["addalternativefile"]="Lägg till alternativ fil";
$lang["editalternativefile"]="Redigera alternativ fil";
$lang["description"]="Beskrivning";
$lang["notuploaded"]="Inte överförda";
$lang["uploadreplacementfile"]="Överför ersättningsfil";
$lang["backtomanagealternativefiles"]="Tillbaka: Hantera alternativa filer";


$lang["resourceistranscoding"]="Detta material kodas just nu om";
$lang["cantdeletewhiletranscoding"]="Du kan inte ta bort material medan det kodas om";

$lang["maxcollectionthumbsreached"]="Det finns för många material i den här samlingen för att kunna visa miniatyrbilder. Miniatyrbilderna kommer nu därför att döljas.";

$lang["ratethisresource"]="Vilket betyg ger du det här materialet?";
$lang["ratingthankyou"]="Tack för ditt betyg!";
$lang["ratings"]="betyg";
$lang["rating_lowercase"]="betyg";
$lang["cannotemailpassword"]="Du kan inte skicka användarna deras existerande lösenord, eftersom de är lagrade i krypterad form.<br/><br/>Klicka på <b>Föreslå</b> om du vill generera ett nytt lösenord, som sedan kan skickas per e-post.";

$lang["userrequestnotification1"]="Användarformuläret har fyllts i med följande uppgifter:";
$lang["userrequestnotification2"]="Om du godtar denna ansökan, kan du gå till webbadressen nedan och skapa ett användarkonto för denna användaren.";
$lang["ipaddress"]="Ip-adress";
$lang["userresourcessubmitted"]="Följande användarbidrag har lagts fram för granskning:";
$lang["userresourcesunsubmitted"]="Följande användarbidrag har dragits tillbaka och kräver inte längre granskning:";
$lang["viewalluserpending"]="Visa alla användarbidrag som väntar på granskning:";

# New for 1.7
$lang["installationcheck"]="Installationskontroll";
$lang["managefieldoptions"]="Hantera fältalternativ";
$lang["matchingresourcesheading"]="Matchande material";
$lang["backtofieldlist"]="Tillbaka: Fältlistan";
$lang["rename"]="Byt namn";
$lang["showalllanguages"]="Visa alla språk";
$lang["hidealllanguages"]="Dölj alla språk";
$lang["clicktologinasthisuser"]="Klicka här om du vill logga in som denna användare";
$lang["addkeyword"]="Lägg till nyckelord";
$lang["selectedresources"]="Valda material";

$lang["internalusersharing"]="Dela med en intern användare";
$lang["externalusersharing"]="Dela med en extern användare";
$lang["accesskey"]="Åtkomstnyckel";
$lang["sharedby"]="Delad av";
$lang["sharedwith"]="Delad med";
$lang["lastupdated"]="Senast uppdaterad";
$lang["lastused"]="Senast använd";
$lang["noattachedusers"]="Ingen tillknuten användare.";
$lang["confirmdeleteaccess"]="Vill du ta bort den här åtkomstnyckeln? Om du väljer att fortsätta kommer användare som har fått tillgång till samlingen med hjälp av denna nyckel inte längre att kunna komma åt samlingen.";
$lang["noexternalsharing"]="Ingen extern delning.";
$lang["sharedcollectionaddwarning"]="Varning! Denna samling delas med externa användare. Det material som du har lagt till har därmed gjorts tillgängligt för dessa användare. Klicka på Dela samling om du vill hantera den externa åtkomsten för denna samling.";
$lang["addresourcebatchlocalfolder"]="Lägg till material i grupp &ndash; överfrån från lokal mapp";

# Setup Script
$lang["setup-alreadyconfigured"]="Installationen av ResourceSpace är redan konfigurerad. Om du vill göra om konfigurationen tar du bort <pre>’include/config.php’</pre> och pekar webbläsaren till den här sidan igen.";
$lang["setup-successheader"]="Gratulerar!";
$lang["setup-successdetails"]="Den grundläggande delen av installationen av ResourceSpace är nu slutförd. Gå igenom filen ’include/default.config.php’ om du vill se fler konfigurationsmöjligheter.";
$lang["setup-successnextsteps"]="Nästa steg:";
$lang["setup-successremovewrite"]="Du bör nu avlägsna skrivrättigheten till katalogen ’include/’.";
$lang["setup-visitwiki"]='Besök <a href="http://wiki.resourcespace.org/index.php/Main_Page">ResourceSpace Documentation Wiki</a> (engelskspråkig wiki) om du vill hitta mer information om hur du anpassar din installation.';
$lang["setup-checkconfigwrite"]="Skrivrättighet till konfigurationskatalog:";
$lang["setup-checkstoragewrite"]="Skrivrättighet till lagringskatalog:";
$lang["setup-welcome"]="Välkommen till ResourceSpace";
$lang["setup-introtext"]="Tack för att du väljer ResourceSpace. Detta konfigurationsskript hjälper dig att installera ResourceSpace. Det behöver endast göras en gång.";
$lang["setup-checkerrors"]="Fel upptäcktes i systemkonfigurationen.<br/>Åtgärda dessa fel, och peka sedan webbläsaren till den här sidan igen när du vill fortsätta.";
$lang["setup-errorheader"]="Fel upptäcktes i konfigurationen. Se detaljerade felmeddelanden nedan.";
$lang["setup-warnheader"]="Några av inställningarna genererade varningsmeddelanden, se nedan. Det betyder inte nödvändigtvis att det är ett problem med konfigurationen.";
$lang["setup-basicsettings"]="Grundläggande inställningar";
$lang["setup-basicsettingsdetails"]="Här gör du de grundläggande inställningarna för installationen av ResourceSpace.<br><strong>*</strong>Obligatoriskt fält";
$lang["setup-dbaseconfig"]="Databaskonfiguration";
$lang["setup-mysqlerror"]="Det finns ett fel i MySQL-inställningarna:";
$lang["setup-mysqlerrorversion"]="MySQL-versionen måste vara 5 eller senare.";
$lang["setup-mysqlerrorserver"]="Kunde inte ansluta till servern.";
$lang["setup-mysqlerrorlogin"]="Inloggningen misslyckades. Kontrollera användarnamn och lösenord.";
$lang["setup-mysqlerrordbase"]="Kunde inte att ansluta till databasen.";
$lang["setup-mysqlerrorperns"]="Kunde inte skapa tabeller. Kontrollera databasanvändarens behörigheter.";
$lang["setup-mysqltestfailed"]="Testet misslyckades (kunde inte verifiera MySQL).";
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
$lang["setup-scramblekey"]="Skramlingsnyckel:";
$lang["setup-apiscramblekey"]="Skramlingsnyckel för api:et:";
$lang["setup-paths"]="Sökvägar";
$lang["setup-pathsdetail"]="Ange sökväg, utan efterföljande snedstreck, för varje program. Lämna sökvägen tom för att inaktivera ett program. En del sökvägar har upptäckts och fyllts i automatiskt.";
$lang["setup-applicationname"]="Programmets namn:";
$lang["setup-basicsettingsfooter"]="Obs! Alla <strong>obligatoriska</strong> inställningar är samlade på den här sidan. Om du inte är intresserad av att kontrollera de avancerade inställningarna kan du klicka på <b>Starta installation</b>.";
$lang["setup-if_mysqlserver"]="Ip-adressen eller <abbr title=\"Fullständigt kvalificerat domännamn\">fqdn</abbr> för MySQL-servern. Ange ’localhost’ om MySQL är installerad på samma server som webbservern.";
$lang["setup-if_mysqlusername"]="Användarnamnet som ska användas för att ansluta till MySQL-servern. Användaren måste ha rättighet att skapa tabeller i databasen.";
$lang["setup-if_mysqlpassword"]="Lösenordet för MySQL-användaren.";
$lang["setup-if_mysqldb"]="Namnet på MySQL-databasen som ResourceSpace ska använda. Databasen måste redan existera.";
$lang["setup-if_mysqlbinpath"]="Sökvägen till MySQL-klientens programfiler &ndash; t.ex. mysqldump. Obs! Denna uppgift behövs bara om du avser att använda exportverktyg.";
$lang["setup-if_baseurl"]="Baswebbadressen för den här installationen. Obs! Utan efterföljande snedstreck.";
$lang["setup-if_emailfrom"]="Adressen som e-post från ResourceSpace tycks komma ifrån.";
$lang["setup-if_emailnotify"]="E-postadress som materialbegäranden, kontoansökningar och researchförfrågningar ska skickas till.";
$lang["setup-if_spiderpassword"]="Spindellösenordet är en obligatorisk uppgift.";
$lang["setup-if_scramblekey"]="Ange en sträng att använda som skramlingssnyckel, om du vill aktivera skramling av materialsökvägar. Om det här är en installation nåbar från Internet rekommenderas detta starkt. Om du lämnar fältet tomt inaktiverar du skramling. Innehållet i fältet har redan slumpats fram för dig, men du kan ändra det så att det matchar en befintlig installation. Strängen ska vara svår att gissa &ndash; som ett lösenord.";
$lang["setup-if_apiscramblekey"]="Ange en sträng att använda som skramlingsnyckel för api:et. Om du planerar att använda api:er rekommenderas detta starkt.";
$lang["setup-if_applicationname"]="Namnet på implementationen/installationen (ex. MittFöretags mediaarkiv).";
$lang["setup-err_mysqlbinpath"]="Det går inte att verifiera sökvägen. Lämna tomt för att inaktivera.";
$lang["setup-err_baseurl"]="Baswebbadressen är ett obligatoriskt fält.";
$lang["setup-err_baseurlverify"]="Baswebbadressen verkar inte vara korrekt (kunde inte läsa in license.txt).";
$lang["setup-err_spiderpassword"]="Lösenord som krävs för ’spider.php’. VIKTIGT! Slumpa fram ett lösenord för varje ny installation. Allt material kommer att kunna läsas av den som kan detta lösenord. Innehållet i fältet har redan slumpats fram för dig, men du kan ändra det så att det matchar en befintlig installation.";
$lang["setup-err_scramblekey"]="Om installationen är nåbar från Internet rekommenderas skramling starkt.";
$lang["setup-err_apiscramblekey"]="Om installationen är nåbar från Internet rekommenderas skramling starkt.";
$lang["setup-err_path"]="Det går inte att verifiera sökvägen för";
$lang["setup-emailerr"]="Ogiltig e-postadress.";
$lang["setup-rs_initial_configuration"]="ResourceSpace: Inledande konfiguration";
$lang["setup-include_not_writable"]="Skrivrättighet till katalogen ’/include’ saknas. Krävs bara under installationen.";
$lang["setup-override_location_in_advanced"]="Sökvägen kan åsidosättas i Avancerade inställningar.";
$lang["setup-advancedsettings"]="Avancerade inställningar";
$lang["setup-binpath"]="Sökväg till %bin"; #%bin will be replaced, e.g. "Imagemagick Path"
$lang["setup-begin_installation"]="Starta installation";
$lang["setup-generaloptions"]="Allmänna alternativ";
$lang["setup-allow_password_change"]="Tillåt byte av lösenord";
$lang["setup-enable_remote_apis"]="Tillåt api-anrop utifrån";
$lang["setup-if_allowpasswordchange"]="Tillåt användarna att byta sina egna lösenord.";
$lang["setup-if_enableremoteapis"]="Tillåt fjärråtkomst till api-tilläggen.";
$lang["setup-allow_account_requests"]="Tillåt ansökningar om användarkonton";
$lang["setup-display_research_request"]="Visa funktionen researchfrågan";
$lang["setup-if_displayresearchrequest"]="Tillåt användarna att skicka in researchförfrågningar via ett formulär, som sedan skickas per e-post.";
$lang["setup-themes_as_home"]="Använd sidan Teman som startsida";
$lang["setup-remote_storage_locations"]="Platser för fjärrlagring";
$lang["setup-use_remote_storage"]="Använd fjärrlagring";
$lang["setup-if_useremotestorage"]="Markera den här kryssrutan om du vill konfigurera fjärrlagring för ResourceSpace. (För att placera lagringskatalogen på en annan server.)";
$lang["setup-storage_directory"]="Lagringskatalog";
$lang["setup-if_storagedirectory"]="Var materialfilerna lagras. Kan vara en absolut sökväg (/var/www/blah/blah) eller relativ till installationen. Obs! Inget efterföljande snedstreck.";
$lang["setup-storage_url"]="Lagringskatalogens webbadress";
$lang["setup-if_storageurl"]="Var lagringskatalogen finns tillgänglig. Kan vara absolut (http://filer.exempel.se) eller relativ till installationen. Obs! Inget efterföljande snedstreck.";
$lang["setup-ftp_settings"]="Ftp-inställningar";
$lang["setup-if_ftpserver"]="Krävs endast om du planerar att hämta material från en ftp-server.";
$lang["setup-login_to"]="Logga in i";
$lang["setup-configuration_file_output"]="Utmatning till konfigurationsfilen";

# Collection log - actions
$lang["collectionlog"]="Samlingslogg";
$lang["collectionlog-r"]="Avlägsnade material";
$lang["collectionlog-R"]="Avlägsnade alla material";
$lang["collectionlog-D"]="Tog bort alla material";
$lang["collectionlog-d"]="Tog bort material"; // this shows external deletion of any resources related to the collection.
$lang["collectionlog-a"]="Lade till material";
$lang["collectionlog-c"]="Lade till material (kopierade)";
$lang["collectionlog-m"]="Lade till materialkommentar";
$lang["collectionlog-*"]="Lade till materialbetyg";
$lang["collectionlog-S"]="Delade samlingen med "; //  + notes field
$lang["collectionlog-E"]="Skickade samlingen per e-post till ";//  + notes field
$lang["collectionlog-s"]="Delade material med ";//  + notes field
$lang["collectionlog-T"]="Slutade dela samlingen med ";//  + notes field
$lang["collectionlog-t"]="Återtog åtkomst till material för ";//  + notes field
$lang["collectionlog-X"]="Tog bort samlingen";
$lang["collectionlog-b"]="Transformerade i grupp";

$lang["viewuncollectedresources"]="Visa material som inte ingår i samlingar";

# Collection requesting
$lang["requestcollection"]="Begär samling";

# Metadata report
$lang["metadata-report"]="Detaljerad metadata";

# Video Playlist
$lang["videoplaylist"]="Videospellista";

$lang["restrictedsharecollection"]="Delning är inte tillåten eftersom du har begränsad åtkomst till minst ett material i den här samlingen.";

$lang["collection"]="Samling";
$lang["idecline"]="Jag accepterar inte"; # For terms and conditions

$lang["mycollection_notpublic"]="Samlingen ’Min samling’ kan inte göras till en gemensam samling eller ett tema. Skapa en ny samling för dessa ändamål.";

$lang["resourcemetadata"]="Metadata för material";

$lang["selectgenerateurlexternal"]="Om du vill skapa en extern webbadress som fungerar för användare utan användarkonto, anger du först den åtkomstnivå som du finner lämplig.";

$lang["externalselectresourceaccess"]="Om du delar material med en användare utan användarkonto väljer du en åtkomstnivå som du finner lämplig";

$lang["externalselectresourceexpires"]="Om du delar material med en användare utan användarkonto väljer du ett utgångsdatum för den genererade webbadressen";

$lang["externalshareexpired"]="Delningens utgångsdatum har passerats och därför är delningen inte längre tillgänglig.";

$lang["expires"]="Utgår";
$lang["never"]="Aldrig";

$lang["approved"]="Godkänd";
$lang["notapproved"]="Ej godkänd";

$lang["userrequestnotification3"]="Klicka på länken nedan om du vill se över detaljerna och sedan eventuellt godkänna användarkontot.";

$lang["ticktoapproveuser"]="Markera kryssrutan om du vill godkänna användaren och aktivera kontot";

$lang["managerequestsorders"]="Hantera begäranden/beställningar";
$lang["editrequestorder"]="Redigera begäran/beställning";
$lang["requestorderid"]="Begäransnr/beställningsnr";
$lang["viewrequesturl"]="Klicka på länken nedan om du vill visa denna begäran:";
$lang["requestreason"]="Anledning till begäran";

$lang["resourcerequeststatus0"]="Väntande";
$lang["resourcerequeststatus1"]="Bifallen";
$lang["resourcerequeststatus2"]="Avslagen";

$lang["ppi"]="ppi"; # (Pixels Per Inch - used on the resource download options list).

$lang["useasthemethumbnail"]="Vill du använda det här materialet som miniatyrbild för temakategorin?";
$lang["sessionexpired"]="Du har blivit utloggad eftersom du var inaktiv i mer än 30&nbsp;minuter. Logga in igen om du vill fortsätta.";

$lang["resourcenotinresults"]="Detta material ingår inte längre i sökresultatet, navigering nästa/föregående är därför inte möjlig."; #!!!
$lang["publishstatus"]="Spara med publiceringsstatus:";
$lang["addnewcontent"]="Nytt innehåll (sida, namn)";
$lang["hitcount"]="Antal träffar";
$lang["downloads"]="Hämtningar";

$lang["addremove"]="";

##  Translations for standard log entries
$lang["all_users"]="alla användare";
$lang["new_resource"]="nytt material";

$lang["invalidextension_mustbe"]="Ogiltigt filnamnstillägg, måste vara";
$lang["allowedextensions"]="Giltiga filnamnstillägg";

$lang["alternativebatchupload"]="Överför alternativa filer i grupp (Java)";

$lang["confirmdeletefieldoption"]="Vill du ta bort det här fältalternativet?";

$lang["cannotshareemptycollection"]="Denna samling är tom och kan inte delas.";

$lang["requestall"]="Begär alla";
$lang["requesttype-email_only"]=$lang["resourcerequesttype0"]="E-post";
$lang["requesttype-managed"]=$lang["resourcerequesttype1"]="Hanterad";
$lang["requesttype-payment_-_immediate"]=$lang["resourcerequesttype2"]="Direktbetalning";
$lang["requesttype-payment_-_invoice"]=$lang["resourcerequesttype3"]="Fakturabetalning";

$lang["requestapprovedmail"]="Din begäran har blivit bifallen. Klicka på länken nedan om du vill visa och hämta de begärda materialen.";
$lang["requestdeclinedmail"]="Din begäran har blivit avslagen för materialen i samlingen nedan.";

$lang["resourceexpirymail"]="För följande material har utgångsdatumet passerats:";
$lang["resourceexpiry"]="Materialets utgångsdatum";

$lang["requestapprovedexpires"]="Din åtkomst till dessa material går ut den";

$lang["pleasewaitsmall"]="(vänta …)";
$lang["removethisfilter"]="(avlägsna detta filter)";

$lang["no_exif"]="Extrahera inte exif-, IPTC- eller xmp-metadata vid denna överföring";
$lang["difference"]="Skillnad";
$lang["viewdeletedresources"]="Visa borttagna material";
$lang["finaldeletion"]="Detta material är redan markerat som borttaget. Om du fortsätter tas material bort permanent.";

$lang["nocookies"]="En kaka kunde inte sparas korrekt. Kontrollera att din webbläsare tillåter kakor.";

$lang["selectedresourceslightroom"]="Valda material (lista kompatibel med Adobe Lightroom):";

# Plugins Manager
$lang['plugins-noneinstalled'] = "Inga tillägg aktiverade.";
$lang['plugins-noneavailable'] = "Inga tillägg tillgängliga.";
$lang['plugins-availableheader'] = 'Tillgängliga tillägg';
$lang['plugins-installedheader'] = 'Aktiverade tillägg';
$lang['plugins-author'] = 'Upphovsman';
$lang['plugins-version'] = 'Version';
$lang['plugins-instversion'] = 'Installerad version';
$lang['plugins-uploadheader'] = 'Överför tillägg';
$lang['plugins-uploadtext'] = 'Rsp-fil att överföra';
$lang['plugins-deactivate'] = 'Inaktivera';
$lang['plugins-moreinfo'] = 'Mer information';
$lang['plugins-activate'] = 'Aktivera';
$lang['plugins-purge'] = 'Rensa ut konfiguration';
$lang['plugins-rejmultpath'] = 'Arkivet innehåller flera sökvägar. (Säkerhetsrisk)';
$lang['plugins-rejrootpath'] = 'Arkivet innehåller absoluta sökvägar. (Säkerhetsrisk)';
$lang['plugins-rejparentpath'] = 'Arkivet innehåller överliggande sökvägar (../). (Säkerhetsrisk)';
$lang['plugins-rejmetadata'] = 'Arkivets informationsfil hittades inte.';
$lang['plugins-rejarchprob'] = 'Ett problem uppstod under uppackningen:';
$lang['plugins-rejfileprob'] = 'Tillägget måste vara en rsp-fil.';
$lang['plugins-rejremedy'] = "Om du litar på detta tillägg kan du installera det manuellt genom att packa upp arkivet direkt i katalogen ’plugins’.";
$lang['plugins-uploadsuccess'] = 'Överföringen av tillägget slutfördes korrekt';
$lang['plugins-headertext'] = 'Tillägg kan ge nya funktioner och ny stil till ResourceSpace.';
$lang['plugins-legacyinst'] = 'Aktiverat via ’config.php’';
$lang['plugins-uploadbutton'] = 'Överför tillägg';

#Location Data
$lang['location-title'] = 'Platsinformation';
$lang['location-add'] = 'Lägg till plats';
$lang['location-edit'] = 'Redigera plats';
$lang['location-details'] = "Med <b>Dragläge</b> växlar du mellan att positionera nålen och att panorera. Använd zoomkontrollerna för att zooma in och ut. Klicka på <b>Spara</b> för att spara nålposition och zoomnivå.";
$lang['location-noneselected']="Ingen plats vald";
$lang['location'] = 'Plats';
$lang['mapzoom'] = 'Kartzoomning';

$lang["publiccollections"]="Gemensamma samlingar";
$lang["viewmygroupsonly"]="Visa bara mina grupper";
$lang["usemetadatatemplate"]="Använd metadatamall";
$lang["undometadatatemplate"]="(ångra val av metadatamall)";

$lang["accountemailalreadyexists"]="Ett användarkonto med samma e-postadress existerar redan";

$lang["backtothemes"]="Tillbaka: Teman";
$lang["downloadreport"]="Hämta rapport";

#Bug Report Page
$lang['reportbug']="Förbered en buggrapport till utvecklarna av ResourceSpace";
$lang['reportbug-detail']="Följande information har sammanställts till buggrapporten. Du kommer i nästa steg att kunna redigera all data innan du skickar in rapporten.";
$lang['reportbug-login']="&gt; Obs! Klicka här för att logga in i bugghanteringssystemet <i>innan</i> du klickar på <b>Förbered buggrapport</b>";
$lang['reportbug-preparebutton']="Förbered buggrapport";

$lang["enterantispamcode"]="<strong>Inloggningstest</strong> <sup>*</sup><br />Fyll i koden:";

$lang["groupaccess"]="Gruppåtkomst";
$lang["plugin-groupsallaccess"]="Detta tillägg är aktiverat för alla grupper";
$lang["plugin-groupsspecific"]="Detta tillägg är aktiverat endast för markerade grupper";


$lang["associatedcollections"]="Samlingar som detta material ingår i";
$lang["emailfromuser"]="Skicka e-postmeddelandet från ";
$lang["emailfromsystem"]="Avmarkera kryssrutan om du vill att e-postmeddelandet ska skickas från systemets e-postadress: ";



$lang["previewpage"]="Förhandsgranska sida";
$lang["nodownloads"]="Inga hämtningar";
$lang["uncollectedresources"]="Material som inte ingår i samlingar";
$lang["nowritewillbeattempted"]="Exiftool kommer inte att försöka skriva metadata.";
$lang["notallfileformatsarewritable"]="Exiftool kan dock inte skriva i alla filtyper.";
$lang["filetypenotsupported"]="Filtypen %filetype stöds inte";
$lang["exiftoolprocessingdisabledforfiletype"]="Exiftool är inaktiverat för filtypen %filetype"; # %filetype will be replaced, e.g. Exiftool processing disabled for file type JPG
$lang["nometadatareport"]="Ingen metadatarapport";
$lang["metadatawritewillbeattempted"]="Exiftool kommer att försöka skriva nedanstående metadata.";
$lang["embeddedvalue"]="Inbäddat värde";
$lang["exiftooltag"]="Exiftool-fält";
$lang["error"]="Fel";
$lang["exiftoolnotfound"]="Exiftool kunde inte hittas";

$lang["indicateusage"]="Beskriv hur du planerar att använda detta material.";
$lang["usage"]="Användning";
$lang["indicateusagemedium"]="Användningsmedia";
$lang["usageincorrect"]="Du måste ange hur du planerar att använda materialet samt välja ett media";

$lang["savesearchassmartcollection"]="Spara sökning som en smart samling";
$lang["smartcollection"]="Smart samling";


$lang["uploadertryflash"]="Om du har problem med den här överföraren, prova <strong>Flash-överföraren</strong>";
$lang["uploadertryjava"]="Om du har problem med den här överföraren eller om du <strong>överför stora filer</strong>, prova <strong>Java-överföraren</strong>";
$lang["getjava"]="Besök Javas webbplats om du vill säkerställa att du har den senaste Java-versionen installerad";
$lang["getflash"]="Besök Flash-spelarens webbplats om du vill säkerställa att du har den senaste Flash-spelaren installerad";

$lang["all"]="Alla";
$lang["backtoresults"]="Tillbaka: Sökresultat";

$lang["preview_all"]="Förhandsgranska alla";

$lang["usagehistory"]="Användningshistorik";
$lang["usagebreakdown"]="Detaljerad användningshistorik";
$lang["usagetotal"]="Totalt hämtat";
$lang["usagetotalno"]="Totalt antal hämtningar";
$lang["ok"]="OK";

$lang["random"]="Slumpmässig";
$lang["userratingstatsforresource"]="Användarbetyg för material";
$lang["average"]="Medel";
$lang["popupblocked"]="Poppuppfönstret har blockerats av webbläsaren.";
$lang["closethiswindow"]="Stäng fönstret";

$lang["requestaddedtocollection"]="Detta material har lagts till i den aktuella samlingen. Du kan begära alla poster i samlingen genom att klicka på Begär alla i panelen Mina samlingar i nederkant av skärmen.";

# E-commerce text
$lang["buynow"]="Köp nu";
$lang["yourbasket"]="Din varukorg";
$lang["addtobasket"]="Lägg i varukorg";
$lang["yourbasketisempty"]="Din varukorg är tom.";
$lang["yourbasketcontains-1"]="Din varukorg innehåller 1 artikel.";
$lang["yourbasketcontains-2"]="Din varukorg innehåller %qty artiklar."; # %qty will be replaced, e.g. Your basket contains 3 items.
$lang["buy"]="Köp";
$lang["buyitemaddedtocollection"]="Detta material har lagts i din varukorg. Du kan köpa alla artiklar i din varukorg genom att klicka på Köp nu.";
$lang["buynowintro"]="Välj de storlekar du önskar.";
$lang["nodownloadsavailable"]="Det finns inga hämtningar tillgängliga för detta material.";
$lang["proceedtocheckout"]="Gå till kassan";
$lang["totalprice"]="Totalsumma";
$lang["price"]="Pris";
$lang["waitingforpaymentauthorisation"]="Vi har ännu inte fått betalningsuppdraget. Vänta en kort stund och klicka sedan på <b>Läs om</b>.";
$lang["reload"]="Läs om";
$lang["downloadpurchaseitems"]="Hämta köpta artiklar";
$lang["downloadpurchaseitemsnow"]="Använd länkarna nedan för att hämta dina köpta artiklar direkt.<br><br>Lämna inte den här sidan innan du har hämtat alla artiklar.";
$lang["alternatetype"]="Alternativ typ";


$lang["subcategories"]="Underkategorier";
$lang["back"]="Tillbaka";

$lang["pleasewait"]="Vänta …";

$lang["autorotate"]="Rotera bilder automatiskt";

# Reports
# Report names (for the default reports)
$lang["report-keywords_used_in_resource_edits"]="Nyckelord använda i material";
$lang["report-keywords_used_in_searches"]="Nyckelord använda i sökningar";
$lang["report-resource_download_summary"]="Materialhämtningar &ndash; sammanställning";
$lang["report-resource_views"]="Materialvisningar";
$lang["report-resources_sent_via_e-mail"]="Material skickade per e-post";
$lang["report-resources_added_to_collection"]="Material tillagda i samling";
$lang["report-resources_created"]="Material skapade";
$lang["report-resources_with_zero_downloads"]="Material utan hämtningar";
$lang["report-resources_with_zero_views"]="Material utan visningar";
$lang["report-resource_downloads_by_group"]="Materialhämtningar per grupp";
$lang["report-resource_download_detail"]="Materialhämtningar &ndash; detaljerad lista";
$lang["report-user_details_including_group_allocation"]="Användaruppgifter inklusive grupptillhörighet";

#Column headers (for the default reports)
$lang["columnheader-keyword"]="Nyckelord";
$lang["columnheader-entered_count"]="Antal förekomster";
$lang["columnheader-searches"]="Sökningar";
$lang["columnheader-date_and_time"]="Datum/tid";
$lang["columnheader-downloaded_by_user"]="Hämtat av användare";
$lang["columnheader-user_group"]="Grupp";
$lang["columnheader-resource_title"]="Materialtitel";
$lang["columnheader-title"]="Titel";
$lang["columnheader-downloads"]="Hämtningar";
$lang["columnheader-group_name"]="Gruppnamn";
$lang["columnheader-resource_downloads"]="Hämtningar";
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
$lang["newemailreportcreated"]="Ett nytt periodiskt återkommande e-postutskick har skapats. Om du vill avbryta utskicken klickar du på webblänken som finns nederst i varje meddelande.";
$lang["unsubscribereport"]="Om du vill avbryta prenumerationen på den här rapporten klickar du på webblänken nedan:";
$lang["unsubscribed"]="Prenumerationen avbruten";
$lang["youhaveunsubscribedreport"]="Du har avbrutit prenumerationen på det periodiskt återkommande e-postutskicket med rapporter.";
$lang["sendingreportto"]="Skickar rapporten till";
$lang["reportempty"]="Ingen matchande data hittades för vald rapport och period.";

$lang["purchaseonaccount"]="Debitera konto";
$lang["areyousurepayaccount"]="Vill du debitera ditt konto med detta köp?";
$lang["accountholderpayment"]="Kontobetalning";
$lang["subtotal"]="Delsumma";
$lang["discountsapplied"]="Avdragna rabatter";
$lang["log-p"]="Köpte material";
$lang["viauser"]="via användare";
$lang["close"]="Stäng";

# Installation Check
$lang["repeatinstallationcheck"]="Repetera installationskontroll";
$lang["shouldbeversion"]="Ska vara version ? eller senare"; # E.g. "should be 4.4 or greater"
$lang["phpinivalue"]="Värde i php.ini för ’?’"; # E.g. "PHP.INI value for 'memory_limit'"
$lang["writeaccesstofilestore"]="Skrivrättighet till katalogen ’" . $storagedir ."’ finns?";
$lang["nowriteaccesstofilestore"]="Skrivrättighet till katalogen ’" . $storagedir ."’ saknas.";
$lang["writeaccesstohomeanim"]="Skrivrättighet till katalogen ’" . $homeanim_folder ."’ finns?";
$lang["nowriteaccesstohomeanim"]="Skrivrättighet till katalogen ’" . $homeanim_folder ."’  saknas. Skrivrättighet måste finnas för att tillägget <b>transform</b> ska kunna infoga bilder i startsidans bildspel.";
$lang["blockedbrowsingoffilestore"]="Åtkomsten till katalogen ’filestore’ är blockerad för webbläsare?";
$lang["noblockedbrowsingoffilestore"]="Mappen ’filestore’ är inte blockerad för webbläsare. Avlägsna ’Indexes’ från ’Options’ i Apache.";
$lang["executionofconvertfailed"]="Exekveringen misslyckades &ndash; ett oväntat svar gavs när kommandot ’convert’ exekverades. Svaret var: ”?”.<br>I Windows och IIS&nbsp;6 måste åtkomst ges för kommandon i kommandotolken. Se installationsinstruktionerna i wikin."; # ? will be replaced.
$lang["exif_extension"]="Exif-utökning";
$lang["lastscheduledtaskexection"]="Senaste körning av schemalagda aktiviteter (dagar)";
$lang["executecronphp"]="Sökningar efter liknande material kommer inte att fungera som de ska och schemalagda e-postrapporter kommer inte att skickas. Se till att <a href='../batch/cron.php'>’batch/cron.php’</a> körs åtminstone en gång per dag som ett cron-jobb eller liknande.";
$lang["shouldbeormore"]="Bör vara ? eller mer."; # E.g. should be 200M or greater

$lang["generateexternalurl"]="Generera extern webbadress";

$lang["starsminsearch"]="Antal stjärnor (minimum)";
$lang["anynumberofstars"]="Valfritt antal stjärnor";

$lang["noupload"]="Ingen överföring";

# System Setup
# System Setup Tree Nodes (for the default setup tree)
$lang["treenode-root"]="Rot";
$lang["treenode-group_management"]="Grupphanteraren";
$lang["treenode-new_group"]="Ny grupp";
$lang["treenode-new_subgroup"]="Ny undergrupp";
$lang["treenode-resource_types_and_fields"]="Materialtyper/-fält";
$lang["treenode-new_resource_type"]="Ny materialtyp";
$lang["treenode-new_field"]="Nytt fält";
$lang["treenode-reports"]="Rapporter";
$lang["treenode-new_report"]="Ny rapport";
$lang["treenode-downloads_and_preview_sizes"]="Storlekar för hämtning/förhandsgranskning";
$lang["treenode-new_download_and_preview_size"]="Ny storlek";
$lang["treenode-database_statistics"]="Databasstatistik";
$lang["treenode-permissions_search"]="Behörighetssökning";
$lang["treenode-no_name"]="Namnlös";

$lang["treeobjecttype-preview_size"]="Storlek";

$lang["permissions"]="Behörigheter";

# System Setup File Editor
$lang["configdefault-title"]="(Kopiera och klistra in inställningarna härifrån.)";
$lang["config-title"]="Var <i>mycket</i> noga med att undvika syntaxfel. Om du skapar en fil med ett syntaxfel kan systemet bli obrukbart och felet kan då inte korrigeras inifrån ResourceSpace!";

# System Setup Properties Pane
$lang["file_too_large"]="Filen är för stor";
$lang["field_updated"]="Fältet uppdaterat.";
$lang["zoom"]="Förstoring";
$lang["deletion_instruction"]="Lämna tomt och klicka på <b>Spara</b> om du vill ta bort denna fil";
$lang["upload_file"]="Överför fil";
$lang["item_deleted"]="Posten borttagen";
$lang["viewing_version_created_by"]="Visar versionen skapad av";
$lang["on_date"]="den";
$lang["launchpermissionsmanager"]="Starta Behörighetshanteraren";
$lang["confirm-deletion"]="Vill du ta bort denna post?";

# Permissions Manager
$lang["permissionsmanager"]="Behörighetshanteraren";
$lang["backtogroupmanagement"]="Tillbaka: Grupphanteraren";
$lang["searching_and_access"]="Sökning/åtkomst";
$lang["metadatafields"]="Metadatafält";
$lang["resource_creation_and_management"]="Skapande/hantering av material";
$lang["themes_and_collections"]="Teman/samlingar";
$lang["administration"]="Administration";
$lang["other"]="Övrigt";
$lang["custompermissions"]="Anpassade behörigheter";
$lang["searchcapability"]="Kan söka efter material";
$lang["access_to_restricted_and_confidential_resources"]="Kan se konfidentiella material, kan hämta material med ’begränsad’ åtkomst<br>(normalt endast för administratörer)";
$lang["restrict_access_to_all_available_resources"]="Tillåts åtkomst endast till tillgängliga material";
$lang["can_make_resource_requests"]="Kan begära material";
$lang["show_watermarked_previews_and_thumbnails"]="Ser förhandsgranskningar/miniatyrbilder vattenstämplade";
$lang["can_see_all_fields"]="Kan se alla fält";
$lang["can_see_field"]="Kan se fältet";
$lang["can_edit_all_fields"]="Kan redigera alla fält<br>(för redigeringsbara material)";
$lang["can_edit_field"]="Kan redigera fältet";
$lang["can_see_resource_type"]="Kan se material av typen";
$lang["restricted_access_only_to_resource_type"]="Tillåts åtkomst endast till material av typen";
$lang["edit_access_to_workflow_state"]="Kan redigera material med statusen";
$lang["can_create_resources_and_upload_files-admins"]="Kan skapa material och överföra filer<br>(administratörer; materialen får statusen ’Aktivt’)";
$lang["can_create_resources_and_upload_files-general_users"]="Kan skapa material och överföra filer<br>(vanliga användare; materialen får statusen ’Väntande på granskning’";
$lang["can_delete_resources"]="Kan ta bort material<br>(till vilka användaren har skrivrättighet)";
$lang["can_manage_archive_resources"]="Kan hantera arkivmaterial";
$lang["can_tag_resources_using_speed_tagging"]="Kan tagga material med Snabbtaggning<br>(måste vara aktiverat i ’config.php’)";
$lang["enable_bottom_collection_bar"]="Aktivera panelen <b>Mina samlingar</b> i nederkant av skärmen";
$lang["can_publish_collections_as_themes"]="Kan publicera samlingar som teman";
$lang["can_see_all_theme_categories"]="Kan se alla temakategorier";
$lang["can_see_theme_category"]="Kan se temakategori";
$lang["display_only_resources_within_accessible_themes"]="Kan endast söka efter material som hör till teman som användaren har åtkomst till";
$lang["can_access_team_centre"]="Kan nå sidan Administration";
$lang["can_manage_research_requests"]="Kan hantera researchförfrågningar";
$lang["can_manage_resource_requests"]="Kan hantera begäranden av material";
$lang["can_manage_content"]="Kan hantera webbplatsinnehåll";
$lang["can_bulk-mail_users"]="Kan göra massutskick";
$lang["can_manage_users"]="Kan hantera användare";
$lang["can_manage_keywords"]="Kan hantera nyckelord";
$lang["can_access_system_setup"]="Kan nå sidan Systemkonfiguration";
$lang["can_change_own_password"]="Kan ändra lösenordet till det egna användarkontot";
$lang["can_manage_users_in_children_groups"]="Kan hantera användare endast i grupper som är underordnade användarens egen grupp";
$lang["can_email_resources_to_own_and_children_and_parent_groups"]="Kan skicka material per e-post endast till användare i användarens egen grupp och till användare i grupper som är underordnade eller direkt överordnad användarens grupp";

$lang["nodownloadcollection"]="Du har inte behörighet att hämta material från den här samlingen.";

$lang["progress"]="Förlopp";
$lang["ticktodeletethisresearchrequest"]="Om du vill ta bort denna förfrågan markerar du kryssrutan och klickar på <b>Spara</b>";

# SWFUpload
$lang["queued_too_many_files"]="Du har försökt att köa för många filer.";
$lang["creatingthumbnail"]="Skapar miniatyrbild …";
$lang["uploading"]="Överför …";
$lang["thumbnailcreated"]="En miniatyrbild är skapad.";
$lang["done"]="Klar.";
$lang["stopped"]="Stoppad."; 

$lang["latlong"]="Latitud, longitud";
$lang["geographicsearch"]="Geografisk sökning";

$lang["geographicsearch_help"]="Klicka och dra när du vill välja ett sökområde.";

$lang["purge"]="Rensa ut";
$lang["purgeuserstitle"]="Rensa ut användare";
$lang["purgeusers"]="Rensa ut användare";
$lang["purgeuserscommand"]="Ta bort användarkonton som inte har varit aktiva de senaste % månaderna, men som skapades före den perioden.";
$lang["purgeusersconfirm"]="Vill du ta bort % användarkonton?";
$lang["pleaseenteravalidnumber"]="Ange ett korrekt nummer";
$lang["purgeusersnousers"]="Det finns inga användare att rensa ut.";

$lang["editallresourcetypewarning"]="Varning! Om du ändrar materialtypen tas eventuell redan lagrad typspecifik metadata bort för materialen.";

$lang["geodragmode"]="<b>Dragläge</b>";
$lang["geodragmodearea"]="Placera nål";
$lang["geodragmodepan"]="Panorera";

$lang["substituted_original"] = "ersattes av original";
$lang["use_original_if_size"] = "Använd original om vald storlek är otillgänglig";

$lang["originals-available-0"] = "tillgängliga"; # 0 (originals) available
$lang["originals-available-1"] = "tillgängligt"; # 1 (original) available
$lang["originals-available-2"] = "tillgängliga"; # 2+ (originals) available

$lang["inch-short"] = "tum";
$lang["centimetre-short"] = "cm";
$lang["megapixel-short"]="Mpx";
$lang["at-resolution"] = "i"; # E.g. 5.9 in x 4.4 in @ 144 PPI

$lang["deletedresource"] = "Borttaget material";
$lang["deletedresources"] = "Borttagna material";
$lang["action-delete_permanently"] = "Ta bort permanent";

$lang["horizontal"] = "Horisontellt";
$lang["vertical"] = "Vertikalt";

$lang["cc-emailaddress"] = "Kopia till %emailaddress"; # %emailaddress will be replaced, e.g. CC [your email address]

$lang["sort"] = "Sortera";
$lang["sortcollection"] = "Sortera samling";
$lang["emptycollection"] = "Avlägsna materialen";
$lang["deleteresources"] = "Ta bort materialen";
$lang["emptycollectionareyousure"]="Vill du avlägsna alla material från den här samlingen?";

$lang["error-cannoteditemptycollection"]="Du kan inte redigera en tom samling.";
$lang["error-permissiondenied"]="Tillåtelse nekades.";
$lang["error-collectionnotfound"]="Samlingen hittades inte.";

$lang["header-upload-subtitle"] = "Steg %number: %subtitle"; # %number, %subtitle will be replaced, e.g. Step 1: Specify Default Content For New Resources
$lang["local_upload_path"] = "Lokal överföringsmapp";
$lang["ftp_upload_path"] = "Ftp-mapp";
$lang["foldercontent"] = "Mappinnehåll";
$lang["intro-local_upload"] = "Välj en eller flera filer från den lokala överföringsmappen och klicka sedan på <b>Överför</b>. När filerna är överförda kan de tas bort från överföringsmappen.";
$lang["intro-ftp_upload"] = "Välj en eller flera filer från ftp-mappen och klicka sedan på <b>Överför</b>.";
$lang["intro-java_upload"] = "Klicka på <b>Bläddra</b> för att välja en eller flera filer och klicka sedan på <b>Överför</b>.";
$lang["intro-swf_upload"] = "Klicka på <b>Överför</b> för att välja en eller flera filer som sedan direkt överförs. Håll ner en skift-tangent för att välja flera filer samtidigt.";
$lang["intro-single_upload"] = "Klicka på <b>Bläddra</b> för att välja en fil och klicka sedan på <b>Överför</b>.";
$lang["intro-batch_edit"] = "Ange förvalda inställningar för överföring och förvald metadata för materialen du kommer att överföra.";

$lang["collections-1"] = "(<strong>1</strong> samling)";
$lang["collections-2"] = "(<strong>%number</strong> samlingar)"; # %number will be replaced, e.g. 3 Collections
$lang["total-collections-0"] = "<strong>Totalt: 0</strong> samlingar";
$lang["total-collections-1"] = "<strong>Totalt: 1</strong> samling";
$lang["total-collections-2"] = "<strong>Totalt: %number</strong> samlingar"; # %number will be replaced, e.g. Total: 5 Collections
$lang["owned_by_you-0"] = "(<strong>0</strong> ägda av dig)";
$lang["owned_by_you-1"] = "(<strong>1</strong> ägd av dig)";
$lang["owned_by_you-2"] = "(<strong>%mynumber</strong> ägda av dig)"; # %mynumber will be replaced, e.g. (2 owned by you)

$lang["listresources"]= "Material:";
$lang["action-log"]="Visa logg";
 
$lang["saveuserlist"]="Spara den här listan";
$lang["deleteuserlist"]="Ta bort den här listan";
$lang["typeauserlistname"]="Ange ett användarlistenamn…";
$lang["loadasaveduserlist"]="Läs in en sparad användarlista";
 
$lang["searchbypage"]="Sök sida";
$lang["searchbyname"]="Sök namn";
$lang["searchbytext"]="Sök text";
$lang["saveandreturntolist"]="Spara och återvänd till lista";
$lang["backtomanagecontent"]="Tillbaka: Hantera webbplatsens innehåll";
$lang["editcontent"]="Redigera innehåll";
 
$lang["confirmcollectiondownload"]="Vänta medan zip-arkivet skapas. Detta kan ta en stund och tiden är beroende av den totala storleken av de ingående materialen.";
 
$lang["starttypingkeyword"]="Ange nyckelord…";
$lang["createnewentryfor"]="Skapa nytt nyckelord: ";
$lang["confirmcreatenewentryfor"]="Vill du skapa en ny post i nyckelordslistan för ’%%’?";
 
$lang["editresourcepreviews"]="Redigera materialens förhandsgranskningar";
 
$lang["can_assign_resource_requests"]="Kan tilldela andra användare begäranden av material";
$lang["can_be_assigned_resource_requests"]="Kan bli tilldelad begäranden av material (kan även se tilldelade begäranden på sidan Hantera begäranden/beställningar)";
 
$lang["declinereason"]="Skäl för avslag";
$lang["requestnotassignedtoyou"]="Denna begäran är inte längre tilldelad dig. Den är nu tilldelad användare %.";
$lang["requestassignedtoyou"]="Materialbegäran tilldelad dig";
$lang["requestassignedtoyoumail"]="En materialbegäran har tilldelats dig. Klicka på länken nedan om du vill bifalla eller avslå den.";
 
$lang["manageresources-overquota"]="Materialhantering inaktiverad &ndash; du har överskridit din diskutrymmestilldelning";
$lang["searchitemsdiskusage"]="Beräkna diskutrymmet som används av resultatet";
$lang["matchingresourceslabel"]="Matchande material";
 
$lang["saving"]="Sparar …";
$lang["saved"]="Sparat";
 
$lang["resourceids"]="Materialnr";
 
$lang["warningrequestapprovalfield"]="Varning! Beträffande materialnr % &ndash; notera följande innan ett eventuellt bifallande!";

$lang["yyyy-mm-dd"]="ÅÅÅÅ-MM-DD";

$lang["resources-with-requeststatus0-0"]="(ingen väntande)"; # 0 Pending
$lang["resources-with-requeststatus0-1"]="(1 väntande)"; # 1 Pending
$lang["resources-with-requeststatus0-2"]="(%number väntande)"; # %number will be replaced, e.g. 3 Pending
$lang["researches-with-requeststatus0-0"]="(alla tilldelade)"; # 0 Unassigned
$lang["researches-with-requeststatus0-1"]="(1 ej tilldelad)"; # 1 Unassigned
$lang["researches-with-requeststatus0-2"]="(%number ej tilldelade)"; # %number will be replaced, e.g. 3 Unassigned
