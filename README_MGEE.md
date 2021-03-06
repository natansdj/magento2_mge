### Setup

Development

Note :
- [magento 2 installation with docker (simple docker config)](https://www.magemodule.com/all-things-magento/magento-2-tutorials/docker-magento-2-development/#download-magento-2)
- [another magento 2 installation with docker (markshust)](https://github.com/markshust/docker-magento#setup)
- [magento 2 prerequisites (devdocs.magento)](https://devdocs.magento.com/guides/v2.4/install-gde/prereq/prereq-overview.html)
- [porto video installation guide (portotheme.com)](http://www.portotheme.com/magento2/porto/video_guide/)
- [porto documentation (portotheme.com)](https://www.portotheme.com/magento2/porto/documentation/)

Shell Command #1 :
```
chmod +x docker/clinotty docker/setup-composer-auth
docker/setup-composer-auth
docker/setup

# to remove magento two factor auth
bin/magento module:disable Magento_TwoFactorAuth
bin/magento cache:flush
```

Shell Command #2 :
```
docker/clinotty php -d memory_limit=-1 bin/magento setup:install
docker/clinotty php -d memory_limit=-1 bin/magento setup:upgrade

docker/clinotty php -d memory_limit=-1 bin/magento sampledata:remove

docker/clinotty php bin/magento cache:flush
```


Installation
- clear static files
  1. rm -rf cache/* page_cache/* generation/*
- Disable all cache related section that you have in your magento.
- php -d memory_limit=-1 bin/magento setup:upgrade
- chown -R mgee7192:mgee7192 /home/mgee7192/public_html
- mariadb 10.5 support
  - https://github.com/magento/magento2/issues/31109#issue-754308571
- disable elasticsearch for gh_magento2
  - php -d memory_limit=-1 bin/magento config:show catalog/search/engine
    - default : elasticsearch7
  - php bin/magento config:set catalog/search/engine none

- Select Select Smartwave Porto Theme In :
  1. Stores > Configuration > General > Design > Design Theme > Design Theme
  2. Content > Design > Configuration
- Input the purchase code in Stores > Configuration > Porto > Activate Theme
- Import Demo
