<?php

namespace App\Controller;

use App\Entity\Personne;

use App\Form\PersonneType;
use App\Service\Helpers;
use App\Service\MailerService;
use App\Service\UploaderService;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('personne')]
class PersonneController extends AbstractController
{

    public function __construct(private LoggerInterface $logger, private Helpers $helpers)
    {}

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
//        echo $this->helpers->saycc();
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
    //Crée un formulaire
    #[Route('/edit/{id?0}', name: 'personne.edit')]
    public function addPersonne(
        Personne $personne=null,
        ManagerRegistry $doctrine,
        Request $request,
        UploaderService $uploaderService,
        MailerService $mailer
    ): Response
    {
        $new=false;
        if (!$personne){
            $new=true;
            $personne = new Personne();
        }


        $form=$this->createForm(PersonneType::class, $personne );
        //supprimer un champs d'un formulaire
        $form->remove('createdAt');
        $form->remove('updateAt');
        //Mon formulaire va allez traiter la requête
        $form->handleRequest($request);
        //Est ce que le formulaire a été soumis
        if ($form->isSubmitted() && $form->isValid()){
            //Si oui
            // on va ajouter l'objet personne dans la base de données

            //ajouter une photo uploads
            $photo = $form->get('photo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($photo) {
                $directory = $this->getParameter('personne_directory');

                $personne->setImage($uploaderService->uploadFile($photo,$directory));
            }


            $manager =  $doctrine->getManager();
            $manager->persist($personne);

            $manager->flush();
            //Afficher un message de succès
            if ($new){
                $message="a été ajouté avec succès";
            }else{
                $message="a été mis a jour avec succès";
            }
//            $mailMessage = $personne->getFirstname().' '.$personne->getName().' '.$message;
            $this->addFlash('success',$personne->getName(). $message);
//            $mailer->sendEmail(content: $mailMessage);
            //Rediriger vers la liste des personnes
            return $this->redirectToRoute('personne.list.alls');
        }else{
            //Si non
            //On affiche notre formulaire
            return $this->render('personne/add-personne.html.twig', [
                'form'=>$form->createView()
            ]);
        }


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