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
previewsize=[string]     return a 'preview' url (ex: "thm")
content=[string]         Return results as json or xml (default json without json headers)

If a signature is required, you must md5([yourhashkey].[querystring]) and submit it as a final parameter called skey.
Your hash key is a shared secret available from plugins/api_core.
The query string you hash this with must not include a leading '?', and must not include an skey parameter.

The simplest example of a signed call is:
url/plugins/api_search/?key=aBCdEf...&skey=md5("yourhaskey"."key=aBCdEf...")
