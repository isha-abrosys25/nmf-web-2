<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreFileRequest;
use App\Models\Category;
use App\Models\WebStories;
use Illuminate\Support\Facades\Log;
use App\Models\HomeSection;
use Illuminate\Support\Facades\File as FileFacade;

class WebStoryController extends Controller
{
    public function webStory(Request $request)
    {
        $query = WebStories::orderBy('id', 'DESC');

        if (isset($request->title)) {
            $query->where('name', 'like', '%' . $request->title . '%');
        }
        $perPage = $request->input('perPage', 20);

        $queryParams = $request->except('page');
        if ($perPage != 20) {
            $queryParams['perPage'] = $perPage;
        }
        $webstories = $query->paginate($perPage);

        if (isset($request->title)) {
            $title = $request->title;
            $webstories->setPath(asset('/webstory') . '?title=' . $title);
        } else {
            $title = '';
            $webstories->setPath(asset('/webstory'));
        }

        // Set the pagination path with query parameters
        $webstories->setPath(asset('/webstory') . '?' . http_build_query($queryParams));

        return view('admin/webStoryList')->with('data', [
            'webstories' => $webstories,
            'title' => $title,
            'perPage' => $perPage
        ]);
    }

    public function webStoryDetailsAdd(Request $request)
    {
        $categories = Category::where('home_page_status', 1)->get();
        return view('admin/addWebStory')->with('data', ['categories' => $categories]);
    }

    public function addWebStoryDetails(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'category' => 'required|integer|exists:categories,id',
            'eng_name' => 'required|string|max:255',
            'image_file' => 'nullable|file|mimes:jpg,jpeg,png|max:200',
        ];

        $messages = [
            'category.exists' => 'The category field is required.',
            'image_file.mimes' => 'Only JPG, JPEG, PNG formats are allowed for image.',
            'image_file.max' => 'The image size must not exceed 200 KB.',
        ];

        $request->validate($rules, $messages);

        $url = $this->clean($request->eng_name);
        $url = strtolower(str_replace(' ', '-', trim($url)));

        // Create directory structure: file/webstories/{year}/{month}
        $year = date('Y');
        $month = date('m');
        $basePath = public_path("file/webstories/$year/$month");
        FileFacade::makeDirectory($basePath, 0755, true, true);

        // Handle image upload
        $fileName = null;
        $thumbPath = null;

        if ($request->hasFile('image_file')) {
            $image = $request->file('image_file');
            $cleanName = preg_replace('/[^A-Za-z0-9_\-]/', '', pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME));
            $fileName = $cleanName . '_' . time() . '.' . $image->getClientOriginalExtension();
            $image->move($basePath, $fileName);

