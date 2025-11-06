<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HomeSection;
use App\Models\Category;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;


class HomeCategorySequence extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 50);

        $categories = HomeSection::where('type', 'section')
            ->where('status', 1)
            ->orderBy('section_order', 'asc')
            ->paginate($perPage);

        $sidebarCategories = HomeSection::where('type', 'sidebar')
            ->where('status', 1)
            ->orderBy('sidebar_sec_order', 'asc')
            ->get();

        $queryParams = $request->except('page');
        if ($perPage != 50) {
            $queryParams['perPage'] = $perPage;
        }

        $categories->setPath(url('/category-sequence') . '?' . http_build_query($queryParams));

        return view('admin.homeCategoryControl', compact('categories', 'sidebarCategories'));
    }

    public function updateCategorySequence(Request $request)
    {
        $order = $request->order;
        $type = $request->input('type', 'main'); // Default to 'main' if not provided

        foreach ($order as $item) {
            if ($type === 'sidebar') {
                HomeSection::where('id', $item['id'])
                    ->update(['sidebar_sec_order' => $item['section_order']]);
            } else {
                HomeSection::where('id', $item['id'])
                    ->update(['section_order' => $item['section_order']]);
            }
        }

        try {
            app(\App\Services\ExportHome::class)->run();
        } catch (\Throwable $e) {
            \Log::error('ExportHome failed', ['error' => $e->getMessage()]);
        }

        return response()->json(['success' => true]);
    }

    public function getAllCategoryList(Request $request)
    {
        $perPage = $request->input('perPage', 20);
        $categories = HomeSection::with('category')->paginate($perPage);

        return response()->view('admin.allHomeCatList', [
            'categories' => $categories,
            'perPage' => $perPage
        ]);
    }

    public function addCat(Request $request)
    {
        $categories = Category::where('home_page_status', 1)->get();
        $maxSectionOrder = HomeSection::where('type', 'section')->max('section_order') ?? 0;
        $maxSidebarOrder = HomeSection::where('type', 'sidebar')->max('sidebar_sec_order') ?? 0;
        return view('admin.addHomeCat', compact('categories', 'maxSectionOrder', 'maxSidebarOrder'));
    }

    public function saveCat(Request $request)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:section,sidebar,banner,other',
            'status' => 'required|boolean',
        ];

        $type = $request->input('type');

        // Conditionally required fields based on 'type'
        if (!in_array($type, ['banner', 'other'])) {
            $rules['catid'] = 'required|integer|exists:categories,id';
        } else {
            $rules['catid'] = 'nullable|integer';
        }

        if ($type === 'banner') {
            $rules['image_url'] = 'required|url';
            $rules['banner_link'] = 'required|url';
        } else {
            $rules['image_url'] = 'nullable|url';
            $rules['banner_link'] = 'nullable|url';
        }

        if ($type === 'section') {
            $rules['section_order'] = [
                'required',
                'integer',
                'min:1',
                Rule::unique('home_sections', 'section_order')->where(function ($query) {
                    return $query->where('type', 'section');
                }),
            ];
        } else {
            $rules['section_order'] = 'nullable|integer';
        }

        if ($type === 'sidebar') {
            $rules['sidebar_sec_order'] = [
                'required',
                'integer',
                'min:1',
                Rule::unique('home_sections', 'sidebar_sec_order')->where(function ($query) {
                    return $query->where('type', 'sidebar');
                }),
            ];
        } else {
            $rules['sidebar_sec_order'] = 'nullable|integer';
        }

        $messages = [
            'title.required' => 'Title is required.',
            'type.required' => 'Section type is required.',
            'type.in' => 'Invalid section type selected.',
            'status.required' => 'Status is required.',
            'status.boolean' => 'Invalid status value.',
            'catid.required' => 'Category is required.',
            'catid.integer' => 'Category must be an integer.',
            'catid.exists' => 'Selected category does not exist.',
            'image_url.required' => 'Image URL is required.',
            'image_url.url' => 'Image URL must be a valid URL.',
            'banner_link.required' => 'Banner Link is required.',
            'banner_link.url' => 'Banner Link must be a valid URL.',
            'section_order.required' => 'Section order is required.',
            'section_order.integer' => 'Section order must be an integer.',
            'section_order.min' => 'Section order must be greater than 0.',
            'section_order.unique' => 'Section order must be unique.',
            'sidebar_sec_order.required' => 'Sidebar section order is required.',
            'sidebar_sec_order.integer' => 'Sidebar section order must be an integer.',
            'sidebar_sec_order.min' => 'Sidebar section order must be greater than 0.',
            'sidebar_sec_order.unique' => 'Sidebar section order must be unique.',
        ];


        // Validate the request
        $validated = $request->validate($rules, $messages);

        // Save data
        HomeSection::create([
            'title' => $validated['title'],
            'catid' => $validated['catid'] ?? 0,
            'image_url' => $validated['image_url'] ?? null,
            'banner_link' => $validated['banner_link'] ?? null,
            'section_order' => $validated['type'] === 'section' ? $validated['section_order'] : 0,
            'sidebar_sec_order' => $validated['type'] === 'sidebar' ? $validated['sidebar_sec_order'] : 0,
            'status' => $validated['status'],
            'type' => $validated['type'],
        ]);

        try {
            app(\App\Services\ExportHome::class)->run();
        } catch (\Throwable $e) {
            \Log::error('ExportHome failed', ['error' => $e->getMessage()]);
        }

        return redirect(config('global.base_url').'home-category/')->with('success', 'Category added successfully');
    }


    public function editCat($id)
    {
        $categories = Category::where('home_page_status', 1)->get();
        $homeCategory = HomeSection::findOrFail($id);

        $maxSectionOrder = HomeSection::where('type', 'section')->max('section_order') ?? 0;
        $maxSidebarOrder = HomeSection::where('type', 'sidebar')->max('sidebar_sec_order') ?? 0;

        return view('admin.editHomeCat', compact('categories', 'homeCategory', 'maxSectionOrder', 'maxSidebarOrder'));
    }

    public function editSave(Request $request, $id)
    {
        $homeCategory = HomeSection::findOrFail($id);
        $originalType = $homeCategory->type; // Capture BEFORE updating

        $rules = [
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:section,sidebar,banner,other',
            'status' => 'required|boolean',
        ];

        $type = $request->input('type');

        // Category validation
        if (!in_array($type, ['banner', 'other'])) {
            $rules['catid'] = 'required|integer|exists:categories,id';
        } else {
            $rules['catid'] = 'nullable|integer';
        }

        // Banner-specific validation
        if ($type === 'banner') {
            $rules['image_url'] = 'required|url';
            $rules['banner_link'] = 'required|url';
        } else {
            $rules['image_url'] = 'nullable|url';
            $rules['banner_link'] = 'nullable|url';
        }

        // Section order
        if ($type === 'section') {
            $rules['section_order'] = [
                'required',
                'integer',
                'min:1',
                Rule::unique('home_sections', 'section_order')
                    ->where('type', 'section')
                    ->ignore($id),
            ];
        } else {
            $rules['section_order'] = 'nullable|integer';
        }

        // Sidebar order
        if ($type === 'sidebar') {
            $rules['sidebar_sec_order'] = [
                'required',
                'integer',
                'min:1',
                Rule::unique('home_sections', 'sidebar_sec_order')
                    ->where('type', 'sidebar')
                    ->ignore($id),
            ];
        } else {
            $rules['sidebar_sec_order'] = 'nullable|integer';
        }

        $messages = [
            'title.required' => 'Title is required.',
            'type.required' => 'Section type is required.',
            'type.in' => 'Invalid section type selected.',
            'status.required' => 'Status is required.',
            'status.boolean' => 'Invalid status value.',
            'catid.required' => 'Category is required.',
            'catid.integer' => 'Category must be an integer.',
            'catid.exists' => 'Selected category does not exist.',
            'image_url.required' => 'Image URL is required.',
            'image_url.url' => 'Image URL must be a valid URL.',
            'banner_link.required' => 'Banner Link is required.',
            'banner_link.url' => 'Banner Link must be a valid URL.',
            'section_order.required' => 'Section order is required.',
            'section_order.integer' => 'Section order must be an integer.',
            'section_order.min' => 'Section order must be greater than 0.',
            'section_order.unique' => 'Section order must be unique.',
            'sidebar_sec_order.required' => 'Sidebar section order is required.',
            'sidebar_sec_order.integer' => 'Sidebar section order must be an integer.',
            'sidebar_sec_order.min' => 'Sidebar section order must be greater than 0.',
            'sidebar_sec_order.unique' => 'Sidebar section order must be unique.',
        ];

        // Validate
        $validated = $request->validate($rules, $messages);

        // Update
        $homeCategory->update([
            'title' => $validated['title'],
            'catid' => $validated['catid'] ?? 0,
            'image_url' => $validated['image_url'] ?? null,
            'banner_link' => $validated['banner_link'] ?? null,
            'section_order' => $type === 'section' ? $validated['section_order'] : 0,
            'sidebar_sec_order' => $type === 'sidebar' ? $validated['sidebar_sec_order'] : 0,
            'status' => $validated['status'],
            'type' => $validated['type'],
        ]);

        //Now compare with original
        if ($originalType !== $type) {
            if ($originalType === 'section') {
                $sections = HomeSection::where('type', 'section')
                    ->where('id', '!=', $id)
                    ->orderBy('section_order')
                    ->get();

                $i = 1;
                foreach ($sections as $section) {
                    $section->update(['section_order' => $i++]);
                }
            }

            if ($originalType === 'sidebar') {
                $sidebars = HomeSection::where('type', 'sidebar')
                    ->where('id', '!=', $id)
                    ->orderBy('sidebar_sec_order')
                    ->get();

                $i = 1;
                foreach ($sidebars as $sidebar) {
                    $sidebar->update(['sidebar_sec_order' => $i++]);
                }
            }
        }

        
        try {
            app(\App\Services\ExportHome::class)->run();
        } catch (\Throwable $e) {
            \Log::error('ExportHome failed', ['error' => $e->getMessage()]);
        }

        return redirect(config('global.base_url').'home-category/')->with('success', 'Category updated successfully');

        
    }

    public function deleteCat($id)
    {
        $homeCategory = HomeSection::findOrFail($id);
        $homeCategory->delete();

         try {
            app(\App\Services\ExportHome::class)->run();
        } catch (\Throwable $e) {
            \Log::error('ExportHome failed', ['error' => $e->getMessage()]);
        }

        return redirect(config('global.base_url').'home-category/')->with('success', 'Category deleted successfully');

        
    }
    public function updateActiveStatus(Request $request)
    {
        $homeCategory = HomeSection::findOrFail($request->home_sec_id);
        $newStatus = $request->active_status;

        $originalType = $homeCategory->type;
        $homeCategory->status = $newStatus;

        // If disabling, reset sequence
        if ((int)$newStatus === 0) {
            if ($originalType === 'section') {
                $homeCategory->section_order = 0;
            } elseif ($originalType === 'sidebar') {
                $homeCategory->sidebar_sec_order = 0;
            }
        } else {
            // If enabling, assign next max order
            if ($originalType === 'section') {
                $maxOrder = HomeSection::where('type', 'section')->where('status', 1)->max('section_order') ?? 0;
                $homeCategory->section_order = $maxOrder + 1;
            } elseif ($originalType === 'sidebar') {
                $maxOrder = HomeSection::where('type', 'sidebar')->where('status', 1)->max('sidebar_sec_order') ?? 0;
                $homeCategory->sidebar_sec_order = $maxOrder + 1;
            }
        }

        $homeCategory->save();

        // Reorder the sequence to fill gaps
        if ($originalType === 'section') {
            $sections = HomeSection::where('type', 'section')
                ->where('status', 1)
                ->orderBy('section_order')
                ->get();

            $i = 1;
            foreach ($sections as $section) {
                $section->update(['section_order' => $i++]);
            }
        }

        if ($originalType === 'sidebar') {
            $sidebars = HomeSection::where('type', 'sidebar')
                ->where('status', 1)
                ->orderBy('sidebar_sec_order')
                ->get();

            $i = 1;
            foreach ($sidebars as $sidebar) {
                $sidebar->update(['sidebar_sec_order' => $i++]);
            }
        }

         try {
            app(\App\Services\ExportHome::class)->run();
        } catch (\Throwable $e) {
            \Log::error('ExportHome failed', ['error' => $e->getMessage()]);
        }


        return response()->json(['success' => true]);
    }
}
