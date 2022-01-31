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

class ApiUrl
{
    /**
     * Options getter
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'https://api.rajaongkir.com/starter',
                'label'=>__('Starter (https://api.rajaongkir.com/starter)')
            ],
            [
                'value' => 'https://api.rajaongkir.com/basic',
                'label'=>__('Basic (https://api.rajaongkir.com/basic)')
            ],
            [
                'value' => 'https://pro.rajaongkir.com/api',
                'label'=>__('Pro (https://pro.rajaongkir.com/api)')
            ]
        ];
    }
}
