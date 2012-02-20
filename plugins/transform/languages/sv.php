<?php
# Swedish
# Language File for the Transform Plugin
# Updated by Henrik Frizén 20120220 for svn r3224
# -------
#
#
# Resource log - actions
$lang["log-a"]="Skapade alternativ fil";

$lang['transform']="Transformera";
$lang['transformimage']="Transformera bild";
$lang['transformed']="Transformerad";
$lang['transformblurb']="Ange eventuell beskärning genom att rita en rektangulär markering direkt på bilden. Du kan sedan flytta och ändra storlek på markeringen. När du är klar anger du ett filnamn för den nya beskurna bilden och klickar på <b>Spara som alternativ fil</b> eller <b>Hämta</b>. Du kan även ange en bredd och/eller höjd för att skala om den beskurna bilden.<br/><br/><strong>Formuläret nedan kan lämnas tomt.</strong> Om du inte anger en bredd och höjd kommer markeringens bredd och höjd att användas.";
$lang['width']="Bredd";
$lang['height']="Höjd";
$lang['px']="px";
$lang['noimagefound'] = "Fel: Ingen bild hittades.";
$lang['scaled'] = "Skalad";
$lang['cropped'] = "Beskuren";
$lang['nonnumericcrop'] = "Fel: En icke-numerisk beskärning begärdes.";
$lang['newfilename'] = "Filnamn";
$lang['errorspecifiedbiggerthanoriginal'] = "Fel: Den angivna bredden eller höjden är större än i originalbilden.";
$lang['errormustchoosecropscale'] = "Fel: Du måste ange en beskärning och/eller ange en ny bredd eller höjd.";
$lang['savealternative']="Spara som alternativ fil";
$lang['rotation']="Rotation";
$lang['rotation0']="Ingen rotation";
$lang['rotation90']="90° medurs";
$lang['rotation180']="180°";
$lang['rotation270']="90° moturs";
$lang['fliphorizontal']="Vänd horisontellt:";
$lang['transform_original']="Transformera original";
$lang['priorversion']="Tidigare version";
$lang['replaced']="Ersattes";
$lang['replaceslideshowimage']="Ersätt bild i bildspel";
$lang['slideshowsequencenumber']="Ordningsnummer (1, 2, 3 etc.)";
$lang['transformcrophelp']="Ange eventuell beskärning genom att rita en rektangulär markering direkt på bilden till vänster.";
$lang['originalsize']="Originalstorlek";
$lang['allow_upscale']="Tillåt förstoring?";
$lang['batchtransform'] = "Transformera i grupp";
$lang['batchtransform-introtext']="<strong>Varning! Ändringarna av materialen blir permanenta.</strong>";
$lang['error-crop-imagemagick-not-configured']="Fel: ImageMagick måste vara konfigurerat för att beskärning ska vara möjlig. Kontakta systemadministratören.";
$lang['no_resources_found']="inga material hittade";
$lang['batch_transforming_collection']="Transformerar samling %col"; # %col will be replaced with the collection id
$lang['not-transformed']="transformerades inte: Åtkomst nekades.";
$lang['error-unable-to-rename']="Fel: Kunde inte byta namn på den transformerade filen för material %res."; # %res will be replaced with the resource id
$lang['success']="Klar!";
$lang['error-transform-failed']="Fel: Transformering av material %res misslyckades."; # %res will be replaced with the resource id
$lang['summary']="Sammanfattning";
$lang['resources_in_collection-1']="1 material i samlingen.";
$lang['resources_in_collection-2']="%qty material i samlingen."; # %qty will be replaced with the quantity of resources in collection
$lang['resources_transformed_successfully-0']="Inga material transformerade.";
$lang['resources_transformed_successfully-1']="1 material transformerat.";
$lang['resources_transformed_successfully-2']="%qty material transformerade."; # %qty will be replaced with the quantity of transformed resources
$lang['errors-1']="1 fel.";
$lang['errors-2']="%qty fel."; # %qty will be replaced with the quantity of errors

$lang['transform_configuration']="Transform &ndash; inställningar";
$lang['cropper_debug']="Felsökningsläge";
$lang['output_formats']="Utdataformat";
$lang['input_formats']="Indataformat";
$lang['custom_filename']="Egna filnamn";
$lang['allow_rotation']="Tillåt rotation";
$lang['allow_transform_original']="Tillåt transformation av original";
$lang['use_repage']="Använd argumentet ’repage’";
$lang['enable_batch_transform']="Aktivera transformering i grupp";
