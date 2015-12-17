<?php

namespace App\Model\Gopay;

use Markette\GopaySimple\GopayException;
use Markette\GopaySimple\GopaySimple;

class GopayService
{

    /** @var GopaySimple */
    private $gopay;

    /** @var string */
    private $goId;

    /**
     * @param GopaySimple $gopay
     * @param string $goId
     */
    public function __construct(GopaySimple $gopay, $goId)
    {
        $this->gopay = $gopay;
        $this->goId = $goId;
    }

    /**
     * @param array $data
     */
    public function createPayment(array $data)
    {
        // Validate payment
        // $this->validate($data);

        // Configure payment
        $payment = $this->configure($data);

        // Create payment
        try {
            $response = $this->gopay->call('POST', 'payments/payment', $payment);
        } catch (GopayException $e) {
            // ..
        }
    }

    /**
     * @param float $paymentId
     */
    public function status($paymentId)
    {
        try {
            $response = $this->gopay->call('GET', 'payments/payment/', $paymentId);
        } catch (GopayException $e) {
            // ..
        }
    }

    /**
     * @param array $payment
     * @return array
     */
    private function configure(array $payment)
    {
        if (!isset($payment['target'])) {
            $payment['target'] = ['type' => 'ACCOUNT', 'goid' => $this->goId];
        }
    }

}
