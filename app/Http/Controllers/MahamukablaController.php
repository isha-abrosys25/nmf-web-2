<?php
 
namespace App\Http\Controllers;
 
use App\Models\Mahamukabla;
use App\Models\ElectionResult;
use App\Models\Candidate;
use App\Models\Party;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
 
class MahamukablaController extends Controller
{
    // Show create form
    public function create()
    {
        $parties = Party::select('id', 'party_name')->get();
        $candidates = Candidate::select('id', 'candidate_name')->get();
        // $authors = User::select('id', 'name')->get();
 
        return view('admin.addMahamukabla', compact('parties', 'candidates' ));
    }
 
    // Store new Mahamukabla entry
    public function store(Request $request)
    {
        $request->validate([
            'slide_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'author_id' => 'required|exists:users,id',
        ]);
 
        $imagePath = null;
 
        if ($request->hasFile('slide_image')) {
            $image = $request->file('slide_image');
 
            // Clean file name
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $cleanName = preg_replace('/[^A-Za-z0-9_\-]/', '', str_replace(' ', '_', $originalName));
            $fileName = $cleanName . '_' . time() . '.' . $image->getClientOriginalExtension();
 
            // Define destination folder in public/assets/images/mahamukabla_slides
            $destinationPath = public_path('asset/images/election-widget');
 
            // Create folder if it doesn't exist
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
 
            // Move file
            $image->move($destinationPath, $fileName);
 
            // Save relative path
            $imagePath = 'asset/images/election-widget/' . $fileName;
        }
 
        Log::info("Slide image path: $imagePath");
 
        // Automatically pick latest candidate and party
        $latestCandidate = Candidate::latest()->first();
        $latestParty = Party::latest()->first();
 
        Mahamukabla::create([
           // 'party_id' => $latestParty->id ?? null,
           // 'candidate_id' => $latestCandidate->id ?? null,
            // 'author_id' => $request->author_id,
            'slide_image' => $imagePath,
        ]);
        try {
            app(\App\Services\ExportHome::class)->run();
        } catch (\Throwable $e) {
             Log::error('ExportHome failed', ['error' => $e->getMessage()]);
        }

        //return redirect()->back()->with('success', 'Mahamukabla entry added successfully!');
        return redirect(config('global.base_url').'election/mahamukabla/show')->with('success', 'Mahamukabla entry added successfully!');
    }
 
    // Show all Mahamukabla entries (with Party + Candidate)
    public function show()
    {
        // Fetch Mahamukabla with relationships
        $mahamukablas = Mahamukabla::with(['candidate', 'party', ])
            ->orderBy('sequence', 'ASC')
            ->get();
 
        // Fetch candidates and parties separately too
        $candidates = Candidate::select('id', 'candidate_name', 'candidate_image', 'area', 'party_id')->get();
        $parties = Party::select('id', 'party_name', 'party_logo')->get();
 
        return view('admin.showMahamukabla', compact(
            'mahamukablas',
            'candidates',
            'parties'
        ));
    }
    public function updateSlideStatus(Request $request)
    {
        $slide = Mahamukabla::findOrFail($request->slide_id);
        $slide->status = $request->status;
        $slide->save();

        return response()->json(['success' => true]);
    }

    public function toggle($id)
    {
        $slide = Mahamukabla::findOrFail($id);
        $slide->status = $slide->status ? 0 : 1;
        $slide->save();

        return back()->with('success', 'Slide status updated successfully!');
    }
    public function destroy($id)
    {
        $slide = Mahamukabla::findOrFail($id);

        // Delete image from folder
        if ($slide->slide_image && file_exists(public_path($slide->slide_image))) {
            unlink(public_path($slide->slide_image));
        }

        // Delete record from DB
        $slide->delete();

        return back()->with('success', 'Slide deleted successfully!');
    }


}