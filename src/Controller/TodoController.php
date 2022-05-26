<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[route("/todo")]
class TodoController extends AbstractController


{
    /**
     * @Route ("/",name="todo")
     */
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        //Afficher notre tableau de todo
        //sinon je l'initialise puis j'affiche
        if (!$session->has('todos')){
            $todos = [
                'achat'=>'Acheter une clé USB',
                'cours'=>'Finaliser mon cours ',
                'correction'=>'Corriger mes examens'
            ];
            $session->set('todos',$todos);
            $this->addFlash('info',"la liste des todos viens d'être initialisée");
        }

        //si j'ai mon tableau de todo dans la session je ne fais que l'afficher

        return $this->render('todo/index.html.twig');
    }
    #[Route(
        "/add/{name?Noemie}/{content?C'est mon enfant}",
        name: 'todo_add',
    )]
    public function addTodo(Request $request, $name , $content) : redirectResponse
    {
        $session = $request->getSession();
      //vérifier si j'ai mon tableau de todo dans la session
        if ($session->has('todos')){
            //si oui
            //Verifier si on a déja un todo avec le même name
            $todos = $session->get('todos');
            if (isset($todos[$name])){
                //si oui afficher erreur
                $this->addFlash('error',"le todo d'id $name existe déja dans la liste ");
            }else{
                //si non on l'ajoute et on affiche un message de succes
                $todos[$name]=$content;
                $session->set('todos', $todos);
                $this->addFlash('success',"le todo d'id $name a été ajouté avec success ");

            }


        }else{
            //si non
            //afficher une erreur et on rediriger vers le controller index
            $this->addFlash('error',"la liste des todos n'est pas encore initialisée");
        }
        return $this->redirectToRoute('todo');
    }

    #[Route('/update/{name}/{content}', name: 'todo_update')]
    public function updateTodo(Request $request, $name , $content) : redirectResponse
    {
        $session = $request->getSession();
        //vérifier si j'ai mon tableau de todo dans la session
        if ($session->has('todos')){
            //si oui
            //Verifier si on a déja un todo avec le même name
            $todos = $session->get('todos');
            if (!isset($todos[$name])){
                //si oui afficher erreur
                $this->addFlash('error',"le todo d'id $name n'existe pas dans la liste ");
            }else{
                //si non on l'ajoute et on affiche un message de succes
                $todos[$name]=$content;
                $session->set('todos', $todos);
                $this->addFlash('success',"le todo d'id $name a été modifié avec success ");

            }


        }else{
            //si non
            //afficher une erreur et on rediriger vers le controller index
            $this->addFlash('error',"la liste des todos n'est pas encore initialisée");
        }
        return $this->redirectToRoute('todo');
    }

    #[Route('/delete/{name}', name: 'todo_delete')]
    public function deleteTodo(Request $request, $name ) : redirectResponse
    {
        $session = $request->getSession();
        //vérifier si j'ai mon tableau de todo dans la session
        if ($session->has('todos')){
            //si oui
            //Verifier si on a déja un todo avec le même name
            $todos = $session->get('todos');
            if (!isset($todos[$name])){
                //si oui afficher erreur
                $this->addFlash('error',"le todo d'id $name n'existe pas dans la liste ");
            }else{
                //si non on l'ajoute et on affiche un message de succes
                unset($todos[$name]);
                $session->set('todos', $todos);
                $this->addFlash('success',"le todo d'id $name a été supprimé avec success ");

            }


        }else{
            //si non
            //afficher une erreur et on rediriger vers le controller index
            $this->addFlash('error',"la liste des todos n'est pas encore initialisée");
        }
        return $this->redirectToRoute('todo');
    }

    #[Route('/reset', name: 'todo_reset')]
    public function resetTodo(Request $request) : redirectResponse
    {
        $session = $request->getSession();
        $session->remove('todos');

        return $this->redirectToRoute('todo');
    }
}
