<?php

namespace Themosis\Core\Repositories;

use Illuminate\Support\LazyCollection;
use Themosis\Core\Enums\PostStatus;
use WP_Post;
use WP_Query;

class PostRepository
{
    public function allScheduledPosts(?string $postType = null): LazyCollection
    {
        return LazyCollection::make(function () use ($postType) {
            $query = new WP_Query([
                'post_type' => $postType ?? 'any',
                'nopaging' => true,
                'post_status' => PostStatus::future()->value,
            ]);

            while ($query->have_posts()) {
                yield $query->next_post();
            }
        });
    }

    public function getBy(int $id): WP_Post
    {
        return get_post($id);
    }
}
