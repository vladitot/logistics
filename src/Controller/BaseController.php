<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{
    public function number()
    {
        return $this->render('body/hello.world.html.twig');
    }
}