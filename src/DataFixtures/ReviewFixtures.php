<?php

namespace App\DataFixtures;

use App\Entity\Review;
use App\Repository\RestaurantRepository;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;



class ReviewFixtures extends Fixture implements DependentFixtureInterface
{
    private $cityRepository;
    private $reviewRepository;
    private $userRepository;



    public function __construct(RestaurantRepository $restaurantRepository, ReviewRepository $reviewRepository, UserRepository $userRepository)
    {
        $this->restaurantRepository = $restaurantRepository;
        $this->reviewRepository = $reviewRepository;
        $this->userRepository = $userRepository;

    }

    public function load(ObjectManager $manager)
    {

        // On configure dans quelles langues nous voulons nos données
        $faker = Faker\Factory::create('fr_FR');

        // on créé 100 review
        for ($i = 0; $i < 100; $i++) {
            $review = new Review();
            $review->setMessage($faker->text(600));
            $review->setRating($faker->numberBetween($min = 0, $max = 5));
            $review->setCreatedAt($faker->dateTime);
            $review->setRestaurant($this->restaurantRepository->find(rand(1, 100)));
            $review->setUser($this->userRepository->findOneBy(["email" => "client@notaresto.com"]));
            $manager->persist($review);
        }
        $manager->flush();

        //on créé 100 reviews enfants
        for ($i = 0; $i < 100; $i++) {
            $review = new Review();
            $review->setMessage($faker->text(600));
            $review->setRating($faker->numberBetween($min = 0, $max = 5));
            $review->setCreatedAt($faker->dateTime);

            $parent = $this->reviewRepository->find(rand(1, 100));
             $review->setParent($parent);
            $review->setRestaurant($parent->getRestaurant());
            $review->setUser($this->userRepository->findOneBy(["email" => "restaurateur@notaresto.com"]));
           
           
            $manager->persist($review);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            AppFixtures::class
        ];
    }
}
