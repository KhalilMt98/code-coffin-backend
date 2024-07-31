<?php

namespace App\Http\Controllers;

use App\Models\User;
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

    public function getUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                "message" => "User not found"
            ], 404);
        }
        return response()->json([
            "user" => $user
        ], 200);
    }
    public function updateUser(Request $req, $id)
    {
        $user = User::find($id);

        if ($user) {
            $validated_data = $req->validate([
                "name" => "required|string|max:255",
                "email" => "required|string|email|max:255|unique:users,email," . $user->id,
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
}