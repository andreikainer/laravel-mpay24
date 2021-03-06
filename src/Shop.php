<?php

namespace LaravelMPay24;

use LaravelMPay24\Models\AbstractShop;
use LaravelMPay24\Models\Exception;

class Shop extends MPay24Shop {
    /** @var AbstractShop a shop object to which all the actions will be delegated to */
    private $shop;

    private $callbacks = [
        'success' => '',
        'error' => '',
        'confirmation' => '',
    ];

    /**
     * @param int|string $merchantID
     * @param string     $soapPassword
     * @param bool       $test
     * @param string     $proxyHost
     * @param string     $proxyPort
     * @param bool       $debug
     */
    function __construct($merchantID, $soapPassword, $test, $proxyHost=null, $proxyPort=null, $debug=false)
    {
        $this->callbacks = [
            'success' => config('services.mpay24.successUrl'),
            'error' => config('services.mpay24.errorUrl'),
            'confirmation' => config('services.mpay24.confirmationUrl'),
        ];

        parent::__construct($merchantID, $soapPassword, $test, $proxyHost, $proxyPort, $debug);
    }

    /**
     * @param AbstractShop $shop
     */
    public function setShopDelegator(AbstractShop $shop)
    {
        $this->shop = $shop;
    }

    /**
     * @return Transaction
     * @throws Exception
     */
    public function createTransaction()
    {
        return $this->shop->createTransaction();
    }

    /**
     * @param Transaction $transaction
     * @return ORDER
     * @throws Exception
     */
    public function createMDXI($transaction)
    {
        $order = $this->shop->createMDXI($transaction);
        $this->prepareOrderUrls($order);

        return $order;
    }

    /**
     * @param ORDER $order
     */
    public function prepareOrderUrls(ORDER $order)
    {
        if(empty($order->Order->URL->Success) && !empty($this->callbacks['success'])) {
            $order->Order->URL->Success = action($this->callbacks['success']);
        }

        if(empty($order->Order->URL->Error) && !empty($this->callbacks['error'])) {
            $order->Order->URL->Error = action($this->callbacks['error']);
        }

        if(empty($order->Order->URL->Confirmation) && !empty($this->callbacks['confirmation'])) {
            $order->Order->URL->Confirmation = action($this->callbacks['confirmation']);
        }
    }

    /******************************************
     * Methods delegated to "shop" delegator  *
     ******************************************/

    /**
     * Actualize the transaction, which has a transaction ID = $tid with the values from $args in your shop and return it
     * @param             string              $tid                          The transaction ID you want to update with the confirmation
     * @param             array               $args                         Arrguments with them the transaction is to be updated
     * @param             bool                $shippingConfirmed            TRUE if the shipping address is confirmed, FALSE - otherwise (in case of PayPal Express Checkout)
     */
    public function updateTransaction($tid, $args, $shippingConfirmed)
    {
        $this->shop->updateTransaction($tid, $args, $shippingConfirmed);
    }

    /**
     * @param string $tid
     * @return Transaction
     */
    public function getTransaction($tid)
    {
        return $this->shop->getTransaction($tid);
    }

    /**
     * Using the ORDER object from order.php, create a order-xml, which is needed for a transaction with profiles to be started
     * @param             string              $tid                          The transaction ID of the transaction you want to make an order transaction XML file for
     * @return            \DOMDocument
     */
    public function createProfileOrder($tid)
    {
        return $this->shop->createProfileOrder($tid);
    }

    /**
     * Using the ORDER object from order.php, create a order-xml, which is needed for a transaction with PayPal Express Checkout to be started
     * @param             string              $tid                          The transaction ID of the transaction you want to make an order transaction XML file for
     * @return            \DOMDocument
     */
    public function createExpressCheckoutOrder($tid)
    {
        return $this->shop->createExpressCheckoutOrder($tid);
    }

    /**
     * Using the ORDER object from order.php, create a order-xml, which is needed for a transaction with PayPal Express Checkout to be finished
     * @param             string              $tid                          The transaction ID of the transaction you want to make an order transaction XML file for
     * @param             string              $shippingCosts                The shipping costs amount for the transaction, provided by PayPal, after changing the shipping address
     * @param             string              $amount                       The new amount for the transaction, provided by PayPal, after changing the shipping address
     * @param             bool                $cancel                       TRUE if the a cancelation is wanted after renewing the amounts and FALSE otherwise
     * @return            \DOMDocument
     */
    public function createFinishExpressCheckoutOrder($tid, $shippingCosts, $amount, $cancel)
    {
        return $this->shop->createFinishExpressCheckoutOrder($tid, $shippingCosts, $amount, $cancel);
    }

    /**
     * Write a log into a file, file system, data base
     * @param             string              $operation                    The operation, which is to log: GetPaymentMethods, Pay, PayWithProfile, Confirmation, UpdateTransactionStatus, ClearAmount, CreditAmount, CancelTransaction, etc.
     * @param             string              $info_to_log                  The information, which is to log: request, response, etc.
     */
    public function write_log($operation, $info_to_log)
    {
        $this->shop->write_log($operation, $info_to_log);
    }

    /**
     * This is an optional function, but it's strongly recomended that you implement it - see details.
     * It should build a hash from the transaction ID of your shop, the amount of the transaction,
     * the currency and the timeStamp of the transaction. The mPAY24 confirmation interface will be called
     * with this hash (parameter name 'token'), so you would be able to check whether the confirmation is
     * really coming from mPAY24 or not. The hash should be then saved in the transaction object, so that
     * every transaction has an unique secret token.
     * @param             string              $tid                          The transaction ID you want to make a secret key for
     * @param             string              $amount                       The amount, reserved for this transaction
     * @param             string              $currency                     The timeStamp at the moment the transaction is created
     * @param             string              $timeStamp                    The timeStamp at the moment the transaction is created
     * @return            string
     */
    public function createSecret($tid, $amount, $currency, $timeStamp)
    {
        return $this->shop->createSecret($tid, $amount, $currency, $timeStamp);
    }

    /**
     * Get the secret (hashed) token for a transaction
     * @param             string              $tid                          The transaction ID you want to get the secret key for
     * @return            string
     */
    public function getSecret($tid)
    {
        return $this->shop->getSecret($tid);
    }
}