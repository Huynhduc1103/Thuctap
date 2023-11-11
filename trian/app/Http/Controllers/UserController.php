<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function readUser()
    {
        $users = User::paginate(5);
        return response()->json(['users' => $users], 200);
    }
    public function createUser(Request $request)
    {
        // Áp dụng validation
        $validator = Validator::make(
            $request->all(),
            [
                'fullname' => ['required', 'string', function ($attribute, $value, $fail) {
                    if (preg_match('/\d/', $value)) {
                        $fail($attribute . ' Vui lòng nhập rõ họ tên.');
                    }
                }],
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
                'phone' => 'required|regex:/^[0-9]{10}$/|unique:users,phone',
                'birthday' => 'required|date_format:Y-m-d',
                'groupcode' => 'required',
            ],
            $customMessages = [
                'fullname.required' => 'Trường họ tên không được để trống.',
                'fullname.string' => 'Trường họ tên phải là một chuỗi.',
                'fullname.regex' => 'Trường họ tên không được chứa số.',
                'email.required' => 'Trường email không được để trống.',
                'email.email' => 'Trường email phải là một địa chỉ email hợp lệ.',
                'email.unique' => 'Email đã tồn tại trong hệ thống.',
                'password.required' => 'Trường mật khẩu không được để trống.',
                'phone.required' => 'Trường số điện thoại không được để trống.',
                'phone.regex' => 'Trường số điện thoại phải có đúng 10 chữ số.',
                'phone.unique' => 'Số điện thoại đã tồn tại trong hệ thống.',
                'birthday.required' => 'Trường ngày sinh không được để trống.',
                'birthday.date_format' => 'Trường ngày sinh phải có định dạng Y-m-d.',
                'groupcode.required' => 'Trường mã nhóm không được để trống.',
            ]
        );
        // Kiểm tra validation fails
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        // Mã hóa mật khẩu
        $bcryptHasher = new BcryptHasher();
        $hashedPassword = $bcryptHasher->make($request->input('password'));
        try {
            // Tạo người dùng mới
            $user = User::create(
                [
                    'fullname' => $request->input('fullname'),
                    'email' => $request->input('email'),
                    'password' => $hashedPassword,
                    'phone' => $request->input('phone'),
                    'birthday' => $request->input('birthday'),
                    'groupcode' => $request->input('groupcode')
                ]
            );
            return response()->json($user);
        } catch (\Exception $e) {
            // Bắt ngoại lệ nếu có lỗi trong quá trình tạo người dùng
            return response()->json(['error' => 'Có lỗi xảy ra trong quá trình tạo người dùng.'], 500);
        }
    }

    public function updateUser(Request $request)
    {
        $user = User::find($request->input('id'));
        if (empty($user)) {
            return response()->json(['error' => 'Người dùng không tồn tại.'], 404);
        }
        $data = $request->only(['fullname', 'email', 'phone', 'birthday', 'groupcode']);
        // Validation rules
        $rules = [
            'fullname' => ['required', 'string', function ($attribute, $value, $fail) {
                if (preg_match('/\d/', $value)) {
                    $fail($attribute . ' Vui lòng nhập rõ họ tên.');
                }
            }],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => ['required', 'regex:/^[0-9]+$/', Rule::unique('users')->ignore($user->id)], // Only allow numbers
            'birthday' => 'required|date',
            'groupcode' => 'required|string',
        ];
        $customMessages = [
            'fullname.required' => 'Trường họ tên không được để trống.',
            'fullname.string' => 'Trường họ tên phải là một chuỗi.',
            'fullname.regex' => 'Trường họ tên không được chứa số.',
            'email.required' => 'Trường email không được để trống.',
            'email.email' => 'Trường email phải là một địa chỉ email hợp lệ.',
            'email.unique' => 'Email đã tồn tại trong hệ thống.',
            'phone.required' => 'Trường số điện thoại không được để trống.',
            'phone.regex' => 'Trường số điện thoại chỉ được chứa số.',
            'phone.unique' => 'Số điện thoại đã tồn tại trong hệ thống.',
            'birthday.required' => 'Trường ngày sinh không được để trống.',
            'birthday.date' => 'Trường ngày sinh phải là một ngày hợp lệ.',
            'groupcode.required' => 'Trường mã nhóm không được để trống.',
            'groupcode.string' => 'Trường mã nhóm phải là một chuỗi.',
        ];
        // Validate the request data
        $validator = app('validator')->make($data, $rules, $customMessages);

        // Check for validation errors
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 404);
        }

        $user->update($data);
        return response()->json(['success' => 'Thông tin người dùng đã được cập nhật.']);
    }
    public function deleteUser(Request $request)
    {
        $user = User::find($request->input('id'));
        // Kiểm tra xem người dùng có tồn tại hay không
        if (empty($user)) {
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

    public function findId(Request $request)
    {
        $id = $request->input('id');
        $user = User::find($id);
        if (empty($user)) {
            return response()->json(['error'=> 'Người dùng không tồn tại.'],404);
        }
        return response()->json($user);
    }
}
