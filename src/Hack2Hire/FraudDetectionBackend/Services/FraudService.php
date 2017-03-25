<?php
namespace Hack2Hire\FraudDetectionBackend\Services;

class FraudService
{
    public function isFraud($id, $deviceId, $transactionValue, $accountId, $tsMillis)
    {
    	$isFraud = false;

    	if (!$isFraud) {
    		$isFraud = $this->isBackDate($id, $deviceId, $transactionValue, $accountId, $tsMillis, 60*60*1000);

    		if ($isFraud) return "Back Date more than 1 hour.";
    	}

    	if (!$isDifferentLocation) {
    		$isFraud = $this->isBackDate($id, $deviceId, $transactionValue, $accountId, $tsMillis, 60*1000);

    		if ($isFraud) return "Same account, different location within 1 minute.";
    	}

    	if (!$isSameValue) {
    		$isFraud = $this->isBackDate($id, $deviceId, $transactionValue, $accountId, $tsMillis, 60*1000);

    		if ($isFraud) return "Same account, same amount at different location within 1 minute.";
    	}

        return $isFraud;
    }

    public function isBackDate($id, $deviceId, $transactionValue, $accountId, $tsMillis, $threshold) {
    	/*
    		check the $tsMillis with last valid (not fraud) transaction tsMillis, with $threshold
    	*/

        /** @var Transaction[] $transactions */
        $last_valid_transaction = $doctrine->getRepository('Transaction')->findOneBy(
        	['isFraud' => false],
        	['ts_millis' => 'DESC']
        );

        if (abs( $tsMillis - $last_valid_transaction->getTsMillis() ) > $threshold) {
        	return true;
        }

    	return false;
    }

    public function isDifferentLocation($id, $deviceId, $transactionValue, $accountId, $tsMillis, $threshold) {
    	/*
    		check the location from $deviceId, compare with last valid transaction from same account, within $threshold
    	*/

        /** @var Transaction[] $transactions */
        $last_valid_transaction = $doctrine->getRepository('Transaction')->findOneBy(
        	['account_id' => $accountId, 'isFraud' => false],
        	['ts_millis' => 'DESC']
        );

    	return false;
    }

    public function isSameValue($id, $deviceId, $transactionValue, $accountId, $tsMillis) {
    	return false;
    }
}
