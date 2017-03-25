<?php
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$collection = new RouteCollection();

// Pages **Just in Case**
$collection->add(
    'PageController_home',
    new Route(
        '/',
        array(
            '_controller' => 'Hack2Hire\FraudDetectionBackend\Controllers\PageController::home'
        ),
        array(
            '_method' => 'GET'
        )
    )
);

$collection->add(
    'PageController_about',
    new Route(
        '/about',
        array(
            '_controller' => 'Hack2Hire\FraudDetectionBackend\Controllers\PageController::about'
        ),
        array(
            '_method' => 'GET'
        )
    )
);

$collection->add(
    'PageController_contact',
    new Route(
        '/contact',
        array(
            '_controller' => 'Hack2Hire\FraudDetectionBackend\Controllers\PageController::contact'
        ),
        array(
            '_method' => 'GET'
        )
    )
);

// DataStream API
$collection->add(
    'DataStreamController_transactions',
    new Route(
        '/api/datastream/transactions',
        array(
            '_controller' => 'Hack2Hire\FraudDetectionBackend\Controllers\DataStreamController::transactions'
        ),
        array(
            '_method' => 'POST'
        )
    )
);

$collection->add(
    'DataStreamController_posdevices',
    new Route(
        '/api/datastream/posdevices',
        array(
            '_controller' => 'Hack2Hire\FraudDetectionBackend\Controllers\DataStreamController::posDevices'
        ),
        array(
            '_method' => 'POST'
        )
    )
);

$collection->add(
    'DataStreamController_zipcodes',
    new Route(
        '/api/datastream/zipcodes',
        array(
            '_controller' => 'Hack2Hire\FraudDetectionBackend\Controllers\DataStreamController::zipCodes'
        ),
        array(
            '_method' => 'POST'
        )
    )
);

// Dashboard API
$collection->add(
    'DashboardController_transactions',
    new Route(
        '/api/dashboard/transactions',
        array(
            '_controller' => 'Hack2Hire\FraudDetectionBackend\Controllers\DashboardController::transactions'
        ),
        array(
            '_method' => 'GET'
        )
    )
);

return $collection;
