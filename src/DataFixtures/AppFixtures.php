<?php

namespace App\DataFixtures;

use App\Entity\Restaurant;
use App\Repository\CityRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;



class AppFixtures extends Fixture implements DependentFixtureInterface
{
    private $cityRepository;

    public function __construct(CityRepository $cityRepository)
    {
        $this->cityRepository=$cityRepository;
    }

    public function load(ObjectManager $manager)
    {
        
        // On configure dans quelles langues nous voulons nos données
        $faker = Faker\Factory::create('fr_FR');

        // on créé 100 restaurant
        for ($i = 0; $i < 100; $i++) {
            $restaurant = new Restaurant();
            $restaurant->setName($faker->name);
            $restaurant->setDescription($faker->text);
            $restaurant->setCreatedAt($faker->dateTime);
            $restaurant->setCity($this->cityRepository->find(rand(1, 100)));

            $manager->persist($restaurant);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CityFixtures::class
        ];
    }
}
