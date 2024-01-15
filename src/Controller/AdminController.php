<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\PasswordFormType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/users', name: 'app_admin_users')]
    public function index(UserRepository $userRepository): Response
    {

        $users = $userRepository->createQueryBuilder('u')
            ->where('u.roles LIKE :superadminRole')
            ->setParameter('superadminRole', '%ROLE_USER%')
            ->getQuery()
            ->getResult();

        if ($this->isGranted("ROLE_SUPERADMIN")) {
            $users = $userRepository->findAll();
        }



        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/user/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if(!in_array("ROLE_SUPERADMIN", $this->getUser()->getRoles() )){
            if(in_array("ROLE_SUPERADMIN", $user->getRoles())){
                $this->addFlash("error", "Vous n'êtes pas autorisé");
                return $this->redirectToRoute("app_admin_users");
            }
        }

        $roles = [
            "ROLE_USER" => "ROLE_USER"
        ];

        if ($this->isGranted('ROLE_SUPERADMIN')) {
            $roles = [
                "ROLE_USER" => "ROLE_USER",
                "ROLE_ADMIN" => "ROLE_ADMIN",
                "ROLE_SUPERADMIN" => "ROLE_SUPERADMIN"
            ];
        }

        $form = $this->createForm(UserType::class, $user, [
            'roles_choices' => $roles,
            'default_role' => $user->getRoles()[0]
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setRoles([$form->get('roles')->getData()]);

            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur modifier');


            return $this->redirectToRoute('app_admin_users');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }



}
