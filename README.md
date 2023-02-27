# MaxRedemption Payment Module for Magento 2

[![Version](https://img.shields.io/github/release/ynloultratech/maxredemption-magento.svg)](https://github.com/ynloultratech/maxredemption-magento)
[![Magento](https://img.shields.io/badge/Magento-2.1+-blue.svg)](http://magento.com)
[![License](https://img.shields.io/github/license/ynloultratech/maxredemption-magento.svg)](https://github.com/ynloultratech/maxredemption-magento/blob/master/LICENSE)

### Requirement & Compatibility
- Requires magento version at least: `2.3` and `2.4`
- Tested and working upto `Magento 2.4.5`

### Installation
- Download the MaxRedemption extension as zip file from [here (maxredemption.zip)](https://github.com/ynloultratech/maxredemption-magento/releases/latest). Make sure you download the most recent version.
- Create the following folder structure inside `app/code` folder `MaxRedemption/MaxRedemptionPayment`
- Unzip contents into `app/code/MaxRedemption/MaxRedemptionPayment` folder
- After you have all the files your folder structure should be like this `app/code/MaxRedemption/MaxRedemptionPayment/composer.json`
- From the server terminal, navigate to the root Magento directory and run  `php bin/magento module:enable MaxRedemption_MaxRedemptionPayment`
- Run Setup Upgrade
  `php bin/magento setup:upgrade`
- Run DI Compilation to generate classes
  `php bin/magento setup:di:compile`
- If you are on Production Environment, make sure you run the following command as well
  `php bin/magento setup:static-content:deploy`
- Finally Flush the Cache
  `php bin/magento cache:flush`

### Configuration
- In Your Magento Store Management Console, enable the MaxRedemption Payment Module
