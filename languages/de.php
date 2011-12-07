<?php
# German
# Language File for ResourceSpace
# UTF-8 encoded
# -------
# Note: when translating to a new language, preserve the original case if possible.
#
# Updated by [Name] [Date] for version [svn version], [comments]
# Updated by Henrik Frizén 20110124 for version 2296, added missing $lang from the en.php (search for #$lang to find the untranslated strings).
# Updated by Stefan Wild 20110201 for version 2296, translated the missing $lang keys from the en.php that Henrik added.
# Updated by Henrik Frizén 20110222 for version 2390+, added missing $lang from the en.php (search for #$lang to find the untranslated strings).
# Updated by Stefan Wild 20110309 for version 2390+, translated the missing $lang keys from the en.php that Henrik added.
# Updated by Stefan Wild 20110427 for version 2652, translated the missing $lang keys from the en.php.
# Updated by Stefan Wild 20110730 for version 2852, translated the missing $lang keys from the en.php.
#
# User group names (for the default user groups)
$lang["usergroup-administrators"]="Administratoren";
$lang["usergroup-general_users"]="Allgemeine Benutzer";
$lang["usergroup-super_admin"]="Super Admin";
$lang["usergroup-archivists"]="Archiv";
$lang["usergroup-restricted_user_-_requests_emailed"]="Engeschränkte Benutzer - Anfragen per E-Mail";
$lang["usergroup-restricted_user_-_requests_managed"]="Engeschränkte Benutzer - Anfragen verwaltet";
$lang["usergroup-restricted_user_-_payment_immediate"]="Engeschränkte Benutzer - Bezahlung sofort";
$lang["usergroup-restricted_user_-_payment_invoice"]="Engeschränkte Benutzer - Bezahlung auf Rechnung";

# Resource type names (for the default resource types)
$lang["resourcetype-photo"]="Foto";
$lang["resourcetype-document"]="Dokument";
$lang["resourcetype-video"]="Video";
$lang["resourcetype-audio"]="Audio";
$lang["resourcetype-global_fields"]="Globale Felder";
$lang["resourcetype-archive_only"]="Nur Archiv";

# Image size names (for the default image sizes)
$lang["imagesize-thumbnail"]="Thumbnail";
$lang["imagesize-preview"]="Vorschau";
$lang["imagesize-screen"]="Bildschirm";
$lang["imagesize-low_resolution_print"]="Druck (niedrige Auflösung)";
$lang["imagesize-high_resolution_print"]="Druck (hohe Auflösung)";
$lang["imagesize-collection"]="Kollektion";

# Field titles (for the default fields)
$lang["fieldtitle-keywords"]="Stichworte";
$lang["fieldtitle-country"]="Land";
$lang["fieldtitle-title"]="Titel";
$lang["fieldtitle-story_extract"]=$lang["storyextract"]="Zusammenfassung";
$lang["fieldtitle-credit"]="Urheber";
$lang["fieldtitle-date"]=$lang["date"]="Datum";
$lang["fieldtitle-expiry_date"]="Ablaufdatum";
$lang["fieldtitle-caption"]="Beschriftung";
$lang["fieldtitle-notes"]="Anmerkungen";
$lang["fieldtitle-named_persons"]="Person(en)";
$lang["fieldtitle-camera_make_and_model"]="Kamera";
$lang["fieldtitle-original_filename"]="Original Dateiname";
$lang["fieldtitle-video_contents_list"]="Video Inhaltsliste";
$lang["fieldtitle-source"]="Quelle";
$lang["fieldtitle-website"]="Website";
$lang["fieldtitle-artist"]="Künstler";
$lang["fieldtitle-album"]="Album";
$lang["fieldtitle-track"]="Lied";
$lang["fieldtitle-year"]="Jahr";
$lang["fieldtitle-genre"]="Genre";
$lang["fieldtitle-duration"]="Dauer";
$lang["fieldtitle-channel_mode"]="Kanalmodus";
$lang["fieldtitle-sample_rate"]="Sample Rate";
$lang["fieldtitle-audio_bitrate"]="Audio Bitrate";
$lang["fieldtitle-frame_rate"]="Bildrate";
$lang["fieldtitle-video_bitrate"]="Video Bitrate";
$lang["fieldtitle-aspect_ratio"]="Seitenverhältnis";
$lang["fieldtitle-video_size"]="Videogröße";
$lang["fieldtitle-image_size"]="Bildgröße";
$lang["fieldtitle-extracted_text"]="Entnommener Text";
$lang["fieldtitle-file_size"]=$lang["filesize"]="Dateigröße";
$lang["fieldtitle-category"]="Kategorie";
$lang["fieldtitle-subject"]="Betreff";
$lang["fieldtitle-author"]="Autor";

# Field types
$lang["fieldtype-text_box_single_line"]="Textfeld (einzeilig)";
$lang["fieldtype-text_box_multi-line"]="Textfeld (mehrzeilig)";
$lang["fieldtype-text_box_large_multi-line"]="Textfeld (mehrzeilig, groß)";
$lang["fieldtype-check_box_list"]="Check box Liste";
$lang["fieldtype-drop_down_list"]="Dropdown Menü";
$lang["fieldtype-date_and_time"]="Datum / Uhrzeit";
$lang["fieldtype-expiry_date"]="Ablaufdatum";
$lang["fieldtype-category_tree"]="Kategoriebaum";

# Property labels (for the default properties)
$lang["documentation-permissions"]="Weitere Informationen über die Berechtigungen finden Sie in der <a href=../../documentation/permissions.txt target=_blank>Berechtigungen Hilfe-Datei</a>.";
$lang["property-reference"]="Referenz";
$lang["property-name"]="Name";
$lang["property-permissions"]="Berechtigungen";
$lang["information-permissions"]="HINWEIS: Globale Berechtigungen aus der config.php könnten außerdem in Kraft sein";
$lang["property-fixed_theme"]="Festes Theme";
$lang["property-parent"]="Übergeordneter Eintrag";
$lang["property-search_filter"]="Suchfilter";
$lang["property-edit_filter"]="Bearbeitungsfilter";
$lang["property-resource_defaults"]="Ressourcen Vorgaben";
$lang["property-override_config_options"]="Konfigurationsoptionen überschreiben";
$lang["property-email_welcome_message"]="Willkommens-E-Mail";
$lang["information-ip_address_restriction"]="Wildcards werden für IP-Adress-Einschränkungen unterstützt, z.B. 128.124.*";
$lang["property-ip_address_restriction"]="IP-Adress-Einschränkungen";
$lang["property-request_mode"]="Anfragemodus";
$lang["property-allow_registration_selection"]="In der Registrierungsauswahl anzeigen";

$lang["property-resource_type_id"]="Ressourcen-Typ ID";
$lang["information-allowed_extensions"]="Wenn gesetzt, können nur die angegebenen Dateierweiterungen hochgeladen werden, z.B. jpg,gif";
$lang["property-allowed_extensions"]="Erlaubte Dateierweiterungen";

$lang["property-field_id"]="Feld ID";
$lang["property-title"]="Titel";
$lang["property-resource_type"]="Ressourcen-Typ";
$lang["property-field_type"]="Feldtyp";
$lang["information-options"]="<br /><b>Bitte beachten Sie</b> - es wird empfohlen, die Funktion 'Feldoptionen verwalten' unter 'Ressourcen verwalten' in der Administration zu nutzen, da so bestehende Einträge beim Umbenennen automatisch übernommen werden.";
$lang["property-options"]="Optionen";
$lang["property-required"]="Pflichtfeld";
$lang["property-order_by"]="Sortieren nach";
$lang["property-index_this_field"]="Feld indizieren";
$lang["information-enable_partial_indexing"]="Partielle Indizierung der Stichworte (Präfix+Infix Indizierung) sollte sparsam eingesetzt werden, da es die Größe des Index deutlich erhöht. Weitere Details im Wiki.";
$lang["property-enable_partial_indexing"]="Partielle Indizierung aktivieren";
$lang["information-shorthand_name"]="Wichtig: Kurzname muss gesetzt sein, damit das Feld in der erweiterten Suche erscheint. Der Kurzname darf nur aus Kleinbuchstaben bestehen - keine Leerzeichen, Ziffern oder Sonderzeichen.";
$lang["property-shorthand_name"]="Kurzname";
$lang["property-display_field"]="Feld anzeigen";
$lang["property-enable_advanced_search"]="In erweiterter Suche aktivieren";
$lang["property-enable_simple_search"]="In einfacher Suche aktivieren";
$lang["property-use_for_find_similar_searching"]="Für ähnliche Suche benutzen";
$lang["property-iptc_equiv"]="IPTC Äquivalent";
$lang["property-display_template"]="Anzeigetemplate";
$lang["property-value_filter"]="Eingabefilter";
$lang["property-tab_name"]="Tab Name";
$lang["property-smart_theme_name"]="Smart-Theme Name";
$lang["property-exiftool_field"]="Exiftool Feld";
$lang["property-exiftool_filter"]="Exiftool Filter";
$lang["property-help_text"]="Hilfetext";
$lang["information-display_as_dropdown"]="Checkbox Listen und Dropdown Menüs: in der erweiterten Suche als Dropdown Menü anzeigen? (wird standardmäßig als Checkbox Liste dargestellt, um ODER Abfrage zu ermöglichen)";
$lang["property-display_as_dropdown"]="Als Dropdown darstellen";
$lang["property-external_user_access"]="Zugriff für externe Benutzer";
$lang["property-autocomplete_macro"]="Makro für Autovervollständigen";
$lang["property-hide_when_uploading"]="Beim Upload verstecken";

$lang["property-query"]="Abfrage";

$lang["information-id"]="Hinweis: 'ID' unten MUSS auf einen eindeutigen, dreistelligen Buchstabencode gesetzt sein";
$lang["property-id"]="ID";
$lang["property-width"]="Breite";
$lang["property-height"]="Höhe";
$lang["property-pad_to_size"]="Auf Größe auffüllen";
$lang["property-internal"]="Intern";
$lang["property-allow_preview"]="Vorschau erlauben";
$lang["property-allow_restricted_download"]="Download bei eingeschränktem Zugriff erlauben";

$lang["property-total_resources"]="Ressourcen gesamt";
$lang["property-total_keywords"]="Stichworte gesamt";
$lang["property-resource_keyword_relationships"]="Ressourcen / Stichworte Verknüpfungen";
$lang["property-total_collections"]="Kollektionen gesamt";
$lang["property-collection_resource_relationships"]="Kollektionen / Ressourcen Verknüpfungen";
$lang["property-total_users"]="Benutzer gesamt";


# Top navigation bar (also reused for page titles)
$lang["logout"]="Abmelden";
$lang["contactus"]="Kontakt";
# next line
$lang["home"]="Startseite";
$lang["searchresults"]="Suchresultate";
$lang["themes"]="Themen";
$lang["mycollections"]="Meine Kollektionen";
$lang["myrequests"]="Meine Anfragen";
$lang["collections"]="Kollektionen";
$lang["mycontributions"]="Meine Beiträge";
$lang["researchrequest"]="Suchanfrage";
$lang["helpandadvice"]="Hilfe & Unterstützung";
$lang["teamcentre"]="Administration";
# footer link
$lang["aboutus"]="Über uns";
$lang["interface"]="Darstellung";

# Search bar
$lang["simplesearch"]="Einfache Suche";
$lang["searchbutton"]="Suchen";
$lang["clearbutton"]="zurücksetzen";
$lang["bycountry"]="Nach Land";
$lang["bydate"]="Nach Datum";
$lang["anyyear"]="beliebiges Jahr";
$lang["anymonth"]="beliebiger Monat";
$lang["anyday"]="beliebiger Tag";
$lang["anycountry"]="beliebiges Land";
$lang["resultsdisplay"]="Resultate anzeigen";
$lang["xlthumbs"]="sehr groß";
$lang["largethumbs"]="groß";
$lang["smallthumbs"]="klein";
$lang["list"]="Liste";
$lang["perpage"]="pro Seite";

$lang["gotoadvancedsearch"]="zur erweiterten Suche";
$lang["viewnewmaterial"]="neue Einträge anzeigen";
$lang["researchrequestservice"]="Suchanfrage";

# Team Centre
$lang["manageresources"]="Ressourcen verwalten";
$lang["overquota"]="Speicherplatz erschöpft; es können keine weiteren Ressourcen hinzugefügt werden";
$lang["managearchiveresources"]="Archivierte Ressourcen verwalten";
$lang["managethemes"]="Themen verwalten";
$lang["manageresearchrequests"]="Suchanfragen verwalten";
$lang["manageusers"]="Benutzer verwalten";
$lang["managecontent"]="Inhalte verwalten";
$lang["viewstatistics"]="Statistiken ansehen";
$lang["viewreports"]="Berichte ansehen";
$lang["viewreport"]="Bericht ansehen";
$lang["treeobjecttype-report"]=$lang["report"]="Bericht";
$lang["sendbulkmail"]="Massenmail senden";
$lang["systemsetup"]="Systemeinstellungen";
$lang["usersonline"]="Benutzer, die zur Zeit online sind (Leerlaufzeit in Minuten)";
$lang["diskusage"]="Speicherplatzverbrauch";
$lang["available"]="gesamt";
$lang["used"]="verwendet";
$lang["free"]="verfügbar";
$lang["editresearch"]="Suchanfragen verwalten";
$lang["editproperties"]="Eigenschaften verwalten";
$lang["selectfiles"]="Dateien auswählen";
$lang["searchcontent"]="Inhalt durchsuchen";
$lang["ticktodeletehelp"]="Anwählen, um diesen Abschnitt zu löschen";
$lang["createnewhelp"]="Neuen Abschnitt erstellen";
$lang["searchcontenteg"]="(Seite, Name oder Text)";
$lang["copyresource"]="Ressource kopieren";
$lang["resourceidnotfound"]="Die Ressourcen-ID konnte nicht gefunden werden";
$lang["inclusive"]="(inklusive)";
$lang["pluginssetup"]="Plugins verwalten";
$lang["pluginmanager"]="Plugin Manager";
$lang["users"]="Benutzer";


