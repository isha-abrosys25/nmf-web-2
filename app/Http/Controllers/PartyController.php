<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Party;
use Illuminate\Support\Facades\Log;

class PartyController extends Controller
{
    public function add()
    {
        return view('admin.addPartyDetails');
    }

    public function save(Request $request)
    {
        $request->validate([
            'party_name'   => 'required|string|max:255',
            'abbreviation' => 'required|string|max:10',
            'alliance'     => 'required|string|max:50',
            'party_logo'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $logoPath = null;

        if ($request->hasFile('party_logo')) {
            $image = $request->file('party_logo');
            $fileName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('asset/images/election-widget'), $fileName);
            $logoPath = 'asset/images/election-widget/' . $fileName;
        }

        Party::create([
            'party_name'   => $request->party_name,
            'abbreviation' => $request->abbreviation,
            'alliance'     => $request->alliance,
            'party_logo'   => $logoPath,
            'status'       => 1 // default enabled
        ]);

        try {
            app(\App\Services\ExportHome::class)->run();
        } catch (\Throwable $e) {
             Log::error('ExportHome failed', ['error' => $e->getMessage()]);
        }

        return redirect(config('global.base_url').'election/party/list')->with('success', 'Parties updated successfully!');
    }
    // Show Party List
    public function list()
    {
        $parties = Party::orderBy('id', 'desc')->get();
        return view('admin.partyList', compact('parties'));
    }

    //  Edit Party Form
    public function edit($id)
    {
        $party = Party::findOrFail($id);
        return view('admin.editParty', compact('party'));
    }

    // Update Party Data
    public function update(Request $request, $id)
    {
        $party = Party::findOrFail($id);

        $request->validate([
            'party_name'   => 'required|string|max:255',
            'abbreviation' => 'required|string|max:10',
            'alliance'     => 'required|string|max:50',
            'party_logo'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $logoPath = $party->party_logo;

        if ($request->hasFile('party_logo')) {
            // Delete old logo if exists
            if ($party->party_logo && file_exists(public_path($party->party_logo))) {
                unlink(public_path($party->party_logo));
            }

            $image = $request->file('party_logo');
            $fileName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('asset/images/election-widget'), $fileName);
            $logoPath = 'asset/images/election-widget/' . $fileName;
        }

        $party->update([
            'party_name'   => $request->party_name,
            'abbreviation' => $request->abbreviation,
            'alliance'     => $request->alliance,
            'party_logo'   => $logoPath
        ]);

        return redirect()->route('party.list')->with('success', 'Party updated successfully!');
    }

    // Update Party Status Enable/Disable
    public function updateStatus($id)
    {
        $party = Party::findOrFail($id);
        $party->status = $party->status == 1 ? 0 : 1;
        $party->save();

        return redirect()->back()->with('success', 'Party status updated successfully!');
    }
    // Delete Party
public function destroy($id)
{
    $party = Party::findOrFail($id);

    // Delete logo file
    if ($party->party_logo && file_exists(public_path($party->party_logo))) {
        unlink(public_path($party->party_logo));
    }

    $party->delete();

    return redirect()->back()->with('success', 'Party deleted successfully!');
}

}
