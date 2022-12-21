<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_LECTEUR")]
#[Route("/espace-lecteur")]
class EspaceController extends AbstractController
{
    #[Route('/', name: 'app_espace')]
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('espace/index.html.twig');
    }
}
