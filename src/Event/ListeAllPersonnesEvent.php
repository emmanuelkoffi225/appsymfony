<?php

namespace App\Event;

use App\Entity\Personne;
use Symfony\Contracts\EventDispatcher\Event;

class ListeAllPersonnesEvent extends Event {
    const LIST_ALL_PERSONNE_EVENT = 'personne.list.alls';

    public function __construct( private int  $nbPersonne ) {

    }

    public function getNbPersonne():int {
        return $this->nbPersonne;
    }

}
