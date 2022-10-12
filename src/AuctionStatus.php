<?php
declare(strict_types=1);

namespace App;

enum AuctionStatus: string
{
    case ACTIVE = 'active';

    case COMPLETED = 'completed';

    case PREVIEW = 'preview';
}
