const path = require('path');
const Encore = require('@symfony/webpack-encore');

const SyliusAdmin = require('../../vendor/sylius/sylius/src/Sylius/Bundle/AdminBundle/index.js');
const SyliusShop = require('../../vendor/sylius/sylius/src/Sylius/Bundle/ShopBundle/index.js');

const adminConfig = SyliusAdmin.getWebpackConfig(path.resolve(__dirname));
const shopConfig = SyliusShop.getWebpackConfig(path.resolve(__dirname));

module.exports = [shopConfig, adminConfig];
