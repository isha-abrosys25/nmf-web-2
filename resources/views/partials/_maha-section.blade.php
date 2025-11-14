<?php
use App\Models\ElectionResult;
use App\Models\Mahamukabla;
use App\Models\Candidate;
use App\Models\Party;
use App\Models\HomeSection;

$mahamukablas = Mahamukabla::all();
$candidates = Candidate::all();
$parties = Party::all();
$status = HomeSection::where('title', 'ElectionMahaSection')->value('status') ?? 0;
?>

@php

    $showMaha = HomeSection::where('title', 'ElectionMahaSection')->where('status', 1)->exists();

@endphp

@if ($showMaha)
    <section class="maha-section">
        <script>
            document.addEventListener("DOMContentLoaded", function() {
            let seconds = 0;
            setInterval(function() {
                seconds++;
                const hours = Math.floor(seconds / 3600);
                const minutes = Math.floor((seconds % 3600) / 60);
                const secs = seconds % 60;
                document.getElementById('stopwatch').textContent = 
                String(hours).padStart(2, '0') + ':' + 
                String(minutes).padStart(2, '0') + ':' + 
                String(secs).padStart(2, '0');
            }, 1000);
            });
        </script>
        <div class="cm-container ">
            <div class="maha-block">
                <img class="maha-img" src="{{ asset('asset/images/Maha-Muqabala-Page-1.jpg') }}" alt="Mahamukabla-img">
                <div class="mh-left">
                    <div class="mh-inner">
                        <div class="mh-title-box">
                            <div class="mh-title1">महामुकाबला</div>
                            <div class="mh-title2">महामुकाबला</div>
                        </div>
                        <div class="swiper mh-carousel">
                            <div class="swiper-wrapper mh-swiper-wrapper">
                                @foreach ($mahamukablas as $index => $slide)
                                    <div class="swiper-slide {{ $index == 0 ? 'active' : '' }}">
                                        <img src="{{ $slide->slide_image }}" alt="">
                                    </div>
                                @endforeach
                            </div>
                            <div class="mh-button-prev mh-nav-left">&#8592;</div>
                            <div class="mh-button-next mh-nav-right">&#8594;</div>
                        </div>
                    </div>
                </div>

                <div class="mh-right">
                    <div class="mh-inner2">
                        <div class="mh-title-box">
                            <div class="mh-title3">बड़े चेहरे</div>
                            <div class="mh-title4">बड़े चेहरे</div>
                        </div>

                        <div class="leader-box">
                            @foreach ($candidates as $candidate)
                                @php
                                    $party = $parties->firstWhere('id', $candidate->party_id);
                                @endphp

                                <div class="leader-card">
                                    <!-- Candidate Image -->
                                    <img src="{{ $candidate->candidate_image }}" alt="{{ $candidate->candidate_name }}"
                                        class="leader-img">

                                    <!-- Candidate Info -->
                                    <div class="leader-info">
                                        <h3>{{ $candidate->candidate_name }}</h3>

                                        @php
                                            $status = strtolower($candidate->c_status ?? 'select');
                                        @endphp

                                        {{-- If status is EMPTY → show area only --}}
                                        @if ($status === 'select')
                                            <p>{{ $candidate->area }}</p>
                                        @else
                                            {{-- STATUS SECTION --}}
                                            @if ($status === 'आगे')
                                                <div class="status-box lead">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                        <path d="M256 32L96 224h96v224h128V224h96L256 32z" />
                                                    </svg>
                                                    <span>आगे</span>
                                                </div>
                                            @elseif ($status === 'जीत')
                                                <div class="status-box win">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20"
                                                        height="20" viewBox="0 0 512 512" fill="#22c55e">
                                                        <path
                                                            d="M313.4 32.9c26 5.2 42.9 30.5 37.7 56.5l-2.3 11.4c-5.3 26.7-15.1 52.1-28.8 75.2H464c26.5 0 48 21.5 48 48c0 18.5-10.5 34.6-25.9 42.6C497 275.4 504 288.9 504 304c0 23.4-16.8 42.9-38.9 47.1c4.4 7.3 6.9 15.8 6.9 24.9c0 21.3-13.9 39.4-33.1 45.6c.7 3.3 1.1 6.8 1.1 10.4c0 26.5-21.5 48-48 48H294.5c-19 0-37.5-5.6-53.3-16.1l-38.5-25.7C176 420.4 160 390.4 160 358.3V320 272 247.1c0-29.2 13.3-56.7 36-75l7.4-5.9c26.5-21.2 44.6-51 51.2-84.2l2.3-11.4c5.2-26 30.5-42.9 56.5-37.7zM32 192H96c17.7 0 32 14.3 32 32V448c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32V224c0-17.7 14.3-32 32-32z" />
                                                    </svg>

                                                    <span>जीत</span>
                                                </div>
                                       
                                            @elseif ($status === 'हार')
                                                <div class="status-box loss">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20"
                                                        height="20" viewBox="0 0 512 512" fill="#dc2626"
                                                        transform="scale(-1,1)" style="transform: scaleX(-1);">
                                                        <path d="M198.6 479.1c-26-5.2-42.9-30.5-37.7-56.5l2.3-11.4c5.3-26.7 15.1-52.1 28.8-75.2H48c-26.5 0-48-21.5-48-48
                                                            c0-18.5 10.5-34.6 25.9-42.6C15 236.6 8 223.1 8 208c0-23.4 16.8-42.9 38.9-47.1C42.5 153.6 40 145.1 40 136
                                                            c0-21.3 13.9-39.4 33.1-45.6c-.7-3.3-1.1-6.8-1.1-10.4c0-26.5 21.5-48 48-48h97.5c19 0 37.5 5.6 53.3 16.1l38.5 25.7
                                                            c26.7 17.9 42.7 47.9 42.7 80V192v48v24.9c0 29.2-13.3 56.7-36 75l-7.4 5.9c-26.5 21.2-44.6 51-51.2 84.2l-2.3 11.4
                                                            c-5.2 26-30.5 42.9-56.5 37.7zM480 320h-64c-17.7 0-32-14.3-32-32V64c0-17.7 14.3-32 32-32h64c17.7 0 32 14.3 32 32v224
                                                            c0 17.7-14.3 32-32 32z" />
                                                    </svg>
                                                    <span>हार</span>
                                                </div>
                                                @elseif ($status === 'पीछे')
                                                <div class="status-box loss">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                        <path d="M256 480L416 288h-96V64H192v224H96l160 192z" />
                                                    </svg>
                                                    <span>पीछे</span>
                                                </div>
                                            @else
                                                <div class="status-box neutral">
                                                    <span>{{ ucfirst($candidate->c_status) }}</span>
                                                </div>
                                            @endif
                                        @endif
                                    </div>

                                    <!-- Party Logo -->
                                    <img src="{{ $party && $party->party_logo ? asset($party->party_logo) : asset('asset/images/default-party.png') }}"
                                        alt="{{ $party->party_name ?? 'No Party Logo' }}" class="party-logo">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
     <script>
                document.addEventListener("DOMContentLoaded", function() {
            new Swiper('.mh-carousel', {
                loop: true,
                navigation: {
                    nextEl: '.mh-button-next',
                    prevEl: '.mh-button-prev',
                },
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                },
            });
        });
        </script>
@endif
