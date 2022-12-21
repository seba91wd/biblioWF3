<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use stdClass;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="app_test")
     *
     */
    /**
     Une méthode d'un contrôleur qui est liée à une route DOIT retourner un objet de la 
     classe Response
     */
    #[Route('/test/biblio', name: 'app_test')]
    #[IsGranted("ROLE_BIBLIO")]
    public function index(): Response
    {
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    #[Route('/nouvelle-route', name: 'app_test_nouvelle')]
    public function test2(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    /* EXERCICE : ajouter une nouvelle route, pour l'URL "/exercice1"
                    qui affiche le texte suivant : "voici la solution de l'exercice"
                    Vous devez utiliser un nouveau fichier twig pour cet affichage.
    */
    #[Route('/exercice1')]
    public function exercice1(): Response
    {
        return $this->render('test/exercice1.html.twig', [
            "a" => 5.2,
            "toto" => "test"
        ]);
    }

    /**  
     ROUTE PARAMètrée 
     Dans le chemin d'une route, la partie entre {} est un paramètre, c'est-à-dire que c'est une partie dynamique
     du chemin.
     L'option requirements permet de mettre une contrainte sur les paramètres d'une route.
        \d+ est une expression régulière (REGEX)

        ex: [a-zA-Z]+ : 1 string constitué d'au moins 1 lettre, maj ou min
    */

    #[Route('/route-parametree/{a}', name: 'app_test_param', requirements: ['a' => '\d+'])]
    public function param($a): Response
    {
        return $this->render('test/exercice1.html.twig', [
            'a' => $a,
        ]);
    }

    /* EXO : ajouter une route dont le chemin  commence par "/salutation" suivi d'un parametre nommé prenom
                Cette route doit afficher "Bonjour prenom"  
                ⚠ prenom sera remplacé par le prénom qui sera tapé dans l'URL par exemple "/salutation/gertrude"

    */
    #[Route('/salutation/{prenom}', name: 'app_test_salutation')]
    public function salutation($prenom): Response
    {

        return $this->render('test/exercice2.html.twig', [
            'prenom' => $prenom,
        ]);
    }

    #[Route('/boucles', name: 'app_test_boucles')]
    public function boucles()
    {
        $tableau = [ "bonjour", "prénom", 45, true, 78.5 ];
        return $this->render("test/boucles.html.twig", [ "table" => $tableau ]);
    }

    #[Route('/tableau-objet', name: 'app_test_tableau_objet')]
    public function tableauObjet()
    {
        $tableau = [ 
            "nom"       => "Onim",
            "prenom"    => "Anne",
            "age"       => 20
        ];

        $objet = new stdClass;
        $objet->nom = "Ateur";
        $objet->prenom = "Nordine";
        $objet->age = 32;

        return $this->render("test/tableau.html.twig", [
            "tableau" => $tableau,
            "objet"   => $objet
        ]);
    }

    #[Route('/calcul/{nb1}/{nb2}', name: 'app_test_calcul', requirements: ["nb1" => "\d+", "nb2" => "[0-9]+"])]
    public function calcul($nb1, $nb2): Response
    {
        return $this->render("test/calcul.html.twig", [ "nb1" => $nb1, "nb2" => $nb2]);
    }


}