# Team Centre - Bulk E-mails
$lang["emailrecipients"]="E-Mail Empfänger";
$lang["emailsubject"]="E-Mail Betreff";
$lang["emailtext"]="E-Mail Text";
$lang["emailhtml"]="HTML aktiviert - Text der E-Mail muss HTML-Formatierung nutzen";
$lang["send"]="Senden";
$lang["emailsent"]="E-Mail wurde gesendet.";
$lang["mustspecifyoneuser"]="Sie müssen mindestens einen Benutzer auswählen";
$lang["couldnotmatchusers"]="Keine passende Benutzer gefunden (oder Benutzer mehrfach angegeben)";

# Team Centre - User management
$lang["comments"]="Kommentare";

# Team Centre - Resource management
$lang["viewuserpending"]="Überprüfung noch nicht erledigt: durch Benutzer eingereichte Ressourcen anzeigen";
$lang["userpending"]="Eingereichte Ressourcen (Überprüfung noch nicht erledigt)";
$lang["viewuserpendingsubmission"]="Freischalten noch nicht erledigt: durch Benutzer eingereichte Ressourcen anzeigen";
$lang["userpendingsubmission"]="Eingereichte Ressourcen (Freischalten noch nicht erledigt)";
$lang["searcharchivedresources"]="Archivierte Ressourcen durchsuchen";
$lang["viewresourcespendingarchive"]="Archivierung noch nicht erledigt: Ressourcen anzeigen";
$lang["resourcespendingarchive"]="Ressourcen (Archivierung noch nicht erledigt)";
$lang["uploadresourcebatch"]="Ressourcen-Stapel hochladen";
$lang["uploadinprogress"]="Hochladen und Größenanpassung in Bearbeitung";
$lang["transferringfiles"]="Daten werden übertragen, bitte warten.";
$lang["donotmoveaway"]="WICHTIG: Bitte verlassen Sie diese Seite nicht bis das Hochladen abgeschlossen ist!";
$lang["pleaseselectfiles"]="Bitte wählen Sie eine oder mehrere Dateien aus.";
$lang["resizingimage"]="Bildgrößenanpassung";
$lang["uploaded"]="hochgeladen";
$lang["andresized"]="und in der Größe angepasst";
$lang["uploadfailedfor"]="Hochladen fehlgeschlagen für"; # E.g. upload failed for abc123.jpg
$lang["uploadcomplete"]="Hochladen abgeschlossen";
$lang["resourcesuploadedok"]="Ressourcen erfolgreich hochgeladen"; # E.g. 17 resources uploaded OK
$lang["failed"]="fehlgeschlagen";
$lang["clickviewnewmaterial"]="Klicken Sie 'Neues Material anzeigen' um hochgeladene Ressourcen anzuzeigen.";
$lang["specifyftpserver"]="Einrichtung des FTP-Servers";
$lang["ftpserver"]="FTP-Server";
$lang["ftpusername"]="FTP-Benutzername";
$lang["ftppassword"]="FTP-Password";
$lang["ftpfolder"]="FTP-Verzeichnis";
$lang["connect"]="Verbinden";
$lang["uselocalupload"]="ODER: Verwenden Sie das lokale 'upload'-Verzeichnis anstelle des FTP-Servers.";

# User contributions
$lang["contributenewresource"]="Neue Ressource einreichen";
$lang["viewcontributedps"]="Meine Beiträge anzeigen - Freischaltung noch nicht erledigt";
$lang["viewcontributedpr"]="Meine Beiträge anzeigen - Prüfung und Freischaltung durch Ressourcen-Team noch nicht erledigt";
$lang["viewcontributedsubittedl"]="Meine Beiträge anzeigen - freigeschalten bzw. online";
$lang["contributedps"]="Meine Beiträge - Freischaltung noch nicht erledigt";
$lang["contributedpr"]="Meine Beiträge - Prüfung und Freischaltung durch Ressourcen-Team noch nicht erledigt";
$lang["contributedsubittedl"]="Meine Beiträge - Live";

# Collections
$lang["editcollection"]="Kollektion bearbeiten";
$lang["editcollectionresources"]="Kollektionsvorschau bearbeiten";
$lang["access"]="Zugriff";
$lang["private"]="privat";
$lang["public"]="öffentlich";
$lang["attachedusers"]="zugeordnete Benutzer";
$lang["themecategory"]="Themen-Kategorie";
$lang["theme"]="Thema";
$lang["newcategoryname"]="ODER: Tragen sie eine neue Themen-Kategorie ein...";
$lang["allowothersaddremove"]="Anderen Benutzern das hinzufügen/entfernen von Ressourcen erlauben";
$lang["resetarchivestatus"]="Archivierungsstatus für alle Ressourcen einer Kollektion zurücksetzen";
$lang["editallresources"]="Alle Ressourcen in der Kollektion bearbeiten";
$lang["editresources"]="Ressourcen bearbeiten";
$lang["multieditnotallowed"]="Mehrfache Bearbeitung nicht erlaubt - die Ressourcen sind nicht vom selben Typ bzw. Status.";
$lang["emailcollection"]="Kollektion als E-Mail senden";
$lang["collectionname"]="Name der Kollektion";
$lang["collectionid"]="Kollektion (ID)";
$lang["collectionidprefix"]="Kol_ID";
$lang["emailtousers"]="E-Mail an Benutzer...";
$lang["removecollectionareyousure"]="Möchten Sie diese Kollektion aus Ihrer Liste löschen?";
$lang["managemycollections"]="'Meine Kollektionen' verwalten";
$lang["createnewcollection"]="Neue Kollektion erstellen";
$lang["findpubliccollection"]="Öffentliche Kollektionen finden";
$lang["searchpubliccollections"]="Öffentliche Kollektionen suchen";
$lang["addtomycollections"]="zu 'Meine Kollektionen' hinzufügen";
$lang["action-addtocollection"]="Zur Kollektion hinzufügen";
$lang["action-removefromcollection"]="Aus Kollektion entfernen";
$lang["addtocollection"]="Zur Kollektion hinzufügen";
$lang["cantmodifycollection"]="Sie können diese Kollektion nicht bearbeiten.";
$lang["currentcollection"]="Aktuelle Kollektion";
$lang["viewcollection"]="Kollektion anzeigen";
$lang["viewall"]="Alle anzeigen";
$lang["action-editall"]="Alle bearbeiten";
$lang["hidethumbnails"]="Vorschaubilder ausblenden";
$lang["showthumbnails"]="Vorschaubilder einblenden";
$lang["contactsheet"]="Kontaktabzug";
$lang["mycollection"]="Meine Kollektion";
$lang["editresearchrequests"]="Suchanfragen bearbeiten";
$lang["research"]="Recherche";
$lang["savedsearch"]="Gespeicherte Suche";
$lang["mustspecifyoneusername"]="Bitte geben Sie mindestens einen Benutzernamen an";
$lang["couldnotmatchallusernames"]="Es konnten nicht alle passenden Benutzer gefunden werden";
$lang["emailcollectionmessage"]="hat Ihnen eine Kollektion an Ressourcen von $applicationname gesendet, welche auf der Seite 'Meine Kollektionen' zu finden ist."; # suffixed to user name e.g. "Fred has e-mailed you a collection.."
$lang["emailcollectionmessageexternal"]="hat Ihnen über $applicationname eine Kollektion von Ressourcen gesendet."; # suffixed to user name e.g. "Fred has e-mailed you a collection.."
$lang["clicklinkviewcollection"]="Klicken Sie auf den untenstehenden Link um die Kollektion anzuzeigen.";
$lang["zippedcollectiontextfile"]="Textdatei mit Kollektions-/Ressourcendaten einfügen.";
$lang["copycollectionremoveall"]="Alle Ressourcen vor dem Kopieren entfernen";
$lang["purgeanddelete"]="Bereinigen und löschen";
$lang["purgecollectionareyousure"]="Sind Sie sicher, dass Sie diese Kollektion entfernen und alle enthaltenen Ressourcen löschen wollen?";
$lang["collectionsdeleteempty"]="Leere Kollektionen löschen";
$lang["collectionsdeleteemptyareyousure"]="Sind Sie sicher dass Sie alle Ihre leeren Kollektionen löschen wollen?";
$lang["collectionsnothemeselected"]="Bitte einen Themennamen auswählen oder eingeben.";
$lang["downloaded"]="Heruntergeladen";
$lang["contents"]="Inhalte";
$lang["forthispackage"]="für dieses Paket";
$lang["didnotinclude"]="Enthielt nicht";
$lang["selectcollection"]="Kollektion auswählen";
$lang["total"]="Gesamt";
$lang["ownedbyyou"]="von Ihnen erstellt";

# Resource create / edit / view
$lang["createnewresource"]="Neue Ressource erstellen";
$lang["treeobjecttype-resource_type"]=$lang["resourcetype"]="Ressourcen-Typ";
$lang["resourcetypes"]="Ressourcen-Typen";
$lang["deleteresource"]="Ressource löschen";
$lang["downloadresource"]="Ressource herunterladen";
$lang["rightclicktodownload"]="Klicken Sie die rechte Maustaste und wählen Sie 'Speichern unter...' um den Datei-Download zu starten...";
$lang["downloadinprogress"]="Download in Bearbeitung";
$lang["editmultipleresources"]="Mehrere Ressourcen bearbeiten";
$lang["editresource"]="Ressource bearbeiten";
$lang["resources_selected-1"]="1 Ressource ausgewählt"; # 1 resource selected
$lang["resources_selected-2"]="%number Ressourcen ausgewählt"; # e.g. 17 resources selected
$lang["image"]="Bild";
$lang["previewimage"]="Vorschaubild";
$lang["file"]="Datei";
$lang["upload"]="Upload";
$lang["action-upload"]="Upload";
$lang["uploadafile"]="Datei hochladen";
$lang["replacefile"]="Datei ersetzen";
$lang["imagecorrection"]="Bild-Korrekturen";
$lang["previewthumbonly"]="(nur Vorschaubild anzeigen)";
$lang["rotateclockwise"]="gegen den Uhrzeigersinn drehen"; # Verkehrte Zuordnung in der Funktion, daher hier vertauscht
$lang["rotateanticlockwise"]="im Uhrzeigersinn drehen"; # Verkehrte Zuordnung in der Funktion, daher hier vertauscht
$lang["increasegamma"]="Gamma-Wert erhöhen (heller)";
$lang["decreasegamma"]="Gamma-Wert verringern (dunkler)";
$lang["restoreoriginal"]="Original wiederhestellen";
$lang["recreatepreviews"]="Vorschaugrößen neu erstellen";
$lang["retrypreviews"]="Vorschaugrößen erneut neu erstellen";
$lang["specifydefaultcontent"]="Standard-Inhalt für neue Ressourcen festlegen";
$lang["properties"]="Eigenschaften";
$lang["relatedresources"]="Verwandte Ressourcen";
$lang["indexedsearchable"]="Indexierte, durchsuchbare Felder";
$lang["clearform"]="Formular zurücksetzen";
$lang["similarresources"]="ähnliche Ressourcen"; # e.g. 17 similar resources
$lang["similarresource"]="ähnliche Ressource"; # e.g. 1 similar resource
$lang["nosimilarresources"]="keine ähnlichen Ressourcen";
$lang["emailresource"]="Ressource senden (E-Mail)";
$lang["resourcetitle"]="Ressourcen-Titel";
$lang["requestresource"]="Ressource anfordern";
$lang["action-viewmatchingresources"]="Passende Ressourcen anzeigen";
$lang["nomatchingresources"]="keine passenden Ressourcen";
$lang["matchingresources"]="passende Ressourcen"; # e.g. 17 matching resources
$lang["advancedsearch"]="Erweiterte Suche";
$lang["archiveonlysearch"]="Nur im Archiv suchen";
$lang["allfields"]="alle Felder";
$lang["typespecific"]="Spezifisch";
$lang["youfound"]="Sie haben"; # e.g. you found 17 resources
$lang["youfoundresources"]="Ressourcen gefunden"; # e.g. you found 17 resources
$lang["youfoundresource"]="Ressource gefunden"; # e.g. you found 1 resource
$lang["display"]="Anzeige"; # e.g. Display: thumbnails / list
$lang["sortorder"]="Sortierung";
$lang["relevance"]="Relevanz";
$lang["asadded"]="nach Eingang";
$lang["popularity"]="Popularität";
$lang["rating"]="Bewertung";
$lang["colour"]="Farbe";
$lang["jumptopage"]="springe zur Seite";
$lang["jump"]="springe";
$lang["titleandcountry"]="Titel / Land";
$lang["torefineyourresults"]="Um Ihre Resultate zu verfeinern versuchen Sie";
$lang["verybestresources"]="Die besten Ressourcen";
$lang["addtocurrentcollection"]="Zur aktuellen Kollektion hinzufügen";
$lang["addresource"]="Ressource hinzufügen";
$lang["addresourcebatch"]="Ressourcen-Stapel hinzufügen";
$lang["fileupload"]="Datei-Upload";
$lang["clickbrowsetolocate"]="für eine Dateiauswahl bitte klicken";
$lang["resourcetools"]="Ressourcen-Werkzeuge";
$lang["fileinformation"]="Datei-Information";
$lang["options"]="Optionen";
$lang["previousresult"]="vorige Resultate";
$lang["viewallresults"]="alle Resultate anzeigen";
$lang["nextresult"]="weitere Resultate";
$lang["pixels"]="Pixel";
$lang["download"]="Download";
$lang["preview"]="Vorschau";
$lang["fullscreenpreview"]="Vollbild-Vorschau";
$lang["originalfileoftype"]="Original ? Datei"; # ? will be replaced, e.g. "Original PDF File"
$lang["fileoftype"]="? Datei"; # ? will be replaced, e.g. "MP4 File"
$lang["log"]="Protokoll";
$lang["resourcedetails"]="Ressourcen-Details";
$lang["offlineresource"]="Offline-Ressource";
$lang["request"]="Anfrage";
$lang["searchforsimilarresources"]="Nach ähnlichen Ressourcen suchen";
$lang["clicktoviewasresultset"]="diese Ressourcen zusammenfassend anzeigen";
$lang["searchnomatches"]="Keine passenden Suchergebnisse verfügbar.";
$lang["try"]="Versuchen Sie";
$lang["tryselectingallcountries"]="Versuchen Sie <strong>alle Länder</strong> auszuwählen, oder";
$lang["tryselectinganyyear"]="versuchen Sie <strong>beliebiges Jahr</strong> auszuwählen, oder";
$lang["tryselectinganymonth"]="versuchen Sie <strong>beliebigen Monat</strong> auszuwählen, oder";
$lang["trybeinglessspecific"]="versuchen Sie Ihre weniger spezifisch zu suchen";
$lang["enteringfewerkeywords"]="(weniger Suchbegriffe eingeben)."; # Suffixed to any of the above 4 items e.g. "Try being less specific by entering fewer search keywords"
$lang["match"]="passend";
$lang["matches"]="passende";
$lang["inthearchive"]="im Archiv";
$lang["nomatchesinthearchive"]="Keine passenden Archiv-Einträge";
$lang["savethissearchtocollection"]="Suchanfrage in der aktuellen Kollektion speichern";
$lang["mustspecifyonekeyword"]="Sie müssen mindestens einen Suchbegriff angeben.";
$lang["hasemailedyouaresource"]="hat Ihnen eine Ressource gesendet"; # Suffixed to user name, e.g. Fred has e-mailed you a resource
$lang["clicktoviewresource"]="Klicken Sie untestehenden Link um die Ressource anzuzeigen.";
$lang["statuscode"]="Statuscode";

