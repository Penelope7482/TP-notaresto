<?php

namespace App\Controller;

use App\Entity\Restaurant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index()
    {
          return $this->render('app/index.html.twig', [
            'restaurants' => $this->getDoctrine()->getRepository(Restaurant::class)->findLastTen(),
        ]);
    }  
}