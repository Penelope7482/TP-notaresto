<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Entity\RestaurantPicture;
use App\Entity\Review;
use App\Entity\User;
use App\Form\RestaurantPictureType;
use App\Form\RestaurantType;
use App\Form\ReviewType;
use App\Repository\RestaurantRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/restaurant")
 */
class RestaurantController extends AbstractController
{
    /**
     * @Route("/", name="restaurant_index", methods={"GET"})
     */
    public function index(RestaurantRepository $restaurantRepository): Response
    {
        return $this->render('restaurant/index.html.twig', [
            'restaurants' => $restaurantRepository->findAll(),
        ]);
    } 

       /**
     * @Route("/mesresto", name="restaurant_indexrestaurateur", methods={"GET"})
     * @IsGranted("ROLE_RESTAURATEUR")
     */
    public function indexRestaurateur(): Response
    {
        $restaurateur = $this->getUser();
        return $this->render('restaurant/index.html.twig', [
            'restaurants' => $restaurateur->getRestaurants(),
        ]);
    } 
///**
// * @Route("/monresto", name="restaurant_monresto", methods={"GET"})
// */
//
//public function mesResto(){
//    $restaurantsRestaurateurId = $this->getDoctrine()->getRepository(User::class)->findBy(['id'=> 'userId']);
//    $restaurantsRestaurateur = array_map(function($data) {
//        return $this->getDoctrine()->getRepository(Restaurant::class)->find($data['user_id']);
//    }, $restaurantsRestaurateurId);
//
//    $restaurantsRestaurateur = [];
//
//    foreach($restaurantsRestaurateurId as $data) {
//        // Pour chaque élément on prend le `restaurantId` et on cherche l'objet Restaurant grace au RestaurantRepository :
//        $restaurantsRestaurateur[] = $this->getDoctrine()->getRepository(Restaurant::class)->find($data['user_id']);
//    }
//
//    return $this->render('app/index.html.twig', [
//        // Cette fois, on envoie à Twig notre nouveau tableau
//        'users' => $restaurantsUser, 
//    ]);}


///**
// *  @Route("/monresto", name="restaurant_monresto", methods={"GET"})
// */
//public function monresto(RestaurantRepository $restaurantRepository)
//{
//    return $this->render('restaurant/index.html.twig', [
//        'restaurants' => $restaurantRepository->findMesresto(),
//    ]);
//}
//


    /**
     * @Route("/new", name="restaurant_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $restaurant = new Restaurant();
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
          $restaurant= $form->getData();
          $restaurant->setUser($this->getUser());
          $entityManager = $this->getDoctrine()->getManager();
          $entityManager->persist($restaurant); 
          $entityManager->flush();

            return $this->redirectToRoute('app_index');
        }

        return $this->render('restaurant/new.html.twig', [
         //   'restaurant' => $restaurant,
            'form' => $form->createView(),
        ]);
    }

//    /**
//     * @Route("/{id}", name="restaurant_show", methods={"GET"})
//     */
//    public function show(Restaurant $restaurant): Response
//    {
//        return $this->render('restaurant/show.html.twig', [
//            'restaurant' => $restaurant,
//        ]);
//    }

/**
 * Affiche un restaurant
 * @Route("/{restaurant}", name="restaurant_show", methods={"GET", "POST"},  requirements={"restaurant"="\d+"})
 * @param Request $request
 * @param Restaurant $restaurant
 * @return Response
*/
public function show(Request $request, Restaurant $restaurant, FileUploader $fileUploader)
    {
        if ($this->isGranted('ROLE_RESTAURATEUR')){
            $picture = new RestaurantPicture();
            $form = $this->createForm(RestaurantPictureType::class, $picture);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $file = $form['filename']->getData();
                if ($file) {

                    $filename = $fileUploader->upload($file);

                    $picture->setFilename($filename);
                    $picture->setRestaurant($restaurant);

                }

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($picture);
                $entityManager->flush();

                return $this->redirectToRoute('restaurant_show', ['restaurant' => $restaurant->getId()]);
            }

            return $this->render('restaurant/edit.html.twig', [
                'restaurant' => $restaurant,
                'formPicture' => $form->createView(),]);
        }
    
        /**
         * Gestion du formulaire Review
         */

        $review = new Review();

        $formReview = $this->createForm(ReviewType::class, $review);
        $formReview->handleRequest($request);

        if ($formReview->isSubmitted() && $formReview->isValid()) {
            $review = $formReview->getData();

            // Le User de la review est le User connecté
            $review->setUser($this->getUser());

            // Le restaurant de la review est le Restaurant qu'on affiche
            $review->setRestaurant($restaurant);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($review);
            $entityManager->flush();

            // On redirige vers la page du restaurant une fois la review postée
            return $this->redirectToRoute('restaurant_show', ['restaurant' => $restaurant->getId()]);
        }

        /**
         * // Fin de gestion du formulaire Review
         */

        /**
         * Par défaut : on renvoie la vue restaurant/show.html.twig avec:
         * - le restaurant à afficher
         * - le formulaire d'images formPicture
         * - le formulaire de review formReview
         */
        return $this->render('restaurant/show.html.twig', [
            'restaurant' => $restaurant,
            'formReview' => $formReview->createView()
        ]);
    }

    /**
     * @Route("/{restaurant}/edit", name="restaurant_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Restaurant $restaurant): Response
    {
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('restaurant_index');
        }

        return $this->render('restaurant/edit.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="restaurant_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Restaurant $restaurant): Response
    {
        if ($this->isCsrfTokenValid('delete' . $restaurant->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($restaurant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('restaurant_index');
    }

   
}
