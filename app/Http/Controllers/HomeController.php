<?php

namespace App\Http\Controllers;

use App\Models\HomeSection;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Blog;
use App\Models\Subscriber;
use App\Models\Vote;
use App\Models\VoteOption;
use App\Models\TrendingTag;
use App\Models\Ads;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function index()
    {
        return view('admin/dashboard');
    }

    public function homePage()
    {
        // Cache frequently used queries for 5 minutes
        $categories = Cache::remember('home_categories', 300, function () {
            return Category::where('home_page_status', '1')->get();
        });

        $homeSections = Cache::remember('home_sections', 300, function () {
            return HomeSection::with('category')
                ->where('type', 'section')
                ->where('status', 1)
                ->orderBy('section_order', 'asc')
                ->get();
        });

        $sidebarCategories = Cache::remember('sidebar_categories', 300, function () {
            return HomeSection::with('category')
                ->where('type', 'sidebar')
                ->where('status', 1)
                ->orderBy('sidebar_sec_order', 'asc')
                ->get();
        });

        $banners = Cache::remember('home_banners', 300, function () {
            return HomeSection::with('category')
                ->where('type', 'banner')
                ->where('status', 1)
                ->get();
        });

        // Optimize random blog selection (avoid inRandomOrder on large tables)
        $blogCount = Blog::count();
        $randomBlog = null;
        if ($blogCount > 0) {
            $randomBlog = Blog::skip(rand(0, $blogCount - 1))->first();
        }

        $homeAds = Cache::remember('home_ads', 300, function () {
            return Ads::where('page_type', 'home')->get()->keyBy('location');
        });

        $trendingTags = Cache::remember('trending_tags', 300, function () {
            return TrendingTag::where('status', 1)
                ->orderBy('sequence_id', 'asc')
                ->pluck('name')
                ->toArray();
        });

        $latestPoll = Cache::remember('latest_poll', 60, function () {
            return Vote::latest()->first();
        });

        if (!$latestPoll) {
            $title = 'Default Question?';
            $allVotes = [];
            $totalVotes = 0;
        } else {
            $title = $latestPoll->title;

            $options = VoteOption::where('vote_id', $latestPoll->id)
                ->select('name', DB::raw('SUM(vote_count) as count'))
                ->groupBy('name')
                ->pluck('count', 'name');

            $allVotes = $options->toArray();
            $totalVotes = array_sum($allVotes);
        }

        return response()
            ->view('welcome', [
                'data' => [
                    'categories'       => $categories ?? [],
                    'homeSections'     => $homeSections ?? [],
                    'randomBlog'       => $randomBlog ?? [],
                    'uniqueTags'       => $trendingTags,
                    'sidebarCategories'=> $sidebarCategories ?? [],
                    'voteTitle'        => $title,
                    'voteOptions'      => $allVotes,
                    'voteTotal'        => $totalVotes,
                    'voteId'           => $latestPoll->id ?? null,
                    'homeAds'          => $homeAds,
                    'banners'          => $banners ?? [],
                ]
            ])
            ->header('Cache-Control', 'public, max-age=300'); // allow browser cache for 5 min
    }

   public function savedVote($id, Request $request)
    {
        // $request->validate([
        //     'vote_id' => 'required|integer',
        //     'option_id' => 'required|integer',
        // ]);

        $userIp = $request->ip();

        // Check if the same user_ip has already voted on this title
        $alreadyVoted = DB::table('votes')
            ->where('id', $id)
            ->where('title', $request->title)
            ->where('user_ip', $userIp)
            ->exists();

        if ($alreadyVoted) {
            return response()->json([
                'status' => 'error',
                'already_voted' => true,
                'message' => 'आप वोट कर चुके हैं'
            ], 403);
        }

        // Get the vote option to increment
        $voteOption = VoteOption::where('vote_id', $request->vote_id)
            ->where('id', $request->option_id)
            ->first();

        if (!$voteOption) {
            return response()->json([
                'status' => 'error',
                'message' => 'Option not found'
            ], 404);
        }

        // Increment the vote count
        $voteOption->increment('vote_count');

        // Update the vote row with user_ip ONLY if it was empty
        DB::table('votes')
            ->where('id', $id)
            ->whereNull('user_ip') // prevent overwriting someone else's IP
            ->update([
                'user_ip' => $userIp,
                'updated_at' => now()
            ]);

        // Get updated results
        $options = VoteOption::where('vote_id', $request->vote_id)->get();
        $results = $options->pluck('vote_count', 'id');
        $totalVotes = $results->sum();

        return response()->json([
            'status' => 'success',
            'message' => 'Vote recorded',
            'results' => $results,
            'totalVotes' => $totalVotes,
            'voteId' => $request->vote_id // Add this line
        ]);
    }

    public function getVoteResults($id)
    {
        $latestPoll = Vote::find($id);

        if (!$latestPoll) {
            return response()->json([
                'status' => 'error',
                'message' => 'Poll not found'
            ], 404);
        }

        $options = VoteOption::where('vote_id', $latestPoll->id)
            ->select('id', 'name', DB::raw('SUM(vote_count) as count'))
            ->groupBy('id', 'name')
            ->pluck('count', 'id');

        $allVotes = $options->toArray();
        $totalVotes = array_sum($allVotes);

        return response()->json([
            'status' => 'success',
            'results' => $allVotes,
            'totalVotes' => $totalVotes
        ]);
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:subscribers,email',
        ]);

        Subscriber::create(['email' => $request->email]);

        return redirect('/')->with('subscribemessage', "Thanks for subscribing!");
    }

    public function handlePost(Request $request)
    {
        if ($request->_action === 'vote') {
            return $this->savedVote($request->vote_id, $request);
        } elseif ($request->_action === 'subscribe') {
            return $this->subscribe($request);
        }

        abort(400, 'Invalid form action.');
    }
