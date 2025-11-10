@php
use App\Models\Blog;
use App\Models\Video;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

$podcastData = Cache::remember('latest_podcast_widget_amp', now()->addMinutes(60), function () {
    $blogPodcast = Blog::where('status', 1)
        ->whereIn('categories_ids', [23])
        ->when(request()->has('ispodcast_homepage'), function ($q) {
            $q->where('ispodcast_homepage', 1);
        })
        ->orderBy('created_at', 'DESC')
        ->first();

    $videoPodcast = Video::where('is_active', 1)
        ->where('category_id', 23)
        ->orderBy('created_at', 'DESC')
        ->first();

    if ($blogPodcast && $videoPodcast) {
        $latestPodcast = $blogPodcast->created_at > $videoPodcast->created_at
            ? $blogPodcast
            : $videoPodcast;
    } else {
        $latestPodcast = $blogPodcast ?? $videoPodcast;
    }

    return [
        'latestPodcast' => $latestPodcast,
        'isBlog' => $latestPodcast instanceof Blog,
        'isVideo' => $latestPodcast instanceof Video,
    ];
});

$latestPodcast = $podcastData['latestPodcast'];
$isBlog = $podcastData['isBlog'];
$isVideo = $podcastData['isVideo'];

function nmf_get_youtube_id_amp($url)
{
    if (!$url) return null;
    preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|v\/))([\w-]+)/i', $url, $m);
    return $m[1] ?? null;
}

$videoId = $isBlog ? nmf_get_youtube_id_amp($latestPodcast->link ?? '') : null;
@endphp


@if ($latestPodcast)
    <div class="news-tabs pb-0 mb-3 mt-3">
        <a class="newstab_title" href="{{ optional($latestPodcast->category)->site_url }}">
            {{ optional($latestPodcast->category)->name }}
        </a>
    </div>

    {{-- ✅ If Blog → YouTube --}}
    @if ($isBlog && $videoId)
        <amp-youtube
            data-videoid="{{ $videoId }}"
            layout="responsive"
            width="480"
            height="270">
        </amp-youtube>

        <div class="podcast_title">
            <h5>{{ $latestPodcast->short_title ?: $latestPodcast->name }}</h5>
        </div>

    {{-- ✅ If Video → Custom AMP Video --}}
    @elseif ($isVideo)
        <amp-video
            width="480"
            height="270"
            layout="responsive"
            poster="{{ $latestPodcast->thumbnail_path ? config('global.base_url_videos') . $latestPodcast->thumbnail_path : '' }}"
            controls>
            <source src="{{ config('global.base_url_videos') . $latestPodcast->video_path }}" type="video/mp4">
        </amp-video>

        <div class="podcast_title">
            <h5>{{ $latestPodcast->title }}</h5>
        </div>
    @endif
@endif
