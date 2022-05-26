<?php

namespace App\Controller;

use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    public function index(\Symfony\Component\HttpFoundation\Request $request): Response
    {
        $session=$request->getSession();
        if ($session->has('nbVisite')){
            $nbreVisite = $session->get('nbVisite') + 1 ;
            $session->set('nbVisite',$nbreVisite);
        }else{
            $nbreVisite = 1 ;
        }
        $session->set('nbVisite',$nbreVisite);

        return $this->render('session/index.html.twig');
    }
}
