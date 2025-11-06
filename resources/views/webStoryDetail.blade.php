<!doctype html>
<html ⚡>

<head>
    <meta charset="utf-8">
    <title>{{ $story->name }} - NMFNews</title>
    <link rel="canonical" href="{{ url()->current() }}">
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
    <link rel="icon" href="https://www.newsnmf.com/frontend/images/logo.png" type="image/x-icon">

    <style amp-boilerplate>
        body {
            -webkit-animation: -amp-start 8s steps(1, end) 0s 1 normal both;
            -moz-animation: -amp-start 8s steps(1, end) 0s 1 normal both;
            -ms-animation: -amp-start 8s steps(1, end) 0s 1 normal both;
            animation: -amp-start 8s steps(1, end) 0s 1 normal both
        }

        @-webkit-keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }

        @-moz-keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }

        @-ms-keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }

        @-o-keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }

        @keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }
    </style>
    <noscript>
        <style amp-boilerplate>
            body {
                -webkit-animation: none;
                -moz-animation: none;
                -ms-animation: none;
                animation: none
            }
        </style>
    </noscript>

    <!-- AMP scripts -->
    <script async src="https://cdn.ampproject.org/v0.js"></script>
    <script async custom-element="amp-story" src="https://cdn.ampproject.org/v0/amp-story-1.0.js"></script>
    <script async custom-element="amp-story-auto-ads" src="https://cdn.ampproject.org/v0/amp-story-auto-ads-0.1.js">
    </script>
    <script async custom-element="amp-video" src="https://cdn.ampproject.org/v0/amp-video-0.1.js"></script>

   

@php
    $get_baseUrl = config('global.base_url_web_stories'); // should be https://www.newsnmf.com
    $filePath = $webstories[0]->filepath ?? '';

    // Remove everything before '/file'
    if (strpos($filePath, '/file') !== false) {
        $relativePath = substr($filePath, strpos($filePath, '/file')); 
    } else {
        $relativePath = $filePath; // fallback
    }

    $image = isset($webstories[0]->filename)
        ? rtrim($get_baseUrl, '/') . $relativePath . '/' . $webstories[0]->filename
        : '';

    //$poster_image = $image ?: 'https://www.newsnmf.com/frontend/images/logo.png';
    $poster_image = $image ?: config('global.base_url_frontend') . 'frontend/images/logo.png';


    
@endphp




    <!-- Open Graph / Twitter Metadata -->
    <meta name="description" content="{{ $story->eng_name }}">
    <meta property="og:title" content="{{ $story->name }}">
    <meta property="og:description" content="{{ $story->eng_name }}">
    <meta property="og:image" content="{{ $image }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="article">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $story->name }}">
    <meta name="twitter:description" content="{{ $story->eng_name }}">
    <meta name="twitter:image" content="{{ $image }}">

    <!-- Custom Styles -->
    <style amp-custom>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #000;
            color: #fff;
        }

        amp-story {
            color: #fff;
        }

        .img-source {
            position: absolute;
            left: -21px;
            right: 0px;
            display: block;
            text-align: right;
            color: #8f8f8f;
            font-size: 12px;
            bottom: 14px;
        }

        /* Smooth Zoom-out animation */
        .zoom-animation {
            animation: smoothZoom 10s ease-in infinite alternate;
        }

        @keyframes smoothZoom {
            0% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        .story-title {
            position: absolute;
            bottom: 55px;
            left: 0;
            right: 0;
            font-size: 19px;
            line-height: 1.4;
            color: #fff;
            text-align: center;
            padding: 10px 15px;
            margin: 0 15px;
            background-color: rgba(0, 0, 0, 0.3);
            border-radius: 4px;
            max-width: 90%;
            box-sizing: border-box;
            animation: textEntrance 1.5s ease-out forwards;
            font-weight: 700;
        }

        .dwnld-button {
            line-height: 1;
            background-color: transparent;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            padding: 11px 15px;
            width: fit-content;
            color: #fff;
            margin: 0 auto;
            margin-top: 10px;
            font-weight: 500;
            border-radius: 8px;
            font-size: 17px;
            transition: transform 0.3s;
            /*background: linear-gradient(90deg, rgb(0, 0, 0) 0%, rgb(19, 19, 19) 100%);*/

            background: linear-gradient(357deg, rgb(217 0 0) 0%, rgb(255 0 0) 100%);
        }

        @keyframes textEntrance {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            position: absolute;
            top: 32px;
            left: 25px;
            width: 50px;
            height: 50px;
        }

        .story-bottom-gradient {
            background: linear-gradient(to top, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.3) 30%, transparent 60%);
        }

        .read-more-button {
            display: inline-block;
            text-decoration: none;
            color: #000;
            font-weight: bold;
            background-color: #fff;
            padding: 10px 20px;
            border-radius: 30px;
            font-size: 16px;
            margin: 20px auto;
        }

        .image-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 7px;
            padding: 0;
            width: 100%;
        }

        .story-box {
            text-decoration: none;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            width: 100%;
            height: auto;
        }

        .grid-image {
            width: 100%;
            aspect-ratio: 10 / 12;
            object-fit: cover;
            border-radius: 8px;
            background: #fff;
        }

        .next-story-title {
            margin-top: 8px;
            font-size: 14px;
            font-weight: bold;
            text-align: left;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            color: #333;
            line-height: 20px;
            height: 40px;
        }

        .story-background {
            background: #fff;
        }

        @media screen and (max-width: 1370px) {
            /* .story-box {
                width: 93%;
                height: 263px;
            } */

        }

        @media (max-width: 600px) {

            .image-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            /*
            .story-box {
                width: 100%;
                height: 293px;
            } */
        }
    </style>

    <!-- Google tag (gtag.js) -->
    <script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>

