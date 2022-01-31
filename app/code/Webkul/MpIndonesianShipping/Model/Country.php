<?php
/**
 * Webkul Software
 *
 * @category  Webkul
 * @package   Webkul_MpIndonesianShipping
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\MpIndonesianShipping\Model;

use Magento\Framework\Api\DataObjectHelper;
use Webkul\MpIndonesianShipping\Api\Data\CountryInterfaceFactory;
use Webkul\MpIndonesianShipping\Api\Data\CountryInterface;

class Country extends \Magento\Framework\Model\AbstractExtensibleModel implements
    \Magento\Framework\DataObject\IdentityInterface,
    \Webkul\MpIndonesianShipping\Api\Data\CountryInterface
{
    /**
     * cache tag for indonesian country
     */
    const CACHE_TAG = 'wk_mpindonesianshipping_country';

    /**
     * @var $_cacheTag
     */
    protected $_cacheTag = 'wk_mpindonesianshipping_country';

    /**
     * @var $_eventPrefix
     */
    protected $_eventPrefix = 'wk_mpindonesianshipping_country';

    protected function _construct()
    {
        $this->_init(\Webkul\MpIndonesianShipping\Model\ResourceModel\Country::class);
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get entity_id
     * @return string|null
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * Set entity_id
     * @param string $entityId
     * @return \Webkul\MpIndonesianShipping\Api\Data\CountryInterface
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * Get country_id
     * @return string|null
     */
    public function getCountryId()
    {
        return $this->getData(self::COUNTRY_ID);
    }

    /**
     * Set country_id
     * @param string $countryId
     * @return \Webkul\MpIndonesianShipping\Api\Data\CountryInterface
     */
    public function setCountryId($countryId)
    {
        return $this->setData(self::COUNTRY_ID, $countryId);
    }

    /**
     * Get country_name
     * @return string|null
     */
    public function getCountryName()
    {
        return $this->getData(self::COUNTRY_NAME);
    }

    /**
     * Set country_name
     * @param string $countryName
     * @return \Webkul\MpIndonesianShipping\Api\Data\CountryInterface
     */
    public function setCountryName($countryName)
    {
        return $this->setData(self::COUNTRY_NAME, $countryName);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Webkul\MpIndonesianShipping\Api\Data\CountryExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Webkul\MpIndonesianShipping\Api\Data\CountryExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Webkul\MpIndonesianShipping\Api\Data\CountryExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
