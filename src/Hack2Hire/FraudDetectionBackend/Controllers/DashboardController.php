<?php
namespace Hack2Hire\FraudDetectionBackend\Controllers;

use Hack2Hire\FraudDetectionBackend\Entities\Transaction;
use Hack2Hire\FraudDetectionBackend\Repositories\POSDeviceRepository;
use Hack2Hire\FraudDetectionBackend\Repositories\ZipCodeRepository;
use Hack2Hire\FraudDetectionBackend\Services\DoctrineService;
use Hack2Hire\FraudDetectionBackend\Services\GeocodeService;

/**
 * Class DashboardController
 * @package Hack2Hire\FraudDetectionBackend\Controllers
 */
class DashboardController extends Controller
{
    public function transactions()
    {
        $doctrine = new DoctrineService();

        /** @var Transaction[] $transactions */
        $transactions = $doctrine->getRepository('Transaction')->findAll();

        usort($transactions, function (Transaction $a, Transaction $b) {
            return strcmp($a->getDateAdded(), $b->getDateAdded());
        });

        /** @var POSDeviceRepository $posDeviceRepository */
        $posDeviceRepository = $doctrine->getRepository('POSDevice');

        /** @var ZipCodeRepository $zipCodeRepository */
        $zipCodeRepository = $doctrine->getRepository('ZipCode');

        $featuresArray = array();
        $count = 0;
        foreach ($transactions as $transaction) {
            $count++;
            if ($count == 101) {
                error_log("101 stopping");
                break;
            }

            // Determine point type
            if ($transaction->getIsFraud()) {
                $name = 'FraudulentTransaction-' . $transaction->getFraudReason();
                $status = 'red';
            } else {
                $name = 'NormalTransaction';
                $status = 'green';
            }

            // Determine lat/lon
            $deviceId = $transaction->getDeviceId();
            $device = $posDeviceRepository->findOneBy(['id' => $deviceId]);
            if (empty($device)) {
                error_log("no device continue");
                //Fraud
                continue;
            }
            $location = explode(", ", $device->getLocation());
            $county = $location[0];
            $state = $location[1];
            $zipCodes = $zipCodeRepository->findBy(['county' => $county, 'name' => $state]);
            if (empty($zipCodes)) {
                error_log("no zip code continue");
                continue;
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
                    continue;
                }
                $zipCode->setLatitude($results[0]);
                $zipCode->setLongitude($results[1]);
                $zipCodeRepository->save($zipCode);
            }

            $featuresArray[] = [
                "id" => $transaction->getId(),
                "type" => "Feature",
                "properties" => [
                    "Name" => $name,
                    "Status" => $status
                ],
                "geometry" => [
                    "type" => "Point",
                    "coordinates" => [
                        $zipCode->getLatitude(), $zipCode->getLongitude()
                    ]
                ]
            ];
        }

        $outputArray = [
            "type" => "FeatureCollection",
            "features" => $featuresArray
        ];

        return $this->createResponse(json_encode($outputArray));
    }
}
