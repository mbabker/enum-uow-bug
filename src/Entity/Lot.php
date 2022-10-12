<?php declare(strict_types=1);

namespace App\Entity;

use App\LotStatus;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Lot
{
    #[ORM\Column(nullable: false), ORM\Id, ORM\GeneratedValue]
    public ?int $id = null;

    #[ORM\Column]
    public ?string $number = null;

    #[ORM\Column(length: 400)]
    public string $title = '';

    #[ORM\ManyToOne(inversedBy: 'lots'), ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    public ?Auction $auction = null;

    #[ORM\Column(length: 15, enumType: LotStatus::class)]
    public LotStatus $status = LotStatus::AVAILABLE;
}
