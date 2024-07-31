<?php

namespace App\Http\Controllers;

use App\Models\SourceCode;
use Auth;
use Illuminate\Http\Request;

class SourceCodesController extends Controller
{
    public function getAllSourceCodes()
    {
        // Retrieve the authenticated user's ID
        $userId = auth()->id();
    
        // Fetch only the source codes associated with the authenticated user
        $sourceCodes = SourceCode::where('user_id', $userId)->get();
    
        return response()->json([
            "source_codes" => $sourceCodes
        ], 200);
    }
    
    public function getSourceCode($id)
    {
        $sourceCode = SourceCode::find($id);
        if (!$sourceCode) {
            return response()->json([
                "message" => "Source Code not found"
            ], 404);
        }
        return response()->json([
            "source_code" => $sourceCode
        ], 200);
    }
    public function SourceCodeByUserId(Request $req)
{
    $user_id = Auth::id();
    if (!$user_id) {
        return response()->json([
            "message" => "User not authenticated"
        ], 401); 
    }

    $sourceCode = SourceCode::where('user_id', $user_id)->get();

    return response()->json([
        "source_code" => $sourceCode
    ], 200); 
}

    public function createSourceCode(Request $req)
    {

        $validated_data = $req->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string',
        ]);

        $user_id = Auth::id();

        if (!$user_id) {
            return response()->json([
                "message" => "User not authenticated"
            ], 401);
        }


        $validated_data['user_id'] = $user_id;


        $sourceCode = SourceCode::create($validated_data);

        return response()->json([
            "source_code" => $sourceCode,
            "message" => 'Source Code created successfully'
        ], 201);
    }

    public function updateSourceCode(Request $req, $id)
    {
        $sourceCode = SourceCode::find($id);

        if ($sourceCode) {
            $validated_data = $req->validate([
                'title' => 'required|string|max:255',
                'code' => 'required|string',
            ]);

            $sourceCode->update($validated_data);

            return response()->json([
                "message" => "Source Code updated successfully"
            ], 200);
        }

        return response()->json([
            "message" => "Source Code not found"
        ], 404);
    }

    public function deleteSourceCode($id)
    {
        $sourceCode = SourceCode::find($id);
        if (!$sourceCode) {
            return response()->json([
                "message" => "Source Code not found"
            ], 404);
        }
        $sourceCode->delete();
        return response()->json(null, 204);
    }
}
