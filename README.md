[<img src="https://www.arvato-systems.de/resource/crblob/192802/c1761df5c2dd23860dde84dcc0a7189f/arvato-logo-svg-data.svg" alt="Logo" title="Arvato Systems GmbH" width="250px"/>](https://www.arvato-systems.de/ "Arvato Systems GmbH")

# Yet Another Confluence REST API

![Latest Version](https://img.shields.io/badge/release-latest-blue?logo=github&style=plastic "Latest Version")
![Software License](https://img.shields.io/github/license/oglowa/ya-corapi?logo=github&style=plastic "Software License")
![GitHub Release](https://img.shields.io/github/v/release/oglowa/ya-corapi?logo=github&style=plastic "GitHub Release")

![Language Count](https://img.shields.io/github/languages/count/oglowa/ya-corapi?logo=github&style=plastic "Language Count")
![Language Top](https://img.shields.io/github/languages/top/oglowa/ya-corapi?logo=github&style=plastic "Language Top")
![Commit Status Master](https://img.shields.io/github/checks-status/oglowa/ya-corapi/master?logo=github&label=checks%20master&style=plastic "Commit Status Master")
![Commit Status Develop](https://img.shields.io/github/checks-status/oglowa/ya-corapi/develop?logo=github&label=checks%20develop&style=plastic "Commit Status Develop")


(c) 2024 Oliver Glowa / Arvato Systems GmbH

# Description

Managing the maintenance of a confluence instance via REST API.

# Requirements

- Commandline
  - Linux / Unix Shell or
  - Windows Shell
- [PHP](https://www.php.net)
- [Composer](https://getcomposer.org/) (optional)

# How to use it

## Unix / Linux

- Clone this repository to your local drive
- Create the folder 
  - `mkdir ${HOME}/.restapi`
- Copy the file to the created folder  
  - `cp ./cfg/myauth.inc.tpl ${HOME}/.restapi/myauth.inc.php`
- Edit the file `${HOME}/.restapi/myauth.inc.php`
  - Replace `#confluence-url_for_production#` with your Confluence URL for the production environment
  - Replace `#confluence-url_for_testing#` with your Confluence URL for the testing environment

## Windows

- Clone this repository to your local drive
- Create the folder 
  - `mkdir %USERPROFILE%\.restapi`
- Copy the file to the created folder
  - `copy .\cfg\myauth.inc.tpl %USERPROFILE%\.restapi\myauth.inc.php` 
- Edit the file `%USERPROFILE%\.restapi\myauth.inc.php`
  - Replace `#confluence-url_for_production#` with your Confluence URL for the production environment
  - Replace `#confluence-url_for_testing#` with your Confluence URL for the testing environment

# Notice

- Examples are working with privat access token (PAT) only!
- Tested & Working with the Confluence Data Center Edition (DCE)
- By default the requests are sent to the URL of `#confluence-url_for_testing#`
  - Edit the file `${HOME}/.restapi/myauth.inc.php` (`%USERPROFILE%\.restapi\myauth.inc.php`)
    - Set `define("USE_PROD", false)` to `define("USE_PROD", true)` 

