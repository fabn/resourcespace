function metadataReport(ref,ext) {
	new Ajax.Updater(
		{success: 'metadata_report'},
		"ajax/metadata_report.php?ref="+ref+"&ext="+ext,
		{method: 'get'});
		}