# Resource log - actions
$lang["resourcelog"]="Ressourcen-Protokoll";
$lang["log-u"]="hochgeladene Datei(en)";
$lang["log-c"]="Erstellte Ressourcen";
$lang["log-d"]="heruntergeladene Datei(en)";
$lang["log-e"]="Bearbeitetes Ressourcen-Feld";
$lang["log-m"]="Bearbeitetes Ressourcen-Feld (Mehrfach-Bearbeitung)";
$lang["log-E"]="Ressource via E-Mail weitergegeben an ";//  + notes field
$lang["log-v"]="Ressource angesehen";
$lang["log-x"]="Ressource gelöscht";
$lang["log-l"]="Eingeloggt"; # For user entries only.
$lang["log-t"]="Datei transformiert";
$lang["log-s"]="Status geändert";
$lang["log-a"]="Zugriff geändert";
$lang["log-r"]="Metadaten zurückgesetzt";

$lang["backtoresourceview"]="zurück zur Ressourcen-Ansicht";

# Resource status
$lang["status"]="Status";
$lang["status-2"]="Benutzer-Beiträge: Freischaltung noch nicht erledigt";
$lang["status-1"]="Benutzer-Beiträge: Überprüfung noch nicht erledigt";
$lang["status0"]="Aktiv";
$lang["status1"]="Archivierung noch nicht erledigt";
$lang["status2"]="Archiviert";
$lang["status3"]="Gelöscht";

# Charts
$lang["activity"]="Aktivität";
$lang["summary"]="Zusammenfassung";
$lang["mostinaday"]="am meisten pro Tag";
$lang["totalfortheyear"]="Gesamt für das Jahr";
$lang["totalforthemonth"]="Gesamt für den Monat";
$lang["dailyaverage"]="Tagesdurchschnitt für aktive Tage";
$lang["nodata"]="Keine Daten für diesen Zeitabschnitt verfügbar.";
$lang["max"]="max."; # i.e. maximum
$lang["statisticsfor"]="Statistik für"; # e.g. Statistics for 2007
$lang["printallforyear"]="Alle Statistiken dieses Jahres ausdrucken";

# Log in / user account
$lang["nopassword"]="Klicken Sie hier, wenn Sie über keinen Zugang verfügen";
$lang["forgottenpassword"]="Klicken Sie hier, wenn Sie Ihr Passwort vergessen haben";
$lang["keepmeloggedin"]="Auf diesem Computer angemeldet bleiben";
$lang["columnheader-username"]=$lang["username"]="Benutzername";
$lang["password"]="Passwort";
$lang["login"]="Anmelden";
$lang["loginincorrect"]="Fehler beim Benutzernamen bzw. Passwort. Bitte versuchen Sie es erneut.";
$lang["accountexpired"]="Ihr Benutzer-Account ist abgelaufen. Bitte kontaktieren Sie das Ressourcen-Team.";
$lang["useralreadyexists"]="Es existiert bereits ein Benutzer-Account mit diesem Benutzernamen bzw. dieser E-Mail Adresse. Änderungen wurden nicht gespeichert.";
$lang["useremailalreadyexists"]="Es existiert bereits ein Benutzer-Account mit dieser E-Mail Adresse.";
$lang["ticktoemail"]="Anklicken, um dem Benutzer den Benutzernamen und das Passwort zu senden (E-Mail)";
$lang["ticktodelete"]="Anklicken, um diesen Benutzer zu löschen";
$lang["edituser"]="Benutzer bearbeiten";
$lang["columnheader-full_name"]=$lang["fullname"]="Vollständiger Name";
$lang["email"]="E-Mail";
$lang["columnheader-e-mail_address"]=$lang["emailaddress"]="E-Mail Adresse";
$lang["suggest"]="vorschlagen";
$lang["accountexpiresoptional"]="Account gültig bis (optional)";
$lang["lastactive"]="Letzte Aktivität";
$lang["lastbrowser"]="Letzter Browser";
$lang["searchusers"]="Benutzer suchen";
$lang["createuserwithusername"]="Benutzer mit Benutzernamen erstellen...";
$lang["emailnotfound"]="die gesuchte E-Mail Adresse konnte nicht gefunden werden";
$lang["yourname"]="Ihr Name";
$lang["youremailaddress"]="Ihre E-Mail Adresse";
$lang["sendreminder"]="Erinnerung senden";
$lang["sendnewpassword"]="Neues Passwort senden";
$lang["requestuserlogin"]="Benutzer-Login anfordern";

# Research request
$lang["nameofproject"]="Name des Projektes";
$lang["descriptionofproject"]="Beschreibung des Projektes";
$lang["descriptionofprojecteg"]="(z.B.: Publikum / Mode / Fachgebiet / geografisches Gebiet)";
$lang["deadline"]="Abgabefrist";
$lang["nodeadline"]="keine Abgabefrist";
$lang["noprojectname"]="Sie müssen einen Projekt-Namen angeben";
$lang["noprojectdescription"]="Sie müssen einen Projekt-Beschreibung angeben";
$lang["contacttelephone"]="Kontakt: Telefon";
$lang["finaluse"]="Endgültiger Verwendungszweck";
$lang["finaluseeg"]="(z.B. Powerpoint / Broschüre / Poster)";
$lang["noresourcesrequired"]="Anzahl der benötigten Ressourcen für das endgültige Produkt?";
$lang["shaperequired"]="Gestaltung/Art der Bilder erforderlich";
$lang["portrait"]="Portrait";
$lang["landscape"]="Landschaft";
$lang["square"]="Quadratisch";
$lang["either"]="egal";
$lang["sendrequest"]="Anfrage senden";
$lang["editresearchrequest"]="Such-Anfrage editieren";
$lang["requeststatus0"]=$lang["unassigned"]="nicht zugeordnet";
$lang["requeststatus1"]="in Bearbeitung";
$lang["requeststatus2"]="fertiggestellt";
$lang["copyexistingresources"]="Ressource dieser Suchanfrage in eine existierende Kollektion kopieren";
$lang["deletethisrequest"]="Diese Anfrage löschen?";
$lang["requestedby"]="Angefragt von";
$lang["requesteditems"]="Angefragte Objekte";
$lang["assignedtoteammember"]="Zuordnung an Team-Mitglied";
$lang["typecollectionid"]="(ID der Kolletion eintragen)";
$lang["researchid"]="ID der Suchanfrage";
$lang["assignedto"]="Zugeordnet an";
$lang["createresearchforuser"]="Suchanfrage für Benutzer erstellen";
$lang["searchresearchrequests"]="Suchanfragen durchsuchen";
$lang["requestasuser"]="Anfrage als Benutzer";
$lang["haspostedresearchrequest"]="hat eine Suchanfrage angefordert"; # username is suffixed to this
$lang["newresearchrequestwaiting"]="Neue Suchanfragen warten auf die Bearbeitung";
$lang["researchrequestassignedmessage"]="Ihre Suchanfrage wurde unserem Team weitergeleitet. Sobald Ihre Suchanfrage abgeschlossen ist, erhalten Sie ein E-Mail mit allen von uns empfohlenen Ressourcen.";
$lang["researchrequestassigned"]="Suchanfrage zugeordnet";
$lang["researchrequestcompletemessage"]="Ihre Suchanfrage ist fertiggestellt und wurde auf Ihrer Seite 'Meine Kollektion' hinzugefügt.";
$lang["researchrequestcomplete"]="Suchanfrage fertiggestellt";


# Misc / global
$lang["selectgroupuser"]="Gruppe/Benutzer auswählen...";
$lang["select"]="auswählen...";
$lang["add"]="hinzufügen";
$lang["create"]="Erstellen";
$lang["treeobjecttype-group"]=$lang["group"]="Gruppe";
$lang["confirmaddgroup"]="Alle Benutzer dieser Gruppe zuordnen?";
$lang["backtoteamhome"]="zurück zur Administration";
$lang["columnheader-resource_id"]=$lang["resourceid"]="Ressource (ID)";
$lang["id"]="ID";
$lang["todate"]="bis";
$lang["fromdate"]="von";
$lang["day"]="Tag";
$lang["month"]="Monat";
$lang["year"]="Jahr";
$lang["hour-abbreviated"]="HH";
$lang["minute-abbreviated"]="MM";
$lang["itemstitle"]="Objekte";
$lang["tools"]="Werkzeuge";
$lang["created"]="erstellt";
$lang["user"]="Benutzer";
$lang["owner"]="Besitzer";
$lang["message"]="Nachricht";
$lang["name"]="Name";
$lang["action"]="Aktion";
$lang["treeobjecttype-field"]=$lang["field"]="Feld";
$lang["save"]="Speichern";
$lang["revert"]="Wiederherstellen";
$lang["cancel"]="abbrechen";
$lang["view"]="anzeigen";
$lang["type"]="Typ";
$lang["text"]="Text";
$lang["yes"]="ja";
$lang["no"]="nein";
$lang["key"]="Bedeutung"; # e.g. explanation of icons on search page
$lang["languageselection"]="Sprache";
$lang["language"]="Sprache";
$lang["changeyourpassword"]="Ändern Sie Ihr Passwort";
$lang["yourpassword"]="Ihr Passwort";
$lang["newpassword"]="Neues Passwort";
$lang["newpasswordretype"]="Neues Passwort (Eingabe wiederholen)";
$lang["passwordnotvalid"]="Dies ist kein gültiges Passwort";
$lang["passwordnotmatch"]="Die eingebenen Passwörter stimmen nicht überein";
$lang["wrongpassword"]="Passwort nicht korrekt, bitte versuchen Sie es erneut";
$lang["action-view"]="Anzeigen";
$lang["action-preview"]="Vorschau";
$lang["action-viewmatchingresources"]="Passende Ressourcen anzeigen";
$lang["action-expand"]="Ausklappen";
$lang["action-select"]="Auswählen";
$lang["action-download"]="Download";
$lang["action-email"]="E-Mail";
$lang["action-edit"]="Bearbeiten";
$lang["action-delete"]="Löschen";
$lang["action-deletecollection"]="Kollektion löschen";
$lang["action-revertmetadata"]="Metadaten wiederherstellen";
$lang["confirm-revertmetadata"]="Sind Sie sicher, dass Sie die ursprünglichen Metadaten aus dieser Datei neu einlesen wollen? Bei dieser Aktion gehen alle Änderungen an den Metadaten verloren.";
$lang["action-remove"]="Entfernen";
$lang["complete"]="Fertig";
$lang["backtohome"]="zurück zur Startseite";
$lang["backtohelphome"]="zurück zur Hilfeseite";
$lang["backtosearch"]="zurück zu meinen Suchresultaten";
$lang["backtoview"]="Ressource-Ansicht";
$lang["backtoeditresource"]="zurück zur Ressourcen-Bearbeitung";
$lang["backtouser"]="zurück zum Benutzer-Login";
$lang["termsandconditions"]="Allg. Geschäfts- und Nutzungsbedingungen";
$lang["iaccept"]="Ich akzeptiere";
$lang["contributedby"]="Beigetragen von";
$lang["format"]="Format";
$lang["notavailableshort"]="N/A";
$lang["allmonths"]="Alle Monate";
$lang["allgroups"]="Alle Gruppen";
$lang["status-ok"]="OK";
$lang["status-fail"]="FEHLER";
$lang["status-warning"]="WARNUNG";
$lang["status-notinstalled"]="Nicht installiert";
$lang["status-never"]="Niemals";
$lang["softwareversion"]="? Version"; # E.g. "PHP version"
$lang["softwarebuild"]="? Build"; # E.g. "ResourceSpace Build"
$lang["softwarenotfound"]="'?' nicht gefunden"; # ? will be replaced.
$lang["client-encoding"]="(Client-encoding: %encoding)"; # %encoding will be replaced, e.g. client-encoding: utf8
$lang["browseruseragent"]="Browser User-Agent";
$lang['serverplatform']="Serverplattform";
$lang["are_available-0"]="sind verfügbar";
$lang["are_available-1"]="ist verfügbar";
$lang["are_available-2"]="sind verfügbar";
$lang["were_available-0"]="waren verfügbar";
$lang["were_available-1"]="war verfügbar";
$lang["were_available-2"]="waren verfügbar";
$lang["resource-0"]="Ressourcen";
$lang["resource-1"]="Ressource";
$lang["resource-2"]="Ressourcen";
$lang["status-note"]="HINWEIS";
$lang["action-changelanguage"]="Sprache ändern";

