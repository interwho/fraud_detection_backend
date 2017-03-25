<?php
namespace Hack2Hire\FraudDetectionBackend\Controllers;

use Hack2Hire\FraudDetectionBackend\Entities\POSDevice;
use Hack2Hire\FraudDetectionBackend\Entities\Transaction;
use Hack2Hire\FraudDetectionBackend\Entities\ZipCode;
use Hack2Hire\FraudDetectionBackend\Services\DoctrineService;
use Hack2Hire\FraudDetectionBackend\Services\FraudService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DataStreamController
 * @package Hack2Hire\FraudDetectionBackend\Controllers
 */
class DataStreamController extends Controller
{
    public function transactions()
    {
        if (empty($this->params['id']) && empty($this->params['transactions'])) {
            return $this->createError("Missing Params", Response::HTTP_BAD_REQUEST);
        }

        if (isset($this->params['transactions'])) {
            foreach (json_decode($this->params['transactions']) as $transactionElement) {
                $id = $transactionElement->id;
                $deviceId = $transactionElement->device_id;
                $transactionValue = $transactionElement->transaction_value;
                $accountId = $transactionElement->account_id;
                $tsMillis = $transactionElement->ts_millis;

                $fraudReason = (new FraudService())->isFraud($id, $deviceId, $transactionValue, $accountId, $tsMillis);
                $isFraud = empty($fraudReason) ? false : true;

                $transaction = new Transaction($id, $deviceId, $transactionValue, $accountId, $tsMillis, $isFraud, $fraudReason);

                $em = (new DoctrineService())->getManager();
                $em->persist($transaction);
                $em->flush($transaction);
            }

            return $this->createResponse("OK");
        }

        $id = $this->params['id'];
        $deviceId = $this->params['device_id'];
        $transactionValue = $this->params['transaction_value'];
        $accountId = $this->params['account_id'];
        $tsMillis = $this->params['ts_millis'];

        $fraudReason = (new FraudService())->isFraud($id, $deviceId, $transactionValue, $accountId, $tsMillis);
        $isFraud = empty($fraudReason) ? false : true;

        $transaction = new Transaction($id, $deviceId, $transactionValue, $accountId, $tsMillis, $isFraud, $fraudReason);

        $em = (new DoctrineService())->getManager();
        $em->persist($transaction);
        $em->flush($transaction);

        return $this->createResponse("OK");
    }

    public function posDevices()
    {
        if (empty($this->params['id']) && empty($this->params['pos_devices'])) {
            return $this->createError("Missing Params", Response::HTTP_BAD_REQUEST);
        }

        if (isset($this->params['pos_devices'])) {
            foreach (json_decode($this->params['pos_devices']) as $posDeviceElement) {
                $id = $posDeviceElement->id;
                $location = $posDeviceElement->location;
                $merchantName = $posDeviceElement->merchant_name;

                $posDevice = new POSDevice($id, $location, $merchantName);

                $em = (new DoctrineService())->getManager();
                $em->persist($posDevice);
                $em->flush($posDevice);
            }

            return $this->createResponse("OK");
        }

        $id = $this->params['id'];
        $location = $this->params['location'];
        $merchantName = $this->params['merchant_name'];

        $posDevice = new POSDevice($id, $location, $merchantName);

        $em = (new DoctrineService())->getManager();
        $em->persist($posDevice);
        $em->flush($posDevice);

        return $this->createResponse("OK");
    }

    public function zipCodes()
    {
        if (empty($this->params['zip']) && empty($this->params['zip_codes'])) {
            return $this->createError("Missing Params", Response::HTTP_BAD_REQUEST);
        }

        if (isset($this->params['zip_codes'])) {
            foreach (json_decode($this->params['zip_codes']) as $zipCodeElement) {
                $zip = $zipCodeElement->zip;
                $longitude = $zipCodeElement->longitude;
                $latitude = $zipCodeElement->latitude;
                $city = $zipCodeElement->city;
                $state = $zipCodeElement->state;
                $county = $zipCodeElement->county;
                $name = $zipCodeElement->name;

                $zipCode = new ZipCode($zip, $latitude, $longitude, $city, $state, $county, $name);

                $em = (new DoctrineService())->getManager();
                $em->persist($zipCode);
                $em->flush($zipCode);
            }

            return $this->createResponse("OK");
        }

        $zip = $this->params['zip'];
        $latitude = $this->params['longitude'];
        $longitude = $this->params['latitude'];
        $city = $this->params['city'];
        $state = $this->params['state'];
        $county = $this->params['county'];
        $name = $this->params['name'];

        $zipCode = new ZipCode($zip, $latitude, $longitude, $city, $state, $county, $name);

        $em = (new DoctrineService())->getManager();
        $em->persist($zipCode);
        $em->flush($zipCode);

        return $this->createResponse("OK");
    }
}
