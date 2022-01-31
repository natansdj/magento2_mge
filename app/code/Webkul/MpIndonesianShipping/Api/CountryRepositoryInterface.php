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

namespace Webkul\MpIndonesianShipping\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface CountryRepositoryInterface
{
    /**
     * Save Country
     * @param \Webkul\MpIndonesianShipping\Api\Data\CountryInterface $country
     * @return \Webkul\MpIndonesianShipping\Api\Data\CountryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Webkul\MpIndonesianShipping\Api\Data\CountryInterface $country
    );

    /**
     * Retrieve Country
     * @param string $countryId
     * @return \Webkul\MpIndonesianShipping\Api\Data\CountryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($countryId);

    /**
     * Retrieve Country matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Webkul\MpIndonesianShipping\Api\Data\CountrySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Country
     * @param \Webkul\MpIndonesianShipping\Api\Data\CountryInterface $country
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Webkul\MpIndonesianShipping\Api\Data\CountryInterface $country
    );

    /**
     * Delete Country by ID
     * @param string $countryId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($countryId);
}
