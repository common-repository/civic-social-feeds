=== Civic Social Feeds ===
Contributors: glympe, ralliaf, tasoscivicuk
Tags: social feeds, feeds, facebook, instagram, twitter
Plugin URI: https://www.civicuk.com
Author: Civicuk
Requires at least: 3.0
Tested up to: 5.7
Requires PHP: 5.6
Stable tag: 1.1.0
Version: 1.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin provides Wordpress administrators a configuration page to set up credentials for various social networks in order to access API’s and gets feeds to display.
It also provides a simple way for users to display their feeds via shortcodes.

== Description ==

#### Twitter
###### Admin Panel Data
  - Consumer Key
  - Consumer Secrer
  - Username
  - Number of tweets


#### Facebook
###### Admin Panel Data
  - Page ID or Page Name
  - Page Access Token
  - URL `'https://graph.facebook.com/${pageID}?fields=id,name,feed{full_picture,type,message,description,link,status_type,created_time}&access_token=${pageAccessToken}`

###### Get Facebook Page Access Token
 - Create App `https://developers.facebook.com/`
 - You need to have admin priviledge on Facebook Page + developer priviledge on Facebook App
 - Add App Domain (website URL to Facebook App - Settings - Basic)
 - Get User Access Token `"https://graph.facebook.com/oauth/access_token
  ?client_id=your-app-id
  &client_secret=your-app-secret
  &grant_type=client_credentials"` (get app id and secret from Facebook App)
- Use User Access Token on this url `https://graph.facebook.com/me/accounts?access_token=user-access-token` to get a list of pages the person administers, including the name of page, page id and page access token

Currently supported social networks

*   Twitter
*   Facebook

** Important Note : Instagram feed has been removed, please don't forget to remove [civic_social_feeds type='instagram'] shortcode.

== Installation ==

1. Upload `civic-social-feeds` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= What about support? =

Create a support ticket at WordPress forum and we will take care of any issue.

== Screenshots ==

1. Plugin in action at main editor section.

== Changelog ==
= 1.1 =
* Removed Instagram Feed.
* Fixed general bugs
= 1.0 =
* Initial Release
