<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Entity\Review;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tenbest")
 */
class TenBestController extends AbstractController
{
    /**
     * @Route("/", name="tenbest_index", methods={"GET"})
 */
public function index()
{

    /**
     * On récupère les données de notre nouvelle méthode
     */
    $tenBestRestaurantsId = $this->getDoctrine()->getRepository(Review::class)->findTenBestRatings();

    $tenBestRestaurants = array_map(function($data) {
        return $this->getDoctrine()->getRepository(Restaurant::class)->find($data['restaurantId']);
    }, $tenBestRestaurantsId);

    /**
     * On prépare le futur array d'objets Restaurant
     */
    $tenBestRestaurants = [];

    /**
     * On boucle sur le tableau de données retourné par le ReviewRepository
     */
    foreach($tenBestRestaurantsId as $data) {
        // Pour chaque élément on prend le `restaurantId` et on cherche l'objet Restaurant grace au RestaurantRepository :
        $tenBestRestaurants[] = $this->getDoctrine()->getRepository(Restaurant::class)->find($data['restaurantId']);
    }

    return $this->render('app/index.html.twig', [
        // Cette fois, on envoie à Twig notre nouveau tableau
        'restaurants' => $tenBestRestaurants, 
    ]);
}
}