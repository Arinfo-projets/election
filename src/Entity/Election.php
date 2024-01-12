<?php

namespace App\Entity;

use App\Repository\ElectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ElectionRepository::class)]
class Election
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\OneToMany(mappedBy: 'election', targetEntity: Candidate::class, cascade: ['remove'])]
    private Collection $candidates;

    #[ORM\OneToMany(mappedBy: 'election', targetEntity: Vote::class, cascade: ['remove'])]
    private Collection $votes;

    #[ORM\OneToMany(mappedBy: 'election', targetEntity: Voter::class, cascade: ['remove'])]
    private Collection $voters;

    #[ORM\ManyToOne(inversedBy: 'elections')]
    private ?User $user = null;

    #[ORM\Column]
    private ?bool $isOpen = true;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $untilAt = null;

    public function __construct()
    {
        $this->candidates = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->voters = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, Candidate>
     */
    public function getCandidates(): Collection
    {
        return $this->candidates;
    }

    public function addCandidate(Candidate $candidate): static
    {
        if (!$this->candidates->contains($candidate)) {
            $this->candidates->add($candidate);
            $candidate->setElection($this);
        }

        return $this;
    }

    public function removeCandidate(Candidate $candidate): static
    {
        if ($this->candidates->removeElement($candidate)) {
            // set the owning side to null (unless already changed)
            if ($candidate->getElection() === $this) {
                $candidate->setElection(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Vote>
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(Vote $vote): static
    {
        if (!$this->votes->contains($vote)) {
            $this->votes->add($vote);
            $vote->setElection($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): static
    {
        if ($this->votes->removeElement($vote)) {
            // set the owning side to null (unless already changed)
            if ($vote->getElection() === $this) {
                $vote->setElection(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Voter>
     */
    public function getVoters(): Collection
    {
        return $this->voters;
    }

    public function addVoter(Voter $voter): static
    {
        if (!$this->voters->contains($voter)) {
            $this->voters->add($voter);
            $voter->setElection($this);
        }

        return $this;
    }

    public function removeVoter(Voter $voter): static
    {
        if ($this->voters->removeElement($voter)) {
            // set the owning side to null (unless already changed)
            if ($voter->getElection() === $this) {
                $voter->setElection(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function isIsOpen(): ?bool
    {
        return $this->isOpen;
    }

    public function setIsOpen(?bool $isOpen): static
    {
        $this->isOpen = $isOpen;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUntilAt(): ?\DateTimeImmutable
    {
        return $this->untilAt;
    }

    public function setUntilAt(\DateTimeImmutable $untilAt): static
    {
        $this->untilAt = $untilAt;

        return $this;
    }
}
