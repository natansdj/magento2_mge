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

interface CountryInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * wk_mpindonesianshipping_country table fields
     */
    const ENTITY_ID = 'entity_id';
    const COUNTRY_ID = 'country_id';
    const COUNTRY_NAME = 'country_name';

    /**
     * Get entity_id
     * @return string|null
     */
    public function getEntityId();

    /**
     * Set entity_id
     * @param string $entityId
     * @return \Webkul\MpIndonesianShipping\Api\Data\CountryInterface
     */
    public function setEntityId($entityId);

    /**
     * Get country_id
     * @return string|null
     */
    public function getCountryId();

    /**
     * Set country_id
     * @param string $countryId
     * @return \Webkul\MpIndonesianShipping\Api\Data\CountryInterface
     */
    public function setCountryId($countryId);

    /**
     * Get country_name
     * @return string|null
     */
    public function getCountryName();

    /**
     * Set country_name
     * @param string $countryName
     * @return \Webkul\MpIndonesianShipping\Api\Data\CountryInterface
     */
    public function setCountryName($countryName);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Webkul\MpIndonesianShipping\Api\Data\CountryExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Webkul\MpIndonesianShipping\Api\Data\CountryExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Webkul\MpIndonesianShipping\Api\Data\CountryExtensionInterface $extensionAttributes
    );
}
