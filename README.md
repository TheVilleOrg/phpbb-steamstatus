# phpBB Steam Status Extension

This is a extension for phpBB 3.2 that allows users to link their Steam account to their account in order to have their current Steam profile status displayed on their forum profile and/or posts.

[![Build Status](https://travis-ci.org/stevotvr/phpbb-steamstatus.svg)](https://travis-ci.org/stevotvr/phpbb-steamstatus)
[![Code Climate](https://codeclimate.com/github/stevotvr/phpbb-steamstatus/badges/gpa.svg)](https://codeclimate.com/github/stevotvr/phpbb-steamstatus)

## Features

* Adds a panel to the UCP under the Profile tab with a button to connect your account to Steam
* Option to display current status on user profiles and/or in the user section of each post
* Auto-refresh status at a configurable interval

## Install

1. [Download the latest validated release](https://www.phpbb.com/customise/db/extension/steamstatus/).
2. Unzip the downloaded release and copy it to the `ext` directory of your phpBB board.
3. Navigate in the ACP to `Customise -> Manage extensions`.
4. Look for `Steam Status` under the Disabled Extensions list, and click its `Enable` link.
5. Set up and configure Steam Status by navigating in the ACP to `Extensions` -> `Steam Status`.
   * Set your `Steam Web API Key` to the your key obtained from https://steamcommunity.com/dev/apikey.

## Uninstall

1. Navigate in the ACP to `Customise -> Extension Management -> Extensions`.
2. Look for `Steam Status` under the Enabled Extensions list, and click its `Disable` link.
3. To permanently uninstall, click `Delete Data` and then delete the `/ext/stevotvr/steamstatus` directory.

## Support

* **Important: Only official release versions validated by the phpBB Extensions Team should be installed on a live forum. Pre-release (beta, RC) versions downloaded from this repository are only to be used for testing on offline/development forums and are not officially supported.**
* Report bugs and other issues to our [Issue Tracker](https://github.com/stevotvr/phpbb-steamstatus/issues).
* Support requests should be posted and discussed in the [Steam Status topic at phpBB.com](https://www.phpbb.com/customise/db/extension/steamstatus/support).

## Translations

* Translations should be posted to the [Steam Status topic at phpBB.com](https://www.phpbb.com/customise/db/extension/steamstatus/support/topic/194671).

## Donate

If you find this extension useful, please consider supporting the project by donating.

[![Donate via PayPal](https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=GTP9FMP7M75E2)

## License
[GNU General Public License v2](https://opensource.org/licenses/GPL-2.0)
