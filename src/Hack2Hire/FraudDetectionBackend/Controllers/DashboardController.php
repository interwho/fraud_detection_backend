<?php
namespace Hack2Hire\FraudDetectionBackend\Controllers;

use Hack2Hire\FraudDetectionBackend\Entities\Transaction;
use Hack2Hire\FraudDetectionBackend\Repositories\POSDeviceRepository;
use Hack2Hire\FraudDetectionBackend\Repositories\ZipCodeRepository;
use Hack2Hire\FraudDetectionBackend\Services\DoctrineService;

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

        /** @var POSDeviceRepository $posDeviceRepository */
        $posDeviceRepository = $doctrine->getRepository('POSDevice');

        /** @var ZipCodeRepository $zipCodeRepository */
        $zipCodeRepository = $doctrine->getRepository('ZipCode');

        $featuresArray = array();
        foreach ($transactions as $transaction) {
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
            $location = explode(", ", $device->getLocation());
            $county = $location[0];
            $state = $location[1];
            $zipCode = $zipCodeRepository->findOneBy(['county' => $county, 'name' => $state]);

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