# Pager
$lang["next"]="vor";
$lang["previous"]="zurück";
$lang["page"]="Seite";
$lang["of"]="von"; # e.g. page 1 of 2
$lang["items"]="Objekte"; # e.g. 17 items
$lang["item"]="Objekt"; # e.g. 1 item

# Statistics
$lang["stat-addpubliccollection"]="Öffentliche Kollektion hinzufügen";
$lang["stat-addresourcetocollection"]="Ressourcen zur Kollektion hinzufügen";
$lang["stat-addsavedsearchtocollection"]="Gespeicherte Suchen zur Kollektion";
$lang["stat-addsavedsearchitemstocollection"]="Gespeicherte Suchen (Objkete) zur Kollektion";
$lang["stat-advancedsearch"]="Erweiterte Suche";
$lang["stat-archivesearch"]="Archivsuche";
$lang["stat-assignedresearchrequest"]="Zugeordnete Suchanfrage";
$lang["stat-createresource"]="Ressource erstellen";
$lang["stat-e-mailedcollection"]="gesendete Kollektion (E-Mail)";
$lang["stat-e-mailedresource"]="gesendete Ressource (E-Mail)";
$lang["stat-keywordaddedtoresource"]="hinzugefügte Suchbegriffe (Ressource)";
$lang["stat-keywordusage"]="Suchbegriffe";
$lang["stat-newcollection"]="Neue Kollektion";
$lang["stat-newresearchrequest"]="Neue Suchanfrage";
$lang["stat-printstory"]="Inhalt drucken";
$lang["stat-processedresearchrequest"]="durchgeführte Suchanfragen";
$lang["stat-resourcedownload"]="Ressource (Download)";
$lang["stat-resourceedit"]="Ressource (bearbeiten)";
$lang["stat-resourceupload"]="Ressource (Upload)";
$lang["stat-resourceview"]="Ressource (Ansicht)";
$lang["stat-search"]="Suchen";
$lang["stat-usersession"]="Benutzersession";
$lang["stat-addedsmartcollection"]="Smarte Kollektion hinzugefügt";

# Access
$lang["access0"]="Offen";
$lang["access1"]="eingeschränkt";
$lang["access2"]="vertraulich";
$lang["access3"]="benutzerdefiniert";
$lang["statusandrelationships"]="Status und Beziehungen";

# Lists
$lang["months"]=array("Januar","Februar","März","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember");

# New for 1.3
$lang["savesearchitemstocollection"]="Gefundene Objekte in der aktuellen Kollektion speichern";
$lang["removeallresourcesfromcollection"]="Alle Ressourcen aus dieser Kollektion entfernen";
$lang["deleteallresourcesfromcollection"]="Alle Ressourcen dieser Kollektion löschen";
$lang["deleteallsure"]="Sind Sie sicher, dass Sie diese Ressourcen LÖSCHEN möchten? Die Ressourcen werden gelöscht und nicht (nur) aus dieser Kollektion entfernt.";
$lang["batchdonotaddcollection"]="(keiner Kollektion hinzufügen)";
$lang["collectionsthemes"]="Verwandte Themen und öffenliche Kollektionen";
$lang["recent"]="Neueste";
$lang["batchcopyfrom"]="Daten von der Ressource (ID) kopieren";
$lang["copy"]="kopieren";
$lang["zipall"]="alle komprimieren (zip)";
$lang["downloadzip"]="Kollektion als ZIP-Datei herunterladen";
$lang["downloadsize"]="Download-Größe";
$lang["tagging"]="Tagging";
$lang["speedtagging"]="Speed Tagging";
$lang["existingkeywords"]="Existierende Suchbegriffe:";
$lang["extrakeywords"]="zusätzliche Suchbegriffe";
$lang["leaderboard"]="Rangliste";
$lang["confirmeditall"]="Sind Sie sicher, dass Sie speichern möchten? Dies wird alle existierenden Werte der ausgewählten Felder für alle Ressourcen in Ihrer gegenwärtigen Kollektion überschreiben.";
$lang["confirmsubmitall"]="Sind Sie sicher, dass Sie alles zur Überprüfung abschicken wollen? Dies wird alle existierenden Einträge für die ausgewählten Felder für alle Ressourcen in Ihrer aktuellen Kollektion überschreiben und sie zur Überprüfung abschicken.";
$lang["confirmunsubmitall"]="Sind Sie sicher, dass Sie alles von der Überprüfung zurücknehmen wollen? Dies wird alle Einträge für die ausgewählten Felder für alle Ressourcen in Ihrer aktuellen Kollektion überschreiben und sie von der Überprüfung zurücknehmen.";
$lang["confirmpublishall"]="Sind Sie sicher, dass Sie alles veröffentlichen wollen? Dies wird alle Einträge für die ausgewählten Felder für alle Ressourcen in Ihrer aktuellen Kollektion überschreiben und sie veröffentlichen.";
$lang["confirmunpublishall"]="Sind Sie sicher, dass Sie alles von der Veröffentlichung zurücknehmen wollen? Dies wird alle Einträge für die ausgewählten Felder für alle Ressourcen in Ihrer aktuellen Kollektion überschreiben und sie von der Veröffentlichung zurücknehmen.";
$lang["collectiondeleteconfirm"]="Sind Sie sicher, dass Sie diese Kollektion löschen möchten?";
$lang["hidden"]="(versteckt)";
$lang["requestnewpassword"]="Neues Passwort anfordern";

# New for 1.4
$lang["reorderresources"]="Ressourcen innerhalb der Kollektion neu anordnen (halten und ziehen)";
$lang["addorviewcomments"]="Kommentare hinzufügen oder ansehen";
$lang["collectioncomments"]="Kommentare zu den Kollektionen";
$lang["collectioncommentsinfo"]="Add a comment to this collection for this resource. This will only apply to this collection.";
$lang["comment"]="Kommentar";
$lang["warningexpired"]="Ressource abgelaufen";
$lang["warningexpiredtext"]="Warnung! Diese Ressource hat das Ablaufdatum überschritten. Klicken Sie den untenstehenden Link um die Download-Funktion wieder zu aktivieren..";
$lang["warningexpiredok"]="&gt; Ressourcen-Download aktivieren";
$lang["userrequestcomment"]="Kommentar";
$lang["addresourcebatchbrowser"]="Ressourcen-Stapelverarbeitung: hinzufügen - Im Browser (Flash)";
$lang["addresourcebatchbrowserjava"]="Ressourcen-Stapelverarbeitung: hinzufügen - Im Browser (Java, empfohlen)";

$lang["addresourcebatchftp"]="Ressourcen-Stapelverarbeitung: hinzufügen - FTP";
$lang["replaceresourcebatch"]="Ressourcen-Stapelverarbeitung: ersetzen";
$lang["editmode"]="Bearbeitungsmodus";
$lang["replacealltext"]="Alle Texte ersetzen";
$lang["findandreplace"]="Suchen und ersetzen";
$lang["appendtext"]="Text hinzufügen";
$lang["removetext"]="Text entfernen / Option(en)";
$lang["find"]="Finden";
$lang["andreplacewith"]="...und ersetzen mit...";
$lang["relateallresources"]="Alle Ressourcen dieser Kollektion miteinander verknüpfen";

# New for 1.5
$lang["columns"]="Spalten";
$lang["contactsheetconfiguration"]="Konfiguration Kontaktblatt";
$lang["thumbnails"]="Vorschaubilder";
$lang["contactsheetintrotext"]="Bitte wählen Sie die Blattgröße und die Spaltenanzahl für Ihr Kontaktblatt.";
$lang["size"]="Größe";
$lang["orientation"]="Ausrichtung";
$lang["requiredfield"]="Das ist ein Pflichtfeld";
$lang["requiredfields"]="Einige Felder wurden nicht komplett ausgefüllt. Bitte überprüfen Sie das Formular und versuchen Sie es erneut.";
$lang["viewduplicates"]="Doppelte Ressourcen anzeigen";
$lang["duplicateresources"]="Doppelte Ressourcen";
$lang["userlog"]="Benutzer-Statistik";
$lang["ipaddressrestriction"]="IP-Adressen Beschränkung (optional)";
$lang["wildcardpermittedeg"]="Wildcard erlaubt; z.B.";

# New for 1.6
$lang["collection_download_original"]="Originaldatei";
$lang["newflag"]="NEUE!";
$lang["link"]="Link";
$lang["uploadpreview"]="Nur ein Vorschaubild hochladen";
$lang["starttypingusername"]="(Bitte Anfangsbuchstaben vom Benutzernamen / Namen / Gruppennamen eingeben)";
$lang["requestfeedback"]="Rückmeldung anfordern<br />(Sie erhalten die Antwort per e-mail)";
$lang["sendfeedback"]="Rückmeldung abschicken";
$lang["feedbacknocomments"]="Sie haben keine Kommentare für die Ressourcen in der Kollektion abgegeben.<br />Clicken Sie die Sprechblase neben den Ressourcen an, um einen Kommentar hinzuzufügen.";
$lang["collectionfeedback"]="Rückmeldung zur Kollektion";
$lang["collectionfeedbackemail"]="Folgende Rückmeldung wurde abgegeben:";
$lang["feedbacksent"]="Ihre Rückmeldung wurde abgeschickt.";
$lang["newarchiveresource"]="Neue archivierte Ressource";
$lang["nocategoriesselected"]="Keine Kategorie ausgewählt";
$lang["showhidetree"]="Baum anzeigen/verstecken";
$lang["clearall"]="Alles zurücksetzen";
$lang["clearcategoriesareyousure"]="Sind Sie sicher, dass Sie alle ausgewählten Optionen zurücksetzen wollen?";
$lang["share"]="Weitergeben";
$lang["sharecollection"]="Kollektion weitergeben";
$lang["sharecollection-name"]="Kollektion weitergeben - %collectionname"; # %collectionname will be replaced, e.g. Share Collection - Cars
$lang["generateurl"]="URL generieren";
$lang["generateurlinternal"]="Die folgende URL funktioniert nur für eingeloggte Benutzer.";
$lang["generateurlexternal"]="Die folgende URL funktioniert ohne Login. <strong>Bitte beachten Sie, wenn neue Ressourcen zur Kollektion hinzugefügt werden, funktioniert diese URL aus Sicherheitsgriünden nicht mehr und muss neu generiert werden.</strong>";
$lang["archive"]="Archiv";
$lang["collectionviewhover"]="Ressourcen dieser Kollektion anzeigen";
$lang["collectioncontacthover"]="Kontaktabzug der Ressourcen dieser Kollektion erstellen";
$lang["original"]="Original";

$lang["password_not_min_length"]="Das Passwort muss mindestens ? Zeichen lang sein";
$lang["password_not_min_alpha"]="Das Passwort muss mindestens ? Buchstaben (a-z, A-Z) enthalten";
$lang["password_not_min_uppercase"]="Das Passwort muss mindestens ? Großbuchstaben (A-Z) enthalten";
$lang["password_not_min_numeric"]="Das Passwort muss mindestens ? Ziffern (0-9) enthalten";
$lang["password_not_min_special"]="Das Passwort muss mindestens ? Sonderzeichen (!@$%&* etc.) enthalten";
$lang["password_matches_existing"]="Das eingegebene Passwort ist identisch mit dem bestehenden Passwort";
$lang["password_expired"]="Ihr Passwort ist abgelaufen. Sie müssen ein neues Passwort eingeben";
$lang["max_login_attempts_exceeded"]="Sie haben die maximale Anzahl an Login Versuchen überschritten. Sie müssen ? Minuten warten, bis Sie es erneut versuchen können.";

$lang["newlogindetails"]="Dies sind Ihre neuen Login Daten."; # For new password mail
$lang["youraccountdetails"]="Ihre Daten"; # Subject of mail sent to user on user details save

$lang["copyfromcollection"]="Aus Kollektion kopieren";
$lang["donotcopycollection"]="Nicht aus einer Kollektion kopieren";

