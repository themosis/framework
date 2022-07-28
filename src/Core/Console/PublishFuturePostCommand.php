<?php

namespace Themosis\Core\Console;

use Carbon\CarbonImmutable;
use Carbon\CarbonTimeZone;
use Illuminate\Console\Command;
use Themosis\Core\Repositories\PostRepository;
use Themosis\Core\WordPressLoader;
use WP_Post;

class PublishFuturePostCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'publish:future-posts {--id=} {--post-type=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish future posts scheduled for the current day and before.';

    /**
     * If no options, publish all scheduled posts of any post-types.
     *
     * --id: publish scheduled post with a specific ID.
     * --post-type: publish all scheduled posts for the given post-type.
     */
    public function handle(WordPressLoader $wordPressLoader, PostRepository $postRepository): void
    {
        $wordPressLoader->load();

        $this->info('Publishing post(s)...');

        $id = $this->option('id');
        $postType = $this->option('post-type');

        $posts = $id
            ? collect([$postRepository->getBy(id: $id)])
            : $postRepository->allScheduledPosts($postType);

        if ($posts->isNotEmpty()) {
            $posts->each(function (WP_Post $post) {
                if (! function_exists('wp_publish_post')) {
                    return;
                }

                if (! function_exists('wp_timezone_string')) {
                    return;
                }

                $tz = CarbonTimeZone::create(wp_timezone_string());
                $now = CarbonImmutable::now($tz);

                if ($now->greaterThanOrEqualTo($post->post_date)) {
                    $this->info("Publishing post with ID: {$post->ID}");
                    wp_publish_post($post->ID);
                }
            });
        }

        $this->info('Post(s) published.');
    }
}
