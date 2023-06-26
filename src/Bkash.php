<?php

namespace Ddbaidya\BkashLaravel;

use Illuminate\Support\Facades\DB;

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
        $idToken = $bkashPayment->idToken();
        $newPayment = $bkashPayment->createPayment($bkashPayment->idToken(), $amount,  $invoiceId, $payerReference,  $currency,  $intent);
        DB::table('bkash_transactions')->insert([
            'transaction_id' => $invoiceId,
            'payment_id' => $newPayment['paymentID'],
            'amount' => $amount,
            'status' => $newPayment['transactionStatus'],
            'token' => $idToken,
        ]);
        return $newPayment;
    }

    /**
     * Check payment status. (Should be call when payment is completed).
     *
     * @param string $paymentId
     * @return
     */
    public static function checkPayment(string $paymentId)
    {
        $bkashTransaction = DB::table('bkash_transactions')->where('payment_id', $paymentId)->first();
        if (!$bkashTransaction) {
            return false;
        }
        $bkashPayment = new BkashPayment();
        $executePayment = $bkashPayment->executePayment($bkashTransaction->token, $paymentId);
        $checkPayment = $bkashPayment->queryPayment($bkashTransaction->token, $paymentId);
        $bkashTransaction->status = $checkPayment['transactionStatus'];
        return $checkPayment;
    }
}
