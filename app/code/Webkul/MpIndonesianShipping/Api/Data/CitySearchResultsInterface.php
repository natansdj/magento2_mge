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

interface CitySearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get City list.
     * @return \Webkul\MpIndonesianShipping\Api\Data\CityInterface[]
     */
    public function getItems();

    /**
     * Set city_id list.
     * @param \Webkul\MpIndonesianShipping\Api\Data\CityInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
