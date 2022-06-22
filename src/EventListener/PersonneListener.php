<?php

namespace App\EventListener;

use App\Event\AddPersonneEvent;
use App\Event\ListeAllPersonnesEvent;
use PhpParser\Node\Expr\List_;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;

class PersonneListener {
    public function __construct( private LoggerInterface $logger ) {

    }

    public function onPersonneAdd( AddPersonneEvent $event ) {
        $this->logger->debug( "cc je suis entrain d'ecouterl'evenement personne.add et une personne vient d'être ajoutée et c'est". $event->getPersonne()->getName() );
    }

    public function onListAllPersonnes( ListeAllPersonnesEvent $event ) {
        $this->logger->debug( 'Le nombre de personne dans la base est'. $event->getNbPersonne() );
    }

    public function onListAllPersonnes2( ListeAllPersonnesEvent $event ) {
        $this->logger->debug( 'le second Listener avec le nombre:'. $event->getNbPersonne() );
    }

    public function logKernelRequest( KernelEvent $event ) {
        dd( $event->getRequest() );
    }
}
