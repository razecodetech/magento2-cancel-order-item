# Magento 2 Cancel Order Item

Magento 2 extension to cancel single Item or Product from an Order in Magento Admin Dashboard

[![Latest Stable Version](https://poser.pugx.org/razecode/cancelorderitem/v/stable)](https://packagist.org/packages/razecode/cancelorderitem)
[![Total Downloads](https://poser.pugx.org/razecode/cancelorderitem/downloads)](https://packagist.org/packages/razecode/cancelorderitem)

## How to install & upgrade Razecode_CancelOrderItem

### 1. Install via composer (recommend)

We recommend you to install Razecode_CancelOrderItem module via composer. It is easy to install, update and maintaince.

Run the following command in Magento 2 root folder.

#### 1.1 Install

```
composer require razecode/cancelorderitem
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

#### 1.2 Upgrade

```
composer update razecode/cancelorderitem
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

Run compile if your store in Production mode:

```
php bin/magento setup:di:compile
```

### 2. Copy and paste

If you don't want to install via composer, you can use this way. 

- Download [the latest version here](https://github.com/razecodetech/magento2-cancel-order-item/archive/master.zip) 
- Extract `master.zip` file to `app/code/Razecode/CancelOrderItem` ; You should create a folder path `app/code/Razecode/CancelOrderItem` if not exist.
- Go to Magento root folder and run upgrade command line to install `Razecode_CancelOrderItem`:

```
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

