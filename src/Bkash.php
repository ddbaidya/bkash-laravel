<?php

namespace Ddbaidya\BkashLaravel;

class Bkash
{
    /**
     * Create payment and return link
     *
     * @param int|string $amount
     * @param string $invoiceId
     * @param string $payerReference = null
     * @param string $currency = "BDT"
     * @param string $intent = "sale"
     * @return array|bool
     */
    public static function paymentLink($amount, string $invoiceId, $payerReference = null, string $currency = "BDT", string $intent = "sale")
    {
        $bkashPayment = new BkashPayment();
        return $bkashPayment->createPayment($bkashPayment->idToken(), $amount,  $invoiceId, $payerReference,  $currency,  $intent);
    }
}
