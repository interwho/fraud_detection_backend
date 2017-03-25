<?php
namespace Hack2Hire\FraudDetectionBackend\Controllers;


/**
 * Class PageController
 * @package Hack2Hire\FraudDetectionBackend\Controllers
 */
class PageController extends Controller
{
    public function home()
    {
        return $this->createResponse($this->render('Home.html.twig', []));
    }

    public function about()
    {
        return $this->createResponse($this->render('About.html.twig', []));
    }

    public function contact()
    {
        return $this->createResponse($this->render('Contact.html.twig', []));
    }
}
