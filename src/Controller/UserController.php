<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Vote;
use App\Form\PasswordFormType;
use App\Form\UserType;
use App\Repository\ElectionRepository;
use App\Repository\UserRepository;
use App\Repository\VoterRepository;
use App\Service\ElectionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/user')]
class UserController extends AbstractController
{
    public function __construct(private ElectionRepository $electionRepository, private VoterRepository $voterRepository)
    {
    }

    #[Route('/', name: 'app_user_index', methods: ['GET', 'POST'])]
    public function index(UserRepository $userRepository, ElectionService $electionService): Response
    {

        $elections = [];

        $userId = $this->getUser()->getId();

        if (!$this->isGranted('ROLE_ADMIN')) {
            $elections = $this->electionRepository->findAllForUser($userId);
        } else {
            if($this->isGranted('ROLE_SUPERADMIN')){
                $elections = $this->electionRepository->findBy([], ['createdAt' => 'desc']);
            }else{
                $elections =  $this->electionRepository->findAllForAdmin($userId);
            }
        }

        $electionService->addMissingVotes();

        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
            'elections' => $elections,
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {


        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/edit-password', name: 'app_admin_user_edit_password', methods: ['GET', 'POST'])]
    public function editPassword(Request $request,UserPasswordHasherInterface $userPasswordHasher, User $user, EntityManagerInterface $entityManager): Response
    {

        if($user->getId() != $this->getUser()->getId()){
           return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(PasswordFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur modifier');


            return $this->redirectToRoute('app_home');
        }

        return $this->render('user/edit_password.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
}