$lang["resourcesincollection"]="Ressourcen in dieser Kollektion"; # E.g. 3 resources in this collection
$lang["removefromcurrentcollection"]="Aus aktueller Kollektion entfernen";
$lang["showtranslations"]="+ Übersetzungen zeigen";
$lang["hidetranslations"]="- Übersetzungen verbergen";
$lang["archivedresource"]="Archivierte Ressourcen";

$lang["managerelatedkeywords"]="Verknüpfte Stichworte verwalten";
$lang["keyword"]="Stichwort";
$lang["relatedkeywords"]="Verknüpfte Stichworte";
$lang["matchingrelatedkeywords"]="Passende verknüpfte Stichworte";
$lang["newkeywordrelationship"]="Neue Verknüpfung für Stichworte hinzufügen...";
$lang["searchkeyword"]="Stichwort für Suche";

$lang["exportdata"]="Export Daten";
$lang["exporttype"]="Export Typ";

$lang["managealternativefiles"]="Alternative Dateien verwalten";
$lang["managealternativefilestitle"]="Alternative Dateien verwalten";
$lang["alternativefiles"]="Alternative Dateien";
$lang["filetype"]="Dateiformat";
$lang["filedeleteconfirm"]="Wollen Sie diese Datei wirklich löschen?";
$lang["addalternativefile"]="Alternative Datei hinzufügen";
$lang["editalternativefile"]="Alternative Datei bearbeiten";
$lang["description"]="Beschreibung";
$lang["notuploaded"]="Nicht hochgeladen";
$lang["uploadreplacementfile"]="Datei ersetzen";
$lang["backtomanagealternativefiles"]="Zurück zu Alternative Dateien verwalten";


$lang["resourceistranscoding"]="Ressource wird momentan umgewandelt";
$lang["cantdeletewhiletranscoding"]="Sie können Ressourcen nicht löschen, während Sie umgewandelt werden.";

$lang["maxcollectionthumbsreached"]="Zu viele Ressourcen in dieser Kollektion, um Thumbnails anzuzeigen. Thumbnails werden jetzt versteckt.";

$lang["ratethisresource"]="Wie bewerten Sie diese Ressource?";
$lang["ratingthankyou"]="Vielen Dank für Ihre Bewertung.";
$lang["ratings"]="Bewertungen";
$lang["rating_lowercase"]="Bewertung";
$lang["cannotemailpassword"]="Sie können dem Benutzer das bestehende Passwort nicht per E-Mail senden, da es nicht gespeichert wird (nur ein verschlüsselter Hash wird gespeichert).<br /><br />Bitte nutzen Sie den 'Vorschlagen' Button oben, der ein neues Passwort generiert und die E-Mail Funktion wieder ermöglicht.";

$lang["userrequestnotification1"]="Die Login Anfrage wurde mit den folgenden Daten gestellt:";
$lang["userrequestnotification2"]="Wenn Sie den Benutzer erstellen möchten, folgen Sie bitte dem untenstehenden Link und legen den Benutzer dort an.";
$lang["ipaddress"]="IP-Adresse";
$lang["userresourcessubmitted"]="Die folgenden Ressourcen wurden von Benutzern zur Überprüfung eingesandt:";
$lang["userresourcesunsubmitted"]="Die folgenden Ressourcen wurden von Benutzern zurückgezogen und müssen nicht mehr überprüft werden:";
$lang["viewalluserpending"]="Alle von Benutzern zur Überprüfung eingesandten Ressourcen anzeigen:";

# New for 1.7
$lang["installationcheck"]="Installation Überprüfen";
$lang["managefieldoptions"]="Feldoptionen verwalten";
$lang["matchingresourcesheading"]="Passende Ressourcen";
$lang["backtofieldlist"]="Zurück zur Feldliste";
$lang["rename"]="Umbenennen";
$lang["showalllanguages"]="Alle Sprachen anzeigen";
$lang["hidealllanguages"]="Alle Sprachen verstecken";
$lang["clicktologinasthisuser"]="Als dieser Benutzer anmelden";
$lang["addkeyword"]="Stichwort hinzufügen";
$lang["selectedresources"]="Ausgewählte Ressourcen";

$lang["internalusersharing"]="Weitergeben an interne Benutzer";
$lang["externalusersharing"]="Weitergeben an externe Benutzer";
$lang["accesskey"]="Zugangscode";
$lang["sharedby"]="Weitergegeben von";
$lang["sharedwith"]="Weitergegeben an";
$lang["lastupdated"]="Letzte Aktualisierung";
$lang["lastused"]="Zuletzt benutzt";
$lang["noattachedusers"]="keine zugeordneten Benutzer";
$lang["confirmdeleteaccess"]="Sind Sie sicher, dass Sie diesen Zugangscode löschen wollen? Benutzer, denen Sie den Zugangscode geschickt haben, können dann nicht mehr auf die Kollektion zugreifen.";
$lang["noexternalsharing"]="Nicht an externe Benutzer weitergegeben.";
$lang["sharedcollectionaddwarning"]="Achtung: Diese Kollektion wurde an externe Benutzer weitergegeben. Die Ressource, die Sie zur Kollektion hinzugefügt haben, ist nun auch für diese Benutzer verfügbar. Klicken Sie auf 'Weitergeben', um die Einstellungen zu verwalten.";
$lang["addresourcebatchlocalfolder"]="Ressourcen-Stapelverarbeitung: hinzufügen - aus Upload Ordner";

# Setup Script
$lang["setup-alreadyconfigured"]="Ihre ResourceSpace installation ist bereits konfiguriert. Um die Installation neu zu konfigurieren, können Sie die Datei <pre>include/config.php</pre> und dann diese Seite neu laden.";
$lang["setup-successheader"]="Glückwunsch!";
$lang["setup-successdetails"]="Ihre ResourceSpace Installation ist abgeschlossen. Weitere Konfigurationsoptionen finden Sie in der Datei 'include/default.config.php'.";
$lang["setup-successnextsteps"]="Nächste Schritte:";
$lang["setup-successremovewrite"]="Sie können nun den Schreibzugriff auf den Ordner 'include/' entfernen.";
$lang["setup-visitwiki"]='Visit the <a href="http://rswiki.montala.net/index.php/Main_Page">ResourceSpace Documentation Wiki</a> for more information about customizing your installation';
$lang["setup-checkconfigwrite"]="Schreibzugriff auf Konfigurationsverzeichnis:";
$lang["setup-checkstoragewrite"]="Schreibzugriff auf Datenverzeichnis:";
$lang["setup-welcome"]="Willkommen bei ResourceSpace";
$lang["setup-introtext"]="Danke, dass Sie sich für ResourceSpace entschieden haben.  Dieser Konfigurationsassistent wird Ihnen helfen, ResourceSpace einzurichten, und muss nur einmal ausgeführt werden.";
$lang["setup-checkerrors"]="Fehler gefunden.<br />Bitte beheben Sie diese Fehler und laden Sie dann diese Seite erneut.";
$lang["setup-errorheader"]="In Ihrer Konfiguration wurden Fehler gefunden.  Eine detaillierte Fehlerbeschreibung finden Sie unten.";
$lang["setup-warnheader"]="Einige Ihrer Einstellungen haben zu Warnungen geführt.  Details finden Sie unten. Das bedeutet nicht, dass Ihre Konfiguration fehlerhaft ist.";
$lang["setup-basicsettings"]="Grundeinstellungen";
$lang["setup-basicsettingsdetails"]="Dies sind die Grundeinstellungen für Ihre ResourceSpace Installation. Pflichtfelder sind mit einem <strong>*</strong> markiert.";
$lang["setup-dbaseconfig"]="Datenbank Konfiguration";
$lang["setup-mysqlerror"]="Fehler in Ihren MySQL Einstellungen:";
$lang["setup-mysqlerrorversion"]="MySQL Version 5 oder neuer benötigt.";
$lang["setup-mysqlerrorserver"]="Server nicht erreichbar.";
$lang["setup-mysqlerrorlogin"]="Login fehlgeschlagen. (Benutzername und Passwort prüfen)";
$lang["setup-mysqlerrordbase"]="Zugriff auf Datenbank fehlgeschlagen.";
$lang["setup-mysqlerrorperns"]="Bitte Benutzerrechte prüfen.  Konnte keine Tabelle erstellen.";
$lang["setup-mysqltestfailed"]="Test fehlgeschlagen (MySQL konnte nicht bestätigt werden)";
$lang["setup-mysqlserver"]="MySQL Server:";
$lang["setup-mysqlusername"]="MySQL Benutzername:";
$lang["setup-mysqlpassword"]="MySQL Passwort:";
$lang["setup-mysqldb"]="MySQL Datenbank:";
$lang["setup-mysqlbinpath"]="MySQL Tools Pfad:";
$lang["setup-generalsettings"]="Allgemeine Einstellungen";
$lang["setup-baseurl"]="Basis URL:";
$lang["setup-emailfrom"]="Absender für E-Mails:";
$lang["setup-emailnotify"]="E-Mail Benachrichtigung:";
$lang["setup-spiderpassword"]="Spider Passwort:";
$lang["setup-scramblekey"]="Scramble Schlüssel:";
$lang["setup-apiscramblekey"]="API Scramble Schlüssel:";
$lang["setup-paths"]="Pfade";
$lang["setup-pathsdetail"]="Geben Sie den Pfad zu den Tools ohne abschließenden Schrägstrich ein. Um ein Tool zu deaktivieren, lassen Sie die Angabe leer. Automatisch erkannte Pfade sind bereits eingetragen.";
$lang["setup-applicationname"]="Name der Installation:";
$lang["setup-basicsettingsfooter"]="HINWEIS: Auf dieser Seite befinden sich alle <strong>erforderlichen</strong> Einstellungen.  Wenn Sie nicht an den erweiterten Optionen interessiert sind, können Sie unten klicken, um die Installation sofort zu starten.";
$lang["setup-if_mysqlserver"]='IP Adresse oder <abbr title="Fully Qualified Domain Name">FQDN</abbr> Ihres MySQL Servers.  Wenn MySQL auf dem selben Server wie ResourceSpace installiert ist, geben Sie bitte &quot;localhost&quot; an.';
$lang["setup-if_mysqlusername"]="Der MySQL Benutzername. Dieser Benutzer muss in der unten angegebenen Datenbank das Recht zum Erstellen von Tabellen haben.";
$lang["setup-if_mysqlpassword"]="Das Passwort zum oben angegebenen MySQL Benutzer.";
$lang["setup-if_mysqldb"]="Name der MySQL Datenbank. (Die Datenbank muss bereits existieren)";
$lang["setup-if_mysqlbinpath"]="Pfad zu den MySQL Tools, z.B. mysqldump. HINWEIS: Diese Angabe wird nur benötigt, wenn Sie die Export Funktion nutzen wollen.";
$lang["setup-if_baseurl"]="Die Basis URL für diese Installation ohne abschließenden Schrägstrich.";
$lang["setup-if_emailfrom"]="Diese E-Mail Adresse wird von ResourceSpace als Absender für E-Mails benutzt.";
$lang["setup-if_emailnotify"]="An diese E-Mail Adresse werden Ressourcen-, Benutzer- und Suchanfragen gesendet.";
$lang["setup-if_spiderpassword"]="Das Spider Passwort ist ein Pflichtfeld.";
$lang["setup-if_scramblekey"]="Um verschlüsselte Pfade zu aktivieren, fügen Sie hier eine zufällige Zeichenkette (ähnlich einem Passwort) ein. Wenn diese Installation öffentlich zugänglich ist, wird dies dringend empfohlen. Um verschlüsselte Pfade nicht zu aktivieren, lassen Sie das Feld bitte leer. Eine zufällige Zeichenkette ist bereits vorgewählt worden, kann aber geändert werden, z.B. um die Einstellungen einer bestehenden Installation wiederherzustellen.";
$lang["setup-if_apiscramblekey"]="Wählen Sie für den API Scramble Schlüssel eine zufällige Zeichenkette (ähnlich einem Passwort), wenn Sie planen, die API zu nutzen.";
$lang["setup-if_applicationname"]="Name dieser Installation (z.B. 'Meine Firma Bilddatenbank').";
$lang["setup-err_mysqlbinpath"]="Konnte Pfad nicht bestätigen. Leer lassen zum deaktivieren.";
$lang["setup-err_baseurl"]="Basis URL muss ausgefüllt werden.";
$lang["setup-err_baseurlverify"]="Basis URL scheint falsch zu sein (konnte license.txt nicht laden).";
$lang["setup-err_spiderpassword"]="Passwort für spider.php. WICHTIG: Wählen Sie hier eine zufällige Zeichenkette für jede Installation. Ihre Ressourcen sind zugreifbar für jeden, der dieses Passwort kennt. Eine zufällige Zeichenkette ist bereits vorgewählt worden, kann aber geändert werden, z.B. um die Einstellungen einer bestehenden Installation wiederherzustellen.";
$lang["setup-err_scramblekey"]="Wenn diese Installation öffentlich zugänglich ist, wird die Nutzung von verschlüsselten Pfaden dringend empfohlen.";
$lang["setup-err_apiscramblekey"]="Wenn diese Installtion öffentlich zugänglich ist, wird das Setzen des API Scramble Schlüssels dringend empfohlen.";
$lang["setup-err_path"]="Konnte Pfad nicht bestätigen von";
$lang["setup-emailerr"]="Ungültige E-Mail Adresse.";
$lang["setup-rs_initial_configuration"]="ResourceSpace: Erstkonfiguration";
$lang["setup-include_not_writable"]="'/include' nicht beschreibbar. Nur während der Konfiguration nötig.";
$lang["setup-override_location_in_advanced"]="Ort überschreiben in 'Erweiterte Einstellungen'.";
$lang["setup-advancedsettings"]="Erweiterte Einstellungen";
$lang["setup-binpath"]="%bin Pfad"; #%bin will be replaced, e.g. "Imagemagick Path"
$lang["setup-begin_installation"]="Installation beginnen!";
$lang["setup-generaloptions"]="Allgemeine Optionen";
$lang["setup-allow_password_change"]="Änderung des Passworts erlauben?";
$lang["setup-enable_remote_apis"]="APIs aktivieren?";
$lang["setup-if_allowpasswordchange"]="Benutzern das Ändern ihres Passworts erlauben.";
$lang["setup-if_enableremoteapis"]="Zugriff auf API Plugins erlauben.";
$lang["setup-allow_account_requests"]="Benutzern erlauben, einen Account anzufragen?";
$lang["setup-display_research_request"]="Suchanfragen Funktion anzeigen?";
$lang["setup-if_displayresearchrequest"]="Benutzern erlauben, Ressourcen anzufragen mittels eines Formulars, das dann per E-Mail versandt wird.";
$lang["setup-themes_as_home"]="Themen Seite als Startseite verwenden?";
$lang["setup-remote_storage_locations"]="Remote Storage";
$lang["setup-use_remote_storage"]="Remote Storage nutzen?";
$lang["setup-if_useremotestorage"]="Auswählen, um Remote Storage (anderer Server für filestore) für RS zu nutzen.";
$lang["setup-storage_directory"]="Storage Verzeichnis";
$lang["setup-if_storagedirectory"]="In welchem Verzeichnis sollen die Dateien abgelegt werden. Kann absolut sein (/var/www/blah/blah) oder relativ zur RS Installation. HINWEIS: Kein / am Ende.";
$lang["setup-storage_url"]="Storage URL";
$lang["setup-if_storageurl"]="Wie kann per HTTP auf das Storage Verzeichnis zugegriffen werden? Kann absolut sein (http://files.example.com) oder relativ zur RS Installation. HINWEIS: Kein / am Ende.";
$lang["setup-ftp_settings"]="FTP Einstellungen";
$lang["setup-if_ftpserver"]="Nur notwendig, wenn Sie planen, die FTP Upload Funktion zu nutzen.";
$lang["setup-login_to"]="Login zu";
$lang["setup-configuration_file_output"]="Ausgabe der Konfigurationsdatei";

