@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('/asset/css/allgallery.css') }}" type="text/css" media="all" />
    <div class="cm-container" style="transform: none;">
        <div class="inner-page-wrapper" style="transform: none;">
            <div id="primary" class="content-area" style="transform: none;">
                <main id="main" class="site-main" style="transform: none;">
                    <div class="cm_archive_page" style="transform: none;">
                        <div class="breadcrumb  default-breadcrumb" style="display: block;">
                            <nav role="navigation" aria-label="Breadcrumbs" class="breadcrumb-trail breadcrumbs"
                                itemprop="breadcrumb">
                                <ul class="trail-items" itemscope="" itemtype="http://schema.org/BreadcrumbList">
                                    <meta name="numberOfItems" content="3">
                                    <meta name="itemListOrder" content="Ascending">
                                    <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem"
                                        class="trail-item trail-begin"><a href="/" rel="home"
                                            itemprop="item"><span itemprop="name">Home</span></a>
                                        <meta itemprop="position" content="1">
                                    </li>
                                    <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem"
                                        class="trail-item trail-end"><a href="" itemprop="item"><span
                                                itemprop="name">All Videos</span></a>
                                        <meta itemprop="position" content="3">
                                    </li>
                                </ul>
                            </nav>
                        </div>

                        {{-- Horizontal-1 Advertise --}}
                        <x-horizontal-ad :ad="$videoAds['category_header_ad'] ?? null" />

                        <section class="news_main_section">
                            <div class="cm-container">
                                <div class="news_main_row">
                                    <div class="col_left">
                                        <div class="custom_ct">
                                            <h2>वीडियो</h2>
                                            {{-- <div class="ct_nav">
                                                <a class="n_link active" href="">सभी</a>
                                                <a class="n_link" href="">राजनीति</a>
                                                <a class="n_link" href="">मनोरंजन</a>
                                                <a class="n_link" href="">खेल</a>
                                                <a class="n_link" href="">तकनीक</a>
                                                <a class="n_link" href="">स्वास्थ्य</a>
                                            </div> --}}
                                            <div class="custom_bl">
                                                @php
                                                    $firstBlog = $allVideos->first();
                                                    $remainingVideos = $allVideos->slice(1, 4); // Get next 4 blogs after the first
                                                @endphp
                                                <div class="at_main">
                                                    <!-- Big Swiper for FIRST blog only -->
                                                    <h1 class="rt_main title_video">{{ $firstBlog->title }}</h1>
                                                    <div>
                                                        <div class="">
                                                            @if ($firstBlog)
                                                                <video class="attachment-full size-full wp-post-image"
                                                                    src="{{ config('global.base_url_videos') . $firstBlog->video_path }}"
                                                                    controls playsinline>
                                                                    Your browser does not support the video tag.
                                                                </video>
                                                                <div>

                                                                </div>
                                                                <p class="rt_sub2">
                                                                    {!! $firstBlog->description !!}
                                                                </p>
                                                            @endif
                                                        </div>

                                                    </div>
                                                    <div class="sub-video-items">
                                                        @foreach ($remainingVideos as $video)
                                                            <?php
                                                            $remainingVideoscaturl = 'video/' . $video->category->site_url;
                                                            $remainingVideosurl = $remainingVideoscaturl . '/' . $video->site_url;
                                                            ?>
                                                            <div class="vdo_card">
                                                                <div class="vdo_card_top m-0">
                                                                    <a href="{{ $remainingVideosurl }}" class="vdoCard_img">
                                                                        <img class="thumbnail" loading="lazy"
                                                                            src="{{ !empty($video->thumbnail_path) ? config('global.base_url_videos') . $video->thumbnail_path : config('global.base_url_videos') . 'default-thumbnail.jpg' }}"
                                                                            alt="{{ $video->title ?? 'Video thumbnail' }}">


                                                                    </a>
                                                                    <div class="playBtn-wrap3">
                                                                        <div class="play-btn"> <i
                                                                                class="fa-solid fa-play"></i></div>
                                                                        <p class="v-duration2">
                                                                            {{ $video->duration ?? '00:00' }}
                                                                        </p>
                                                                    </div>

                                                                </div>
                                                                <div class="vdo_card_bottom">
                                                                    <a
                                                                        href="{{ $remainingVideosurl }}">{{ $video->title }}</a>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                @foreach ($categoryWiseVideos as $group)
                                                    {{-- Debug (optional): --}}


                                                    <div class="sub">
                                                        <div class="news-tabs nwstb">
                                                            <a class="newstab_title"
                                                                href="{{ url('nmfvideos/' . $group['category']->site_url) }}">{{ $group['category']->name === 'विधानसभा चुनाव' ? 'बिहार चुनाव' : $group['category']->name }}
                                                            </a>
                                                            <a
                                                                href="{{ url('nmfvideos/' . $group['category']->site_url) }}">अधिक<i
                                                                    class="fa-solid fa-arrow-right"></i></a>
                                                        </div>

                                                        <div class="video-items-left">
                                                            @foreach ($group['videos'] as $video)
                                                                <?php
                                                                $videocaturl = 'video/' . $group['category']->site_url;
                                                                $videourl = 'video/' . $group['category']->site_url . '/' . $video->site_url;
                                                                ?>

                                                                <div class="vdo_card">
                                                                    <div class="vdo_card_top m-0">
                                                                        <a href="{{ $videourl }}"
                                                                            class="vdo__card__img">
                                                                            <div class="playBtn-wrap3">
                                                                                <div class="play-btn"> <i
                                                                                        class="fa-solid fa-play"></i></div>
                                                                                <p class="v-duration2">
                                                                                    {{ $video->duration ?? '00:00' }}
                                                                                </p>
                                                                            </div>
                                                                            <img class="thumbnail" loading="lazy"
                                                                                src="{{ !empty($video->thumbnail_path) ? config('global.base_url_videos') . $video->thumbnail_path : config('global.base_url_videos') . 'default-thumbnail.jpg' }}"
                                                                                alt="{{ $video->title ?? 'Video thumbnail' }}">

                                                                        </a>
                                                                        <a href="{{ $videourl }}" class="play-btn">
                                                                            <i class="fa-solid fa-play"></i>
                                                                        </a>

                                                                        @if ($video->state)
                                                                            <a class="category_strip">
                                                                                <span
                                                                                    class="category">{{ $video->state->name }}</span>
                                                                            </a>
                                                                        @endif


                                                                    </div>
                                                                    <div class="vdo_card_bottom">
                                                                        <a
                                                                            href="{{ $videourl }}">{{ $video->title }}</a>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    {{-- Insert ad after every 2 categories --}}
                                                    @if ($loop->iteration % 2 === 0)
                                                        <div class="video-ad">
                                                            <x-horizontal-sm-ad :ad="$videoAds['category_middle_horz_sm_ad1'] ?? null" />
                                                        </div>
                                                    @endif
                                                @endforeach

                                            </div>

                                        </div>


                                    </div>
                                    <div class="col_right">
                                        {{-- - 10 latest articles displayed - --}}
                                        @include('components.latestStories')

                                        {{-- Vertical-Small-1 Category Advertise --}}
                                        <x-vertical-sm-ad :ad="$videoAds['category_sidebar_vaerical_ad1'] ?? null" />

                                        {{-- Side Widgets --}}
                                        <?php
                                        $categories = [['name' => 'क्या कहता है कानून?', 'limit' => 3], ['name' => 'पॉडकास्ट', 'limit' => 1], ['name' => 'टेक्नोलॉजी', 'limit' => 5], ['name' => 'स्पेशल्स', 'limit' => 5]];
                                        ?>
                                        @foreach ($categories as $cat)
                                            <?php
                                            $category = App\Models\Category::where('name', $cat['name'])->first();
                                            $blogs = App\Models\Blog::where('status', '1')->where('categories_ids', $category->id)->orderBy('updated_at', 'DESC')->limit($cat['limit'])->get()->all();
                                            ?>
                                            @include('components.side-widgets', [
                                                'categoryName' => $cat['name'],
                                                'category' => $category,
                                                'blogs' => $blogs,
                                            ])
                                        @endforeach

                                        {{-- Vertical-Small-2 Category Advertise --}}
                                        <x-vertical-sm-ad :ad="$videoAds['category_sidebar_vaerical_ad2'] ?? null" />

                                    </div>
                                </div>

                                {{-- Horizontal-2 Advertise --}}
                                <x-horizontal-ad :ad="$videoAds['category_bottom_ad'] ?? null" />

                            </div>
                        </section>

                    </div>
                </main>
            </div>
        </div>

    </div>
    {{-- <script>
        const swiper = new Swiper(".mySwiper3", {
            loop: true,
            spaceBetween: 20,
            slidesPerView: 1,
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },

        });
    </script> --}}
@endsection
