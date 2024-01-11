<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Election;
use App\Entity\User;
use App\Entity\Vote;
use App\Entity\Voter;
use App\Form\ElectionType;
use App\Form\VoteType;
use App\Repository\CandidateRepository;
use App\Repository\ElectionRepository;
use App\Repository\UserRepository;
use App\Service\ElectionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/election')]
class ElectionController extends AbstractController
{

    public function __construct(
        private UserRepository $userRepository,
        private ElectionRepository $electionRepository,
        private CandidateRepository $candidateRepository,
        private ElectionService $electionService
        )
    {
    }

    #[Route('/', name: 'app_election_index', methods: ['GET'])]
    public function index(ElectionRepository $electionRepository): Response
    {
        return $this->render('election/index.html.twig', [
            'elections' => $electionRepository->findAll(),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name: 'app_election_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $election = new Election();

        $form = $this->createForm(ElectionType::class, $election);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $candidates = $request->get('election')['candidates'] ?? [];
            $voters = $request->get('election')['voters'] ?? [];

            foreach ($candidates as $candidate) {
                $user = $this->userRepository->find($candidate);
                if ($user) {
                    $candidateEntity = new Candidate();
                    $candidateEntity->setUser($user);
                    $candidateEntity->setElection($election);
                    $election->addCandidate($candidateEntity);
                    $election->setUser($this->getUser());
                    $entityManager->persist($candidateEntity);
                }
            }

            foreach ($voters as $voter) {
                $user = $this->userRepository->find($voter);
                if ($user) {
                    $voterEntity = new Voter();
                    $voterEntity->setUser($user);
                    $voterEntity->setElection($election);
                    $election->addVoter($voterEntity);
                    $election->setUser($this->getUser());
                    $entityManager->persist($voterEntity);
                }
            }

            $entityManager->persist($election);
            $entityManager->flush();

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('election/new.html.twig', [
            'election' => $election,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_election_show', methods: ['GET', 'POST'])]
    public function show(
        Election $election,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {

        $vote = new Vote();

        $hasAlreadyVoted = false;
        $userId = $this->getUser()->getId();

        //Create new array of vote ids
        $voteIds = $election->getVotes()->map(fn ($vote) => $vote->getUser()->getId())->toArray();
        if (in_array($userId, $voteIds)) {
            $hasAlreadyVoted = true;
        }

        if ($request->request->get('election') && $request->request->get('user')) {
            $election = $this->electionRepository->find($request->request->get('election'));
            $candidate = $this->candidateRepository->find($request->request->get('candidate'));
            $vote->setElection($election);
            $vote->setUser($this->getUser());
            $vote->setCandidate($candidate ?? null);

            $entityManager->persist($vote);
            $entityManager->flush();

            $this->addFlash('success', 'Votre vote a été pris en compte');

            return $this->redirectToRoute('app_election_show', ['id' => $election->getId()], Response::HTTP_SEE_OTHER);
        }

        if($request->get('is_open')){
            if($election->getUser()->getId() === $this->getUser()->getId()){
                $election->setIsOpen(false);
                $entityManager->persist($election);
                $entityManager->flush();

                $this->addFlash('success', 'Vote fermé');
                return $this->redirectToRoute('app_election_show', ['id' => $election->getId()], Response::HTTP_SEE_OTHER);

            }
        }

        return $this->render('election/show.html.twig', [
            'election' => $election,
            'hasAlreadyVoted' => $hasAlreadyVoted
        ]);
    }

    #[Route('/{id}/edit', name: 'app_election_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Election $election, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ElectionType::class, $election);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_election_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('election/edit.html.twig', [
            'election' => $election,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_election_delete', methods: ['POST'])]
    public function delete(Request $request, Election $election, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $election->getId(), $request->request->get('_token'))) {
            $entityManager->remove($election);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_election_index', [], Response::HTTP_SEE_OTHER);
    }
}
