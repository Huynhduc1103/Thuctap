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

        // Sử dụng biểu thức chính quy để kiểm tra định dạng email
        $email = $request->input('email');
        if (!preg_match('/^\S+@\S+\.\S+$/', $email)) {
            return response()->json(['error' => 'Định dạng email không hợp lệ.'], 400);
        }

        $users = User::create([
            'fullname' => $request->input('fullname'),
            'email' => $email,
            'password' => $hashedPassword, // mật khẩu đã được mã hóa
            'phone' => $request->input('phone'),
            'birthday' => $request->input('birthday'),
            'groupcode' => $request->input('groupcode')
        ]);

        return response()->json($users);
    }


    public function updateUser(Request $request, $id)
    {
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

    public function searchUsers(Request $request)
    {
        // Lấy các thông tin tìm kiếm từ request
        $searchTerm = $request->input('searchTerm');

        // Thực hiện tìm kiếm sử dụng Eloquent
        $users = User::where('fullname', 'like', '%' . $searchTerm . '%')
            ->orWhere('phone', 'like', '%' . $searchTerm . '%')
            ->orWhere('email', 'like', '%' . $searchTerm . '%')
            ->get();

        // Kiểm tra xem có người dùng nào khớp với tìm kiếm không
        if ($users->isEmpty()) {
            return response()->json(['error' => 'Không tìm thấy người dùng.'], 404);
        }

        return response()->json($users);
    }
}
