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

namespace Webkul\MpIndonesianShipping\Api\Data;

interface CityInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * wk_mpindonesianshipping_city table fields
     */
    const ENTITY_ID = 'entity_id';
    const CITY_ID = 'city_id';
    const CITY_NAME = 'city_name';
    const POSTAL_CODE = 'postal_code';

    /**
     * Get entity_id
     * @return string|null
     */
    public function getEntityId();

    /**
     * Set entity_id
     * @param string $entityId
     * @return \Webkul\MpIndonesianShipping\Api\Data\CityInterface
     */
    public function setEntityId($entityId);

    /**
     * Get city_id
     * @return string|null
     */
    public function getCityId();

    /**
     * Set city_id
     * @param string $cityId
     * @return \Webkul\MpIndonesianShipping\Api\Data\CityInterface
     */
    public function setCityId($cityId);

    /**
     * Get city_name
     * @return string|null
     */
    public function getCityName();

    /**
     * Set city_name
     * @param string $cityName
     * @return \Webkul\MpIndonesianShipping\Api\Data\CityInterface
     */
    public function setCityName($cityName);

    /**
     * Get postal_code
     * @return string|null
     */
    public function getPostalCode();

    /**
     * Set postal_code
     * @param string $postalCode
     * @return \Webkul\MpIndonesianShipping\Api\Data\CityInterface
     */
    public function setPostalCode($postalCode);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Webkul\MpIndonesianShipping\Api\Data\CityExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Webkul\MpIndonesianShipping\Api\Data\CityExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Webkul\MpIndonesianShipping\Api\Data\CityExtensionInterface $extensionAttributes
    );
}
