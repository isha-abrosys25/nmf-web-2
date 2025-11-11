<div id="categories-canoon" class="widget widget_categories">
  <!-- NL1024:17Sept:2025:Added home page podcast component to display all places-->
    @if ($categoryName != 'पॉडकास्ट')
        <div class="news-tabs widget_tab">
            <a href="{{ asset('/') }}{{ $category->site_url }}" class="newstab_title"
                style="margin-right: 14px">{{ $category->name ?? '' }}</a>
        </div>
    @endif

    @if ($categoryName == 'पॉडकास्ट')
        @include('components.podcast')
    @else
        <ul class="side_widgets">
            @foreach ($blogs as $blog)
                <?php
                $symbol = $blog->link ? '<i class="fa fa-video-camera" aria-hidden="true" style="color: red;"></i>&nbsp;&nbsp;' : '';
                $truncated = $symbol . (isset($blog->short_title) && $blog->short_title ? $blog->short_title : $blog->name);
                //$ff = config('global.blog_images_everywhere')($blog);
                  $ff  = cached_blog_image($blog); 
                ?>
                <li class="card_small">
                    <div class="card_small_top">
                        <a
                            href="{{ asset('/') }}{{ isset($category->site_url) ? $category->site_url : '' }}/<?php echo isset($blog->site_url) ? $blog->site_url : ''; ?>">
                            <img @if (!empty($ff)) src="{{ asset($ff) }}" @endif
                                alt="{{ isset($blog->short_title) && $blog->short_title ? $blog->short_title : $blog->name }}">
                        </a>
                    </div>
                    <div class="card_small_title">
                        <a
                            href="{{ asset('/') }}{{ isset($category->site_url) ? $category->site_url : '' }}/<?php echo isset($blog->site_url) ? $blog->site_url : ''; ?>"><?php echo $truncated; ?>
                        </a>
                    </div>
                </li>
            @endforeach
        </ul>


        <a href="{{ asset('/') }}{{ $category->site_url }}" class="more_btn mt-3">
            अधिक <i class="fa-solid fa-arrow-right"></i>
        </a>
    @endif
</div>
