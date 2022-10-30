<?php

declare(strict_types=1);

namespace ModernCMS\Core\Abstractions\Assets;

enum AssetType: string
{
    case CSS = 'css';
    case JAVASCRIPT = 'js';
    case IMAGE = 'img';
}
