@php
    $contentService = app(\App\Services\ContentAggregatorService::class);

    $allItems = $contentService->getAllItems($cat_id, [
        'blogs' => 10,
        'videos' => 5,
        'clips' => 5,
    ])->sortByDesc('created_at')->values()->take(9);

    //$allItems = $allItems->sortByDesc('created_at')->values()->take(9);
    //if ($allItems->count() > 0) {
        // Split into 3 roughly equal chunks
        //$chunks = $allItems->chunk(ceil($allItems->count() / 3));

        // Always define $chunks (empty collection if no items)
        $perCol = max(1, (int) ceil($allItems->count() / 3));
        $chunks = $allItems->chunk($perCol);
    //}
@endphp


<div class="cm-container">
    <div class="news-tabs tbs nwstb">
        <a class="newstab_title" href="{{ asset($cat_site_url) }}">
            <h2>
                {{ $cat_name }}
            </h2>
        </a>
        <a href="{{ $cat_site_url }}">अधिक<i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>
    <div class="cs_row px-0">
        @foreach ($chunks as $chunk)
	{{-- NL1060:27oct2025 - Added --}}
            <div class="custom_left pl-0">
                <div>
                    @foreach ($chunk as $item)
                        <div class="card_small">
                            <div class="card_small_top">

                                {{-- If it's a blog with a link, show video icon --}}
                                @if ($item['type'] !== 'blog')
                                    <div class="video_icon">
                                        <i class="fa-solid fa-video"></i>
                                    </div>
                                @elseif(!empty($item['link']))
                                    <div class="video_icon">
                                        <i class="fa-solid fa-video"></i>
                                    </div>
                                @endif

                                {{-- Thumbnail --}}
                                <a href="{{ $item['url'] }}">
                                    @if (!empty($item['image']))
                                        <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}"
                                            loading="lazy">
                                    @endif
                                </a>
                            </div>

                            <div class="card_small_title">
                                {{-- @if (!empty($item['category']))
                                    <a href="{{ $item['category_url'] }}" class="category_strip">
                                        <span class="category">{{ $item['category'] }}</span>
                                    </a>
                                @endif --}}
                                <a href="{{ $item['url'] }}">
                                    {{ $item['title'] }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
