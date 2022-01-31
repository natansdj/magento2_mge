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
use Webkul\MpIndonesianShipping\Api\Data\CityInterface;
use Webkul\MpIndonesianShipping\Api\Data\CityInterfaceFactory;

class City extends \Magento\Framework\Model\AbstractExtensibleModel implements
    \Magento\Framework\DataObject\IdentityInterface,
    \Webkul\MpIndonesianShipping\Api\Data\CityInterface
{
    /**
     * cache key for indoensian city
     */
    const CACHE_TAG = 'wk_mpindonesianshipping_city';

    /**
     * @var $_cacheTag
     */
    protected $_cacheTag = 'wk_mpindonesianshipping_city';

    /**
     * @var $_eventPrefix
     */
    protected $_eventPrefix = 'wk_mpindonesianshipping_city';

    protected function _construct()
    {
        $this->_init(\Webkul\MpIndonesianShipping\Model\ResourceModel\City::class);
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
     * @return \Webkul\MpIndonesianShipping\Api\Data\CityInterface
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * Get city_id
     * @return string|null
     */
    public function getCityId()
    {
        return $this->getData(self::CITY_ID);
    }

    /**
     * Set city_id
     * @param string $cityId
     * @return \Webkul\MpIndonesianShipping\Api\Data\CityInterface
     */
    public function setCityId($cityId)
    {
        return $this->setData(self::CITY_ID, $cityId);
    }

    /**
     * Get city_name
     * @return string|null
     */
    public function getCityName()
    {
        return $this->getData(self::CITY_NAME);
    }

    /**
     * Set city_name
     * @param string $cityName
     * @return \Webkul\MpIndonesianShipping\Api\Data\CityInterface
     */
    public function setCityName($cityName)
    {
        return $this->setData(self::CITY_NAME, $cityName);
    }

    /**
     * Get postal_code
     * @return string|null
     */
    public function getPostalCode()
    {
        return $this->getData(self::POSTAL_CODE);
    }

    /**
     * Set postal_code
     * @param string $postalCode
     * @return \Webkul\MpIndonesianShipping\Api\Data\CityInterface
     */
    public function setPostalCode($postalCode)
    {
        return $this->setData(self::POSTAL_CODE, $postalCode);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Webkul\MpIndonesianShipping\Api\Data\CityExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Webkul\MpIndonesianShipping\Api\Data\CityExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Webkul\MpIndonesianShipping\Api\Data\CityExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
