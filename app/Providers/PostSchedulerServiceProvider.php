<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Blog;
use App\Services\NotificationService;

class PostSchedulerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind NotificationService so it's always resolvable
        $this->app->singleton(NotificationService::class, function () {
            return new NotificationService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Don't run when in console (artisan, queue, etc.)
        if ($this->app->runningInConsole()) {
            return;
        }

        // Only run once every 2 minutes
        if (!Cache::has('lazy_scheduler_checked')) {
            $blogsToPublish = DB::table('blogs')
                ->where('status', Blog::STATUS_SCHEDULED)
                ->where('published_at', '<=', Carbon::now('Asia/Kolkata'))
                ->orderBy('published_at', 'asc')
                ->get();

            foreach ($blogsToPublish as $blog) {
                $newSequence = $blog->scheduled_sequence_id;
                $oldSequence = $blog->sequence_id;

                // Handle sequence updates
                if ($newSequence > 0) {
                    if ($oldSequence && $newSequence > $oldSequence) {
                        DB::table('blogs')
                            ->where('id', '!=', $blog->id)
                            ->where('status', Blog::STATUS_PUBLISHED)
                            ->whereNotNull('sequence_id')
                            ->whereBetween('sequence_id', [$oldSequence + 1, $newSequence])
                            ->decrement('sequence_id');
                    } elseif ($oldSequence && $newSequence < $oldSequence) {
                        DB::table('blogs')
                            ->where('id', '!=', $blog->id)
                            ->where('status', Blog::STATUS_PUBLISHED)
                            ->whereNotNull('sequence_id')
                            ->whereBetween('sequence_id', [$newSequence, $oldSequence - 1])
                            ->increment('sequence_id');
                    } else {
                        DB::table('blogs')
                            ->where('id', '!=', $blog->id)
                            ->where('status', Blog::STATUS_PUBLISHED)
                            ->whereNotNull('sequence_id')
                            ->where('sequence_id', '>=', $newSequence)
                            ->increment('sequence_id');
                    }
                }

                // Publish blog
                DB::table('blogs')
                    ->where('id', $blog->id)
                    ->update([
                        'status' => Blog::STATUS_PUBLISHED,
                        'created_at' => DB::raw('published_at'),
                        'updated_at' => DB::raw('published_at'),
                        'sequence_id' => $newSequence,
                        'scheduled_sequence_id' => null,
                    ]);

                
                

                // Get updated blog
                $updatedBlog = Blog::find($blog->id);
                Log::info("PostSchedulerServiceProvider: Blog {$blog->id} published.{$updatedBlog->isNotification}");
                Log::info('Updated Blog: ' . json_encode($updatedBlog));

                // Send notification if required
                if ($updatedBlog->isNotification == 1) {
                    Log::info("PostSchedulerServiceProvider: Sending notification for Blog {$updatedBlog->id}");
                    $request = new Request($updatedBlog->toArray());
                    app(\App\Services\NotificationService::class)->sendNotification($request, $updatedBlog->id);
                   
                }
            }

            // Cache so it doesn't run too often
            Cache::put('lazy_scheduler_checked', true, now()->addMinutes(2));
        }
    }
}
