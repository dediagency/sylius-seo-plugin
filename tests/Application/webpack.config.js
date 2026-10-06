const fs = require('fs');
const path = require('path');

const vendorDir = path.resolve(__dirname, '../../vendor/sylius/sylius/src/Sylius/Bundle');

// Sylius 2.0 ships its bundle assets without package.json and is built against Tabler 1.0.
const isSylius20 = !fs.existsSync(path.join(vendorDir, 'AdminBundle/Resources/assets/package.json'));

// Since Sylius 2.1, bundle assets are resolved as the "@sylius/admin-bundle" and "@sylius/shop-bundle" packages.
// They live in vendor and depend on the installed Sylius version, so they are linked here instead of in package.json.
[['admin-bundle', 'AdminBundle'], ['shop-bundle', 'ShopBundle']].forEach(([name, bundle]) => {
    const target = path.join(vendorDir, bundle, 'Resources/assets');
    const link = path.resolve(__dirname, 'node_modules/@sylius', name);

    if (isSylius20 || fs.existsSync(link)) {
        return;
    }

    // Drop a dangling link left by a previously installed Sylius version
    fs.rmSync(link, { force: true });
    fs.mkdirSync(path.dirname(link), { recursive: true });
    fs.symlinkSync(target, link, 'dir');
});

// Since Sylius 2.3, the webpack helpers live in Resources/npm (the bundle root entry point is deprecated).
const syliusPackage = (bundle) => {
    const npmDir = path.join(vendorDir, bundle, 'Resources/npm');

    return require(fs.existsSync(npmDir) ? npmDir : path.join(vendorDir, bundle));
};

const SyliusAdmin = syliusPackage('AdminBundle');
const SyliusShop = syliusPackage('ShopBundle');

const adminConfig = SyliusAdmin.getWebpackConfig(path.resolve(__dirname));
const shopConfig = SyliusShop.getWebpackConfig(path.resolve(__dirname));

if (isSylius20) {
    adminConfig.resolve.alias = {
        ...adminConfig.resolve.alias,
        '@tabler/core': path.resolve(__dirname, 'node_modules/@tabler/core-1.0'),
    };
}

module.exports = [shopConfig, adminConfig];
