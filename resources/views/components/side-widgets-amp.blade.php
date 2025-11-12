@section('head')

<style amp-custom>
    /* ---------------------------
       CATEGORY HEADING (news-tabs)
       --------------------------- */
    .news-tabs {
        font-size: 20px;
        margin-top: 0;
        padding-left: 10px;
        border-left: 4px solid #ff0000;
        color: #1a1a1a;
        font-weight: 700;
        margin-bottom: 15px;
        border-radius: 3px;
        display: block;
    }

    .news-tabs a {
        color: #1a1a1a;
        text-decoration: none;
    }


    /* ---------------------------
       WIDGET BOX (categories-canoon)
       --------------------------- */
    #categories-canoon {
        background-color: #fff;
        margin-bottom: 15px;
        padding: 0 0 10px;
        border-radius: 0;
        border-width: 1px;
        border-style: solid;
        border-color: #fff #fff #c9c9c9;
        border-bottom: 1px solid #c9c9c9;
    }

    #categories-canoon:last-of-type {
        border-bottom: none;
        padding-bottom: 0;
    }


    /* ---------------------------
       MORE BUTTON (अधिक →)
       --------------------------- */
    .more_btn {
        display: inline-block;
        color: #f3242b;
        font-size: 15px;
        font-weight: 600;
        line-height: 17px;
        border-radius: 13px;
        padding: 3px 12px;
        text-decoration: none;
        background-color: transparent;
        margin-top: 12px;
        margin-bottom: 12px;
    }

    .more_btn:hover {
        opacity: 0.8;
    }
</style>
@endsection

@if ($categoryName == 'पॉडकास्ट')
    @php return; @endphp
@endif

<div id="categories-canoon" class="widget widget_categories">

    <div class="news-tabs widget_tab">
        <a href="{{ url($category->site_url) }}" class="newstab_title">
            {{ $category->name ?? '' }}
        </a>
    </div>

    <div class="rel_artcle">

        <div class="swiper-wrapper">

            @foreach ($blogs as $blog)
                @php
                    $truncated = $blog->short_title ?: $blog->name;
                    $ff = cached_blog_image($blog);
                    $url = url(($category->site_url ?? '') . '/' . ($blog->site_url ?? ''));
                    $cat_url = url($category->site_url);
                @endphp

                <div class="swiper-slide">
                    <article class="rel_article">

                        <div class="rel_top">
                            <a href="{{ $url }}">
                                <amp-img src="{{ asset($ff) }}" width="300" height="200"
                                         layout="responsive" alt="{{ $truncated }}">
                                </amp-img>
                            </a>

                            <a href="{{ $cat_url }}" class="nws_article_strip">
                                {{ $category->name ?? '' }}
                            </a>
                        </div>

                        <div class="rel_bottom">
                            <a href="{{ $url }}" class="rel_link">
                                {{ $truncated }}
                            </a>
                        </div>

                    </article>
                </div>

            @endforeach

        </div>

        <a href="{{ url($category->site_url) }}" class="more_btn mt-3">
            अधिक →
        </a>

    </div>

</div>
