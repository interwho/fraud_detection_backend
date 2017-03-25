<?php
namespace Hack2Hire\FraudDetectionBackend\Controllers;

use Doctrine\DBAL\Query\QueryBuilder;
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

        $queryBuilder = $doctrine->getManager()->createQueryBuilder();

        /** @var Transaction[] $transactions */
        $transactions = $queryBuilder->select('*')
            ->from('transactions', 't')
            ->orderBy('t.date_added', 'DESC')
            ->setMaxResults(100)
            ->getQuery()
            ->execute();

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
            if (empty($device)) {
                //Fraud
                continue;
            }
            $location = explode(", ", $device->getLocation());
            $county = $location[0];
            $state = $location[1];
            $zipCodes = $zipCodeRepository->findBy(['county' => $county, 'name' => $state]);
            if (empty($zipCodes)) {
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
