<?php
declare(strict_types=1);

namespace App;

enum AuctionType: string
{
    case DIRECT_PURCHASE = 'direct_purchase';

    case LIVE = 'live';

    case ONLINE = 'online';
}
