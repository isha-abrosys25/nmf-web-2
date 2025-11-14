<!doctype html>
<html lang="hi">

<head>

    <title>Express News</title>
    {{-- <meta name="description" content="{{ $metaDescription }}"> --}}
    <meta name="keywords"
        content="{{ isset($data['blog']->keyword) ? $data['blog']->keyword : (isset($setting->keyword) ? $setting->keyword : '') }}">
    <meta charset="UTF-8">
    @if (!str_contains(strtolower(config('global.base_url')), 'stgn'))
        <meta name="robots" content="index, follow" />
    @endif
    <meta name="language" content="hi" />
    <meta name="googlebot" content="notranslate">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @if (config('global.gtm_enabled'))
        <script>
            (function(w, d, s, l, i) {
                w[l] = w[l] || [];
                w[l].push({
                    'gtm.start': new Date().getTime(),
                    event: 'gtm.js'
                });
                var f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s),
                    dl = l != 'dataLayer' ? '&l=' + l : '';
                j.async = true;
                j.src =
                    'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', '{{ config('global.gtm_id') }}');
        </script>
    @endif

    @php
        $current = rtrim(url()->current(), '/');
        $home = rtrim(config('global.base_url_frontend'), '/');
        $canonicalUrl = str_replace('/amp', '/', url()->current());
    @endphp

    @if ($current != $home)
        <link rel="canonical" href="{{ $home }}">
    @else
        <link rel="canonical" href="{{ $canonicalUrl }}">
    @endif
    @yield('head')
    <link href="{{ config('global.base_url_frontend') }}frontend/images/logo.png" rel="shortcut icon"
        type="image-x-icon">

    @if (config('global.schema_enabled'))
    @endif

    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0"></script>

    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <link rel="stylesheet" href="{{ config('global.base_url_asset') }}asset/css/main.css?v=1.18" type="text/css"
        media="all" />
    <link rel="stylesheet" href="{{ config('global.base_url_asset') }}/asset/css/webstory.css" type="text/css"
        media="all" />
    <link rel="stylesheet" href="{{ config('global.base_url_asset') }}/asset/css/category.css?v=1.1" type="text/css"
        media="all" />

    <script type="text/javascript" src="{{ config('global.base_url_asset') }}asset/js/swiper-bundle.min.js"></script>

    <link rel="stylesheet" href="{{ config('global.base_url_asset') }}asset/css/style.css" type="text/css"
        media="all" />
    <style id="theia-sticky-sidebar-stylesheet-TSS">
        .theiaStickySidebar:after {
            content: "";
            display: table;
            clear: both;
        }

        li.item.new a span {
            color: #ffdf00;
            font-weight: 400
        }

        .mobile-new {
            color: #ff0000 !important;
            font-weight: 600
        }
    </style>
</head>

<body style="min-height: 100%; display: flex; flex-direction: column; margin-top: auto; margin-top: 50px;">

  <section
    style="display: flex; flex-direction: column; gap: 20px; justify-content: center;  margin: auto; width: 100%;">
    
    {{-- **THIS WRAPPER IS REQUIRED FOR THE REFRESH** --}}
    <div id="election-live-wrapper">
        @include('partials._election-live-section')
    </div>

    <div id="maha-section-wrapper">
        {{-- This loads the component on the initial page load --}}
        @include('partials._maha-section')
    </div>

