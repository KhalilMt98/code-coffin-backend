<?php

namespace App\Http\Controllers;

use App\Models\SourceCode;
use Auth;
use Illuminate\Http\Request;

class SourceCodesController extends Controller
{
    public function getAllSourceCodes()
    {
        $userId = auth()->id();
    
        $sourceCodes = SourceCode::where('user_id', $userId)->get();
    
        return response()->json([
            "source_codes" => $sourceCodes
        ], 200);
    }
public function getUserProjects($userId)
{
    try {
        $sourceCodes = SourceCode::where('user_id', $userId)->get();
        
        if ($sourceCodes->isEmpty()) {
            return response()->json([
                "message" => "No projects found for this user"
            ], 404);
        }

        return response()->json($sourceCodes, 200);
    } catch (\Exception $e) {
        return response()->json([
            "message" => "Internal Server Error",
            "error" => $e->getMessage()
        ], 500);
    }
}

    
    public function getSourceCode($id)
{
    $userId = auth()->id();
    $sourceCode = SourceCode::where('id', $id)->where('user_id', $userId)->first();
    
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
            'title' => 'required|string|max:255|unique:source_codes,title',
             'code' => 'string',
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
        $userId = auth()->id();
        $sourceCode = SourceCode::where('id', $id)->where('user_id', $userId)->first();

        if ($sourceCode) {
            $validated_data = $req->validate([
                'title' => 'required|string|max:255',
                'code' => 'string',
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
