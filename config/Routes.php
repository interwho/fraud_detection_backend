<?php
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$collection = new RouteCollection();

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

$collection->add(
    'PageController_search',
    new Route(
        '/search',
        array(
            '_controller' => 'Hack2Hire\FraudDetectionBackend\Controllers\PageController::search'
        ),
        array(
            '_method' => 'GET'
        )
    )
);

$collection->add(
    'PageController_twitter',
    new Route(
        '/twitter',
        array(
            '_controller' => 'Hack2Hire\FraudDetectionBackend\Controllers\PageController::twitter'
        ),
        array(
            '_method' => 'GET'
        )
    )
);

return $collection;
