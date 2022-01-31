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

class SubDistricts
{
    /**
     * @var \Webkul\MpIndonesianShipping\Helper\Data
     */
    protected $_indoHelper;

    public function __construct(
        \Webkul\MpIndonesianShipping\Helper\Data $indoHelper
    ) {
        $this->_indoHelper = $indoHelper;
    }

    /**
     * Options getter
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->_indoHelper->getApiKey()) {
            $subDistrictArray = [];
            $originFailed = false;
            $originPostalCode = $this->_indoHelper->getOriginPostalCode();

            if ($originPostalCode > 0) {
                $cityId = $this->_indoHelper->getCityIdByZipcode($originPostalCode);

                if ($cityId != 0) {
                    $this->_indoHelper->setSellerApiUrlForCalc('pro');
                    $subDistricts = $this->_indoHelper->getAllSubDistrict($cityId);
                    $subDistricts = (array)json_decode($subDistricts, true);

                    return $this->getSubDistrictArray($subDistricts);
                } else {
                    $originFailed = true;
                }
            } else {
                $originFailed = true;
            }

            if ($originFailed) {
                return [
                    ['value' => 1, 'label'=>__('ORIGIN FAILED')],
                ];
            }
        } else {
            return [
                ['value' => 1, 'label'=>__('API KEY INVALID')],
            ];
        }
    }

    /**
     * get sub districts in selectbox
     * @return array
     */
    private function getSubDistrictArray($subDistricts)
    {
        if (isset($subDistricts['rajaongkir']['status']['code'])) {
            if ($subDistricts['rajaongkir']['status']['code'] == 200) {
                $records = $subDistricts['rajaongkir']['results'];

                $subDistrictArray[] = [
                    'value' => '-1',
                    'label'=> __('--- Select Origin ('.count($records).' Sub District Registered) ---')
                ];

                foreach ($records as $record) {
                    $subDistrictId =  $record['subdistrict_id'];
                    $subDistrictName = $record["subdistrict_name"].", ".$record["city"];
                    $subDistrictArray[] = ['value' => $subDistrictId, 'label'=> $subDistrictName];
                }
                return $subDistrictArray;
            } elseif ($subDistricts['rajaongkir']['status']['code'] == 400) {
                return [['value' => -1, 'label'=> __('Api Key Invalid')]];
            }
        }
        return [['value' => -1, 'label'=> __('Api Key Invalid')]];
    }
}