public function toggleMahaSection()
{
    $section = HomeSection::where('title', 'ElectionMahaSection')->first();

    if ($section) {
        // Toggle between 1 and 0
        $section->status = $section->status == 1 ? 0 : 1;
        $section->save();
    } else {
        // Create record if it doesn't exist
        HomeSection::create([
            'title' => 'ElectionMahaSection',
            'status' => 1,
            'section_order' => 0
        ]);
    }
 try {
            app(\App\Services\ExportHome::class)->run();
        } catch (\Throwable $e) {
            Log::error('ExportHome failed', ['error' => $e->getMessage()]);
        }


   // return back()->with('success', 'Election Maha Section visibility updated!');
 return redirect(config('global.base_url').'election/mahamukabla/show')->with('success', 'Election Mahamukabla visibility updated!');


}
public function toggleLiveSection()
{
    $section = HomeSection::where('title', 'ElectionLiveSection')->first();

    if ($section) {
        // Toggle between 1 and 0
        $section->status = $section->status == 1 ? 0 : 1;
        $section->save();
    } else {
        // Create record if it doesn't exist
        HomeSection::create([
            'title' => 'ElectionLiveSection',
            'status' => 1,
            'section_order' => 0
        ]);
    }

     try {
            app(\App\Services\ExportHome::class)->run();
        } catch (\Throwable $e) {
            Log::error('ExportHome failed', ['error' => $e->getMessage()]);
        }


    //return back()->with('success', 'Election Live Section visibility updated!');
 return redirect(config('global.base_url').'election/manage-vote-count')->with('success', 'Election Result visibility updated!');

}

public function toggleExitPoll()
{
    $section = HomeSection::where('title', 'ExitPollSection')->first();

    if ($section) {
        // Toggle between 1 and 0
        $section->status = $section->status == 1 ? 0 : 1;
        $section->save();
    } else {
        // Create record if it doesn't exist
        HomeSection::create([
            'title' => 'ExitPollSection',
            'status' => 1,
            'section_order' => 0
        ]);
    }

     try {
            app(\App\Services\ExportHome::class)->run();
        } catch (\Throwable $e) {
            \Log::error('ExportHome failed', ['error' => $e->getMessage()]);
        }


    
 return redirect(config('global.base_url').'election/exit-poll')->with('success', 'Exit Poll visibility updated!');

}

}
