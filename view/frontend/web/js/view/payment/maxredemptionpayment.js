define([
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';

        rendererList.push(
            {
                type: 'maxredemptionpayment',
                component: 'MaxRedemption_MaxRedemptionPayment/js/view/payment/method-renderer/maxredemptionpayment'
            }
        );

        return Component.extend({});
    });
