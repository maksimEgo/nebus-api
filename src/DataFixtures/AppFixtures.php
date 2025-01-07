<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use App\Entity\Building;
use App\Entity\Organization;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $activityFood = new Activity();
        $activityFood->setName('Еда');
        $manager->persist($activityFood);

        $activityMeat = new Activity();
        $activityMeat->setName('Мясная продукция');
        $activityMeat->setParent($activityFood);
        $manager->persist($activityMeat);

        $activityDairy = new Activity();
        $activityDairy->setName('Молочная продукция');
        $activityDairy->setParent($activityFood);
        $manager->persist($activityDairy);

        $building1 = new Building();
        $building1->setAddress('г. Москва, ул. Ленина, 1');
        $building1->setLatitude(55.7558);
        $building1->setLongitude(37.6173);
        $manager->persist($building1);

        $building2 = new Building();
        $building2->setAddress('г. Санкт-Петербург, ул. Достоевского, 15');
        $building2->setLatitude(59.9343);
        $building2->setLongitude(30.3351);
        $manager->persist($building2);

        $organization1 = new Organization();
        $organization1->setName('ООО Рога и Копыта');
        $organization1->setPhoneNumbers(['+7-923-111-11-11', '+7-923-222-22-22']);
        $organization1->setBuilding($building1);
        $organization1->addActivity($activityMeat);
        $manager->persist($organization1);

        $organization2 = new Organization();
        $organization2->setName('ЗАО Молочные продукты');
        $organization2->setPhoneNumbers(['+7-923-333-33-33']);
        $organization2->setBuilding($building2);
        $organization2->addActivity($activityDairy);
        $manager->persist($organization2);

        $organization3 = new Organization();
        $organization3->setName('ИП Продукты питания');
        $organization3->setPhoneNumbers(['+7-923-444-44-44']);
        $organization3->setBuilding($building1);
        $organization3->addActivity($activityFood);
        $manager->persist($organization3);

        $manager->flush();
    }
}
