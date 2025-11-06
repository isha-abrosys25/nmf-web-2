@extends('layouts.app')

@section('content')
    <div class="cm-container" style="transform: none;">
        <div class="inner-page-wrapper" style="transform: none;">
            <div id="primary" class="content-area" style="transform: none;">
                <main id="main" class="site-main" style="transform: none;">
                    <div class="cm_post_page_lay_wrap mt-2" style="transform: none;">
                        <a href="{{ config('global.base_url') }}state-legislative-assembly-election?state=bihar"
                            style="display:inline-block;text-decoration:none;background:#000000;color:#fff;padding:6px 16px; margin-bottom:10px; border-radius:6px;font-size:16px;border:1px solid rgba(0,0,0,0.05);cursor:pointer;"
                            title="Back to Bihar Election 2025" role="button" aria-label="Back to Bihar Election 2025">
                            ← Back to Bihar Election 2025
                        </a>
                         <h4 class="my-2">बिहार विधानसभा चुनाव 2025 फेज 2</h4>
                      <!--   <iframe src="{{ config('global.base_url') }}Second-Phase-122-Assembly-Constituencies.pdf" width="100%" height="800px"
                            style="border:none;">
                        </iframe> -->
                        <iframe
                        src="https://docs.google.com/gview?url={{ config('global.base_url') }}Second-Phase-122-Assembly-Constituencies.pdf&embedded=true"
                        style="width:100%; height:800px;" frameborder="0">
                        </iframe>

                    </div>
                </main>
            </div>
        </div>
    </div>
@endsection