            // Store relative path for DB
            $thumbPath = "file/webstories/$year/$month/$fileName";
        }

        $maxSequence = WebStories::max('topnews_sequence');
        $nextSequence = $maxSequence ? $maxSequence + 1 : 1;

        WebStories::create([
            'name' => $request->name,
            'eng_name' => $request->eng_name,
            'status' => '0',
            'siteurl' => $url,
            'categories_id' => $request->category,
            'show_in_topnews' => $request->has('show_on_top') ? 1 : 0,
            'topnews_sequence' => $nextSequence,
            'thumb_path' => $thumbPath,
        ]);

        app(\App\Services\ExportHome::class)->run();

        return redirect(config('global.base_url').'webstory')->with('success', 'Web Story added successfully.');
    }

    public function editWebStory($id, Request $request)
    {
        $webstories = WebStories::find($id); // More efficient way to fetch record
        if (!$webstories) {
            dump("No file found with ID: " . $id);
        }
        $categories = Category::where('home_page_status', 1)->get();
        return view('admin/editWebStory')->with('data', ['categories' => $categories, 'webstories' => $webstories]);
    }

    public function webStoryEdit($id, Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|integer|exists:categories,id',
            'eng_name' => 'required|string|max:255',
            'image_file' => 'sometimes|file|mimes:jpg,jpeg,png|max:200', // add this for thumb
        ], [
            'category.exists' => 'The category field is required.',
            'image_file.max' => 'The image size must not exceed 200 KB.',
            'image_file.mimes' => 'Only JPG, JPEG, PNG formats are allowed.'
        ]);

        $webStory = WebStories::findOrFail($id);

        $year = date('Y');
        $month = date('m');
        $basePath = public_path("file/webstories/$year/$month");
        FileFacade::makeDirectory($basePath, 0755, true, true);

        $url = $this->clean($request->eng_name);
        $url = strtolower(str_replace(' ', '-', trim($url)));

        $showOnTop = $request->has('show_on_top') ? 1 : 0;

        $updateData = [
            'name' => $request->name,
            'eng_name' => $request->eng_name,
            'status' => '1',
            'siteurl' => $url,
            'categories_id' => $request->category,
            'show_in_topnews' => $showOnTop,
        ];

        // If thumbnail is uploaded
        if ($request->hasFile('image_file')) {
            // delete old thumbnail if exists
            if ($webStory->thumb_path && FileFacade::exists($webStory->thumb_path)) {
                FileFacade::delete($webStory->thumb_path);
            }

            $image = $request->file('image_file');
            $cleanThumb = preg_replace('/[^A-Za-z0-9_\-]/', '', pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME));
            //$thumbFileName = 'thumb_' . time() . '.' . $image->getClientOriginalExtension();
            $thumbFileName = 'thumb_' . $cleanThumb . '_' . time() . '.' . $image->getClientOriginalExtension();
            $image->move($basePath, $thumbFileName);

            $thumbPath = "file/webstories/$year/$month/$thumbFileName";
            $updateData['thumb_path'] = $thumbPath;
        }

        if ($showOnTop) {
            // If not already in top news, assign next available unique sequence
            if (!$webStory->show_in_topnews || $webStory->topnews_sequence == 0) {
                $usedSequences = WebStories::where('show_in_topnews', 1)
                    ->where('topnews_sequence', '>', 0)
                    ->pluck('topnews_sequence')
                    ->toArray();

                $sequence = 1;
                while (in_array($sequence, $usedSequences)) {
                    $sequence++;
                }

                $updateData['topnews_sequence'] = $sequence;
            }
            // else: keep existing sequence
        } else {
            $updateData['topnews_sequence'] = 0; // clear if not in top news
        }

        $webStory->update($updateData);

        app(\App\Services\ExportHome::class)->run();

        return redirect(config('global.base_url').'webstory')
            ->with('success', 'Webstory has been updated successfully!');
    }


    public function clean($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
        $string = trim($string);
        return preg_replace('/-+/', ' ', $string); // Replaces multiple hyphens with single one.
    }
    public function deleteWebStory($id, Request $request)
    {
        WebStories::where('id', $id)->delete();
        
        app(\App\Services\ExportHome::class)->run();

        return redirect(config('global.base_url').'webstory?t=' . time())->with('success', 'WebStory deleted successfully!');
    }

    public function webstorySequence(Request $request)
    {

        $perPage = $request->input('perPage', 20);
        $webstories = WebStories::where('status', 1)->orderBy('sequence', 'asc')->paginate($perPage);

        $queryParams = $request->except('page');
        if ($perPage != 20) {
            $queryParams['perPage'] = $perPage;
        }

        $webstories->setPath(asset('/webstory/sequence') . '?' . http_build_query($queryParams));

        return view('admin/webStorySequence', compact('webstories'));
    }

    public function updatewebstorySequence(Request $request)
    {
        $order = $request->order;


        foreach ($order as $item) {
            WebStories::where('id', $item['id'])
                ->update(['sequence' => $item['section_order']]);
        }
        app(\App\Services\ExportHome::class)->run();

        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request)
    {
        //Log::info('WebStory Update Status Request: ', $request->all());
        $webstory = WebStories::find($request->webstory_id);

        if (!$webstory) {
            return response()->json(['success' => false, 'message' => 'Invalid webstory ID']);
        }

        $webstory->status = $request->active_status ? 1 : 0;
        $webstory->save();
        app(\App\Services\ExportHome::class)->run();

        return response()->json(['success' => true]);
    }

    public function publish($id)
    {
        $webstory = WebStories::find($id);

        if (!$webstory) {
            return redirect()->back()->with('error', 'Webstory not found.');
        }

        $webstory->status = 1;
        $webstory->save();
        app(\App\Services\ExportHome::class)->run();
           // return redirect()->back()->with('success', 'Webstory published successfully.');
       return redirect(config('global.base_url').'webstory')->with('success', 'Web Story published successfully.');
       


    }

    public function topWebStorySequence(Request $request)
    {

        $perPage = $request->input('perPage', 20);
        $webstories = WebStories::where('status', 1)->where('show_in_topnews', 1)->orderBy('topnews_sequence', 'asc')->paginate($perPage);

        $homeSectionStatus = HomeSection::where('title', 'ShowTopNewsWithWebStory')->first();

        $queryParams = $request->except('page');
        if ($perPage != 20) {
            $queryParams['perPage'] = $perPage;
        }

        $webstories->setPath(asset('/webstory/top-webstory') . '?' . http_build_query($queryParams));

        return view('admin/topWebStorySequence', compact('webstories', 'homeSectionStatus'));
    }

    public function topWebSeqUpdate(Request $request)
    {
        $order = $request->order;

        foreach ($order as $item) {
            WebStories::where('id', $item['webstorySequence_id'])
                ->update(['topnews_sequence' => $item['position']]);
        }
        app(\App\Services\ExportHome::class)->run();

        return response()->json(['success' => true]);
    }

    public function WebStoryInTopUpdate(Request $request)
    {
        //log::info('WebStory In Top Update Request: ', $request->all());
        $status = $request->input('active_status');
        $id = $request->input('section_id');

        $section = HomeSection::find($id);
        if ($section) {
            $section->status = $status;
            $section->save();
            app(\App\Services\ExportHome::class)->run();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Section not found'], 404);
    }

    public function updateTopWebstoryStatus(Request $request)
    {
        // log::info('WebStory In Top Update Request: ', $request->all());
        $webstory = WebStories::find($request->webstory_id);

        if (!$webstory) {
            return response()->json(['success' => false, 'message' => 'Invalid webstory ID']);
        }

        $webstory->show_in_topnews = $request->active_status ? 1 : 0;
        $webstory->save();
        app(\App\Services\ExportHome::class)->run();

        return response()->json(['success' => true]);
    }
}
