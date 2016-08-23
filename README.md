Flickr Browser Field for ACF Pro
=============

## Description
Browse and include photos from your Flickr collections and albums. The plugin will generate the image size urls and other metadata you select.

## Notice
- Currently, this plugin does not work with private photos.
- This add-on has only been tested on [Advanced Custom Fields Pro](https://www.advancedcustomfields.com/pro/).

## Installation
Download and unzip to /plugins/acf-flickr-browser

## Usage Example
**Getting a single photo**
	$photo = get_field(FIELD_NAME);
	echo $photo['title'];
	echo '<img src="' . $photo['url_o'] . '" />';

**Getting multiple photos**
	$photos = get_field(FIELD_NAME);
	foreach ($photos as $photo) {
    echo $photo['title'];
  	echo '<img src="' . $photo['url_o'] . '" />';
	}

## Supported Sizes and Metadata Fields
All fields that are supported by the Flickr API are available:
```
license, date_upload, date_taken, owner_name, icon_server, original_format, last_update, geo, tags, machine_tags, o_dims, views, media, path_alias, url_sq, url_t, url_s, url_m, url_o
```
