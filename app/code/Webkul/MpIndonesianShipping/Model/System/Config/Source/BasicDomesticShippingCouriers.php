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

class BasicDomesticShippingCouriers
{
    /**
     * Options getter
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'JNE', 'label'=>__('JNE')],
            ['value' => 'POS', 'label'=>__('POS')],
            ['value' => 'TIKI', 'label'=>__('TIKI')],
            ['value' => 'PCP', 'label'=>__('PCP')],
            ['value' => 'ESL', 'label'=>__('ESL')],
            ['value' => 'RPX', 'label'=>__('RPX')]
        ];
    }
}
