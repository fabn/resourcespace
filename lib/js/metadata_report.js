function metadataReport(ref) {
	new Ajax.Updater(
		{success: 'metadata_report'},
		"ajax/metadata_report.php?ref="+ref,
		{method: 'get'});
		}
