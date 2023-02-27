<?php

namespace MaxRedemption\MaxRedemptionPayment\Model;

use Magento\Payment\Model\Method\AbstractMethod;

class MaxRedemptionPayment extends AbstractMethod
{
    const CODE = 'maxredemptionpayment';

    protected $_code = self::CODE;
}
