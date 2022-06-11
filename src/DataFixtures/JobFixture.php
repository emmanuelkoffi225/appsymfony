<?php

namespace App\DataFixtures;

use App\Entity\Job;
use ContainerB8BpKqK\getJobRepositoryService;
use ContainerB8BpKqK\getJobRepositoryService as getJobRepositoryServiceAlias;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class JobFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $data=[
          "Informaticien",
          "Docteur",
          "Infirmier",
          "Footballeur",
          "Sage Femme",
          "Mathématicien",
          "Dentiste",
          "Journaliste",
          "Peintre",
          "Ingenieur Batiment"
        ];

        for($i=0;$i<count($data);$i++){
            $job = new Job();
            $job->setDesignation($data[$i]);
            $manager->persist($job);
        }


        $manager->flush();
    }
}
