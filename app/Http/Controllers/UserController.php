<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Hiển thị danh sách người dùng.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Tìm kiếm theo tên hoặc email
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                ->orWhere('email', 'LIKE', '%' . $request->search . '%');
            });
        }

        // Lọc theo số điện thoại
        if ($request->has('phone') && $request->phone != '') {
            $query->where('phone', 'LIKE', '%' . $request->phone . '%');
        }

        // Lọc theo vai trò
        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        // Lọc theo trạng thái
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Phân trang dữ liệu
        $users = $query->orderBy('role', 'asc')->paginate(10);

        // Giữ lại các giá trị lọc khi chuyển trang
        $users->appends($request->query());

        $totalPages = $users->lastPage();

        return view('users.index', compact('users', 'totalPages'));
    }

    /**
     * Hiển thị form tạo người dùng mới.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Lưu người dùng mới.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|max:15|unique:users,phone',
            'password' => 'required|min:4',
            'role' => 'required|in:admin,manager,user',
            'status' => 'required|in:active,inactive',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ], [
            'name.required' => 'Tên không được để trống.',
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email đã tồn tại.',
            'phone.required' => 'Số điện thoại không được để trống.',
            'phone.unique' => 'Số điện thoại đã tồn tại.',
            'password.required' => 'Mật khẩu không được để trống.',
            'password.min' => 'Mật khẩu phải có ít nhất 4 ký tự.',
            'role.required' => 'Vai trò không được để trống.',
            'status.required' => 'Trạng thái không được để trống.',
            'avatar.image' => 'Ảnh đại diện phải là định dạng ảnh.',
            'avatar.mimes' => 'Ảnh đại diện chỉ chấp nhận các định dạng: jpeg, png, jpg, gif.',
            'avatar.max' => 'Ảnh đại diện không được lớn hơn 10MB.'
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->status,
            'avatar' => $avatarPath
        ]);

        return redirect()->route('users.index')->with('success', 'Người dùng đã được tạo.');
    }

    /**
     * Hiển thị form chỉnh sửa người dùng.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    /**
     * Cập nhật thông tin người dùng.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $currentUser = auth()->user(); // Lấy tài khoản đang đăng nhập

        // Nếu người dùng đang sửa là chính mình và cố gắng thay đổi role hoặc status
        if ($user->id === $currentUser->id) {
            if ($request->role !== 'admin') {
                return redirect()->route('users.edit', $id)->with('error', 'Bạn không thể thay đổi vai trò của chính mình.');
            }
            if ($request->status !== 'active') {
                return redirect()->route('users.edit', $id)->with('error', 'Bạn không thể tự khóa tài khoản của chính mình.');
            }
        }

        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|max:15|unique:users,phone,' . $id,
            'password' => 'nullable|min:6',
            'role' => 'required|in:admin,manager,user',
            'status' => 'required|in:active,inactive',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'name.max' => 'Họ tên không được vượt quá 255 ký tự.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email đã tồn tại.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.max' => 'Số điện thoại không quá 15 ký tự.',
            'phone.unique' => 'Số điện thoại đã tồn tại.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'role.required' => 'Vui lòng chọn vai trò.',
            'role.in' => 'Vai trò không hợp lệ.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'avatar.image' => 'Ảnh đại diện phải là file ảnh.',
            'avatar.mimes' => 'Ảnh đại diện chỉ chấp nhận các định dạng: jpeg, png, jpg, gif.',
            'avatar.max' => 'Ảnh đại diện không được lớn hơn 10MB.'
        ]);

        // Xử lý avatar nếu có upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        // Nếu không phải đang cập nhật chính mình, mới cho phép thay đổi vai trò và trạng thái
        if ($user->id !== $currentUser->id) {
            $user->role = $request->role;
            $user->status = $request->status;
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        return redirect()->route('users.edit', $id)->with('success', 'Người dùng đã được cập nhật.');
    }



    /**
     * Xóa người dùng.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $currentUser = auth()->user(); // Lấy người dùng hiện tại đang đăng nhập

        // Kiểm tra nếu người dùng là admin hoặc đang tự xóa chính mình
        if ($user->role === 'admin') {
            return redirect()->route('users.index')->with('error', 'Không thể xóa quản trị viên.');
        }

        if ($user->id === $currentUser->id) {
            return redirect()->route('users.index')->with('error', 'Bạn không thể xóa chính mình.');
        }

        // Xóa ảnh đại diện nếu có
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Xóa người dùng
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Người dùng đã bị xóa.');
    }

}