</head>

<body>
    <amp-story standalone title="{{ $story->name }}" publisher="NMFNews"
        publisher-logo-src="{{config('global.base_url_frontend')}}frontend/images/logo.png" poster-portrait-src="{{ $poster_image }}"
        poster-square-src="{{ $poster_image }}" poster-landscape-src="{{ $poster_image }}">

        @foreach ($webstories as $index => $stories)
            @php
                $cat = App\Models\Category::find($story->categories_id);
                $story_file_path = '';
                $thumbnailFilePath = '';

                // Construct file path (image or video)
                if ($stories && $stories->filename) {
                    $relativePath = strstr($stories->filepath, 'file');
                    $story_file_path = $get_baseUrl . $relativePath . '/' . $stories->filename;
                }

                // Process thumbnail only if the file type is 'video'
                if ($stories->file_type === 'video') {
                    $thumbnailPath = $stories->thumb_path;
                    if (!empty($thumbnailPath)) {
                        if (strpos($thumbnailPath, 'file') !== false) {
                            $thumbnailFilePath = substr($thumbnailPath, strpos($thumbnailPath, 'file'));
                        } else {
                            $thumbnailFilePath = ltrim(str_replace(public_path(), '', $thumbnailPath), '/\\');
                        }
                    }
                }
            @endphp

            <amp-story-page id="page{{ $index + 1 }}"
                @if ($stories->file_type !== 'video') auto-advance-after="7s" @endif>
                <amp-story-grid-layer template="fill">
                    @if ($stories->file_type === 'video')
                        <amp-video autoplay loop width="720" height="1280" layout="responsive"
                            poster="{{ $thumbnailFilePath}}" src="{{ $story_file_path }}">
                        </amp-video>
                    @else
                        <amp-img src="{{$story_file_path}}" width="720" height="1280" layout="responsive"
                            alt="{{ $stories->description }}" class="zoom-animation">
                        </amp-img>
                    @endif
                </amp-story-grid-layer>

                <amp-story-grid-layer template="vertical" class="story-bottom-gradient">
                    <amp-img class="logo" src="{{config('global.base_url_frontend')}}frontend/images/logo.png" width="50" height="50"
                        layout="fixed" alt="NMFNews Logo">
                    </amp-img>
                    <div class="story-title">
                        {{ $stories->description }}
                    </div>
                    @if (!empty($stories->credit))
                        <span class="img-source">Credit : {{ $stories->credit }}</span>
                    @endif
                </amp-story-grid-layer>
            </amp-story-page>
        @endforeach

        <!-- Promo Page -->
        {{-- <amp-story-page id="promo-page" auto-advance-after="7s">
            <amp-story-grid-layer template="fill">
                <amp-img src="{{config('global.base_url_web_stories')}}file/webstories/nmfAd.png" width="720" height="1280"
                    layout="responsive" alt="Download NMFNews App" class="zoom-animation">
                </amp-img>
            </amp-story-grid-layer>

            <amp-story-grid-layer template="vertical" class="story-bottom-gradient">
                <amp-img class="logo" src="{{config('global.base_url_asset')}}asset/images/logo.png" width="50" height="50"
                    layout="fixed" alt="NMFNews Logo">
                </amp-img>
            </amp-story-grid-layer>

            <amp-story-page-outlink layout="nodisplay">
                <a class="read-more-button"
                    href="https://play.google.com/store/apps/details?id=com.kmcliv.nmfnews&pli=1">
                    Download App
                </a>
            </amp-story-page-outlink>
        </amp-story-page> --}}
        <!-- Promo Page -->


        @if (!empty($nextStoriesWithImages) && count($nextStoriesWithImages) >= 1)
            <amp-story-page id="next-story-preview-grid" auto-advance-after="7s">
                <amp-story-grid-layer template="vertical" class="story-background">
                    <div class="image-grid">
                        @foreach ($nextStoriesWithImages as $index => $item)
                            @if ($index < 4 && $item['image'])
                                @php
                                    $relativePath = strstr($item['image']->filepath, 'file');
                                    $imgSrc = $get_baseUrl . $relativePath . '/' . $item['image']->filename;
                                    $storyUrl =
                                        asset('/web-stories') .
                                        '/' .
                                        $category->site_url .
                                        '/' .
                                        $item['story']->siteurl;
                                @endphp
                                <a href="{{ $storyUrl }}" class="story-box">
                                    <amp-img src="{{ $imgSrc }}" width="360" height="640"
                                        layout="responsive" alt="Next Story Image {{ $index + 1 }}"
                                        class="grid-image"></amp-img>
                                    <div class="next-story-title">{{ $item['story']->name }}</div>
                                </a>
                                {{-- <a href="{{ $storyUrl }}" class="story-box">
                                    <amp-img
                                        src="https://www.newsnmf.com/file/webstories/2025/08/1_Medium1756448794.jpeg"
                                        width="360" height="640" layout="responsive"
                                        alt="Next Story Image {{ $index + 1 }}" class="grid-image"></amp-img>
                                    <div class="next-story-title">{{ $item['story']->name }}</div>
                                </a> --}}
                            @endif
                        @endforeach
                    </div>
                   <!--  <a href="https://www.newsnmf.com/nmfapps/" class="dwnld-button">
                        <svg viewBox="0 0 256 256" height="20" width="25" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M74.34 85.66a8 8 0 0 1 11.32-11.32L120 108.69V24a8 8 0 0 1 16 0v84.69l34.34-34.35a8 8 0 0 1 11.32 11.32l-48 48a8 8 0 0 1-11.32 0ZM240 136v64a16 16 0 0 1-16 16H32a16 16 0 0 1-16-16v-64a16 16 0 0 1 16-16h52.4a4 4 0 0 1 2.83 1.17L111 145a24 24 0 0 0 34 0l23.8-23.8a4 4 0 0 1 2.8-1.2H224a16 16 0 0 1 16 16m-40 32a12 12 0 1 0-12 12a12 12 0 0 0 12-12"
                                fill="currentColor"></path>
                        </svg>

                        <span class="button__text">Download App</span>
                    </a> -->
                   <amp-story-page-outlink layout="nodisplay"
  cta-text="Download App"
  cta-accent-color="#d90000">
  <a href="https://www.newsnmf.com/nmfapps/" class="dwnld-button">Download App</a>
</amp-story-page-outlink>


                </amp-story-grid-layer>
            </amp-story-page>
        @endif

        <amp-story-auto-ads>
            <script type="application/json">
                {
                "ad-attributes": 
                    {
                        "type": "adsense",
                        "data-ad-client": "ca-pub-3986924419662120",
                        "data-ad-slot": "8262881200"
                    }
                }
            </script>
        </amp-story-auto-ads>


        
       @if (config('global.gtm_enabled'))
            <!-- <amp-analytics
            config="https://www.googletagmanager.com/amp.json?id={{ config('global.gtm_id') }}"
            data-credentials="include">
            </amp-analytics> -->
            <amp-analytics type="gtag" data-credentials="include">
                <script type="application/json">
                {
                    "vars": {
                    "gtag_id": "G-9D3VCPPRWL",
                    "config": {
                        "G-9D3VCPPRWL": { "groups": "default" }
                    }
                    }
                }
                </script>
                </amp-analytics>
        @endif

    </amp-story>
</body>

</html>