# Collection log - actions
$lang["collectionlog"]="Kollektionen Log";
$lang["collectionlog-r"]="Ressource entfernt";
$lang["collectionlog-R"]="Alle Ressourcen entfernt";
$lang["collectionlog-D"]="Alle Ressourcen gelöscht";
$lang["collectionlog-d"]="Ressource gelöscht"; // this shows external deletion of any resources related to the collection.
$lang["collectionlog-a"]="Ressource hinzugefügt";
$lang["collectionlog-c"]="Ressource hinzugefügt (kopiert)";
$lang["collectionlog-m"]="Ressource kommentiert";
$lang["collectionlog-*"]="Ressource bewertet";
$lang["collectionlog-S"]="Kollektion weitergegeben an "; //  + notes field
$lang["collectionlog-E"]="Kollektion per E-Mail weitergegeben an ";//  + notes field
$lang["collectionlog-s"]="Ressource weitergegeben an ";//  + notes field
$lang["collectionlog-T"]="Kollektion nicht mehr weitergegeben an ";//  + notes field
$lang["collectionlog-t"]="Ressource  nicht mehr weitergegeben an ";//  + notes field
$lang["collectionlog-X"]="Kollektion gelöscht";


$lang["viewuncollectedresources"]="Ressourcen anzeigen, die nicht in einer Kollektion enthalten sind";

# Collection requesting
$lang["requestcollection"]="Kollektion anfordern";

# Metadata report
$lang["metadata-report"]="Metadaten Bericht";

# Video Playlist
$lang["videoplaylist"]="Video Wiedergabeliste";

$lang["restrictedsharecollection"]="Sie haben eingeschränkten Zugriff auf eine oder mehrere Ressourcen in dieser Kollektion, daher ist die Weitergabe deaktiviert.";

$lang["collection"]="Kollektion";
$lang["idecline"]="Ablehnen"; # For terms and conditions

$lang["mycollection_notpublic"]="'Meine Kollektion' kann nicht in eine öffentliche Kollektion oder ein Thema umgewandelt werden. Bitte erstellen Sie für diesen Zweck eine neue Kollektion.";

$lang["resourcemetadata"]="Ressourcen-Felder";

$lang["selectgenerateurlexternal"]="Um eine URL für Nutzer ohne Login zu generieren, wählen Sie bitte die Zugriffsrechte aus, die Sie für diese Ressourcen gewähren wollen.";

$lang["externalselectresourceaccess"]="Wenn Sie die Ressourcen per E-Mail an Nutzer ohne Login weitergeben wollen, wählen Sie bitte die Zugriffsrechte aus, die Sie für diese Ressourcen gewähren wollen.";

$lang["externalselectresourceexpires"]="Wenn Sie die Ressourcen per E-Mail an Nutzer ohne Login weitergeben wollen, geben Sie bitte ein Ablaufdatum für den Link ein.";

 $lang["externalshareexpired"]="Dieser Link ist leider abgelaufen und damit nicht mehr verfügbar.";

 $lang["expires"]="Läuft ab";
 $lang["never"]="Niemals";

 $lang["approved"]="Freigegeben";
 $lang["notapproved"]="Nicht freigegeben";

 $lang["userrequestnotification3"]="Wenn diese Anfrage gültig ist, klicken Sie den untenstehenden Link, um die Details des Benutzers anzusehen und den Benutzer freizugeben.";

 $lang["ticktoapproveuser"]="Sie müssen dieses Kästchen aktivieren, um den Benutzer zu aktivieren.";

 $lang["managerequestsorders"]="Anfragen / Bestellungen verwalten";
 $lang["editrequestorder"]="Anfrage / Bestellung bearbeiten";
 $lang["requestorderid"]="Anfrage / Bestellung Nr.";
 $lang["viewrequesturl"]="Um diese Anfrage anzusehen, klicken Sie bitte diesen Link:";
 $lang["requestreason"]="Grund für die Anfrage";

 $lang["resourcerequeststatus0"]="Warten";
 $lang["resourcerequeststatus1"]="Akzeptiert";
 $lang["resourcerequeststatus2"]="Abgelehnt";

 $lang["ppi"]="PPI"; # (Pixels Per Inch - used on the resource download options list).

 $lang["useasthemethumbnail"]="Diese Ressource als Thumbnail für Thema nutzen?";
 $lang["sessionexpired"]="Sie wurden automatisch ausgeloggt, weil Sie länger als 30 Minuten inaktiv waren. Bitte geben Sie Ihre Login Daten erneut ein, um fortzufahren.";

 $lang["resourcenotinresults"]="Die aktuelle Ressource ist nicht mehr in Ihren Suchergebnissen. Daher ist eine Navigation mit zurück/nächste nicht möglich.";
 $lang["publishstatus"]="Speichern mit Veröffentlichungsstatus:";
 $lang["addnewcontent"]="Neuer Inhalt (Seite,Name)";
 $lang["hitcount"]="Zugriffszähler";
 $lang["downloads"]="Downloads";

 $lang["addremove"]="Hinzufügen/Entfernen";

##  Translations for standard log entries
 $lang["all_users"]="alle Benutzer";
 $lang["new_resource"]="Neue Ressource";

 $lang["invalidextension_mustbe"]="Ungültige Erweiterung, muss eine der folgenden sein";
 $lang["allowedextensions"]="Erlaubte Erweiterungen";

 $lang["alternativebatchupload"]="Alternative Dateien Stapelverarbeitung (Java)";

 $lang["confirmdeletefieldoption"]="Wollen Sie wirklich diese Option LÖSCHEN?";

 $lang["cannotshareemptycollection"]="Diese Kollektion ist leer und kann daher nicht weitergegeben werden.";

 $lang["requestall"]="Alle anfordern";
 $lang["requesttype-email_only"]=$lang["resourcerequesttype0"]="Nur E-Mail";
 $lang["requesttype-managed"]=$lang["resourcerequesttype1"]="Verwaltete Anfrage";
 $lang["requesttype-payment_-_immediate"]=$lang["resourcerequesttype2"]="Zahlung – sofort";
 $lang["requesttype-payment_-_invoice"]=$lang["resourcerequesttype3"]="Zahlung – auf Rechnung";

 $lang["requestapprovedmail"]="Ihre Anfrage wurde akzeptiert. Bitte Klicken Sie den untenstehenden Link, um die angefragten Ressourcen anzuzeigen und herunterzuladen.";
 $lang["requestdeclinedmail"]="Es tut uns leid, Ihre Anfrage für die Ressourcen in der Kollektion wurde abgelehnt.";

 $lang["resourceexpirymail"]="Die folgenden Ressourcen sind abgelaufen:";
 $lang["resourceexpiry"]="Ablauf";

 $lang["requestapprovedexpires"]="Ihr Zugriff auf diese Ressourcen wir ablaufen am";

 $lang["pleasewaitsmall"]="(bitte warten)";
 $lang["removethisfilter"]="(diesen Filter entfernen)";

 $lang["no_exif"]="EXIF/IPTC/XMP Metadaten für diesen Upload nicht importieren";
 $lang["difference"]="Unterschied";
 $lang["viewdeletedresources"]="Gelöschte Ressourcen anzeigen";
 $lang["finaldeletion"]="Diese Ressource ist bereits im Status 'gelöscht'. Diese Aktion wird die Ressource vollständig vom System entfernen.";

 $lang["nocookies"]="Ein Cookie konnte nicht richtig gesetzt werden. Bitte stellen Sie sicher, dass Cookies in Ihrem Browser aktiviert sind.";

 $lang["selectedresourceslightroom"]="Ausgewählte Ressourcen (Lightroom kompatible Liste):";

# Plugins Manager
 $lang['plugins-noneinstalled'] = "Derzeit keine Plugins aktiviert.";
 $lang['plugins-noneavailable'] = "Derzeit keine Plugins verfügbar.";
 $lang['plugins-availableheader'] = 'Verfügbare Plugins';
 $lang['plugins-installedheader'] = 'Derzeit aktivierte Plugins';
 $lang['plugins-author'] = 'Autor';
 $lang['plugins-version'] = 'Version';
 $lang['plugins-instversion'] = 'Installierte Version';
 $lang['plugins-uploadheader'] = 'Plugin hochladen';
 $lang['plugins-uploadtext'] = '.rsp Datei zum Installieren auswählen.';
 $lang['plugins-deactivate'] = 'Deaktivieren';
 $lang['plugins-moreinfo'] = 'Weitere Infos';
 $lang['plugins-activate'] = 'Aktivieren';
 $lang['plugins-purge'] = 'Konfiguration bereinigen';
 $lang['plugins-rejmultpath'] = 'Archiv enthält mehrere Pfade. (Sicherheitsrisiko)';
 $lang['plugins-rejrootpath'] = 'Archiv enthält absolute Pfade. (Sicherheitsrisiko)';
 $lang['plugins-rejparentpath'] = 'Archiv enthält Pfade mit übergeordneten Verzeichnissen (../). (Sicherheitsrisiko)';
 $lang['plugins-rejmetadata'] = 'Beschreibungsdatei des Archivs nicht gefunden.';
 $lang['plugins-rejarchprob'] = 'Es gab ein Problem beim entpacken des Archivs:';
 $lang['plugins-rejfileprob'] = 'Plugin muss im .rsp Format hochgeladen werden.';
 $lang['plugins-rejremedy'] = 'Wenn Sie dem Plugin vertrauen, können Sie es manuell installieren, in dem Sie es im plugins Verzeichnis entpacken.';
 $lang['plugins-uploadsuccess'] = 'Plugin erfolgreich hochgeladen.';
 $lang['plugins-headertext'] = 'Plugins erweitern die Funktionalität von ResourceSpace.';
 $lang['plugins-legacyinst'] = 'Aktiviert durch die Datei config.php';
 $lang['plugins-uploadbutton'] = 'Plugin hochladen';

#Location Data
 $lang['location-title'] = 'Geodaten';
 $lang['location-add'] = 'Geodaten hinzufügen';
 $lang['location-edit'] = 'Geodaten bearbeiten';
 $lang['location-details'] = 'Karte doppelklicken, um einen Pin zu platzieren. Anschließend können Sie dann Pin an die gewünschte Stelle ziehen.';
 $lang['location-noneselected']="Kein Ort ausgewählt";
 $lang['location'] = 'Ort';

 $lang["publiccollections"]="Öffentliche Kollektionen";
 $lang["viewmygroupsonly"]="Nur meine Gruppen anzeigen";
 $lang["usemetadatatemplate"]="Metadatenvorlage nutzen";
 $lang["undometadatatemplate"]="(Vorlagenauswahl rückgängig machen)";

 $lang["accountemailalreadyexists"]="Unter dieser E-Mail Adresse gibt es bereits einen Benutzer";

 $lang["backtothemes"]="Zurück zu Themen";
 $lang["downloadreport"]="Download Bericht";

