<?php

namespace BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BaseController extends Controller
{
    public function indexAction()
    {
        return $this->render('BaseBundle:Default:index.html.twig');
    }
}
