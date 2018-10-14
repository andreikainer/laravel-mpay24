<?php

namespace LaravelMPay24\Models;

use LaravelMPay24\ORDER;
use LaravelMPay24\Transaction;

/**
 * A simple class demonstrating use of AbstractShop
 *
 * Class BasicShop
 * @package LaravelMPay24\Models
 */
class BasicShop extends AbstractShop {
    /** @var Transaction */
    private $transaction;
    /** @var ORDER */
    private $order;

    /**
     * @param Transaction $transaction
     */
    public function setTransaction(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * @param ORDER $order
     */
    public function setOrder(ORDER $order)
    {
        $this->order = $order;
    }

    public function createTransaction()
    {
        return $this->transaction;
    }

    public function createMDXI($transaction)
    {
        return $this->order;
    }

    public function updateTransaction($tid, $args, $shippingConfirmed)
    {
        $fh = fopen(storage_path('app/Payments/')."payment-".$args['MPAYTID'].".txt", 'w') or die("can't open file");

        $result = "TID : " . $tid . "\n\ntransaction arguments:\n\n";
        foreach($args as $key => $value)
            $result.= $key . " = " . $value . "\n";

        fwrite($fh, $result);
        fclose($fh);
    }

    public function getTransaction($tid)
    {
        $transaction = new Transaction($tid);
        $transaction->PRICE = '150';
        return $transaction;
    }

    public function createSecret($tid, $amount, $currency, $timeStamp)
    {
        return bcrypt($tid.$amount.$currency.$timeStamp);
    }

    public function getSecret($tid)
    {
        //
    }
}