// Functions to support frameless collections.

function ChangeCollection(collection)
	{
	// Set the collection and update the count display
	new Ajax.Updater('CollectionFrameless', baseurl_short + 'pages/ajax/collections_frameless_loader.php?collection=' + collection, { method: 'post' });
	}
	
function UpdateCollectionDisplay()
	{
	// Update the collection count display
	new Ajax.Updater('CollectionFrameless', baseurl_short + 'pages/ajax/collections_frameless_loader.php', { method: 'post' });
	}

function AddResourceToCollection(resource)
	{
	new Ajax.Updater('CollectionFrameless', baseurl_short + 'pages/ajax/collections_frameless_loader.php?add=' + resource, { method: 'post' });
	}
	
function RemoveResourceFromCollection(resource,pagename)
	{
	new Ajax.Updater('CollectionFrameless', baseurl_short + 'pages/ajax/collections_frameless_loader.php?remove=' + resource + '&pagename=' + pagename, { method: 'post', evalScripts: true });
	}
