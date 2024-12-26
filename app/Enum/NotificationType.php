<?php

namespace App\Enum;

enum NotificationType: string
{
    case STORY_REJECTED = 'story_rejected';
    case POST_REJECTED = 'post_rejected';
    case POST_COMMENT = 'post_comment';
    case POST_COMMENT_REJECTED = 'post_comment_rejected';
    case POST_FAVORITE = 'post_favorite';
    case POST_RATING = 'post_rating';
}