</section>

    {{-- 
      * Correction 2:
      * Combined both script blocks into one.
      * This new script correctly re-initializes BOTH the Swiper and the Chart
      * every time the section refreshes.
    --}}
  <script>
    document.addEventListener("DOMContentLoaded", function() {

        // --- 1. REGISTER CHART PLUGIN (Run once) ---
        if (window.Chart && window.ChartDataLabels && !Chart._nmfDataLabelsRegistered) {
            Chart.register(ChartDataLabels);
            Chart._nmfDataLabelsRegistered = true;
        }

        // --- 2. FUNCTION TO CREATE CHART ---
        function createSemiCircleChart(canvasId, results, options = {}) {
            const canvas = document.getElementById(canvasId);
            if (!canvas) return null; // Safely skip if canvas not found

            // Destroy existing chart instance if it exists
            if (canvas._chartInstance) {
                try {
                    canvas._chartInstance.destroy();
                } catch (e) {}
                canvas._chartInstance = null;
            }

            const filteredResults = results.filter(r => r.abbreviation.toLowerCase() !== 'ljp');
            const labels = filteredResults.map(r => r.party_name);
            const values = filteredResults.map(r => r.seats_won);
            const colorMap = {
                'nda': '#fd6101',
                'rjd': '#13B605',
                'jsp': '#FABB00',
                'oth': '#D13A37'
            };
            const colors = filteredResults.map(r => colorMap[r.abbreviation.toLowerCase()] || '#13B605');
            const aspectRatio = (typeof options.aspectRatio !== 'undefined') ?
                options.aspectRatio :
                (window.innerWidth < 768 ? 1 : 1.5);

            const config = {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 2,
                        borderColor: 'white',
                        hoverOffset: 15,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: aspectRatio,
                    rotation: -90,
                    circumference: 180,
                    cutout: options.cutout || '60%',
                    animation: {
                        duration: options.duration || 600
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        },
                        datalabels: {
                            color: 'black',
                            font: {
                                weight: 'bold',
                                size: options.datalabelSize || 14
                            },
                            formatter: () => ''
                        }
                    }
                },
                plugins: []
            };

            if (window.ChartDataLabels) config.plugins.push(ChartDataLabels);
            canvas._chartInstance = new Chart(canvas.getContext('2d'), config);
            return canvas._chartInstance;
        }

        // --- 3. FUNCTION TO INIT MAHA-SWIPER ---
        function initializeMahaSwiper() {
            var swiperContainer = document.querySelector('#maha-section-wrapper .mh-carousel');
            if (swiperContainer) {
                if (swiperContainer.swiper) {
                    swiperContainer.swiper.destroy(true, true);
                }
                new Swiper(swiperContainer, {
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
            }
        }

        // --- 4. FUNCTION TO INIT ALL MAHA-SECTION COMPONENTS ---
        function initializeMahaSectionComponents() {
            initializeMahaSwiper();

            if (typeof results3 !== 'undefined') {
                createSemiCircleChart('semiCircleChart3', results3, {
                    duration: 500
                });
            } else {
                console.log("Maha-Section components loaded. 'results3' not found yet.");
            }
        }

        // --- 5. **NEW** FUNCTION TO INIT LIVE-SECTION ---
        function initializeLiveSectionComponents() {
            console.log("Live section components re-initialized.");
            
            // **IMPORTANT**: Add your JavaScript for the
            // `_election-live-section` partial here.
            // For example, if it has a chart with data `results1`:
            //
            // if (typeof results1 !== 'undefined') {
            //    createSemiCircleChart('electionChart1', results1);
            // }
        }

        // --- 6. FUNCTION TO REFRESH MAHA-SECTION ---
        async function refreshMahaSection() {
            try {
                const response = await fetch('/refresh-maha-section');
                const html = await response.text();
                document.getElementById('maha-section-wrapper').innerHTML = html;
                initializeMahaSectionComponents();
            } catch (error) {
                console.error('Error refreshing Maha Muqabala:', error);
            }
        }

        // --- 7. **NEW** FUNCTION TO REFRESH LIVE-SECTION ---
        async function refreshLiveSection() {
            try {
                // Fetch from the route you created
                const response = await fetch('/refresh-live-section');
                const html = await response.text();
                
                // Target the new wrapper ID
                document.getElementById('election-live-wrapper').innerHTML = html;
                
                // Re-run the JavaScript for the new content
                initializeLiveSectionComponents();

            } catch (error) {
                console.error('Error refreshing Election Live:', error);
            }
        }

        // --- 8. INITIALIZE ON PAGE LOAD ---
        initializeMahaSectionComponents();
        initializeLiveSectionComponents(); // Call the new function

        // --- 9. SET REFRESH INTERVALS ---
        setInterval(refreshMahaSection, 5000); // 5 seconds
        setInterval(refreshLiveSection, 8000); // 8 seconds (or your choice)

        // --- 10. HANDLE RESIZE ---
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                // Re-render maha chart
                if (typeof results3 !== 'undefined') {
                    createSemiCircleChart('semiCircleChart3', results3);
                }
                // **Add your live-section chart here too if it needs to resize**
                // if (typeof results1 !== 'undefined') {
                //     createSemiCircleChart('electionChart1', results1);
                // }
            }, 200);
        });

    });
</script>
</body>

</html>