<?php

namespace App\Helpers;

use League\Fractal\Pagination\PaginatorInterface;
use League\Fractal\Serializer\ArraySerializer;

class FractalSerializer extends ArraySerializer
{
    public function collection(?string $resourceKey, array $data): array
    {
        if ($resourceKey) {
            return [$resourceKey => $data];
        }

        return $data;
    }

    public function item(?string $resourceKey, array $data): array
    {
        if ($resourceKey) {
            return [$resourceKey => $data];
        }

        return $data;
    }

    public function paginator(PaginatorInterface $paginator): array
    {
        $pagination = [
            'current_page' => $paginator->getCurrentPage(),
            'last_page' => $paginator->getLastPage(),
            'per_page' => $paginator->getPerPage(),
            'total' => $paginator->getTotal(),
        ];

        return ['pagination' => $pagination];
    }
}
