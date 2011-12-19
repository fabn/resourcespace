Search API

Usage:
http://url/plugins/api_search/?key=[authkey]&[optional parameters]

Parameters:
help=true                display this file
search=[string]          Return results of any valid search string (default "")
restypes=[string]        Limit search results to specific Resource Types (comma-separated list) (default all)
order_by=[string]        Order by, ex. (colour, popularity, field12) (default= relevance)
sort=[string]            Sort order ("ASC" or "DESC")
archive=[integer]        Archive Status (default 0 (active))
starsearch=[integer]     Minimum # of stars (User Rated)
previewsize=[string]     return a 'preview' url (ex: "thm","col","pre","scr")
flvfile=[string]         return a 'flvpath' and 'flvthumb' urls (ex. "true")
content=[string]         Return results as json or xml (default json without json headers)
videosonly=[boolean]     Omit results without an flvthumb and flvpath
page=[int]               Select page of paginated results (changes result structure to pagination style)
results_per_page=[int]   Paginate results (changes result structure to pagination style) (default 15)


If a signature is required, you must md5([yourhashkey].[querystring]) and submit it as a final parameter called skey.
Your hash key is a shared secret available from plugins/api_core.
The query string you hash this with must not include a leading '?', and must not include an skey parameter.

The simplest example of a signed call is:
url/plugins/api_search/?key=aBCdEf...&skey=<?php echo md5("yourhashkey"."key=aBCdEf...")?>
