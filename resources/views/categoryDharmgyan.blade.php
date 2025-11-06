@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{config('global.base_url_asset')}}asset/css/dharm-gyan.css" type="text/css" media="all" />
    <div class="" style="transform: none;">
        <div class="custom-inner-page-wrapper" style="transform: none;">
            <div id="primary" class="content-area" style="transform: none;">
                <main id="main" class="site-main" style="transform: none;">
                    <div class="cm_archive_page" style="transform: none;">
                        <div class="dharm_banner">
                            <div class="cm-container">
                                <div class="breadcrumb  default-breadcrumb" style="display: block;">
                                    <nav role="navigation" aria-label="Breadcrumbs" class="breadcrumb-trail breadcrumbs"
                                        itemprop="breadcrumb">
                                        <ul class="trail-items" itemscope="" itemtype="http://schema.org/BreadcrumbList">
                                            <meta name="numberOfItems" content="3">
                                            <meta name="itemListOrder" content="Ascending">
                                            <li itemprop="itemListElement" itemscope=""
                                                itemtype="http://schema.org/ListItem" class="trail-item trail-begin"><a
                                                    href="/" rel="home" itemprop="item"><span
                                                        itemprop="name">Home</span></a>
                                                <meta itemprop="position" content="1">
                                            </li>
                                            <li itemprop="itemListElement" itemscope=""
                                                itemtype="http://schema.org/ListItem" class="trail-item trail-end"><a
                                                    href="{{ asset('/') }}{{ isset($category->site_url) ? $category->site_url : '-' }}"
                                                    itemprop="item"><span
                                                        itemprop="name">{{ isset($category->name) ? $category->name : '' }}</span></a>
                                                <meta itemprop="position" content="3">
                                            </li>
                                        </ul>
                                    </nav>
                                </div>

                                @php $topAd = $categoryAds['category_header_ad'] ?? null; @endphp
                                <div class="adContainer">
                                    <div id="media_image-1" class="addBgTop bg-white"
                                        style="margin-top:10px!important; margin-bottom:20px!important;">
                                        <div class="adtxt">Advertisement</div>
                                        <div class="ad-section" style="height:130px; overflow:hidden;">
                                            @if ($topAd)
                                                @if ($topAd->is_google_ad)
                                                    <!-- Google Ad -->
                                                    <ins class="adsbygoogle"
                                                        style="display:inline-block; width:300px; height:100px;"
                                                        data-ad-client="{{ $topAd->google_client }}"
                                                        data-ad-slot="{{ $topAd->google_slot }}"></ins>
                                                    <script>
                                                        (adsbygoogle = window.adsbygoogle || []).push({});
                                                    </script>
                                                @else
                                                    <!-- Custom Image Ad -->
                                                    @if (!empty($topAd->file_path) || !empty($topAd->custom_image))
                                                        @php
                                                            $imagePath = $topAd->file_path . '/' . $topAd->custom_image;
                                                        @endphp

                                                        <a href="{{ $topAd->custom_link ?? '#' }}" target="_blank">
                                                            <img src="{{ config('global.base_url_image') . $imagePath }}" alt="Advertisement" />
                                                        </a>
                                                    @endif
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <h2> धर्म ज्ञान</h2>

                            </div>
                            <div class="custom-shape-divider-bottom-1746094967">
                                <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120"
                                    preserveAspectRatio="none">
                                    <rect x="1200" height="3.6"></rect>
                                    <rect height="3.6"></rect>
                                    <path d="M0,0V3.6H580.08c11,0,19.92,5.09,19.92,13.2,0-8.14,8.88-13.2,19.92-13.2H1200V0Z"
                                        class="shape-fill"></path>
                                </svg>
                            </div>
                        </div>
                        <section class="news_main_section ns_wrap">
                            <div class="cm-container">
                                <div class="news_main_row">
                                    <div class="col_left">
                                        <div class="news_main_wrap">
                                            <div class="nws-left">
                                                @if (count($topBlogs) > 0)
                                                    <?php
                                                    $blog = $topBlogs->first();
                                                    $cat = App\Models\Category::where('id', $blog->categories_ids)->first();
                                                    $symbol = $blog->link ? '<i class="fa fa-video-camera" aria-hidden="true" style="color: red;"></i>&nbsp;&nbsp;' : '';
                                                    $truncated = $symbol . $blog->name;
                                                    //$ff = config('global.blog_images_everywhere')($blog);
                                                    $ff  = cached_blog_image($blog); 
                                                    ?>
                                                    <div class="nws_card">
                                                        <div class="nws_card_top dg_top">
                                                            <a
                                                                href="{{ asset('/') }}@if(isset($blog->isLive) && $blog->isLive != 0)live/@endif{{ isset($cat->site_url) ? $cat->site_url : '' }}/<?php echo isset($blog->site_url) ? $blog->site_url : ''; ?>">
                                                                <img @if (!empty($ff)) src="{{ $ff }}" @endif
                                                                    alt="{{ $blog->name }}">
                                                            </a>
                                                            <div class="category_strip">
                                                                <a href="{{ asset('/') }}{{ isset($cat->site_url) ? $cat->site_url : '' }}"
                                                                    class="category">{{ $cat->name ?? '' }}
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="nws_card_bottom">
                                                            <a
                                                                href="{{ asset('/') }}@if(isset($blog->isLive) && $blog->isLive != 0)live/@endif{{ isset($category->site_url) ? $category->site_url : '-' }}/<?php echo isset($blog->site_url) ? $blog->site_url : ''; ?>"><?php echo $truncated; ?>
                                                            </a>
                                                        </div>
                                                        <div class="publish_wrap">
                                                            <div class="publish_dt">
                                                                <i class="fa-regular fa-calendar-days"></i>
                                                                <span>{{ $blog->created_at->format('d M, Y') }}</span>
                                                            </div>
                                                            <div class="publish_tm">
                                                                <i class="fa-regular fa-clock"></i>
                                                                <span>{{ $blog->created_at->format('h:i A') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="nws-right">
                                                @foreach ($topBlogs->skip(1)->take(4) as $blog)
                                                    <?php
                                                    $cat = App\Models\Category::where('id', $blog->categories_ids)->first();
                                                    //$ff = config('global.blog_images_everywhere')($blog);
                                                    $ff  = cached_blog_image($blog); 
                                                    $symbol = $blog->link ? '<i class="fa fa-video-camera" aria-hidden="true" style="color: red;"></i>&nbsp;&nbsp;' : '';
                                                    $truncated = $symbol . $blog->name;
                                                    ?>
                                                    <div class="custom-tab-card">
                                                        <a class="custom-img-link"
                                                            href="{{ asset('/') }}@if(isset($blog->isLive) && $blog->isLive != 0)live/@endif{{ isset($cat->site_url) ? $cat->site_url : '' }}/<?php echo isset($blog->site_url) ? $blog->site_url : ''; ?>">
                                                            <img @if (!empty($ff)) src="{{ asset($ff) }}" @endif
                                                                alt="{{ $blog->name }}">
                                                        </a>
                                                        <div class="custom-tab-title">
                                                            {{-- <a href="{{ asset('/') }}{{ isset($cat->site_url) ? $cat->site_url : '' }}"
                                                                class="nws_article_strip">{{ $cat->name ?? '' }}
                                                            </a> --}}
                                                            <a id="cat-t"
                                                                href="{{ asset('/') }}@if(isset($blog->isLive) && $blog->isLive != 0)live/@endif{{ isset($category->site_url) ? $category->site_url : '-' }}/<?php echo isset($blog->site_url) ? $blog->site_url : ''; ?>"><?php echo $truncated; ?>
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="news_sub_wrap">

                                            {{-- -Horoscope- --}}
                                            <div class="horoscope_container">
                                                <h3>राशिफल 2025</h3>
                                                <?php
                                                $get_rashifal = App\Models\Rashifal::where('status', '1')->get();
                                                ?>
                                                <div id="vertical_tab_nav">
                                                    <ul class="vt_tab_list">
                                                        @foreach ($get_rashifal as $rashifal)
                                                            <?php
                                                            $get_fileName = isset($rashifal) ? $rashifal->full_path . '/' . $rashifal->file_name : '';
                                                            ?>
                                                            <li>
                                                                <a href="">
                                                                    <img src="{{ config('global.base_url_image') . $get_fileName }}" alt="">
                                                                    {{ $rashifal->name }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                    <div class="vt_content">
                                                        @foreach ($get_rashifal as $rashifal)
                                                            <article>
                                                                <p>{{ $rashifal->description }}</p>
                                                            </article>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- -Web Stories- --}}
                                            <?php
                                            $webStories = App\Models\WebStories::where('status', '1')->where('categories_id', $category->id)->orderBy('id', 'DESC')->limit(10)->get();
                                            ?>
                                            @if ($webStories->isNotEmpty())
                                                @include('components.category.cat-web-story', [
                                                    'webStories' => $webStories
                                                ])
                                            @endif
                                            

                                            {{-- -Photo Gallery- --}}
                                            <?php /* ?> ?> ?>
                                            <?php
                                            $cat_name = App\Models\Category::where('name', 'धर्म ज्ञान')->first();
                                            $galleryblogs = App\Models\Blog::with('category')->where('status', '1')->where('categories_ids', $cat_name->id)->whereNull('link')->orderByDesc('id')->limit(6)->get();
                                            ?>

                                            <div class="dharm-img-container">
                                                <h4>फोटो गैलरी</h4>
                                                <div class="news-grid">
                                                    @foreach ($galleryblogs as $blog)
                                                        <?php
                                                        $imageUrl = config('global.blog_images_everywhere')($blog);
                                                        $categorySlug = isset($blog->category->site_url) ? $blog->category->site_url : '';
                                                        $blogUrl = isset($blog->site_url) ? asset($categorySlug . '/' . $blog->site_url) : '';
                                                        ?>
                                                        <div class="news-grid-item">
                                                            <a href="{{ $blogUrl }}">
                                                                <img src="{{ $imageUrl }}" alt="{{ $blog->name }}"
                                                                    loading="lazy">
                                                            </a>
                                                            <div class="news-overlay">
                                                                <a href="{{ $blogUrl }}"
                                                                    class="cstm-news-title">{{ $blog->name }}</a>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <?php */ ?>

                                            {{-- <div class="news-tabs "><a class="newstab_title"
                                                    href="{{ asset('/') }}{{ isset($category->site_url) ? $category->site_url : '-' }}">{{ isset($category->name) ? $category->name : '' }}</a>
                                            </div> --}}

                                            <ul class="nws_list" id="blog-list">
                                                @include('components.category.blog-list', [
                                                    'blogs' => $blogs,
                                                    'categoryAds' => $categoryAds,
                                                    'category' => $category,
                                                ])
                                            </ul>

                                            {{-- <div class="nws_pagination">
                                                
                                                @if ($blogs->onFirstPage())
                                                    <span class="page-btn prev disabled">Previous</span>
                                                @else
                                                    <a href="{{ $blogs->previousPageUrl() }}"
                                                        class="page-btn prev">Previous</a>
                                                @endif

                                                
                                                @foreach ($blogs->links()->elements as $element)
                                                    @if (is_string($element))
                                                        <span class="page-btn">...</span>
                                                    @endif

                                                    @if (is_array($element))
                                                        @foreach ($element as $page => $url)
                                                            <a href="{{ $url }}"
                                                                class="page-btn {{ $blogs->currentPage() == $page ? 'active' : '' }}">
                                                                {{ $page }}
                                                            </a>
                                                        @endforeach
                                                    @endif
                                                @endforeach

                                                
                                                @if ($blogs->hasMorePages())
                                                    <a href="{{ $blogs->nextPageUrl() }}" class="page-btn next">Next</a>
                                                @else
                                                    <span class="page-btn next disabled">Next</span>
                                                @endif
                                            </div> --}}

                                            @if ($blogs->count() > 0)
                                                <div class="text-center my-4">
                                                    <button id="load-more-btn" class="show-more-btn"
                                                        data-offset="{{ $blogs->count() }}"
                                                        data-name="{{ $category->site_url }}"
                                                        data-state="{{ request('state', '') }}"
                                                        data-subcat="{{ request('subcat', '') }}">
                                                        Show More <i class="fa-solid fa-angle-down"></i>
                                                    </button>
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                    <div class="col_right">
                                        {{-- - 10 latest articles displayed - --}}
                                        @include('components.latestStories')

                                        {{-- Vertical-Small-1 Category Advertise --}}
                                        <x-vertical-sm-ad :ad="$categoryAds['category_sidebar_vaerical_ad1'] ?? null" />

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
                                        <x-vertical-sm-ad :ad="$categoryAds['category_sidebar_vaerical_ad2'] ?? null" />

                                    </div>
                                </div>

                                {{-- Horizontal-2 Advertise --}}
                                <x-horizontal-ad :ad="$categoryAds['category_bottom_ad'] ?? null" />

                            </div>
                        </section>

                    </div>
                </main>
            </div>
        </div>

    </div>
    <script>
        const swipernew = new Swiper('.swiper2', {
            direction: 'horizontal',
            loop: true,
            slidesPerView: 2,
            spaceBetween: 10,


            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },

            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },


            scrollbar: {
                el: '.swiper-scrollbar',
            },


            breakpoints: {
                640: {
                    slidesPerView: 2,
                    spaceBetween: 10,
                },
                768: {
                    slidesPerView: 3,
                    spaceBetween: 20,
                },
                1024: {
                    slidesPerView: 5,
                    spaceBetween: 10,
                },
            },
        });
    </script>
@endsection
