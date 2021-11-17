### Setup

Development
```
chmod +x docker/clinotty docker/setup-composer-auth
docker/setup-composer-auth
docker/setup

# to remove magento two factor auth
bin/magento module:disable Magento_TwoFactorAuth
bin/magento cache:flush 
```

Note :
```
docker/clinotty php -d memory_limit=-1 bin/magento setup:install
docker/clinotty php -d memory_limit=-1 bin/magento setup:upgrade

docker/clinotty php -d memory_limit=-1 bin/magento sampledata:remove

docker/clinotty php bin/magento cache:flush
```
