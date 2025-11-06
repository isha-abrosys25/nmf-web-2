<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Party;  // Use Party model now
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
class CandidateController extends Controller
{
    // Show add candidate form
    public function create()
    {
        $parties = Party::select('id', 'party_name')->get();
        return view('admin.addCandidate', compact('parties'));
    }

    // Store candidate data
    public function store(Request $request)
{
    $request->validate([
       'party_id'        => 'required|exists:parties,id',
        'candidate_name'  => 'required|string|max:255',
        'candidate_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'area'            => 'required|string|max:255',
    ]);

        $imagePath = null;
        if ($request->hasFile('candidate_image')) {
            $image = $request->file('candidate_image');
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $cleanName = preg_replace('/[^A-Za-z0-9_\-]/', '', str_replace(' ', '_', $originalName));
            $fileName = $cleanName . '_' . time() . '.' . $image->getClientOriginalExtension();

            $destinationPath = public_path('asset/images/election-widget');
            if (!file_exists($destinationPath)) mkdir($destinationPath, 0755, true);

            $image->move($destinationPath, $fileName);
            $imagePath = 'asset/images/election-widget/' . $fileName;
        }

        Candidate::create([
            'party_id'        => $request->party_id,
            'candidate_name'  => $request->candidate_name,
            'candidate_image' => $imagePath,
            'area'            => $request->area,
            'is_active'       => 1,
            'c_status'        => 'SELECT', // default
        ]);

        try {
            app(\App\Services\ExportHome::class)->run();
        } catch (\Throwable $e) {
             Log::error('ExportHome failed', ['error' => $e->getMessage()]);
        }
       return redirect(config('global.base_url').'election/candidates')->with('success', 'Candidate added successfully!');

//        return redirect()->route('candidates.list')->with('success', 'Candidate added successfully!');
    }

    // Candidate List
    public function list()
    {
        $candidates = Candidate::with('party')->orderBy('id', 'desc')->get();
        $candidateStatuses = config('global.candidates_status');
        return view('admin.candidateList', compact('candidates', 'candidateStatuses'));
    }

    // Update Candidate Status (dropdown change)
    public function updateCandidateStatus(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);
        $candidate->c_status = $request->status; // store dropdown selection
        $candidate->save();

         try {
            app(\App\Services\ExportHome::class)->run();
        } catch (\Throwable $e) {
             Log::error('ExportHome failed', ['error' => $e->getMessage()]);
        }

        
        return redirect(config('global.base_url').'election/candidates')->with('success', 'Candidate status updated successfully!');

        //return back()->with('success', 'Candidate status updated successfully!');
    }

    // Edit Candidate
    public function edit($id)
    {
        $candidate = Candidate::findOrFail($id);
        $parties = Party::select('id', 'party_name')->get();
        return view('admin.editCandidate', compact('candidate', 'parties'));
    }
    

    // Update Candidate
    public function update(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);

        $request->validate([
            'party_id'        => 'required|exists:parties,id',
            'candidate_name'  => 'required|string|max:255',
            'candidate_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'area'            => 'required|string|max:255',
        ]);

        $imagePath = $candidate->candidate_image;

        if ($request->hasFile('candidate_image')) {
            if ($candidate->candidate_image && file_exists(public_path($candidate->candidate_image))) {
                unlink(public_path($candidate->candidate_image));
            }

            $image = $request->file('candidate_image');
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $cleanName = preg_replace('/[^A-Za-z0-9_\-]/', '', str_replace(' ', '_', $originalName));
            $fileName = $cleanName . '_' . time() . '.' . $image->getClientOriginalExtension();

            $destinationPath = public_path('asset/images/election-widget');
            if (!file_exists($destinationPath)) mkdir($destinationPath, 0755, true);

            $image->move($destinationPath, $fileName);
            $imagePath = 'asset/images/election-widget/' . $fileName;
        }

        $candidate->update([
            'party_id'        => $request->party_id,
            'candidate_name'  => $request->candidate_name,
            'candidate_image' => $imagePath,
            'area'            => $request->area
        ]);

        try {
            app(\App\Services\ExportHome::class)->run();
        } catch (\Throwable $e) {
             Log::error('ExportHome failed', ['error' => $e->getMessage()]);
        }

        return redirect(config('global.base_url').'election/candidates')->with('success', 'Candidate updated successfully!');

       // return redirect()->route('candidates.list')->with('success', 'Candidate updated successfully!');
    }
    // Delete Candidate
public function destroy($id)
{
    $candidate = Candidate::findOrFail($id);

    // Delete image file
    if ($candidate->candidate_image && file_exists(public_path($candidate->candidate_image))) {
        unlink(public_path($candidate->candidate_image));
    }

    $candidate->delete();
     try {
            app(\App\Services\ExportHome::class)->run();
        } catch (\Throwable $e) {
             Log::error('ExportHome failed', ['error' => $e->getMessage()]);
        }

        return redirect(config('global.base_url').'election/candidates')->with('success', 'Candidate deleted successfully!');
   // return redirect()->back()->with('success', 'Candidate deleted successfully!');
}

}
