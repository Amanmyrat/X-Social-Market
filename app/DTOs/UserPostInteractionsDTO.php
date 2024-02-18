<?php

namespace App\DTOs;

class UserPostInteractionsDTO
{
    public array $favoritePostIds;

    public array $bookmarkedPostIds;

    public array $viewedPostIds;

    public function __construct(array $favoritePostIds = [], array $bookmarkedPostIds = [], array $viewedPostIds = [])
    {
        $this->favoritePostIds = $favoritePostIds;
        $this->bookmarkedPostIds = $bookmarkedPostIds;
        $this->viewedPostIds = $viewedPostIds;
    }
}