#Bug Report Page
 $lang['reportbug']="Bug Bericht für ResourceSpace Team vorbereiten";
 $lang['reportbug-detail']="Die folgenden Informationen werden im Bug Bericht enthalten sein. Sie können alle Werte ändern, bevor Sie den Bericht abschicken.";
 $lang['reportbug-login']="HINWEIS: Klicken Sie hier, um sich einzuloggen, BEVOR Sie auf vorbereiten klicken.";
 $lang['reportbug-preparebutton']="Fehlerbericht vorbereiten";

 $lang["enterantispamcode"]="<strong>Anti-Spam</strong><br /> Bitte geben Sie den folgenden Code ein:";

 $lang["groupaccess"]="Gruppenzugriff";
 $lang["plugin-groupsallaccess"]="Dieses Plugin ist für alle Gruppen aktiv";
 $lang["plugin-groupsspecific"]="Dieses Plugin ist nur für ausgewählte Gruppen aktiv";


 $lang["associatedcollections"]="Verbundene Kollektionen";
 $lang["emailfromuser"]="E-Mail senden von ";
 $lang["emailfromsystem"]="Haken entfernen, um die E-Mail von der System Adresse zu senden: ";



 $lang["previewpage"]="Vorschauseite";
 $lang["nodownloads"]="Keine Downloads";
 $lang["uncollectedresources"]="Ressourcen, die nicht in Kollektionen enthalten sind";
 $lang["nowritewillbeattempted"]="Es werden keine Daten geschrieben";
 $lang["notallfileformatsarewritable"]="Nicht alle Dateiformate können mit dem exiftool geschrieben werden";
 $lang["filetypenotsupported"]="Dateiformat %filetype nicht unterstützt";  # %filetype will be replaced, e.g. JPG filetype not supported
 $lang["exiftoolprocessingdisabledforfiletype"]="Exiftool Verarbeitung für dieses Dateiformat (%filetype) deaktiviert"; # %filetype will be replaced, e.g. JPG filetype not supported
 $lang["nometadatareport"]="Kein Metadaten Bericht";
 $lang["metadatawritewillbeattempted"]="Schreiben der Metadaten wird versucht.";
 $lang["embeddedvalue"]="Eingebetteter Wert";
 $lang["exiftooltag"]="Exiftool Tag";
 $lang["error"]="Fehler";
 $lang["exiftoolnotfound"]="Exiftool konnte nicht gefunden werden.";

 $lang["indicateusage"]="Bitte beschreiben Sie die geplante Nutzung dieser Ressource.";
 $lang["usage"]="Nutzung";
 $lang["indicateusagemedium"]="Nutzung auf Medium";
 $lang["usageincorrect"]="Sie müssen die geplante Nutzung und das Medium angeben";

 $lang["savesearchassmartcollection"]="Als Smarte Kollektion speichern";
 $lang["smartcollection"]="Smarte Kollektion";


 $lang["uploadertryflash"]="Wenn Sie Probleme mit dem Upload haben, versuchen Sie bitte den <strong>Flash uploader</strong>.";
 $lang["uploadertryjava"]="Wenn Sie Probleme mit dem Upload haben, versuchen Sie bitte den <strong>Java uploader</strong>.";
 $lang["getjava"]="Um sicherzustellen, dass Sie die neueste Java Version installiert haben, besuchen Sie bitte die Java Website.";
 $lang["getflash"]="Um sicherzustellen, dass Sie die neueste Version des Flash Players installiert haben, besuchen Sie bitte die Flash Website.";

 $lang["all"]="Alle";
 $lang["backtoresults"]="Zurück zu den Suchergebnissen";

 $lang["preview_all"]="Alle in Vorschau zeigen";

 $lang["usagehistory"]="Nutzungsprotokoll";
 $lang["usagebreakdown"]="Nutzungsanalyse";
 $lang["usagetotal"]="Downloads gesamt";
 $lang["usagetotalno"]="Gesamtzahl der Downloads";
 $lang["ok"]="OK";

 $lang["random"]="Zufällig";
 $lang["userratingstatsforresource"]="Bewertungsstatistik für diese Ressource";
 $lang["average"]="Durchschnitt";
 $lang["popupblocked"]="Das Popup-Fenster wurde von Ihrem Browser geblockt.";
 $lang["closethiswindow"]="Fenster schließen";

 $lang["requestaddedtocollection"]="Diese Ressource wurde zu Ihrer aktuellen Kollektion hinzugefügt. Sie können die Ressourcen in Ihrer Kollektion mit dem Link \'Alle anfordern\' anfordern.";

# E-commerce text
 $lang["buynow"]="Jetzt kaufen";
 $lang["yourbasket"]="Ihr Warenkorb";
 $lang["addtobasket"]="Zum Warenkorb hinzufügen";
 $lang["yourbasketcontains"]="Ihr Warenkorb enthält ? Ressourcen.";
 $lang["yourbasketisempty"]="Ihr Warenkorb ist leer.";
 $lang["buy"]="Kaufen";
 $lang["buyitemaddedtocollection"]="Diese Ressource wurde zu Ihrem Warenkorb hinzugefügt. Sie können alle Ressourcen im Warenkorb mit dem Link \'Jetzt kaufen\' erwerben.";
 $lang["buynowintro"]="Bitte wählen Sie die Größen aus, die Sie benötigen.";
 $lang["nodownloadsavailable"]="Für diese Ressource gibt es leider keine Downloads.";
 $lang["proceedtocheckout"]="Weiter zur Kasse";
 $lang["totalprice"]="Gesamtpreis";
 $lang["price"]="Preis";
 $lang["waitingforpaymentauthorisation"]="Wir haben leider noch keine Zahlungsbestätigung erhalten. Bitte warten Sie einen Augenblick und klicken Sie dann 'Aktualisieren'.";
 $lang["reload"]="Aktualisieren";
 $lang["downloadpurchaseitems"]="Gekaufte Ressourcen herunterladen";
 $lang["downloadpurchaseitemsnow"]="Bitte benutzen Sie die untenstehenden Links, um Ihre gekauften Ressourcen jetzt herunterzuladen.<br><br>Verlassen Sie diese Seite nicht, bis Sie alle Ressourcen heruntergeladen haben.";
 $lang["alternatetype"]="Alternative Art";


 $lang["subcategories"]="Unterkategorien";
 $lang["back"]="Zurück";

 $lang["pleasewait"]="Bitte warten...";

 $lang["autorotate"]="Bilder automatisch drehen?";

# Reports
# Report names (for the default reports)
$lang["report-keywords_used_in_resource_edits"]="Benutzte Stichworte beim Bearbeiten von Ressourcen";
$lang["report-keywords_used_in_searches"]="Benutzte Stichworte in der Suche";
$lang["report-resource_download_summary"]="Zusammenfassung der Ressourcendownloads";
$lang["report-resource_views"]="Ressourcen Aufrufe";
$lang["report-resources_sent_via_e-mail"]="Per E-Mail weitergegebene Ressourcen";
$lang["report-resources_added_to_collection"]="Zu Kollektionen hinzugefügte Ressourcen";
$lang["report-resources_created"]="Erstellte Ressourcen";
$lang["report-resources_with_zero_downloads"]="Ressourcen ohne Downloads";
$lang["report-resources_with_zero_views"]="Ressourcen ohne Aufrufe";
$lang["report-resource_downloads_by_group"]="Ressourcendownloads nach Gruppe";
$lang["report-resource_download_detail"]="Ressourcendownloads im Detail";
$lang["report-user_details_including_group_allocation"]="Benutzer Details inkl. Gruppenzuordnung";

#Column headers (for the default reports)
$lang["columnheader-keyword"]="Stichwort";
$lang["columnheader-entered_count"]="Anzahl";
$lang["columnheader-searches"]="Suchanfragen";
$lang["columnheader-date_and_time"]="Datum / Uhrzeit";
$lang["columnheader-downloaded_by_user"]="Heruntergeladen von";
$lang["columnheader-user_group"]="Benutzergruppe";
$lang["columnheader-resource_title"]="Titel der Ressource";
$lang["columnheader-title"]="Titel";
$lang["columnheader-downloads"]="Downloads";
$lang["columnheader-group_name"]="Gruppenname";
$lang["columnheader-resource_downloads"]="Ressourcendownloads";
$lang["columnheader-views"]="Aufrufe";
$lang["columnheader-added"]="Hinzugefügt";
$lang["columnheader-creation_date"]="Erstellungsdatum";
$lang["columnheader-sent"]="Versendet";
$lang["columnheader-last_seen"]="Zuletzt gesehen";

 $lang["period"]="Zeitraum";
 $lang["lastndays"]="Letzte ? Tage"; # ? is replaced by the system with the number of days, for example "Last 100 days".
 $lang["specificdays"]="Spezifische Anzahl von Tagen";
 $lang["specificdaterange"]="Spezifischer Zeitraum";
 $lang["to"]="bis";

 $lang["emailperiodically"]="Neue regelmäßige E-Mail erstellen";
 $lang["emaileveryndays"]="Diesen Bericht regelmäßig alle ? per E-Mail versenden";
 $lang["newemailreportcreated"]="Eine neue regelmäßige E-Mail wurde erstellt. Sie können die E-Mail stoppen, indem Sie den Link am Ende der E-Mail anklicken.";
 $lang["unsubscribereport"]="Um sich von diesem Bericht abzumelden, klicken Sie bitte diesen Link an:";
 $lang["unsubscribed"]="Abgemeldet";
 $lang["youhaveunsubscribedreport"]="Sie wurden von dieser regelmäßigen E-Mail abgemeldet.";
 $lang["sendingreportto"]="Sende Bericht an";
 $lang["reportempty"]="Keine passenden Daten für den ausgewählen Bericht und Zeitraum gefunden.";

 $lang["purchaseonaccount"]="Zum Konto hinzufügen";
 $lang["areyousurepayaccount"]="Sind Sie sicher, dass Sie diesen Einkauf zu Ihrem Konto hinzufügen wollen?";
 $lang["accountholderpayment"]="Zahlung Kontoinhaber";
 $lang["subtotal"]="Zwischensumme";
 $lang["discountsapplied"]="Angewendete Rabatte";
 $lang["log-p"]="Gekaufte Ressource";
 $lang["viauser"]="durch Benutzer";
 $lang["close"]="Schließen";

# Installation Check
 $lang["repeatinstallationcheck"]="Installation erneut überprüfen";
 $lang["shouldbeversion"]="sollte Version ? oder höher sein"; # E.g. "should be 4.4 or greater"
 $lang["phpinivalue"]="PHP.INI Wert für '?'"; # E.g. "PHP.INI value for 'memory_limit'"
 $lang["writeaccesstofilestore"]="Schreibzugriff auf $storagedir";
 $lang["nowriteaccesstofilestore"]="$storagedir nicht beschreibbar";
 $lang["writeaccesstohomeanim"]="Schreibzugriff auf $homeanim_folder";
 $lang["nowriteaccesstohomeanim"]="$homeanim_folder nicht beschreibbar. Ändern Sie die Berechtigungen, um die Beschnitt-Funktion des transform Plugins für die Startseiten-Animation zu ermöglichen.";
 $lang["blockedbrowsingoffilestore"]="Browsen des 'filestore' Verzeichnisses nicht erlaubt";
 $lang["noblockedbrowsingoffilestore"]="filestore scheint durchsuchbar zu sein; entfernen Sie 'Indexes' aus den Apache 'Options'.";
 $lang["executionofconvertfailed"]="Ausführung fehlgeschlagen; unerwartete Ausgabe des convert Befehls. Die Ausgabe war '?'.<br>Wenn Sie Windows und IIS 6 einsetzen, muss der Zugriff auf die Kommandozeile erlaubt werden. Bitte schauen Sie in den Installation Instructions im Wiki nach."; # ? will be replaced.
$lang["exif_extension"]="EXIF Erweiterung";
 $lang["lastscheduledtaskexection"]="Letzte geplante Ausführung der Aufgaben (Tage)";
 $lang["executecronphp"]="Relevanzberechnung wird nicht effektiv sein und regelmäßige E-Mail Berichte nicht versandt werden. Stellen Sie sicher, dass <a href='../batch/cron.php'>batch/cron.php</a> mindestens einmal täglich per cron job oder ähnlich gestartet wird.";
 $lang["shouldbeormore"]="sollte ? oder höher sein"; # E.g. should be 200M or greater

 $lang["generateexternalurl"]="Externe URL generieren";

 $lang["starsminsearch"]="Sterne (Minimum)";
 $lang["anynumberofstars"]="Beliebige Anzahl Sterne";

 $lang["noupload"]="Kein Upload";

# System Setup
# System Setup Tree Nodes (for the default setup tree)
 $lang["treenode-root"]="Root";
 $lang["treenode-group_management"]="Gruppenverwaltung";
 $lang["treenode-new_group"]="Neue Gruppe";
 $lang["treenode-new_subgroup"]="Neue Untergruppe";
 $lang["treenode-resource_types_and_fields"]="Ressourcen Typen / Felder";
 $lang["treenode-new_resource_type"]="Neuen Ressourcen Typ";
 $lang["treenode-new_field"]="Neues Feld";
 $lang["treenode-reports"]="Berichte";
 $lang["treenode-new_report"]="Neuer Bericht";
 $lang["treenode-downloads_and_preview_sizes"]="Download- / Vorschaugrößen";
 $lang["treenode-new_download_and_preview_size"]="Neue Download- / Vorschaugröße";
 $lang["treenode-database_statistics"]="Datenbank Statistiken";
 $lang["treenode-permissions_search"]="Suche nach Berechtigungen";
 $lang["treenode-no_name"]="(ohne Namen)";

 $lang["treeobjecttype-preview_size"]="Vorschaugröße";

 $lang["permissions"]="Berechtigungen";

