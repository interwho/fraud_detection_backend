<?php
namespace Hack2Hire\FraudDetectionBackend\Services;

use Hack2Hire\FraudDetectionBackend\Entities\Transaction;
use Hack2Hire\FraudDetectionBackend\Entities\ZipCode;
use Hack2Hire\FraudDetectionBackend\Repositories\POSDeviceRepository;
use Hack2Hire\FraudDetectionBackend\Repositories\ZipCodeRepository;

class FraudService
{
    /**
     * FraudService Entry Point
     *
     * @param $id
     * @param $deviceId
     * @param $transactionValue
     * @param $accountId
     * @param $tsMillis
     * @return bool|string
     */
    public function isFraud($id, $deviceId, $transactionValue, $accountId, $tsMillis)
    {
        if ($this->isBackDated($accountId, $tsMillis)) {
            return "FakeDeviceId";
        }

        if ($this->isFakeDeviceId($deviceId)) {
            return "FakeDeviceId";
        }

        if ($this->isTooFrequent($accountId, $tsMillis, 120)) {
            return "TransactionTooFrequent";
        }

        if ($this->tooMuchSpendOverTime($accountId, $tsMillis, $transactionValue, 1000, 3600)) {
            return "TooRapidSpendOverTime";
        }

        if ($this->tooMuchSpendOverTransactions($accountId, $transactionValue, 5000, 10)) {
            return "TooRapidSpendOverTransactions";
        }

        if ($this->isTooFarAway($deviceId, $accountId, $tsMillis, 7200, 80000)) {
            return "TooFarAwayFromLastTransactions";
        }

        return false;
    }

