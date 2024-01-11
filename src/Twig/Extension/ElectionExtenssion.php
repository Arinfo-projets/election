<?php

namespace App\Twig\Extension;

use App\Service\ElectionService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ElectionExtenssion extends AbstractExtension
{
    private ElectionService $electionService;

    public function __construct(ElectionService $electionService)
    {
        $this->electionService = $electionService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('countVote', [$this->electionService, 'countVote']),
            new TwigFunction('blackVote', [$this->electionService, 'blackVote']),
        ];
    }
}
