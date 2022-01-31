/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MpIndonesianShipping
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        'Webkul_MpIndonesianShipping/js/model/shipping-rates-validator',
        'Webkul_MpIndonesianShipping/js/model/shipping-rates-validation-rules'
    ],
    function (
        Component,
        defaultShippingRatesValidator,
        defaultShippingRatesValidationRules,
        mpindonesianshippingRatesValidator,
        mpindonesianshippingRatesValidationRules
    ) {
        'use strict';
        defaultShippingRatesValidator.registerValidator('mpindonesianshipping', mpindonesianshippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('mpindonesianshipping', mpindonesianshippingRatesValidationRules);

        return Component;
    }
);
