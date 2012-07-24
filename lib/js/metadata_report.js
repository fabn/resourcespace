function metadataReport(ref) {
	jQuery('#metadata_report').load(
		"ajax/metadata_report.php?ref="+ref
		);
	}
