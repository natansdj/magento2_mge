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
    'mage/translate',
    'Magento_Ui/js/modal/confirm'
],function ($, storage, Component, $t, confirmation) {
    "use strict";
    return Component.extend({
        initialize: function (config) {
            $("#load_data_btn").click(function () {
                confirmation({
                    title: $t('Load/Update Data'),
                    content: $t('Are you sure want to load/update data ? It might take upto two minutes.'),
                    actions: {
                        confirm: function () {
                            $('body').trigger('processStart');
                            storage.post(config.ajaxUrl).done(function (response) {
                                var res = JSON.parse(response.result);
                                if (res.status == 'success') {
                                    $("#load_data_dot").removeClass("reddot graydot").addClass("greendot");
                                    $("#load_data_msg").removeClass("redtext").addClass("greentext").html($t(res.message));
                                } else {
                                    $("#load_data_dot").removeClass("greendot graydot").addClass("reddot");
                                    $("#load_data_msg").removeClass("greentext").addClass("redtext").html($t(res.message));
                                }
                                $('body').trigger('processStop');
                            }).fail(
                                function (response) {
                                    $("#load_data_dot").removeClass("greendot graydot").addClass("reddot");
                                    $("#load_data_msg").removeClass("greentext").addClass("redtext").html($t("Error Occurred !"));
                                    $('body').trigger('processStop');
                                }
                            );
                        }
                    }
                });
            });

            storage.post(config.ajaxCheckUrl).done(function (response) {
                if (response.status == 'success') {
                    $("#load_data_dot").removeClass("reddot graydot").addClass("greendot");
                } else if (response.status == 'failed') {
                    $("#load_data_dot").removeClass("greendot graydot").addClass("reddot");
                } else {
                    $("#load_data_dot").removeClass("greendot reddot").addClass("graydot");
                }
            }).fail(
                function (response) {
                    $("#load_data_dot").removeClass("greendot reddot").addClass("graydot");
                }
            );
        }
    });
});
