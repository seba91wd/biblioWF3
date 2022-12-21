<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Livre;
use App\Repository\LivreRepository;
use App\Form\FormLivreType;
use Symfony\Component\String\Slugger\AsciiSlugger;

class LivreController extends AbstractController
{
    #[Route('/livre', name: 'app_livre')]
    public function index(LivreRepository $lr): Response
    {
        return $this->render('livre/index.html.twig', [
            'livres' => $lr->findAll(), // findAll renvoie tous les enregistrements d'une table
        ]);
    }


    #[Route('/livre/ajouter', name: 'app_livre_ajouter')]
    public function add(Request $request, LivreRepository $livreRepository): Response
    {
        /**
            La classe Request est une classe qui va rassembler toutes les valeurs des superglobales de PHP($_GET, $_POST, ...);
            Pour instancier un objet de cette classe, on doit passer par les arguments d'une fonction d'un contrôleur,
                on ne peut pas écrire $request = new Request;
            Cette technique s'appelle l'INJECTION DE DÉPENDANCE. Plusieurs classes de Symfony ne peuvent être utilisées que de cette façon : 
                - Request
                - Repository
                - ...
            Pour accéder aux valeurs des superglobales, l'objet Request dispose de propriétés qui correspondent à chaque
            superglobales : 
                $_GET           $request->query
                $_POST          $request->request
                $_FILES         $request->files
                ...             ...
            Chaqune de ces propriétés est un objet qui a une fonction 'get' qui permet d'accéder à une valeur en particulier.

            La classe Request permet aussi d'avoir des informations concernant la requete HTTP en cours.
            Par exemple, pour savoir tester la méthode HTTP de la requete,
                $request->isMethod("POST")
        */

        // dump($request); // la fonction dump est la version Symfony de var_dump
        // dd($request);   // la fonction dd exécute 'dump' suivi de 'die'
        if( $request->isMethod("POST") ) {
            // Récupération des données du formulaire
            $titre = $request->request->get("titre");
            $resume = $request->request->get("resume");
            /**
            On instancie un objet d'une classe entity. C'est a partir de cet objet qu'on va pouvoir enregistrer en bdd
             */
            $livre = new Livre;
            $livre->setTitre($titre);
            $livre->setResume($resume);
             /**
             j'enregistre les données dans la table Livre avec un objet de la classe LivreRepository
             Les objets des classes Repository vont permettre d'executer des requetes SQL sur la table correspondant a la classe.
             La méthode 'save' permet de faire les requetes INSERT INTO et UPDATE.
                Le 1er argument est un objet Entity
                Le 2eme argument doit etre égal a true pour que la requete soit vraiment exécutée (sinon elle est mise en attente)
             */
            $livreRepository->save($livre, true);

            // on redirige vers la route qui affiche la liste des livres
            return $this->redirectToRoute("app_livre");
        }
        return $this->render('livre/form.html.twig');
    }

    #[Route('/livre/modifier/{id}', name: 'app_livre_modifier', requirements: ["id" => "\d+"])]
    public function edit(int $id, LivreRepository $lr, Request $rq)
    {
        $livre = $lr->find($id);
        $form = $this->createForm(FormLivreType::class, $livre);
        /**
        La méthode 'handleRequest' permet a la variable $form de gérer les informations venant de la requete HTTP grace a l'objet Request passé en argument.
         */
        $form->handleRequest($rq);
        if( $form->isSubmitted() && $form->isValid()){
            // est-ce qu'un fichier a ete uploadé ?
            $fichier = $form->get('couverture')->getData();
            if($fichier){
                // récupération du nom du fichier uploadé
                $fileName = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                $slug = new AsciiSlugger();
                $newFileName = $slug->slug($fileName); // retourne une chaine qui ne contient que des caracteres acceptes dans un URL (pas d'espace, d'accent, ...)
                // ajout d'un srting unique pour éviter d'avoir plusieurs fichiers avec le meme nom
                $newFileName .= "_" . uniqid();

                // ajout de l'extension
                $newFileName .= "." . $fichier->guessExtension();

                // copie du fichier uploadé dans un dossier qui doit exister dans le dossier 'public'
                $fichier->move("images", $newFileName);

                $livre->setCouverture($newFileName);
            }
            $lr->save($livre, true);
            return $this->redirectToRoute("app_livre");
        }
        return $this->render("livre/modifier.html.twig", [ "formLivre" => $form->createView() ]);
    }

    #[Route('/livre/supprimer/{id}', name: 'app_livre_supprimer', requirements: ["id" => "\d+"])]
    public function del(Livre $livre, LivreRepository $lr, Request $rq)
    {
        if($rq->isMethod("POST")) {
            $lr->remove($livre, true);
            return $this->redirectToRoute("app_livre");
        }
        return $this->render("livre/confirmation_suppression.html.twig", [
            "livre" => $livre
        ]);
    }
}
