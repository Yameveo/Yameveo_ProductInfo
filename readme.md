Yameveo ProductInfo Extension
=====================
Simple SOAP API Extension
Facts
-----
- version: 1.0.0
- extension key: Yameveo_ProductInfo
- Magento Connect 1.0 extension key: Not yet on Magento Connect
- Magento Connect 2.0 extension key: Not yet on Magento Connect
- [extension on GitHub](https://github.com/yameveo/Yameveo_ProductInfo

Description
-----------
This Magento extensions add some features to catalogProductInfo SOAP API call for V1 and V2. Get all product's attributes, including stock info and images. Get configurable's subproducts informations in the same response.

Usage
----------

SOAP API V1
```php
// connect to soap server
$client = new SoapClient('http://store.dev/api/soap?wsdl=1',  array('cache_wsdl' => WSDL_CACHE_NONE, 'trace' => 1));

// log in
$session = $client->login('user', 'apikey');

$product = $client->call($session, 'catalog_product.info', array(126, null, array('*')));
```

SOAP API V2
```php
// connect to soap server
$client = new SoapClient('http://store.dev/api/v2_soap?wsdl=1',  array('cache_wsdl' => WSDL_CACHE_NONE, 'trace' => 1));

// log in
$session = $client->login('user', 'apikey');

$attributes = new stdclass();
$attributes->attributes = array('*');
$product = $client->catalogProductInfo($session, 126, null, $attributes);
```

Requirements
------------
- PHP >= 5.2.0
- Mage_Core
- Mage_Api

Compatibility
-------------
- Magento >= 1.6

Installation Instructions
-------------------------
1. Install [modman](https://github.com/colinmollenhour/modman)
2. cd /var/www/store      # Magento is installed here
3. modman init            # This is only done once in the application root
4. modman clone git@github.com:Yameveo/Yameveo_ProductInfo.git

The module will also be published on composer firegento repository.

Uninstallation
--------------
1. Remove all extension files from your Magento installation

Support
-------
If you have any issues with this extension, open an issue on [GitHub](https://github.com/yameveo/Yameveo_ProductInfo/issues).

Contribution
------------
Any contribution is highly appreciated. The best way to contribute code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).

Developer
---------
Andrea De Pirro [@akira28](https://twitter.com/akira28)

[http://www.yameveo.com](http://www.yameveo.com)



Licence
-------
[OSL - Open Software Licence 3.0](http://opensource.org/licenses/osl-3.0.php)
