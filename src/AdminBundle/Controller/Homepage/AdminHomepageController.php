<?php

namespace AdminBundle\Controller\Homepage;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Description
 *
 * @author lionel
 */
class AdminHomepageController extends Controller
{
    /**
    * Homepage
    */
    public function indexAction()
    {
        return $this->render('AdminBundle:Homepage:index.html.twig');
    }
}
