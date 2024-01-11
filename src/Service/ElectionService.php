<?php

namespace App\Service;

use App\Repository\ElectionRepository;

class ElectionService
{
    public function __construct(private ElectionRepository $electionRepository) {}

    public function countVote($userId, $votes)
    {
        $count = 0;

        foreach ($votes as $vote) {
            // Vérifier si l'ID de l'utilisateur du candidat correspond à $userId
            if($vote->getCandidate()){
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

        foreach($votes as $vote){
            if(is_null($vote->getCandidate())){
                $count += 1;
            }
        }

        return $count;
    }
}