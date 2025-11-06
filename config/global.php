<?php

use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use App\Models\File;
use App\Models\BlogVideoGifs;
use App\Models\WebStoryFiles;

return [
    /**
     * Sanitizes HTML content for AMP pages by converting
     * standard embeds to their AMP-compliant equivalents.
     *
     * @param string $html The raw HTML content.
     * @return string The sanitized, AMP-compliant HTML.
     */
'sanitize_amp_content' => function ($html) {
    if (empty($html)) {
        return '';
    }

    // 1. Decode HTML entities first (e.g., &lt;blockquote&gt; -> <blockquote>)
    $html = html_entity_decode($html);

    // 2. ⭐ Convert Facebook <iframe> embeds FIRST
    // We must run this *before* the generic iframe stripper in step 3
    $html = preg_replace_callback(
        // This regex looks for the specific Facebook plugin iframe
        '/<iframe[^>]*src="https?:\/\/www\.facebook\.com\/plugins\/(?:post|video)\.php\?href=([^"&]+)[^"]*"[^>]*><\/iframe>/i',
        function ($m) {
            // $m[1] is the URL-encoded href (e.g., "https%3A%2F%2F...")
            // We must decode it to get the clean URL.
            $decoded_href = urldecode($m[1]);
            return '<amp-facebook width="552" height="310" layout="responsive" data-href="'.$decoded_href.'"></amp-facebook>';
        },
        $html
    );

    // 2b. ⭐ NEW: Convert YouTube <iframe> embeds
    // This must also run before the stripper in Step 3
    $html = preg_replace_callback(
        // This regex looks for the specific YouTube embed iframe
        '/<iframe[^>]*src="https?:\/\/(?:www\.)?youtube\.com\/embed\/([a-zA-Z0-9_-]+)[^"]*"[^>]*><\/iframe>/i',
        function ($m) {
            // $m[1] is the YouTube Video ID
            return '<amp-youtube data-videoid="'.$m[1].'" layout="responsive" width="480" height="270"></amp-youtube>';
        },
        $html
    );


    // 3. Clean ALL OTHER invalid tags (style, script, and any remaining iframes)
    $html = preg_replace('/<(script|style|iframe|video|source|embed|object)[^>]*>.*?<\/\1>/si', '', $html);
    $html = preg_replace('/\sstyle=(\'|")(.*?)\1/i', '', $html);

    // 4. Convert <img> to <amp-img>
    $html = preg_replace_callback(
        '/<img[^>]+>/i',
        function ($match) {
            preg_match('/src=["\']([^"\']+)["\']/', $match[0], $srcMatch);
            $src = $srcMatch[1] ?? '';
            if (empty($src)) return '';

            preg_match('/alt=["\']([^"\']+)["\']/', $match[0], $altMatch);
            $alt = $altMatch[1] ?? 'image';

            return '<amp-img src="'.$src.'" alt="'.htmlspecialchars($alt).'" width="600" height="400" layout="responsive"></amp-img>';
        },
        $html
    );

    // 5. Convert YouTube (RAW LINKS)
    // This will now only catch raw text links, as iframes were handled in 2b
    $html = preg_replace_callback(
        '/https?:\/\/(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/i',
        fn($m) => '<amp-youtube data-videoid="'.$m[1].'" layout="responsive" width="480" height="270"></amp-youtube>',
        $html
    );

    // 6. Convert Twitter (Blockquotes FIRST, then raw links)
    $html = preg_replace_callback(
        '/<blockquote[^>]*twitter[^>]*>.*?twitter\.com\/[^\/]+\/status\/(\d+).*?<\/blockquote>/is',
        fn($m) => '<amp-twitter width="375" height="472" layout="responsive" data-tweetid="'.$m[1].'"></amp-twitter>',
        $html
    );
    $html = preg_replace_callback(
        '/(?:<p>)?https?:\/\/(?:www\.)?(twitter\.com|x\.com)\/[^\/]+\/status\/(\d+)(?:.*?)(?:<\/p>)?/i',
        fn($m) => '<amp-twitter width="375" height="472" layout="responsive" data-tweetid="'.$m[2].'"></amp-twitter>',
        $html
    );

    // 7. Convert Instagram (Blockquotes FIRST, then raw links)
    $html = preg_replace_callback(
        '/<blockquote[^>]*instagram-media[^>]*>.*?\/(?:p|reel)\/([a-zA-Z0-9_-]+)\/.*?(<\/blockquote>)/is',
        fn($m) => '<amp-instagram data-shortcode="'.$m[1].'" width="400" height="400" layout="responsive"></amp-instagram>',
        $html
    );
    $html = preg_replace_callback(
        '/(?:<p>)?https?:\/\/(?:www\.)?instagram\.com\/(?:p|reel)\/([a-zA-Z0-9_-]+)\/?(?:.*?)(?:<\/p>)?/i',
        function ($m) {
            return '<amp-instagram data-shortcode="'.$m[1].'" width="400" height="500" layout="responsive"></amp-instagram>';
        },
        $html
    );
    
    // 8. Convert Facebook (Embedded Divs and Raw Links)
    $html = preg_replace_callback(
        '/<div class="fb-post"[^>]*data-href="([^"]+)"[^>]*><\/div>/i',
        fn($m) => '<amp-facebook width="552" height="310" layout="responsive" data-href="'.$m[1].'"></amp-facebook>',
        $html
    );
    $html = preg_replace_callback(
        '/(?:<p>)?(https?:\/\/(?:www\.)?facebook\.com\/(?:[a-zA-Z0-9_.-]+\/(?:posts|videos)\/|video\.php\?v=)([0-9]+))(?:.*?)(?:<\/p>)?/i',
        fn($m) => '<amp-facebook width="552" height="310" layout="responsive" data-href="'.$m[1].'"></amp-facebook>',
        $html
    );

    return $html;
},
    'sequence_global_array' => [
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
            '6' => '6',
            '7' => '7',
            '8' => '8',
            '9' => '9',
            '10' => '10'       
    ],

        'candidates_status' => [
            'SELECT_ONE',
            'WIN',
            'LOW',
            'LEAD',
            'LOSS',
            'TRAIL',
            'जीते',
            'हारे',
            'आगे',
            'पीछे',
    
    ],
    'area' => [
        'वाल्मीकिनगर',
        'रामनगर',
        'नरकटियागंज',
        'बगहा',
        'लौरिया',
        'नौतन',
        'चनपटिया',
        'बेतिया',
        'सिकटा',
        'रक्सौल',
        'सुगौली',
        'नरकटिया',
        'हरसिद्धि',
        'गोविन्दगंज',
        'केसरिया',
        'कल्याणपुर',
        'पिपरा',
        'मधुबन',
        'मोतिहारी',
        'चिरैया',
        'ढाका',
        'शिवहर',
        'रीगा',
        'बथनाहा',
        'परिहार',
        'सुरसन्ड',
        'बाजपट्टी',
        'सीतामढ़ी',
        'रून्नीसैदपुर',
        'बेलसंड',
        'हरलाखी',
        'बेनीपट्टी',
        'खजौली',
        'बाबूबरही',
        'बिस्फी',
        'मधुबनी',
        'राजनगर',
        'झंझारपुर',
        'फुलपरास',
        'लौकहा',
        'निर्मली',
        'पिपरा',
        'सुपौल',
        'त्रिवेणीगंज',
        'छातापुर',
        'नरपतगंज',
        'रानीगंज',
        'फारबिसगंज',
        'अररिया',
        'जोकीहाट',
        'सिकटी',
        'बहादुरगंज',
        'ठाकुरगंज',
        'किशनगंज',
        'कोचाधामन',
        'अमौर',
        'बायसी',
        'कसबा',
        'बनमनखी',
        'रूपौली',
        'धमदाहा',
        'पूर्णिया',
        'कटिहार',
        'कदवा',
        'बलरामपुर',
        'प्राणपुर',
        'मनिहारी',
        'बरारी',
        'कोढ़ा',
        'आलमनगर',
        'बिहारीगंज',
        'सिंघेश्वर',
        'मधेपुरा',
        'सोनबरसा',
        'सहरसा',
        'सिमरी बख्तियारपुर',
        'महिशी',
        'कुशेश्वरस्थान',
        'गौड़ाबौराम',
        'बेनीपुर',
        'अलीनगर',
        'दरभंगा ग्रामीण',
        'दरभंगा',
        'हायाघाट',
        'बहादुरपुर',
        'केवटी',
        'जाले',
        'गायघाट',
        'औराई',
        'मीनापुर',
        'बोचहाँ',
        'सकरा',
        'कुढ़नी',
        'मुजफ्फरपुर',
        'काँटी',
        'बरूराज',
        'पारू',
        'साहेबगंज',
        'बैकुण्ठपुर',
        'बरौली',
        'गोपालगंज',
        'कुचायकोट',
        'भोरे',
        'हथुआ',
        'सिवान',
        'जीरादेई',
        'दरौली',
        'रघुनाथपुर',
        'दरौंदा',
        'बड़हरिया',
        'गोरेयाकोठी',
        'महराजगंज',
        'एकमा',
        'मांझी',
        'बनियापुर',
        'तरैया',
        'मढ़ौरा',
        'छपरा',
        'गरखा',
        'अमनौर',
        'परसा',
        'सोनपुर',
        'हाजीपुर',
        'लालगंज',
        'वैशाली',
        'महुआ',
        'राजा पाकार',
        'राघोपुर',
        'महनार',
        'पातेपुर',
        'कल्याणपुर',
        'वारिसनगर',
        'समस्तीपुर',
        'उजियारपुर',
        'मोरवा',
        'सरायरंजन',
        'मोहिउद्दीननगर',
        'विभूतिपुर',
        'रोसड़ा',
        'हसनपुर',
        'चेरिया बरियारपुर',
        'बछवाड़ा',
        'तेघड़ा',
        'मटिहानी',
        'साहेबपुर कमाल',
        'बेगूसराय',
        'बखरी',
        'अलौली',
        'खगड़िया',
        'बेलदौर',
        'परबत्ता',
        'बिहपुर',
        'गोपालपुर',
        'पीरपैंती',
        'कहलगांव',
        'भागलपुर',
        'सुलतानगंज',
        'नाथनगर',
        'अमरपुर',
        'धौरैया',
        'बांका',
        'कटोरिया',
        'बेलहर',
        'तारापुर',
        'मुंगेर',
        'जमालपुर',
        'सूर्यगढ़ा',
        'लखीसराय',
        'शेखपुरा',
        'बरबीघा',
        'अस्थावाँ',
        'बिहारशरीफ',
        'राजगीर',
        'इस्लामपुर',
        'हिलसा',
        'नालन्दा',
        'हरनौत',
        'मोकामा',
        'बाढ़',
        'बख्तियारपुर',
        'दीघा',
        'बाँकीपुर',
        'कुम्हरार',
        'पटना साहिब',
        'फतुहा',
        'दानापुर',
        'मनेर',
        'फुलवारी',
        'मसौढ़ी',
        'पालीगंज',
        'बिक्रम',
        'संदेश',
        'बड़हरा',
        'आरा',
        'अगिआँव',
        'तरारी',
        'जगदीशपुर',
        'शाहपुर',
        'ब्रहमपुर',
        'बक्सर',
        'डुमरांव',
        'राजपुर',
        'रामगढ़',
        'मोहनियाँ',
        'भभुआ',
        'चैनपुर',
        'चेनारी',
        'सासाराम',
        'करगहर',
        'दिनारा',
        'नोखा',
        'डिहरी',
        'काराकाट',
        'अरवल',
        'कुर्था',
        'जहानाबाद',
        'घोसी',
        'मखदुमपुर',
        'गोह',
        'ओबरा',
        'नवीनगर',
        'कुटुम्बा',
        'औरंगाबाद',
        'रफीगंज',
        'गुरूआ',
        'शेरघाटी',
        'इमामगंज',
        'बाराचट्टी',
        'बोध गया',
        'गया टाऊन',
        'टिकारी',
        'बेलागंज',
        'अतरी',
        'वजीरगंज',
        'रजौली',
        'हिसुआ',
        'नवादा',
        'गोबिन्दपुर',
        'वारसलीगंज',
        'सिकन्दरा',
        'जमुई',
        'झाझा',
        'चकाई'

    ],
    'candidate_names' => [
        'तेजस्वी यादव', 
        'तेज प्रताप यादव' ,
        'सम्राट चौधरी' ,
        'अनंत सिंह' ,
        'आनंद मिश्रा' ,
        'विजय सिन्हा' ,
        'मैथिली ठाकुर' ,
        'रामकृपाल यादव' ,
        'मुकेश सहनी' ,
        'खेसारी लाल यादव' ,
        'स्नेहलता' ,
        'मनीष कश्यप'
    ],
    'blog_images_everywhere' => function ($blog) {
        // just call your helper internally
        return blog_images_everywhere($blog);
    },

    'blog_video_gifs_everywhere' => function ($blog) {
        return blog_video_gifs_everywhere($blog);
    },

    'getuser_role' => function () {
        return getuser_role();
    },

    
    'base_url_image' => 'https://www.newsnmf.com/',
    'base_url_web_stories' => 'https://www.newsnmf.com/',
    'base_url_short_videos' => 'https://www.newsnmf.com/',
    'base_url_videos' => 'https://www.newsnmf.com/',
    'base_url_big_event' => 'http://127.0.0.1:8000/',

     /* NL1031:20Sept:2025:Added:base path for asset and frontend */

    'base_url_asset' => 'https://stgn.newsnmf.com/',
    'base_url_frontend' => 'https://www.newsnmf.com/',
    'base_url' => 'http://127.0.0.1:8000/',


     /* NL1025:15Sept:2025:Added:Google Tag Manager */
    'gtm_enabled' => env('ENABLE_GTM', false),
    'gtm_id' => env('GTM_ID', 'GTM-5BSHD2LX'),
    'schema_enabled' => env('ENABLE_SCHEMA', false),

];
