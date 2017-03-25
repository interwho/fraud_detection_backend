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
    'DataStreamAPIController_transactions',
    new Route(
        '/api/datastream/transactions',
        array(
            '_controller' => 'Hack2Hire\FraudDetectionBackend\Controllers\DataStreamAPIController::transactions'
        ),
        array(
            '_method' => 'POST'
        )
    )
);

$collection->add(
    'DataStreamAPIController_posdevices',
    new Route(
        '/api/datastream/posdevices',
        array(
            '_controller' => 'Hack2Hire\FraudDetectionBackend\Controllers\DataStreamAPIController::posDevices'
        ),
        array(
            '_method' => 'POST'
        )
    )
);

$collection->add(
    'DataStreamAPIController_zipcodes',
    new Route(
        '/api/datastream/zipcodes',
        array(
            '_controller' => 'Hack2Hire\FraudDetectionBackend\Controllers\DataStreamAPIController::zipCodes'
        ),
        array(
            '_method' => 'POST'
        )
    )
);

// Dashboard API
$collection->add(
    'DashboardAPIController_transactions',
    new Route(
        '/api/dashboard/transactions',
        array(
            '_controller' => 'Hack2Hire\FraudDetectionBackend\Controllers\DashboardAPIController::transactions'
        ),
        array(
            '_method' => 'GET'
        )
    )
);

return $collection;
