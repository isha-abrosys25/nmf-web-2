<?php

namespace App\Http\Controllers;
use App\Models\File;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

class FileController extends Controller
{
    public function index(Request $request)
    {    
        $files = File::orderBy('id', 'DESC');
        if (isset($request->title)) {
            $files->Where('file_name', 'like', '%' .$request->title . '%');
        }

        // $files = $files->paginate(20);

        $perPage = $request->input('perPage', 30);
        $files = $files->whereIn('file_type', ['image/jpeg', 'image/png'])->paginate($perPage);

        if (isset($request->title)) {
            $title = $request->title;
            $files->setPath(asset('/files').'?title='.$title);
        } else {
            $title = '';
            $files->setPath(asset('/files'));
        }

        // Build query parameters for pagination links
        $queryParams = $request->except('page');
        if ($perPage != 30) {
            $queryParams['perPage'] = $perPage;
        }

        // Set the pagination path with query parameters
        $files->setPath(asset('/files') . '?' . http_build_query($queryParams));

        return view('admin/imageFile')->with('data',['files' => $files, 'title' => $title, 'perPage' => $perPage ]);
    }
    public function fileAdd(Request $request)
    {
        return view('admin/addImage');
    }
    public function addFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png|max:200', // max 200KB
        ],[
            'file.max' => 'The thumb image size must not exceed 200 KB.',
            //'file.dimensions' => 'The thumbnail dimensions must be exactly 800x450 pixels.',
        ]);

        // Determine the current year and month
        $year = date('Y');
        $month = date('m');

        $basePath = public_path("file/Image/");

        // Define the destination path
        $destinationPath = $basePath . $year . '/' . $month;

        // Get file details
        $originalFileName = $request->file->getClientOriginalName();
        $fileNameWithoutExt = pathinfo($originalFileName, PATHINFO_FILENAME);
        $fileName = str_replace(' ', '_', $fileNameWithoutExt) . time() . '.' . $request->file->extension();

        // Save file data to the database
        File::create([
            "user_id" => '1',
            "file_name" => $fileName,
            "file_type" => $request->file->getClientMimeType(),
            "file_size" => $request->file->getSize(),
            "full_path" => $destinationPath,
        ]);

        // Move the file to the destination path
        $request->file->move($destinationPath, $fileName);

        return redirect('/files')->with('success', 'Image has been uploaded successfully!');
    }

   public function uploadFile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|image|max:200', // max is in KB
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first('file'),
                ], 422);
            }

            $year = date('Y');
            $month = date('m');
            $basePath = public_path("file/Image");

  //$basePath = '/var/www/html/new_cms/public/file/Image/';

            $destinationPath = $basePath . '/' . $year . '/' . $month;

            // ✅ Ensure directory exists (like move() does internally)
            if (!is_dir($destinationPath)) {
                mkdir($destinationPath, 0775, true);
            }

            $file = $request->file('file');

            // Safe file name
            $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeName = str_replace(' ', '_', $originalFileName);
            $fileName = $safeName . '_' . time() . '.webp';

            $fullPath = $destinationPath . '/' . $fileName;

            // ✅ Convert + save directly to destination
            $manager = new ImageManager(new GdDriver());
            $manager->read($file)
                ->toWebp(80)
                ->save($fullPath);

            // Save info in DB
            $file_data = File::create([
                "user_id"   => auth()->id() ?? 1,
                "file_name" => $fileName,
                "file_type" => 'image/webp',
                "file_size" => filesize($fullPath),
                "full_path" => $destinationPath,
            ]);

            // Public URL NL1030:18Sept:2025:Commented and Added config path to fix the image error in app
            //$imageUrl = asset("file/Image/$year/$month/$fileName");
	   $imageUrl = config('global.base_url_image') . "file/Image/$year/$month/$fileName";

            return response()->json([
                'file_id'   => $file_data->id,
                'file_name' => $fileName,
                'success'   => true,
                'location'  => $imageUrl,
            ]);
        } catch (\Exception $e) {
            \Log::error('Image upload failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function editFile($id)
    {
        // dump("Edit Video Method Reached, ID: " . $id);
        $file = File::find($id); // More efficient way to fetch record
        //dump($file);
    
        if (!$file) {
            dump("No file found with ID: " . $id);
        }
        return view('admin/editImage')->with('file', $file);
    }
    public function fileEdit($id, Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png|max:200', // max 200KB'
        ],[
            'file.max' => 'The thumb image size must not exceed 200 KB.',
            // 'file.dimensions' => 'The thumbnail dimensions must be exactly 800x450 pixels.',
        ]);

        // Determine the current year and month
        $year = date('Y');
        $month = date('m');

        $basePath = public_path("file/Image");

        // Define the destination path
        $destinationPath = $basePath . '/' . $year . '/' . $month;

        // Get file details
        $originalFileName = $request->file->getClientOriginalName();
        $fileNameWithoutExt = pathinfo($originalFileName, PATHINFO_FILENAME);
        $fileName = str_replace(' ', '_', $fileNameWithoutExt) . time() . '.' . $request->file->extension();

        // Save updated file data to the database
        File::where('id', $id)->update([
            "user_id" => '1',
            "file_name" => $fileName,
            "file_type" => $request->file->getClientMimeType(),
            "file_size" => $request->file->getSize(),
            "full_path" => $destinationPath,
        ]);

        // Move the file to the destination path
        $request->file->move($destinationPath, $fileName);

        return redirect('/files')->with('success', 'Image has been edited successfully!');
    }
    public function del($id, Request $request) 
    {
        ?>
        <script>
            if (confirm('Are you sure? This action will permanently delete this image.')) {
                window.location.href =  '<?php echo asset('/files/del').'/'.$id; ?>'
            } else {
                window.location.href =  '<?php echo asset('/files'); ?>'
            }
        </script>
        <?php
    }
    public function deleteFile($id, Request $request)
    {
        // Retrieve the Image01 record
        $file = File::find($id);

        $imagePath = $file->full_path;
        if (strpos($imagePath, 'file') !== false) 
        {
            $findFilePos = strpos($imagePath, 'file');                      
            $imageFilePath = substr($imagePath, $findFilePos);
            $imageFilePath = $imageFilePath . '/' . $file->file_name;
        }

        // Delete image file if it exists
        if (!empty($file->file_name) && file_exists($imageFilePath)) {
            unlink($imageFilePath);
        }

        // Delete the database record
        $file->delete();

        return redirect('/files')->with('success', 'Image has been deleted successfully.');
    }
}

