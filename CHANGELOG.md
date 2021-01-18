# Changelog

## 1.3.2 (1/17/21)

* Fixed error caused by missing lastlogoff field in the API response

## 1.3.1 (10/1/19)

* Fixed invalid HTML
* Removed local certificate authority file

## 1.3.0 (9/3/19)

* Added support for the cURL extension
* Added local certificate authority file
* Removed HTTPS setting from the ACP
* Updated OpenID URL generation code

## 1.2.2 (1/9/19)

* Fixed broken image link with some board configurations

## 1.2.1 (10/5/18)

* Added event for when a user changes their SteamID
* Fixed missing ACP module permission
* Fixed CSS compatibility issues
* Moved the image path out of the language file and updated the file structure

## 1.2.0 (8/23/18)

* Fixed OpenID redirect URL
* Updated error messages for connection errors

## 1.2.0-beta1 (7/27/18)

* Added Steam OpenID to authenticate and retrieve user SteamIDs
* Replaced the SteamID input fields with a UCP module
* Added user permission to control access to the extension
* Added option to disable HTTPS in the backend
* Fixed errors in topics that contain guest posts
* Fixed PHP errors when a connection fails
* Fixed storage errors caused by special characters in a display name

## 1.1.5 (1/5/18)

* Fixed malformed profile names when posts are first loaded

## 1.1.4 (12/9/17)

* Fixed errors caused by Unicode characters larger than 3 bytes
* Fixed some vanity names being erroneously reported as invalid
* Added French translation

## 1.1.3 (11/4/17)

* Fixed status text for in-game statuses on the profile page
* Separated CSS specific to the Prosilver style
* Prosilver: Hid the status block in the post profile area when the screen is small

## 1.1.2 (10/28/17)

* Fixed compatibility with PHP 5.4

## 1.1.1 (9/28/17)

* Changed all URLs to use https (if available)

## 1.1.0 (9/11/17)

* Added option to show the SteamID field on the user registration form
* Added Spanish translation [[Raul [ThE KuKa]](https://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=94590)]
* Fixed some layout issues on smaller screens

## 1.0.0 (8/12/17)

* Initial release
