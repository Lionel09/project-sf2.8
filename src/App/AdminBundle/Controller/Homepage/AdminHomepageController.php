<?php

namespace App\AdminBundle\Controller\Homepage;

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
        return $this->render('AppAdminBundle:Homepage:index.html.twig');
    }
}
