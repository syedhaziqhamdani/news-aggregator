<?php
namespace App\Http\Controllers;

use App\Models\Preference;
use Illuminate\Http\Request;

class PreferenceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'sources' => 'array',
            'categories' => 'array',
            'authors' => 'array',
        ]);

        $preference = Preference::updateOrCreate(
            ['user_id' => $request->user()->id],
            $request->only(['sources', 'categories', 'authors'])
        );

        return response()->json(['message' => 'Preferences updated successfully'], 200);
    }

    public function show(Request $request)
    {
        $preference = Preference::where('user_id', $request->user()->id)->first();

        if (!$preference) {
            return response()->json(['message' => 'No preferences found'], 404);
        }

        return response()->json($preference);
    }
    public function getPreferences(Request $request)
    {
        $user = $request->user();

        // Fetch user preferences
        $preferences = Preference::where('user_id', $user->id)->first();

        if (!$preferences) {
            return response()->json(['message' => 'Preferences not found'], 404);
        }

        return response()->json([
            'data' => $preferences // Wrap the response in a "data" key
        ], 200);
    }
}