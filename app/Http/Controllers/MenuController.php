<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\MenuType;
use App\Models\MenuCategory;
use App\Models\File;
use Illuminate\Support\Facades\Log;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $menus = Menu::where('type_id', 1)->orderBy('created_at', 'DESC');
        if (isset($request->title)) {
            $menus->Where('menu_name', 'like', '%' . $request->title . '%');
        }

        $perPage = $request->input('perPage', 30);
        $menus = $menus->paginate($perPage);

        //$menus->setPath(asset('/menu'));

        if (isset($request->title)) {
            $title = $request->title;
            $menus->setPath(asset('/menu') . '?title=' . $title);
        } else {
            $title = '';
            $menus->setPath(asset('/menu'));
        }

        // Build query parameters for pagination links
        $queryParams = $request->except('page');
        if ($perPage != 30) {
            $queryParams['perPage'] = $perPage;
        }

        // Set the pagination path with query parameters
        $menus->setPath(asset('/menu') . '?' . http_build_query($queryParams));

        return view('admin/menuList')->with('data', ['menus' => $menus, 'title' => $title, 'perPage' => $perPage]);
    }
    public function allMenus(Request $request)
    {
        $menus = Menu::orderBy('created_at', 'DESC');
        if (isset($request->title)) {
            $menus->Where('menu_name', 'like', '%' . $request->title . '%');
        }

        $perPage = $request->input('perPage', 30);
        $menus = $menus->paginate($perPage);

        //$menus->setPath(asset('/menu'));

        if (isset($request->title)) {
            $title = $request->title;
            $menus->setPath(asset('/menu') . '?title=' . $title);
        } else {
            $title = '';
            $menus->setPath(asset('/menu'));
        }

        // Build query parameters for pagination links
        $queryParams = $request->except('page');
        if ($perPage != 30) {
            $queryParams['perPage'] = $perPage;
        }

        // Set the pagination path with query parameters
        $menus->setPath(asset('/menu') . '?' . http_build_query($queryParams));

        return view('admin/menuList')->with('data', ['menus' => $menus, 'title' => $title, 'perPage' => $perPage]);
    }
    public function addMenu(Request $request)
    {
        $file = File::orderBy('id', 'DESC')->paginate(12);
        $menu = Menu::all();
        $type = MenuType::all();
        $category = MenuCategory::all();
        return view('admin/addMenu')->with('data', ['menus' => $menu, 'types' => $type, 'categories' => $category, 'file' => $file]);
    }
    public function menuAdd(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|integer|exists:menu_types,id',
            'category' => 'required|integer|exists:menu_categories,id',
            'link' => 'required|string|max:255',
        ], [
            'type.exists' => 'The menu type is required.',
            'category.exists' => 'The menu category field is required.',
        ]);
        $maxSequence = Menu::max('sequence_id');
        $nextSequence = $maxSequence ? $maxSequence + 1 : 1;
        $menu = Menu::create([
            'menu_name' => $request->name,
            'menu_id' => $request->menu,
            'status' => '1',
            'type_id' => $request->type,
            'category_id' => $request->category,
            'menu_link' => $request->link,
            'image' => $request->thumb_images,
            'menu_class' => '',
            'sequence_id' => $nextSequence,
        ]);
	
	
	try {
            app(\App\Services\ExportHome::class)->run();
        } catch (\Throwable $e) {
            \Log::error('ExportHome failed', ['error' => $e->getMessage()]);
        }
        return redirect(config('global.base_url').'menu');
	
       // return redirect('/menu');
    }
    public function editmenu(Request $request, $id)
    {
        $file = File::orderBy('id', 'DESC')->paginate(12);
        $menus = Menu::all();
        $type = MenuType::all();
        $category = MenuCategory::all();
        $menu = Menu::where('id', $id)->first();
        return view('admin/editMenu')->with('data', ['menus' => $menus, 'types' => $type, 'categories' => $category, 'menu' => $menu, 'file' => $file]);
    }
    public function menuedit(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|integer|exists:menu_types,id',
            'category' => 'required|integer|exists:menu_categories,id',
            'link' => 'required|string|max:255',
            //'class' => 'required|string|max:255',
        ], [
            'type.exists' => 'The menu type is required.',
            'category.exists' => 'The menu category field is required.',
        ]);
        $status = '0';
        if ($request->status) {
            $status = '1';
        }
        $menu = Menu::where('id', $id)->update([
            'menu_name' => $request->name,
            'menu_id' => $request->menu,
            'status' => $status,
            'type_id' => $request->type,
            'category_id' => $request->category,
            'menu_link' => $request->link,
            'image' => $request->thumb_images,
            'menu_class' => '',
        ]);
	try {
            app(\App\Services\ExportHome::class)->run();
        } catch (\Throwable $e) {
            \Log::error('ExportHome failed', ['error' => $e->getMessage()]);
        }
        return redirect(config('global.base_url').'menu');
       // return redirect('/menu');
    }
    public function del($id, Request $request)
    {
    ?>
        <script>
            if (confirm('Are You Sure You want Delete Menu')) {
                window.location.href = '<?php echo asset('/menu/del') . '/' . $id; ?>'
            } else {
                window.location.href = '<?php echo asset('/menu'); ?>'
            }
        </script>
    <?php
    }
    public function deleteMenu($id, Request $request)
    {
        Menu::where('id', $id)->delete();
	try {
            app(\App\Services\ExportHome::class)->run();
        } catch (\Throwable $e) {
            \Log::error('ExportHome failed', ['error' => $e->getMessage()]);
        }
        return redirect(config('global.base_url').'menu');
       // return redirect('/menu');
    }
    public function updateMenuStatus(Request $request)
    {
        $menu = Menu::find($request->menu_id);

        if (!$menu) {
            return response()->json(['success' => false, 'message' => 'Invalid menu ID']);
        }

        // Check if enabling (activating) the menu
        if ($request->active_status && empty($menu->sequence_id)) {
            // Get the max sequence_id for the same menu group (menu_id)
            $maxSequence = Menu::where('menu_id', $menu->menu_id)->max('sequence_id');
            $menu->sequence_id = $maxSequence ? $maxSequence + 1 : 1;
        }

        $menu->status = $request->active_status ? '1' : '0';
        $menu->save();

        try {
            app(\App\Services\ExportHome::class)->run();
        } catch (\Throwable $e) {
            \Log::error('ExportHome failed', ['error' => $e->getMessage()]);
        }
        //return redirect(config('global.base_url').'menu');
 
        return response()->json(['success' => true]);
    }
}
