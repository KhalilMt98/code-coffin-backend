<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserController extends Controller 
{
    public function getAllUsers()
    {
        $users = User::all();
        return response()->json([
            "users" => $users
        ], 200);
    }

    public function getUser()
    {
        $user_id = Auth::id();
        $user = User::find($user_id);
        if (!$user) {
            return response()->json([
                "message" => "User not found"
            ], 404);
        }
        return response()->json([
            "user" => $user
        ], 200);
    }
    public function updateUser(Request $req)
{
    $user_id = Auth::id();
    $user = User::find($user_id);

    if ($user) {
        $validated_data = $req->validate([
            "name" => "sometimes|string|max:255",
            "email" => "sometimes|string|email|max:255|unique:users,email," . $user->id,
            "password" => "nullable|string|min:6",
        ]);

        $updateData = [
            "name" => $validated_data["name"],
            "email" => $validated_data["email"],
        ];

        if ($req->filled("password")) {
            $updateData["password"] = Hash::make($validated_data["password"]);
        }

        $user->update($updateData);

        return response()->json([
            "message" => "User updated successfully"
        ], 200);
    }

    return response()->json([
        "message" => "User not found"
    ], 404);
}
        public function deleteUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                "message" => "User not found"
            ], 404);
        }
        $user->delete();
        return response()->json(null, 204);
    }


    public function upload(Request $request)
{
    $validator = Validator::make($request->all(), [
        'file' => 'required|mimes:xlsx,xls,csv',
    ]);

    if ($validator->fails()) {
        \Log::error('File validation failed', $validator->errors()->toArray());
        return response()->json(['errors' => $validator->errors()], 422);
    }

    try {
        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        \Log::info('Sheet data', ['data' => $sheetData]);
    } catch (\Exception $e) {
        \Log::error('Error loading file', ['exception' => $e->getMessage()]);
        return response()->json(['message' => 'Error loading file'], 500);
    }

    $errors = [];
    $users = [];

    foreach ($sheetData as $index => $row) {
        \Log::info('Row data', ['index' => $index, 'rowData' => $row]);

        if ($index === 1) {
            continue;
        }

        $rowData = [
            'name' => $row['A'],
            'email' => $row['B'],
            'password' => $row['C'],
            'role' => $row['D'],
        ];

        $rowValidator = Validator::make($rowData, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:admin,user',
        ]);

        if ($rowValidator->fails()) {
            $errors[] = "Row {$index}: " . implode(', ', $rowValidator->errors()->all());
            \Log::error("Row {$index} validation failed", ['errors' => $rowValidator->errors()->toArray()]);
        } else {
            $users[] = [
                'name' => $row['A'],
                'email' => $row['B'],
                'password' => Hash::make($row['C']),
                'role' => $row['D'],
            ];
        }
    }

    if (!empty($errors)) {
        \Log::error('Row validation failed', ['errors' => $errors]);
        return response()->json(['errors' => $errors], 422);
    }
    if (empty($users)) {
        \Log::info('No users to create');
        return response()->json(['message' => 'No users to create'], 422);
    }

    try {
        User::insert($users);
    } catch (\Exception $e) {
        \Log::error('Error inserting users', ['exception' => $e->getMessage()]);
        return response()->json(['message' => 'Error inserting users'], 500);
    }

    \Log::info('Users imported successfully', ['users' => $users]);
    return response()->json(['message' => 'Users imported successfully', 'users' => $users], 200);
}

}
