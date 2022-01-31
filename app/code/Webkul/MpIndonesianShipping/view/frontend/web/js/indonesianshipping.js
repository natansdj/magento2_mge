/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MpIndonesianShipping
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

define([
    "jquery",
    "mage/storage",
    'uiComponent',
    'mage/translate'
],function ($, storage, Component, $t) {
    "use strict";
    return Component.extend({
        initialize: function (config) {
            $("#mp_indonesian_origin_type").change(function () {
                if ($(this).val() == 1) {
                    $("#subdistricts_section").css("display", "block");
                } else {
                    $("#subdistricts_section").css("display", "none");
                }
            });

            $("#mp_indonesian_api_url").change(function () {
                if ($(this).val() == config.starterUrl) {
                    $("#starter_section").css("display", "block");
                    $("#basic_section, #pro_section").css("display", "none");
                } else if ($(this).val() == config.basicUrl) {
                    $("#basic_section").css("display", "block");
                    $("#starter_section, #pro_section").css("display", "none");
                } else {
                    $("#pro_section").css("display", "block");
                    $("#starter_section, #basic_section").css("display", "none");
                }
            });
        }
    });
});