    /**
     * Is the transaction older than previous transactions by that user?
     *
     * @param $accountId
     * @param $tsMillis
     * @return bool
     */
    public function isBackDated($accountId, $tsMillis)
    {
        /** @var Transaction $transaction */
        $transaction = (new DoctrineService())->getRepository('Transaction')->findOneBy(['accountId' => $accountId], ['tsMillis' => 'DESC']);

        if (($tsMillis - $transaction->getTsMillis()) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Does the device ID exist?
     *
     * @param $deviceId
     * @return bool
     */
    public function isFakeDeviceId($deviceId)
    {
        if (empty($this->getLatLonFromDeviceId($deviceId))) {
            return true;
        }

        return false;
    }

    /**
     * Returns true if the last transaction was within threshold seconds of the current one.
     *
     * @param $accountId
     * @param $tsMillis
     * @param $threshold
     * @return bool
     */
    public function isTooFrequent($accountId, $tsMillis, $threshold)
    {
        /** @var Transaction $transaction */
        $transaction = (new DoctrineService())->getRepository('Transaction')->findOneBy(['accountId' => $accountId], ['tsMillis' => 'DESC']);

        if (($tsMillis - $transaction->getTsMillis()) < $threshold) {
            return true;
        }

        return false;
    }

    /**
     * Too much spend within threshold time?
     *
     * @param $accountId
     * @param $tsMillis
     * @param $transactionValue
     * @param $thresholdValue
     * @param $thresholdTime
     * @return bool
     */
    public function tooMuchSpendOverTime($accountId, $tsMillis, $transactionValue, $thresholdValue, $thresholdTime)
    {
        /** @var Transaction[] $transactions */
        $transactions = (new DoctrineService())->getRepository('Transaction')->findBy(['accountId' => $accountId], ['tsMillis' => 'DESC']);

        $totalValue = $transactionValue;
        foreach ($transactions as $transaction) {
            if (($tsMillis - $thresholdTime) > $transaction->getTsMillis()) {
                break;
            }

            $totalValue += $transaction->getTransactionValue();
        }

        if ($totalValue > $thresholdValue) {
            return true;
        }

        return false;
    }

    /**
     * Too much spend within threshold Transactions?
     *
     * @param $accountId
     * @param $transactionValue
     * @param $thresholdValue
     * @param $thresholdTransactions
     * @return bool
     */
    public function tooMuchSpendOverTransactions($accountId, $transactionValue, $thresholdValue, $thresholdTransactions)
    {
        /** @var Transaction[] $transactions */
        $transactions = (new DoctrineService())->getRepository('Transaction')->findBy(['accountId' => $accountId], ['tsMillis' => 'DESC']);

        $totalValue = $transactionValue;
        foreach ($transactions as $transaction) {
            $totalValue += $transaction->getTransactionValue();

            $thresholdTransactions--;
            if ($thresholdTransactions == 0) {
                break;
            }
        }

        if ($totalValue > $thresholdValue) {
            return true;
        }

        return false;
    }

    /**
     * Is the transaction within $threshold km and period of the last transaction
     *
     * @param $deviceId
     * @param $accountId
     * @param $tsMillis
     * @param $thresholdTime
     * @param $thresholdDistanceMeters
     * @return bool
     */
    public function isTooFarAway($deviceId, $accountId, $tsMillis, $thresholdTime, $thresholdDistanceMeters)
    {
        /** @var Transaction $transaction */
        $transaction = (new DoctrineService())->getRepository('Transaction')->findOneBy(['accountId' => $accountId], ['tsMillis' => 'DESC']);

        if (($tsMillis - $transaction->getTsMillis()) < $thresholdTime) {
            $from = $this->getLatLonFromDeviceId($transaction->getDeviceId());
            $to = $this->getLatLonFromDeviceId($deviceId);

            if ($this->distanceDifferenceInMeters($from[0], $from[1], $to[0], $to[1]) > $thresholdDistanceMeters) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets the latitude and longitude from device id. False if device id not found.
     *
     * @param $deviceId
     * @return array|bool
     */
    private function getLatLonFromDeviceId($deviceId)
    {
        $doctrine = new DoctrineService();

        /** @var POSDeviceRepository $posDeviceRepository */
        $posDeviceRepository = $doctrine->getRepository('POSDevice');

        /** @var ZipCodeRepository $zipCodeRepository */
        $zipCodeRepository = $doctrine->getRepository('ZipCode');

        $device = $posDeviceRepository->findOneBy(['id' => $deviceId]);
        if (empty($device)) {
            return false;
        }
        $location = explode(", ", $device->getLocation());
        $county = $location[0];
        $state = $location[1];

        /** @var ZipCode[] $zipCodes */
        $zipCodes = $zipCodeRepository->findBy(['county' => $county, 'name' => $state]);
        if (empty($zipCodes)) {
            $newCounty = explode(" ", $county);
            array_pop($newCounty);
            $newCounty = implode(" ", $newCounty);
            $zipCodes = $zipCodeRepository->findBy(['county' => $newCounty, 'name' => $state]);
            if (empty($zipCodes)) {
                return false;
            }
        }

        $zipCode = null;
        foreach ($zipCodes as $currZipCode) {
            if (!empty($currZipCode->getLatitude()) && !empty($currZipCode->getLongitude())) {
                $zipCode = $currZipCode;
                break;
            }
        }

        if (empty($zipCode)) {
            $zipCode = $zipCodes[0];
            $results = (new GeocodeService())->geocodeCounty($county, $state);
            if (empty($results)) {
                return false;
            }
            $zipCode->setLatitude($results[0]);
            $zipCode->setLongitude($results[1]);
            $zipCodeRepository->save($zipCode);
        }

        return [$zipCode->getLatitude(), $zipCode->getLongitude()];
    }

    /**
     * Haversine Formula
     *
     * @param $fromLat
     * @param $fromLon
     * @param $toLat
     * @param $toLon
     * @return int
     */
    private function distanceDifferenceInMeters($fromLat, $fromLon, $toLat, $toLon)
    {
        $latitudeFrom = deg2rad($fromLat);
        $longitudeFrom = deg2rad($fromLon);
        $latitudeTo = deg2rad($toLat);
        $longitudeTo = deg2rad($toLon);

        $latitudeDelta = $latitudeTo - $latitudeFrom;
        $longitudeDelta = $longitudeTo - $longitudeFrom;

        return 6371000 * 2 * asin(sqrt(pow(sin($latitudeDelta / 2), 2) + cos($latitudeFrom) * cos($latitudeTo) * pow(sin($longitudeDelta / 2), 2)));
    }
}
