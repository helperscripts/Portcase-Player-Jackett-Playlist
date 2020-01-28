# Portcase Player Jackett Playlist

This simple script helps to convert [Jackett](https://github.com/Jackett/Jackett) torrents feed to [Portcase Player](https://play.google.com/store/apps/details?id=com.portcase.player.android) torrent playlist.

To use it, you need your own Jackett server instance running and php hosting for this script.

Installation:

- Clone this repository
- Run `composer install`
- Modify parameters `$jackettUrl`, `jackettApiKey`, `playlistSearchUrl` in `index.php` file

Usage:

To open playlist, call *https://FULL_URL_TO/playlist/?t=`[TRACKER_NAME]`&q=`[YOUR_QUERY]`&f=`[xml|json]`*

Request parameters:

- `[TRACKER_NAME]` - the name of torrent tracker, taken from Jackett. If empty, `"all"` will be used.
- `[YOUR_QUERY]` - your search query. If empty, `"CURRENT_YEAR 1080p"` will be used.
- `[xml|json]` - format of returned playlist, `xml` or `json`. If empty `"json"` is used by default.
