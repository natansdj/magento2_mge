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

namespace Webkul\MpIndonesianShipping\Model\ResourceModel\City;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var $_idFieldName
     */
    protected $_idFieldName = 'entity_id';

    /**
     * @var \Magento\Framework\App\Request\Http $request
     */
    protected $_eventPrefix = 'wk_mpindonesianshipping_city_collection';

    /**
     * @var \Magento\Framework\App\Request\Http $request
     */
    protected $_eventObject = 'wk_mpindonesianshipping_city_collection';

    /**
     * Define resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Webkul\MpIndonesianShipping\Model\City::class,
            \Webkul\MpIndonesianShipping\Model\ResourceModel\City::class
        );
    }
}
