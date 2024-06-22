<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE MATERIALIZED VIEW common_post_scores AS
            SELECT
                posts.id,
                (
                    COUNT(DISTINCT post_favorites.id) * 1 +
                    COUNT(DISTINCT post_comments.id) * 2 +
                    COUNT(DISTINCT post_bookmarks.id) * 1 +
                    GREATEST(5 - EXTRACT(DAY FROM NOW() - posts.created_at), 0)
                ) AS common_score
            FROM posts
            LEFT JOIN post_favorites ON posts.id = post_favorites.post_id
            LEFT JOIN post_comments ON posts.id = post_comments.post_id
            LEFT JOIN post_bookmarks ON posts.id = post_bookmarks.post_id
            WHERE posts.is_active = true
            GROUP BY posts.id;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP MATERIALIZED VIEW IF EXISTS post_scores');
    }
};
