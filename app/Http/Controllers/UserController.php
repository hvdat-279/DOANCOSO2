<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    // Hiển thị danh sách người dùng
    public function index()
    {
        $users = User::latest()->paginate(5);
        return view('partials.admin.user.user', compact('users'));
    }

    // Hiển thị form thêm người dùng
    public function add()
    {
        return view('partials.admin.user.addUser');
    }

    // Xử lý việc tạo người dùng
    public function create(Request $request)
    {
        // // Kiểm tra dữ liệu đầu vào
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Tạo mới người dùng
        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');
        $user->address = $request->input('address');
        $user->role = $request->input(
            'role',
            'user'
        ); // Gán role mặc định nếu không có
        $user->password = bcrypt($request->input('password')); // Mã hóa mật khẩu
        $user->save();

        // Thông báo thành công
        // session()->flash('success', 'Thêm người dùng thành công!');
        // return redirect()->route('user.index');
        return redirect('/admin/user')->with('success', 'Thêm người dùng thành công!');
    }


    // Hiển thị form chỉnh sửa người dùng
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('partials.admin.user.editUser', compact('user'));
    }

    // Cập nhật người dùng
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');
        $user->address = $request->input('address');

        // Nếu mật khẩu mới được nhập, cập nhật mật khẩu
        if ($request->has('password')) {
            $user->password =
                bcrypt($request->input('password'));
        }
        $user->save();

        // session()->flash('success', 'Cập nhật người dùng thành công!');
        return redirect("/admin/user")->with('success', 'Cập nhật người dùng thành công!');
    }

    // Xóa người dùng
    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        // session()->flash('success', 'Xóa người dùng thành công!');
        return redirect()->route('partials.admin.user.index')->with('success', 'Xóa người dùng thành công!');
    }

    //home
    public function showProfile()
    {
        $user = Auth::user();

        return view('partials.home.profile.info-profile', compact('user'));
    }
    public function editProfile()
    {
        $user = Auth::user();
        return view('partials.home.profile.edit-profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|digits_between:10,15',
            'address' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $user->phone,
            'address' => $validated['address'] ?? $user->address,
        ];

        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        DB::table('users')->where('id', $user->id)->update($updateData);

        return redirect()->route('profile')->with('success', 'Thông tin đã được cập nhật.');
    }

    //update anh
    public function updateImage(Request $request)
    {

        $request->validate([
            'img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Kiểm tra file ảnh
        ]);

        $user = Auth::user();

        // Xóa ảnh cũ nếu có
        if ($user->img && Storage::exists('storage/product_images/' . $user->img)) {
            Storage::delete('storage/product_images/' . $user->img);
        }

        // Lưu ảnh mới
        $path = $request->file('img')->store('product_images', 'public');

        // Cập nhật ảnh trong cơ sở dữ liệu
        $user->img = $path;
        $user->save();



        return response()->json([
            'success' => true,
            'new_image_url' => asset('storage/' . $path)
        ]);
    }
}
