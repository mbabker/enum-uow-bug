<?php
declare(strict_types=1);

namespace App;

enum LotStatus: string
{
    case AVAILABLE = 'available';

    case PASSED = 'passed';

    case PREVIEW = 'preview';

    case SOLD = 'sold';
}
