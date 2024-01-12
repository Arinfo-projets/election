<?php

namespace App\Controller;

use App\Entity\Election;
use App\Repository\ElectionRepository;
use App\Repository\VoterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeController extends AbstractController
{

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {

        if($this->getUser()){
            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('home/index.html.twig', [
            'controller' => 'home'
        ]);
    }
}
