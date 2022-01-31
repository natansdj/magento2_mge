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

class ProDomesticShippingCouriers
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
            ['value' => 'RPX', 'label'=>__('RPX')],
            ['value' => 'PANDU', 'label'=>__('PANDU')],
            ['value' => 'WAHANA', 'label'=>__('WAHANA')],
            ['value' => 'JNT', 'label'=>__('J & T')],
            ['value' => 'PAHALA', 'label'=>__('PAHALA')],
            ['value' => 'CAHAYA', 'label'=>__('CAHAYA')],
            ['value' => 'SAP', 'label'=>__('SAP')],
            ['value' => 'JET', 'label'=>__('JET')],
            ['value' => 'INDAH', 'label'=>__('INDAH')],
            ['value' => 'DSE', 'label'=>__('DSE')],
            ['value' => 'SLIS', 'label'=>__('SLIS')],
            ['value' => 'FIRST', 'label'=>__('FIRST')],
            ['value' => 'NCS', 'label'=>__('NCS')],
            ['value' => 'STAR', 'label'=>__('STAR')]
        ];
    }
}
