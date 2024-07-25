<?php

namespace App\Http\Controllers;

use App\Models\SourceCode;
use Illuminate\Http\Request;

class SourceCodesController extends Controller
{
    public function getAllSourceCodes()
    {
        $sourceCodes = SourceCode::all();
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

    public function createSourceCode(Request $req)
    {
        $validated_data = $req->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string',
        ]);

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
