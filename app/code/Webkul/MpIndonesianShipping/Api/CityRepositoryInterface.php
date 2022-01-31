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

interface CityRepositoryInterface
{
    /**
     * Save City
     * @param \Webkul\MpIndonesianShipping\Api\Data\CityInterface $city
     * @return \Webkul\MpIndonesianShipping\Api\Data\CityInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Webkul\MpIndonesianShipping\Api\Data\CityInterface $city
    );

    /**
     * Retrieve City
     * @param string $cityId
     * @return \Webkul\MpIndonesianShipping\Api\Data\CityInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($cityId);

    /**
     * Retrieve City matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Webkul\MpIndonesianShipping\Api\Data\CitySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete City
     * @param \Webkul\MpIndonesianShipping\Api\Data\CityInterface $city
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Webkul\MpIndonesianShipping\Api\Data\CityInterface $city
    );

    /**
     * Delete City by ID
     * @param string $cityId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($cityId);
}
