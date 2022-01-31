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

interface CountrySearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get Country list.
     * @return \Webkul\MpIndonesianShipping\Api\Data\CountryInterface[]
     */
    public function getItems();

    /**
     * Set entity_id list.
     * @param \Webkul\MpIndonesianShipping\Api\Data\CountryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
