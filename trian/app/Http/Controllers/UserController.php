<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function readUser()
    {
        $users = User::all();
        return response()->json($users);
    }
    public function createUser(Request $request)
    {
        // Áp dụng validation
        $validator = Validator::make($request->all(), [
            'fullname' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'phone' => 'required|regex:/^[0-9]{10}$/|unique:users,phone',
            'birthday' => 'required',
            'groupcode' => 'required',
        ]);
    
        // Kiểm tra validation fails
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
    
        // Mã hóa mật khẩu
        $bcryptHasher = new BcryptHasher();
        $hashedPassword = $bcryptHasher->make($request->input('password'));
    
        try {
            // Tạo người dùng mới
            $user = User::create([
                'fullname' => $request->input('fullname'),
                'email' => $request->input('email'),
                'password' => $hashedPassword,
                'phone' => $request->input('phone'),
                'birthday' => $request->input('birthday'),
                'groupcode' => $request->input('groupcode')
            ]);
    
            return response()->json($user);
        } catch (\Exception $e) {
            // Bắt ngoại lệ nếu có lỗi trong quá trình tạo người dùng
            return response()->json(['error' => 'Có lỗi xảy ra trong quá trình tạo người dùng.'], 500);
        }
    }
    
    

   

    public function updateUser(Request $request, $id)
    {
        $user = User::find($id);
        $data = $request->only(['fullname', 'email', 'phone', 'birthday', 'groupcode']);

        // Validation rules
        $rules = [
            'fullname' => ['required', 'string', function ($attribute, $value, $fail) {
                if (preg_match('/\d/', $value)) {
                    $fail($attribute . ' Vui lòng nhập rõ họ tên.');
                }
            }],
            'email' => 'required|email',
            'phone' => ['required', 'regex:/^[0-9]+$/'], // Only allow numbers
            'birthday' => 'required|date',
            'groupcode' => 'required|string',
        ];

        // Validate the request data
        $validator = app('validator')->make($data, $rules);

        // Check for validation errors
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        if (!$user) {
            return response()->json(['error' => 'Người dùng không tồn tại.']);
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
