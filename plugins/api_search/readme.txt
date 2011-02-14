Search API

Usage:
http://url/plugins/api_search?key=[authkey]&[optional parameters]

Parameters:
help=true                display this file
search=[string]          Return results of any valid search string (default "")
restypes=[string]        Limit search results to specific Resource Types (comma-separated list) (default all)
order_by=[string]        Order by, ex. (!recent, !last50, colour, popularity, field12) (default relevance)
sort=[string]            Sort order ("ASC" or "DESC")
archive=[integer]        Archive Status (default 0 (active))
starsearch=[integer]     Minimum # of stars (User Rated)
previewsize=[string]     return a 'preview' url (ex: "thm")
content=[string]         Return results as json with json headers (xml not implemented yet)
