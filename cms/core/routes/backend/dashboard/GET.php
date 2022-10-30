<?php

declare(strict_types=1);

namespace ModernCMS\Core\Routes\Backend\Dashboard;

use Psr\Http\Message\ResponseInterface;

use function ModernCMS\Core\APIs\Views\view_response;

final class GET
{
    public function __invoke(): ResponseInterface
    {
        return view_response('/backend/dashboard.twig');
    }
}
