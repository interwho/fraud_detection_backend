<?php
namespace Hack2Hire\FraudDetectionBackend\Entities;

use Doctrine\ORM\Mapping;

/**
 * Transaction
 *
 * @Table(name="transactions")
 * @Entity
 */
class Transaction
{
    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", nullable=true)
     * @Id
     */
    protected $id;

    /**
     * @var integer $deviceId
     *
     * @Column(name="device_id", type="integer", nullable=false)
     */
    protected $deviceId;

    /**
     * @var integer $transactionValue
     *
     * @Column(name="transaction_value", type="integer", nullable=false)
     */
    protected $transactionValue;

    /**
     * @var integer $accountId
     *
     * @Column(name="account_id", type="integer", nullable=false)
     */
    protected $accountId;

    /**
     * @var integer $tsMillis
     *
     * @Column(name="ts_millis", type="integer", nullable=false)
     */
    protected $tsMillis;

    /**
     * @var integer $isFraud
     *
     * @Column(name="is_fraud", type="boolean", nullable=true)
     */
    protected $isFraud;

    /**
     * @var integer $fraudReason
     *
     * @Column(name="fraud_reason", type="string", nullable=true)
     */
    protected $fraudReason;

    /**
     * Transaction constructor.
     * @param int $id
     * @param int $deviceId
     * @param int $transactionValue
     * @param int $accountId
     * @param int $tsMillis
     * @param int $isFraud
     * @param int $fraudReason
     */
    public function __construct($id, $deviceId, $transactionValue, $accountId, $tsMillis, $isFraud, $fraudReason)
    {
        $this->id = $id;
        $this->deviceId = $deviceId;
        $this->transactionValue = $transactionValue * 100;
        $this->accountId = $accountId;
        $this->tsMillis = $tsMillis;
        $this->isFraud = $isFraud;
        $this->fraudReason = $fraudReason;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getDeviceId()
    {
        return $this->deviceId;
    }

    /**
     * @param int $deviceId
     */
    public function setDeviceId($deviceId)
    {
        $this->deviceId = $deviceId;
    }

    /**
     * @return int
     */
    public function getTransactionValue()
    {
        return $this->transactionValue / 100;
    }

    /**
     * @param int $transactionValue
     */
    public function setTransactionValue($transactionValue)
    {
        $this->transactionValue = $transactionValue * 100;
    }

    /**
     * @return int
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @param int $accountId
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
    }

    /**
     * @return int
     */
    public function getTsMillis()
    {
        return $this->tsMillis;
    }

    /**
     * @param int $tsMillis
     */
    public function setTsMillis($tsMillis)
    {
        $this->tsMillis = $tsMillis;
    }

    /**
     * @return int
     */
    public function getIsFraud()
    {
        return $this->isFraud;
    }

    /**
     * @param int $isFraud
     */
    public function setIsFraud($isFraud)
    {
        $this->isFraud = $isFraud;
    }

    /**
     * @return int
     */
    public function getFraudReason()
    {
        return $this->fraudReason;
    }

    /**
     * @param int $fraudReason
     */
    public function setFraudReason($fraudReason)
    {
        $this->fraudReason = $fraudReason;
    }
}