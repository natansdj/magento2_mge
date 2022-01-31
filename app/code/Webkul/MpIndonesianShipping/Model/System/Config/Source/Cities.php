<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MpIndonesianShipping
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\MpIndonesianShipping\Model\System\Config\Source;

class Cities
{
    /**
     * @var \Webkul\MpIndonesianShipping\Helper\Data
     */
    protected $_indoHelper;

    /**
     * @var \Webkul\MpIndonesianShipping\Model\CityFactory
     */
    protected $_cityFactory;

    public function __construct(
        \Webkul\MpIndonesianShipping\Helper\Data $indoHelper,
        \Webkul\MpIndonesianShipping\Model\CityFactory $cityFactory
    ) {
        $this->_indoHelper = $indoHelper;
        $this->_cityFactory = $cityFactory;
    }

    /**
     * Options getter
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->_indoHelper->getApiKey()) {
            $cityArray = [];
            $collection = $this->_cityFactory->create()->getCollection();
            $cityList = $collection->setOrder('province', 'ASC');
            $count = $collection->getSize();

            $cityArray[] = ['value' => '', 'label'=> __('--- Select Origin ('.$count.' Cities Registered) ---')];
            foreach ($cityList->getData() as $data) {
                $cityId =  $data['city_id'];
                $cityName = "Provinsi ".$data["province"]." ".$data["type"]." ".
                    $data["city_name"]." Kodepos ".$data["postal_code"];
                $cityArray[] = ['value' => $cityId, 'label'=> $cityName];
            }

            return $cityArray;
        } else {
            return [
                ['value' => 1, 'label'=>__('API KEY INVALID')],
            ];
        }
    }
}
