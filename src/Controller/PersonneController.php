<?php

namespace App\Controller;

use App\Entity\Personne;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('personne')]
class PersonneController extends AbstractController
{
    #[Route('/', name: 'personne.list')]
    public function index(ManagerRegistry $doctrine):response{
        $repositiry = $doctrine->getRepository(Personne::class);
        $personnes  = $repositiry->findAll();
        return $this->render('personne/index.html.twig',['personnes'=>$personnes]);
    }

    #[Route('/alls/age/{ageMin}/{ageMax}', name: 'personne.list.age')]
    public function personnesByAge(ManagerRegistry $doctrine ,$ageMin,$ageMax):response{
        $repositiry = $doctrine->getRepository(Personne::class);
        $personnes  = $repositiry->findPersonnesByAgeInterval($ageMin,$ageMax);
        return $this->render('personne/index.html.twig',['personnes'=>$personnes]);
    }

    #[Route('/stats/age/{ageMin}/{ageMax}', name: 'personne.stats.age')]
    public function statsPersonnesByAge(ManagerRegistry $doctrine ,$ageMin,$ageMax):response{
        $repositiry = $doctrine->getRepository(Personne::class);
        $stats  = $repositiry->statsPersonnesByAgeInterval($ageMin,$ageMax);
        return $this->render('personne/stats.html.twig',[
            'stats'=>$stats[0],
            'ageMin'=>$ageMin,
            'ageMax'=>$ageMax
        ]);
    }

    #[Route('/alls/{page?1}/{nbre?12}', name: 'personne.list.alls')]
    public function indexalls(ManagerRegistry $doctrine , $page , $nbre):response{
        $repositiry = $doctrine->getRepository(Personne::class);
        $nbPersonne = $repositiry->count([]);
        $nbrePage =  ceil($nbPersonne / $nbre);
        $personnes  = $repositiry->findBy([] , [],$nbre,($page-1)*$nbre);
        return $this->render('personne/index.html.twig',[
            'personnes'=>$personnes,
            'ispaginated'=>true,
            'nbrePage' =>$nbrePage,
            'page' => $page,
            'nbre' => $nbre
        ]);
    }

    #[Route('/{id<\d+>}', name: 'personne.detail')]
    public function detail( Personne $personne = null):response{

        if(!$personne){
            $this->addFlash('error',"La personne n'existe pas ");
            return $this->redirectToRoute('personne.list');
        }

        return $this->render('personne/detail.html.twig',['personne'=>$personne]);
    }

    #[Route('/add', name: 'app_personne')]
    public function addPersonne(ManagerRegistry $doctrine): Response
    {
        $entityManager =  $doctrine->getManager();
        $personne = new personne();
        $personne->setFirstname('Luc Ange');
        $personne->setName('Koffi');
        $personne->setAge(25);

//        $personne2 = new personne();
//        $personne2->setFirstname('Martine');
//        $personne2->setName('Azamati');
//        $personne2->setAge(19);

        //Ajouter l'operation d'insertion de la personne dans ma transaction
//        $entityManager->persist($personne);
        //$entityManager->persist($personne2);

        //Execute la transaction todo
        $entityManager->flush();
        return $this->render('personne/detail.html.twig', [
            'personne' => $personne,
        ]);
    }
    
    #[Route('/delete/{id}',name: 'personne.delete')]
    public function deletePersonne(Personne $personne=null , ManagerRegistry $doctrine): RedirectResponse{
            //Recuperer la personne
        if ($personne){
            //Si la personne existe => le supprimer et retourner un flashMessage de success
            $manager = $doctrine->getManager();
            //Ajoute la fonction de suppression dans la transaction
            $manager->remove($personne);
            //Execute la transaction
            $manager->flush();
            $this->addFlash('success',"La personne a été supprimer avec succès");
        }else{
            //Sinon retourner un flashMessage d'erreur
            $this->addFlash('error',"Personne inexistante");

        }
       return $this->redirectToRoute('personne.list.alls');

    }

    #[Route('/update/{id}/{name}/{firstname}/{age}',name: 'personne.update')]
    public function updatePersonne(Personne $personne = null,ManagerRegistry $doctrine,$name,$firstname,$age):Response{
        //Véifier que la personne a mettre a jour existe
        if ($personne){
            //Si la personne existe => mettre a jour notre personne + message de succès
            $personne->setName($name);
            $personne->setFirstname($firstname);
            $personne->setAge($age);

            $manager= $doctrine->getManager();
            $manager->persist($personne);
            $manager->flush();
            $this->addFlash('success',"La personne a été mise a jour  avec succès");
        }else{
            //sinon => declencher un message d'erreur
            $this->addFlash('error',"personne inexistante");
        }
        return $this->redirectToRoute('personne.list.alls');
    }
}
