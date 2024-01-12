<?php

namespace App\Service;

use App\Entity\Vote;
use App\Repository\ElectionRepository;
use Doctrine\ORM\EntityManagerInterface;

class ElectionService
{
    public function __construct(private ElectionRepository $electionRepository, private EntityManagerInterface $entityManager)
    {
    }

    public function countVote($userId, $votes)
    {
        $count = 0;

        foreach ($votes as $vote) {
            // Vérifier si l'ID de l'utilisateur du candidat correspond à $userId
            if ($vote->getCandidate()) {
                if ($vote->getCandidate()->getUser()->getId() == $userId) {
                    $count += 1;
                }
            }
        }

        return $count;
    }

    public function blackVote($votes)
    {
        $count = 0;

        foreach ($votes as $vote) {
            if (is_null($vote->getCandidate())) {
                $count += 1;
            }
        }

        return $count;
    }

    public function addMissingVotes()
    {
        $electionsRepo = $this->electionRepository->getOldElection();
        if (Count($electionsRepo) > 0) {
            foreach ($electionsRepo as $election) {
                $votes = $election->getVotes();
                $voters = $election->getVoters();

                foreach ($voters as $voter) {
                    $hasVoted = false;

                    foreach ($votes as $vote) {
                        if ($vote->getUser()->getId() == $voter->getUser()->getId()) {
                            $hasVoted = true;
                        }
                    }

                    if (!$hasVoted) {
                        $voteEntity = new Vote(); // Créer un nouvel objet Vote à chaque itération
                        $voteEntity->setCandidate(null);
                        $voteEntity->setUser($voter->getUser());
                        $voteEntity->setElection($voter->getElection());

                        $electionsRepo->persist($voteEntity);
                        $this->entityManager->flush();
                    }
                }

                $election->setIsOpen(false);
                $electionsRepo->persist($election);

            }
        }
    }
}
