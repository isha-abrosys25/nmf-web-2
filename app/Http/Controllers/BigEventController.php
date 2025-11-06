<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\File as FileFacade;
use App\Models\BigEvent;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\Ads;
use Illuminate\Support\Facades\DB;


class BigEventController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('perPage', 30);

        // Eager-load related models
        $blogs = BigEvent::with(['author','category'])
            ->where('is_active', 1)
            ->orderBy('created_at', 'DESC')
            ->when($request->title, fn($q, $title) => $q->where('title', 'like', "%{$title}%"))
            ->when($request->category, fn($q, $cat) => $q->where('category_id', $cat))
            ->when($request->author, fn($q, $author) => $q->where('author_id', $author))
            ->paginate($perPage)
            ->appends($request->except('page'));  // keep filters in pagination links

        // Lists for dropdowns
        $authors = User::orderBy('name')->get();             // or ->where('id','!=',6)
        $categories = Category::orderBy('name')->get();

        // Selected labels for dropdown buttons
        $selectedAuthor = $request->author
            ? optional(User::find($request->author))->name ?? 'Select Author'
            : 'Select Author';

        $selectedCategory = $request->category
            ? optional(Category::find($request->category))->name ?? 'Select Category'
            : 'Select Category';

        return view('admin.eventList', [
            'data' => [
                'blogs'    => $blogs,
                'title'    => $request->title,
                'author'   => $request->author,    // keep as IDs for hidden inputs
                'category' => $request->category,  // keep as IDs for hidden inputs
                'status'   => $request->status,
                'perPage'  => $perPage,
            ],
            // top-level vars used directly by Blade
            'authors'          => $authors,
            'categories'       => $categories,
            'selectedAuthor'   => $selectedAuthor,
            'selectedCategory' => $selectedCategory,
        ]);
    }


    public function showEventStory($cat_name, $name)
    {
        $user = Auth::check() ? Auth::user() : null;

        $blog = BigEvent::with(['category', 'author'])->where('site_url', $name)->first();

        if (!$blog) {
            return view('error');
        }

        if ($blog->is_active != 1) {
            return redirect('/');
        }

        $category = Category::where('site_url', $cat_name)->first();

        if (!$category) {
            return view('error');
        }

        $detailsAds = Ads::where('page_type', 'details')->get()->keyBy('location');

        $event = BigEvent::where('is_active', 1)
            ->where('id', $blog->id)
            ->with(['blogs' => function($query) {
                $query->orderBy('sort_order', 'asc')->take(6);
            }])
            ->first();

        $allBlogs = $event ? $event->blogs : collect();

        // Load comments here instead of separate index()
        $comments = $blog->comments()
            ->withCount('likes')
            ->with('replies.viewer')
            ->paginate(10);

        return view('eventDetail')->with('data', [
            'blog' => $blog,
            'latests' => $allBlogs,
            'category' => $blog->category,
            'author' => $blog->author,
            'user' => $user,
            'detailsAds' => $detailsAds,
            'comments' => $comments,
            'isLoggedIn' => Auth::guard('viewer')->check(),
            'currentViewer' => Auth::guard('viewer')->user(),
        ]);
    }

    public function showEventVideo($cat_name, $name)
    {
        $user = Auth::user();

        $video = BigEvent::with(['category', 'author'])->where('site_url', $name)->first();

        if (!$video || !$video->category || $video->category->site_url !== $cat_name) {
            return abort(404);
        }

        $detailsAds = Ads::where('page_type', 'details')->get()->keyBy('location');

        $latests = BigEvent::where('is_active', 1)
            ->where('id', $video->id)
            ->with(['blogs' => function($query) {
                $query->orderBy('sort_order', 'asc')->take(6);
            }])
            ->first();

        // Define widget categories
        $widgetCategoryNames = ['ट्रेंडिंग न्यूज़', 'पॉडकास्ट', 'टेक्नोलॉजी', 'स्पेशल्स'];

        $widgetCategories = Category::whereIn('name', $widgetCategoryNames)->get()->keyBy('name');

        $sideWidgets = [];
        foreach ($widgetCategoryNames as $name) {
            if ($widgetCategories->has($name)) {
                $cat = $widgetCategories[$name];
                $blogs = Blog::with('category') // If needed, you can eager-load category
                    ->where('status', 1)
                    ->where('categories_ids', $cat->id)
                    ->latest('updated_at')
                    ->limit($name === 'पॉडकास्ट' ? 1 : 5)
                    ->get();

                if ($blogs->count()) {
                    $sideWidgets[] = [
                        'categoryName' => $name,
                        'category' => $cat,
                        'blogs' => $blogs,
                    ];
                }
            }
        }

        return view('eventVideoDetail', compact(
            'video',
            'latests',
            'user',
            'detailsAds',
            'sideWidgets'
        ));
    }


    public function eventAdd()
    {
        $categories = Category::where('home_page_status', 1)->get();
        $authors = User::where('id', '!=', 6)->where('status', 1)->get();
        $data = [
            'categories' => $categories,
            'authors' =>  $authors
        ];

        return view('admin/addBigEvent')->with('data', $data);
    }


    public function addEvent(Request $request)
    {
        // Generate base slug
        $baseSlug = Str::slug($request->eng_name, '-');
        $slug = $baseSlug;
        $counter = 1;

        // Ensure slug is unique by checking DB
        while (BigEvent::where('site_url', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        // Validation rules
        $rules = [
            'name'          => 'required|string|max:255',
            // 'eng_name'      => 'required|string|max:255|unique:big_events,eng_name',
            // 'author'        => 'required|exists:users,id',
            // 'category'      => 'required|exists:categories,id',
            // 'sort_desc'     => 'required|string|max:500',
            // 'description'   => 'nullable|string',

            // Images
            // 'file'          => 'nullable|file|mimes:jpg,jpeg,png|max:200', // event main image
            'video_file'    => 'nullable|file|mimes:mp4|max:204800',       // 200MB
            'video-thumb-file' => 'nullable|file|mimes:jpg,jpeg,png|max:200',
            'bg-file'       => 'nullable|file|mimes:jpg,jpeg,png|max:200',
            'banner-file'   => 'nullable|file|mimes:jpg,jpeg,png|max:200',
        ];

        $messages = [
            'file.mimes'            => 'Only JPG, JPEG, PNG formats are allowed for the event image.',
            'file.max'              => 'The event image size must not exceed 200 KB.',
            'video_file.mimes'      => 'Only MP4 format is allowed for the video.',
            'video_file.max'        => 'The video size must not exceed 200 MB.',
            'video-thumb-file.mimes'=> 'Only JPG, JPEG, PNG formats are allowed for the video thumbnail.',
            'video-thumb-file.max'  => 'The video thumbnail size must not exceed 200 KB.',
            'bg-file.mimes'         => 'Only JPG, JPEG, PNG formats are allowed for the background image.',
            'bg-file.max'           => 'The background image size must not exceed 200 KB.',
            'banner-file.mimes'     => 'Only JPG, JPEG, PNG formats are allowed for the banner image.',
            'banner-file.max'       => 'The banner image size must not exceed 200 KB.',
        ];

        $request->validate($rules, $messages);

        // Prepare directories
        $year = date('Y');
        $month = date('m');
        $basePath = public_path("file/big_events/$year/$month");
        FileFacade::makeDirectory($basePath, 0755, true, true);

        // Helper for moving and sanitizing files
        $moveFile = function ($file, $prefix = '') use ($basePath, $year, $month) {
            $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = str_replace(' ', '_', $fileName) . '_' . time();
            if ($prefix) {
                $fileName = $prefix . '_' . $fileName;
            }
            $fileName .= '.' . $file->getClientOriginalExtension();
            $file->move($basePath, $fileName);
            return "file/big_events/$year/$month/$fileName";
        };

        // Process uploads
        $mainImage      = $request->hasFile('file') ? $moveFile($request->file('file'), 'img') : null;
        $videoFile      = $request->hasFile('video_file') ? $moveFile($request->file('video_file'), 'video') : null;
        $videoThumb     = $request->hasFile('video-thumb-file') ? $moveFile($request->file('video-thumb-file'), 'vthumb') : null;
        $bgImage        = $request->hasFile('bg-file') ? $moveFile($request->file('bg-file'), 'bg') : null;
        $bannerImage    = $request->hasFile('banner-file') ? $moveFile($request->file('banner-file'), 'banner') : null;

        // Store in DB
        BigEvent::create([
            'title'             => $request->name,
            'eng_name'          => $request->eng_name,
            'site_url'          => $slug,
            'author_id'         => $request->author,
            'category_id'       => $request->category,
            'short_desc'        => $request->sort_desc,
            'description'       => $request->description,
            'event_image'       => $mainImage,
            'video_path'        => $videoFile,
            'video_thumb'       => $videoThumb,
            'background_image'  => $bgImage,
            'banner_image'      => $bannerImage,
            'is_active'         => $request->has('publish') ? 1 : 0,
            'tag'              => $request->name,
            'video_url'        => $request->video_url,
        ]);

        //return redirect()->route('big_events.index')->with('success', 'Big Event added successfully!');
    
	
        try {
            app(\App\Services\ExportHome::class)->run();
        } catch (\Throwable $e) {
            Log::error('ExportHome failed', ['error' => $e->getMessage()]);
        }
      //  return redirect(config('global.base_url').'home');
     return redirect(config('global.base_url').'events')->with('success', 'Big Event added successfully!');
	
       // return view('/home');
    }

    public function editEvent($id)
    {
        $event = BigEvent::findOrFail($id);
        $categories = Category::where('home_page_status', 1)->get();
        $authors = User::where('id', '!=', 6)->where('status', 1)->get();
        $data = [
            'categories' => $categories,
            'authors' =>  $authors,
            'event' => $event
        ];

        return view('admin/editBigEvent')->with('data', $data);
    }

    public function eventEdit(Request $request, $id)
    {
        $bigEvent = BigEvent::findOrFail($id);

        // Validation rules
        $rules = [
            'name'          => 'required|string|max:255',
            // 'eng_name'      => 'required|string|max:255|unique:big_events,eng_name,' . $id,
            // 'author'        => 'required|exists:users,id',
            // 'category'      => 'required|exists:categories,id',
            // 'sort_desc'     => 'required|string|max:500',
            // 'description'   => 'nullable|string',
            'video_url'     => 'nullable|url',

            // Optional file uploads
            // 'file'          => 'nullable|file|mimes:jpg,jpeg,png|max:200',
            'video_file'    => 'nullable|file|mimes:mp4|max:204800',
            'video-thumb-file' => 'nullable|file|mimes:jpg,jpeg,png|max:200',
            'bg-file'       => 'nullable|file|mimes:jpg,jpeg,png|max:200',
            'banner-file'   => 'nullable|file|mimes:jpg,jpeg,png|max:200',
        ];
        $request->validate($rules);

        // Prepare upload directory
        $year = date('Y');
        $month = date('m');
        $basePath = public_path("file/big_events/$year/$month");
        FileFacade::makeDirectory($basePath, 0755, true, true);

        // Helper to move files
        $moveFile = function ($file, $prefix = '') use ($basePath, $year, $month) {
            $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = str_replace(' ', '_', $fileName) . '_' . time();
            if ($prefix) {
                $fileName = $prefix . '_' . $fileName;
            }
            $fileName .= '.' . $file->getClientOriginalExtension();
            $file->move($basePath, $fileName);
            return "file/big_events/$year/$month/$fileName";
        };

        // Update slug only if eng_name changes
        if ($bigEvent->eng_name !== $request->eng_name) {
            $baseSlug = Str::slug($request->eng_name, '-');
            $slug = $baseSlug;
            $counter = 1;
            while (BigEvent::where('site_url', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $bigEvent->site_url = $slug;
        }

        // Update simple fields
        $bigEvent->title       = $request->name;
        $bigEvent->eng_name    = $request->eng_name;
        $bigEvent->author_id   = $request->author;
        $bigEvent->category_id = $request->category;
        $bigEvent->short_desc  = $request->sort_desc;
        $bigEvent->description = $request->description;
        $bigEvent->is_active   = $request->has('publish') ? 1 : 0;
        $bigEvent->tag         = $request->name;
        $bigEvent->video_url   = $request->video_url;

        // File updates (delete old if new exists)
        if ($request->hasFile('file')) {
            if (!empty($bigEvent->event_image)) {
                $oldPath = public_path($bigEvent->event_image);
                if (FileFacade::exists($oldPath)) {
                    FileFacade::delete($oldPath);
                }
            }
            $bigEvent->event_image = $moveFile($request->file('file'), 'img');
        }

        if ($request->hasFile('video_file')) {
            if (!empty($bigEvent->video_path)) {
                $oldPath = public_path($bigEvent->video_path);
                if (FileFacade::exists($oldPath)) {
                    FileFacade::delete($oldPath);
                }
            }
            $bigEvent->video_path = $moveFile($request->file('video_file'), 'video');
        }

        if ($request->hasFile('video-thumb-file')) {
            if (!empty($bigEvent->video_thumb)) {
                $oldPath = public_path($bigEvent->video_thumb);
                if (FileFacade::exists($oldPath)) {
                    FileFacade::delete($oldPath);
                }
            }
            $bigEvent->video_thumb = $moveFile($request->file('video-thumb-file'), 'vthumb');
        }

        if ($request->hasFile('bg-file')) {
            if (!empty($bigEvent->background_image)) {
                $oldPath = public_path($bigEvent->background_image);
                if (FileFacade::exists($oldPath)) {
                    FileFacade::delete($oldPath);
                }
            }
            $bigEvent->background_image = $moveFile($request->file('bg-file'), 'bg');
        }

        if ($request->hasFile('banner-file')) {
            if (!empty($bigEvent->banner_image)) {
                $oldPath = public_path($bigEvent->banner_image);
                if (FileFacade::exists($oldPath)) {
                    FileFacade::delete($oldPath);
                }
            }
            $bigEvent->banner_image = $moveFile($request->file('banner-file'), 'banner');
        }

        // Save changes
        $bigEvent->video_url = $request->video_url;
        $bigEvent->save();
	
    
	 try {
            app(\App\Services\ExportHome::class)->run();
        } catch (\Throwable $e) {
            Log::error('ExportHome failed', ['error' => $e->getMessage()]);
        }
        //return redirect(config('global.base_url').'home');
	return redirect(config('global.base_url').'events')->with('success', 'Big Event updated successfully!');


        //return view('/home');
    }

    public function eventBlogs($id, Request $request)
    {
        $perPage = $request->input('perPage', 30);

        // Load event and its related blogs with filtering on blog title
        $event = BigEvent::findOrFail($id);

        $blogs = $event->blogs()
            ->when($request->title, fn($q, $title) => 
                $q->where('name', 'like', "%{$title}%")
            )
            ->orderBy('big_event_blogs.sort_order', 'asc')
            ->paginate($perPage)
            ->appends($request->except('page'));

        return view('admin.eventBlogList', [
            'event' => $event,
            'blogs' => $blogs,
            'title' => $request->title,
            'perPage' => $perPage
        ]);
    }

    public function addEventBlog()
    {
        $events = BigEvent::orderBy('created_at', 'desc')->get(['id', 'title']);
        return view('admin.addBlogEvent', compact('events'));
    }

    public function storeEventBlog(Request $request)
    {
        $validated = $request->validate([
            'event_id'   => 'required|exists:big_events,id',
            'blog_id'    => 'required|exists:blogs,id',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $event = BigEvent::findOrFail($validated['event_id']);

        // Attach or update without creating duplicates
        $event->blogs()->syncWithoutDetaching([
            $validated['blog_id'] => ['sort_order' => $validated['sort_order'] ?? 0],
        ]);
	
        try {
            app(\App\Services\ExportHome::class)->run();
        } catch (\Throwable $e) {
            Log::error('ExportHome failed', ['error' => $e->getMessage()]);
        }

        return redirect(config('global.base_url')."events/event-blogs/{$validated['event_id']}")
            ->with('success', 'Blog attached to event successfully.');
    }

    public function editEventBlog($id)
    {
        // Load the pivot record directly
        $eventBlog = DB::table('big_event_blogs')
            ->where('id', $id) // assuming 'id' exists in pivot table
            ->first();

        if (!$eventBlog) {
            abort(404);
        }

        // Get event list for dropdown
        $events = BigEvent::orderBy('created_at', 'desc')->get(['id', 'title']);

        return view('admin.editBlogEvent', compact('eventBlog', 'events'));
    }

    public function updateEventBlog(Request $request, $id)
    {
        $validated = $request->validate([
            'event_id'   => 'required|exists:big_events,id',
            'blog_id'    => 'required|exists:blogs,id',
            'sort_order' => 'nullable|integer',
        ]);

        // Make sure the record exists
        $eventBlog = DB::table('big_event_blogs')->where('id', $id)->first();
        if (!$eventBlog) {
            abort(404);
        }

        // Update the pivot table directly
        DB::table('big_event_blogs')
            ->where('id', $id)
            ->update([
                'big_event_id' => $validated['event_id'],
                'blog_id'      => $validated['blog_id'],
                'sort_order'   => $validated['sort_order'] ?? 0,
                'updated_at'   => now(),
            ]);
	    
	 try {
            app(\App\Services\ExportHome::class)->run();
        } catch (\Throwable $e) {
            Log::error('ExportHome failed', ['error' => $e->getMessage()]);
        }

        return redirect(config('global.base_url')."events/event-blogs/{$validated['event_id']}")
            ->with('success', 'Blog attached to event successfully.');
    }
    
        // NL1034:23Sep2025:add remove button method
    public function confirmDeleteEventBlog($id)
	{
	    ?>
	    <script>
	        if (confirm('Are you sure? This action will permanently delete this Event Blog.')) {
	            window.location.href =  '<?php echo url('events/delete-eventblog/').'/'.$id; ?>';
	        } else {
	            window.location.href =  '<?php echo url('/events'); ?>';
	        }
	    </script>
	    <?php
	}
    public function confirmDeleteEvent($id)
	{
	    ?>
	    <script>
	        if (confirm('Are you sure? This action will permanently delete this Event .')) {
	            window.location.href =  '<?php echo url('events/del/').'/'.$id; ?>';
	        } else {
	            window.location.href =  '<?php echo url('/events'); ?>';
	        }
	    </script>
	    <?php
	}


	public function deleteEventBlog($id, Request $request)  
	{
	    // Fetch the pivot row (single record)
	    $eventBlog = DB::table('big_event_blogs')->where('id', $id)->first();

	    if (!$eventBlog) {
	        return redirect()->back()->with('error', 'Event Blog not found.');
	    }

	    // Grab event_id before deleting
	    $eventId = $eventBlog->big_event_id;
	    // Delete the record
	    DB::table('big_event_blogs')->where('id', $id)->delete();
	     try {
                  app(\App\Services\ExportHome::class)->run();
	        } catch (\Throwable $e) {
	            Log::error('ExportHome failed', ['error' => $e->getMessage()]);
	        }

	    // Redirect back to the event blogs page
	    return redirect(config('global.base_url')."events/event-blogs/{$eventId}")
	        ->with('success', 'Event Blog deleted successfully.');
	}
    public function deleteEvent($id, Request $request)  
	{
	    // Fetch the pivot row (single record)
	    $events = DB::table('big_events')->where('id', $id)->first();

	    if (!$events) {
	        return redirect()->back()->with('error', 'Event not found.');
	    }

	    // Grab event_id before deleting
	    $eventId = $events->id;
	    // Delete the record
	    DB::table('big_events')->where('id', $id)->delete();
	     try {
                  app(\App\Services\ExportHome::class)->run();
	        } catch (\Throwable $e) {
	            Log::error('ExportHome failed', ['error' => $e->getMessage()]);
	        }

	    // Redirect back to the event blogs page
	    return redirect(config('global.base_url')."events")
	        ->with('success', 'Event  deleted successfully.');
	}

}