# System Setup File Editor
 $lang["configdefault-title"]="(Optionen von hier kopieren und einfügen)";
 $lang["config-title"]="(BITTE BEACHTEN: Sollte diese Datei nicht mehr ausführbar sein (z.B. durch Syntaxfehler), muss der Fehler direkt auf dem Server behoben werden!)";

# System Setup Properties Pane
 $lang["file_too_large"]="Datei zu groß";
 $lang["field_updated"]="Feld aktualisiert";
 $lang["zoom"]="Zoom";
 $lang["deletion_instruction"]="Leer lassen und speichern, um die Datei zu löschen";
 $lang["upload_file"]="Datei hochladen";
 $lang["item_deleted"]="Eintrag gelöscht";
 $lang["viewing_version_created_by"]="Ansichtsversion erstellt durch";
 $lang["on_date"]="am";
 $lang["launchpermissionsmanager"]="Berechtigungs-Manager öffnen";
 $lang["confirm-deletion"]="Sind Sie sicher?";

# Permissions Manager
$lang["permissionsmanager"]="Berechtigungsmanager";
$lang["backtogroupmanagement"]="Zurück zur Gruppenverwaltung";
$lang["searching_and_access"]="Suchen / Zugriff";
$lang["metadatafields"]="Metadatenfelder";
$lang["resource_creation_and_management"]="Ressourcen verwalten";
$lang["themes_and_collections"]="Themen / Kollektionen";
$lang["administration"]="Administration";
$lang["other"]="Sonstiges";
$lang["custompermissions"]="Eigene Berechtigungen";
$lang["searchcapability"]="Suchmöglichkeiten";
$lang["access_to_restricted_and_confidential_resources"]="Kann eingeschränkte Ressourcen herunterladen und vertrauliche Ressourcen ansehen<br>(normalerweise nur Administratoren)";
$lang["restrict_access_to_all_available_resources"]="Zugriff auf alle verfügbaren Ressourcen einschränken";
$lang["can_make_resource_requests"]="Kann Ressourcen anfragen";
$lang["show_watermarked_previews_and_thumbnails"]="Vorschau/Thumbnails mit Wasserzeichen anzeigen";
$lang["can_see_all_fields"]="Kann alle Felder sehen";
$lang["can_see_field"]="Kann Feld sehen";
$lang["can_edit_all_fields"]="Kann alle Felder bearbeiten<br>(für bearbeitbare Ressourcen)";
$lang["can_edit_field"]="Kann Feld bearbeiten";
$lang["can_see_resource_type"]="Kann Ressourcen-Typ sehen";
$lang["restricted_access_only_to_resource_type"]="Eingeschränkter Zugriff nur auf Ressourcen-Typ";
$lang["edit_access_to_workflow_state"]="Zugriff auf Workflow Status";
$lang["can_create_resources_and_upload_files-admins"]="Kann Ressourcen erstellen / Dateien hochladen<br>(Administratoren; Ressourcen erhalten den Status 'Aktiv')";
$lang["can_create_resources_and_upload_files-general_users"]="Kann Ressourcen erstellen / Dateien hochladen<br>(Normale Benutzer; Ressourcen erhalten den Status 'Benutzer-Beiträge: Freischaltung noch nicht erledigt')";
$lang["can_delete_resources"]="Kann Ressourcen löschen<br>(die der Benutzer bearbeiten kann)";
$lang["can_manage_archive_resources"]="Kann archivierte Ressourcen verwalten";
$lang["can_tag_resources_using_speed_tagging"]="Kann Ressourcen via 'Speed Tagging' taggen<br>(falls konfiguriert)";
$lang["enable_bottom_collection_bar"]="Kollektionen erlauben ('Leuchtkasten')";
$lang["can_publish_collections_as_themes"]="Kann Kollektionen als Themen veröffentlichen";
$lang["can_see_all_theme_categories"]="Kann alle Themenkategorien sehen";
$lang["can_see_theme_category"]="Kann Themenkategorie sehen";
$lang["display_only_resources_within_accessible_themes"]="Bei Suchabfragen nur Ressourcen anzeigen, die sich in Themen befinden, auf die der Benutzer Zugriff hat";
$lang["can_access_team_centre"]="Kann auf die Administration zugreifen";
$lang["can_manage_research_requests"]="Kann Suchanfragen verwalten";
$lang["can_manage_resource_requests"]="Kann Ressourcenanfragen verwalten";
$lang["can_manage_content"]="Kann Inhalte verwalten (Intro/Hilfetexte)";
$lang["can_bulk-mail_users"]="Kann Massenmail senden";
$lang["can_manage_users"]="Kann Benutzer verwalten";
$lang["can_manage_keywords"]="Kann Stichworte verwalten";
$lang["can_access_system_setup"]="Kann auf die Systemeinstellungen zugreifen";
$lang["can_change_own_password"]="Kann das eigene Passwort ändern";
$lang["can_manage_users_in_children_groups"]="Kann nur Benutzer in Untergruppen zur eigenen Benutzergruppe verwalten";
$lang["can_email_resources_to_own_and_children_and_parent_groups"]="Kann Ressourcen nur an Benutzer aus der eigenen Gruppe oder Untergruppen weitergeben";

$lang["nodownloadcollection"]="Sie haben keinen Zugriff, um Ressourcen aus dieser Kollektion herunterzuladen.";

$lang["progress"]="Fortschritt";
$lang["ticktodeletethisresearchrequest"]="Auswählen, um diese Anfrage zu löschen";

# SWFUpload
$lang["queued_too_many_files"]="Sie haben versucht, zu viele Dateien hochzuladen.";
$lang["creatingthumbnail"]="Erstelle Thumbnail...";
$lang["uploading"]="Hochladen...";
$lang["thumbnailcreated"]="Thumbnail erstellt.";
$lang["done"]="Fertig.";
$lang["stopped"]="Abgebrochen."; 

$lang["latlong"]="Breite / Länge";
$lang["geographicsearch"]="Geographische Suche";

$lang["geographicsearch_help"]="Ziehen, um einen Suchbereich zu erstellen.";

$lang["purge"]="Bereinigen";
$lang["purgeuserstitle"]="Benutzer bereinigen";
$lang["purgeusers"]="Benutzer bereinigen";
$lang["purgeuserscommand"]="Benutzer-Accounts löschen, die in den letzten % Monaten nicht aktiv waren, aber vor diesem Zeitraum erstellt wurden.";
$lang["purgeusersconfirm"]="% Benutzer-Accounts löschen. Sind Sie sicher?";
$lang["pleaseenteravalidnumber"]="Bitte geben Sie eine gültige Zahl ein";
$lang["purgeusersnousers"]="Keine Benutzer-Accounts zu bereinigen.";

$lang["editallresourcetypewarning"]="Warnung: Durch das Ändern des Ressourcen-Typs werden sämtliche spezifischen Metadaten für den jetzigen Ressourcen-Typ der Ressource gelöscht.";

$lang["geodragmode"]="Zieh-Modus";
$lang["geodragmodearea"]="Auswahl";
$lang["geodragmodepan"]="schwenken";

$lang["substituted_original"] = "ersetztes Original";
$lang["use_original_if_size"] = "Original benutzen, wenn die ausgewählte Größe nicht verfügbar ist?";

$lang["originals-available-0"] = "verfügbar"; # 0 (originals) available
$lang["originals-available-1"] = "verfügbar"; # 1 (original) available
$lang["originals-available-2"] = "verfügbar"; # 2+ (originals) available

$lang["inch-short"] = "in";
$lang["centimetre-short"] = "cm";
$lang["megapixel-short"]="MP";
$lang["at-resolution"] = "@"; # E.g. 5.9 in x 4.4 in @ 144 PPI

$lang["deletedresource"] = "Gelöschte Ressource";
$lang["deletedresources"] = "Gelöschte Ressourcen";
$lang["action-delete_permanently"] = "Dauerhaft löschen";

$lang["horizontal"] = "Horizontal";
$lang["vertical"] = "Vertikal";

$lang["cc-emailaddress"] = "CC %emailaddress"; # %emailaddress will be replaced, e.g. CC [your email address]

$lang["sort"] = "Sortieren";
$lang["sortcollection"] = "Kollection sortieren";
$lang["emptycollection"] = "Leere Kollektion";
$lang["emptycollectionareyousure"]="Sind Sie sicher, dass Sie alle Ressourcen aus dieser Kollektion entfernen wollen?";

$lang["error-cannoteditemptycollection"]="Sie können eine leere Kollektion nicht bearbeiten.";
$lang["error-permissiondenied"]="Zugriff verweigert.";
$lang["error-collectionnotfound"]="Kollektion nicht gefunden.";

$lang["header-upload-subtitle"] = "Schritt %number: %subtitle"; # %number, %subtitle will be replaced, e.g. Step 1: Specify Default Content For New Resources
$lang["local_upload_path"] = "Lokaler Upload Ordner";
$lang["ftp_upload_path"] = "FTP Ordner";
$lang["foldercontent"] = "Ordnerinhalt";
$lang["intro-local_upload"] = "Wählen Sie eine oder mehrere Dateien vom lokalen Upload Ordner aus und klicken Sie auf <b>Upload</b>. Nachdem die Dateien hochgeladen sind, können Sie aus dem Upload Ordner gelöscht werden.";
$lang["intro-ftp_upload"] = "Wählen Sie eine oder mehrere Dateien vom FTP Ordner aus und klicken Sie <b>Upload</b> an.";
$lang["intro-java_upload"] = "Klicken Sie auf <b>Durchsuchen</b>, um eine oder mehrere Dateien auszuwählen, und klicken Sie dann <b>Upload</b> an.";
$lang["intro-swf_upload"] = "Klicken Sie auf <b>Upload</b>, um eine oder mehrere Dateien auszuwählen. Halten Sie die Shift-Taste gedrückt, um mehrere Dateien auszuwählen.";
$lang["intro-single_upload"] = "Klicken Sie auf <b>Durchsuchen</b>, um eine Datei auszuwählen, und klicken Sie dann <b>Upload</b> an.";
$lang["intro-batch_edit"] = "Bitte wählen Sie die Standard-Uploadeinstellungen und die Standardwerte für die Metadaten der Ressourcen, die Sie hochladen wollen.";

$lang["collections-1"] = "(<strong>1</strong> Kollektion)";
$lang["collections-2"] = "(<strong>%number</strong> Kollektionen)"; # %number will be replaced, e.g. 3 Collections
$lang["total-collections-0"] = "<strong>Gesamt: 0</strong> Kollektionen";
$lang["total-collections-1"] = "<strong>Gesamt: 1</strong> Kollektion";
$lang["total-collections-2"] = "<strong>Gesamt: %number</strong> Kollektionen"; # %number will be replaced, e.g. Total: 5 Collections
$lang["owned_by_you-0"] = "(<strong>0</strong> eigene)";
$lang["owned_by_you-1"] = "(<strong>1</strong> eigene)";
$lang["owned_by_you-2"] = "(<strong>%mynumber</strong> eigene)"; # %mynumber will be replaced, e.g. (2 owned by you)

$lang["listresources"]= "Ressourcen:";
$lang["action-log"]="Log anzeigen";

$lang["saveuserlist"]="Diese Liste speichern";
$lang["deleteuserlist"]="Diese Liste löschen";
$lang["typeauserlistname"]="Geben Sie einen Namen ein...";
$lang["loadasaveduserlist"]="Gespeicherte Benutzerliste laden";

$lang["searchbypage"]="Seite suchen";
$lang["searchbyname"]="Namen suchen";
$lang["searchbytext"]="Text suchen";
$lang["saveandreturntolist"]="Speichern und zurück zur Liste";
$lang["backtomanagecontent"]="Zurück zu Inhalte verwalten";
$lang["editcontent"]="Inhalt bearbeiten";

$lang["confirmcollectiondownload"]="Bitte warten Sie, bis das ZIP-Archiv erstellt wird. Dieser Vorgang kann abhängig von der Gesamtgröße der Ressourcen eine Weile dauern.";

$lang["starttypingkeyword"]="Geben Sie den Anfang eines Stichworts ein...";
$lang["createnewentryfor"]="Neuen Eintrag erstellen für";
$lang["confirmcreatenewentryfor"]="Sind Sie sicher, dass Sie einen neuen Eintrag für '%%' in der Stichwortliste erstellen wollen?";

$lang["editresourcepreviews"]="Ressourcenvorschau bearbeiten";

$lang["can_assign_resource_requests"]="Kann Ressourcenanfragen zuweisen";
$lang["can_be_assigned_resource_requests"]="Kann Ressourcenanfragen zugewiesen bekommen (und nur die zugewiesenen Ressourcenanfragen sehen)";

$lang["declinereason"]="Grund für Ablehnung";
$lang["requestnotassignedtoyou"]="Diese Anfrage ist Ihnen nicht länger zugewiesen. Sie ist nun Benutzer % zugewiesen.";
$lang["requestassignedtoyou"]="Ressourcenanfrage zugewiesen";
$lang["requestassignedtoyoumail"]="Eine Ressourcenanfrage wurde Ihnen zur Freigabe zugewiesen. Bitte nutzen Sie den untenstehenden Link, um die Ressourcenanfrage zu erlauben oder abzulehnen.";

$lang["manageresources-overquota"]="Ressourcenverwaltung deaktiviert – Sie haben Ihr Datenvolumen überschritten";
$lang["searchitemsdiskusage"]="Verbrauchten Speicherplatz dieser Ergebnisse berechnen";
$lang["matchingresourceslabel"]="Passende Ressourcen";