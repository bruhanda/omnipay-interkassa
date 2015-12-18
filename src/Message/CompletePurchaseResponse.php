<?php

/*
 * InterKassa driver for the Omnipay PHP payment processing library
 *
 * @link      https://github.com/hiqdev/omnipay-interkassa
 * @package   omnipay-interkassa
 * @license   MIT
 * @copyright Copyright (c) 2015, HiQDev (http://hiqdev.com/)
 */

namespace Omnipay\InterKassa\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * InterKassa Complete Purchase Response
 *
 * @package Omnipay\InterKassa\Message
 */
class CompletePurchaseResponse extends AbstractResponse
{
    /**
     * {@inheritdoc}
     * @var CompletePurchaseRequest
     */
    public $request;

    /**
     * {@inheritdoc}
     */
    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);

        if ($this->getSign() !== $this->request->calculateSign($this->data)) {
            throw new InvalidResponseException('Failed to validate sign');
        }

        if ($this->getState() !== 'success') {
            throw new InvalidResponseException('Failed to validate sign');
        }
    }

    /**
     * Whether the payment is successful
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return $this->getState() === 'success';
    }

    /**
     * The checkout ID
     *
     * @return string
     */
    public function getCheckoutId()
    {
        return $this->data['ik_co_id'];
    }

    /**
     * @return string
     */
    public function getSign()
    {
        return $this->data['ik_sign'];
    }

    /**
     * The transaction identifier generated by the merchant website
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->data['ik_inv_id'];
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionReference()
    {
        return $this->data['ik_trn_id'];
    }

    /**
     * The amount of payment
     *
     * @return mixed
     */
    public function getAmount()
    {
        return $this->data['ik_am'];
    }

    /**
     * The currency of the payment
     *
     * @return string
     */
    public function getCurrency()
    {
        return strtoupper($this->data['ik_cur']);
    }

    /**
     * The time of request processing
     *
     * @return string
     */
    public function getTime()
    {
        return strtotime($this->data['ik_inv_prc'] . ' Europe/Moscow');
    }

    /**
     * The state of the payment.
     * Possible results:
     *  - `new`: newly created payment
     *  - `waitAccept`: waits for the payment
     *  - `process`: the payment is being processed
     *  - `success`: the payment processed successfully
     *  - `canceled`: the payment have been canceled
     *  - `fail`: the payment failed
     *
     * @return string
     * @see isSuccessful
     */
    public function getState()
    {
        return $this->data['ik_inv_st'];
    }
}
