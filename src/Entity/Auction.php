<?php
declare(strict_types=1);

namespace App\Entity;

use App\AuctionStatus;
use App\AuctionType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Auction
{
    #[ORM\Column(nullable: false), ORM\Id, ORM\GeneratedValue]
    public ?int $id = null;

    #[ORM\Column(length: 400)]
    public string $name = '';

    #[ORM\Column(length: 20, enumType: AuctionType::class)]
    public AuctionType $type = AuctionType::LIVE;

    #[ORM\Column(length: 20, enumType: AuctionStatus::class)]
    public AuctionStatus $status = AuctionStatus::PREVIEW;

    /**
     * @var Collection<int, Lot>
     */
    #[ORM\OneToMany(mappedBy: 'auction', targetEntity: Lot::class, cascade: ['persist', 'remove'], fetch: 'EXTRA_LAZY')]
    private Collection $lots;

    public function __construct()
    {
        $this->lots = new ArrayCollection();
    }

    public function addLot(Lot $lot): void
    {
        if (!$this->hasLot($lot)) {
            $this->lots->add($lot);
            $lot->auction = $this;
        }
    }

    /**
     * @return Collection<int, Lot>
     */
    public function getLots(): Collection
    {
        return $this->lots;
    }

    public function hasLot(Lot $lot): bool
    {
        return $this->lots->contains($lot);
    }

    public function removeLot(Lot $lot): void
    {
        if ($this->hasLot($lot)) {
            $this->lots->removeElement($lot);
            $lot->auction = null;
        }
    }
}
