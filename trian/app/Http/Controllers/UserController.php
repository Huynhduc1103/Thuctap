<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Hashing\BcryptHasher;

class UserController extends Controller
{
    public function readUser()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function createUser(Request $request)
    {
        $bcryptHasher = new BcryptHasher();
        $hashedPassword = $bcryptHasher->make($request->input('password'));

        $users = User::create([
            'fullname' => $request->input('fullname'),
            'email' => $request->input('email'),
            'password' => $hashedPassword, // mật khẩu đã được mã hóa
            'phone' => $request->input('phone'),
            'birthday' => $request->input('birthday'),
            'groupcode' => $request->input('groupcode')
        ]);
        return response()->json($users);
    }

    public function updateUser(Request $request, $id) {
        $user = User::find($id);
        $data = $request->only(['fullname', 'email', 'phone', 'birthday', 'groupcode']);
        if (!$user) {
            return response()->json(['error' => 'Người dùng không tồn tại.']);
        }
      
        if (empty(array_filter($data))) {
            return response()->json(['error' => 'Không có dữ liệu để cập nhật.']);
        }
        $user->update($data);
        return response()->json(['success' => 'Thông tin người dùng đã được cập nhật.']);
    }

    public function deleteUser($id)
    {
        $user = User::find($id);
        // Kiểm tra xem người dùng có tồn tại hay không
        if (!$user) {
            return response()->json(['error' => 'Người dùng không tồn tại.']);
        } else {
            //Xóa người dùng
            $user->delete();
            return response()->json(['success' => 'Người dùng đã bị xóa.']);
        }
    }
}
