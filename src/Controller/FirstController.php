<?php

namespace App\Controller;

use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FirstController extends AbstractController
{
   #[route('/order/{maVar}',name: 'test.order.route')]
   public function testOrderRoute($maVar){
        return new response("
        <html><body> $maVar</body></html>
       ");
   }

    #[Route('/first', name: 'app_first')]
    public function index(): Response
    {
        return $this->render('first/index.html.twig',[
            'name'=>'Koffi',
            'firstname'=>'Luc Ange'
        ]);
    }

    #[Route('/sayHello/{name}/{firstname}', name: 'say_hello')]
    public function SayHello(\Symfony\Component\HttpFoundation\Request $request, $name,$firstname): Response
    {
       return $this->render('first/hello.html.twig', [
           'nom' => $name,
           'prenom'=>$firstname
       ]);
    }
    #[route(
        'multi/{entier1<\d+>}/{entier2<\d+>}',
        name: 'multiplication'
        //requirements: ['entier1' => '\d+','entier2' => '\d+']
    )]
   public function mulitiplication ($entier1,$entier2) {
        $resultat = $entier1 * $entier2;
        return new response("<h1>$resultat</h1>");
   }
}
