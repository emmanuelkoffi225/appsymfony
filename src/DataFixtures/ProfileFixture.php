<?php

namespace App\DataFixtures;

use App\Entity\Profile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProfileFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
         $profile = new Profile();
         $profile->setRs('Facebook');
         $profile->setUrl('Https://www.facebook.com');

        $profile1 = new Profile();
        $profile1->setRs('Twitter');
        $profile1->setUrl('Https://www.twitter.com');

        $profile2 = new Profile();
        $profile2->setRs('GitHub');
        $profile2->setUrl('Https://www.github.com');


        $manager->persist($profile);
        $manager->persist($profile1);
        $manager->persist($profile2);
        $manager->flush();
    }
}
