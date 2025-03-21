@extends('layouts.app')
@section('title', 'Chỉnh Sửa Người Dùng')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Chỉnh Sửa Người Dùng</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Quản Lý Người Dùng</a></li>
                    <li class="breadcrumb-item active">Chỉnh Sửa Người Dùng</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Cập Nhật Thông Tin Người Dùng</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label>Họ Tên</label>
                                <input type="text" class="form-control" placeholder="Nhập họ tên" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" placeholder="Nhập email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Số Điện Thoại</label>
                                <input type="text" class="form-control" placeholder="Nhập số điện thoại" name="phone" value="{{ old('phone', $user->phone) }}" required>
                                @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Mật Khẩu (Để trống nếu không muốn đổi)</label>
                                <input type="password" class="form-control" placeholder="Mật khẩu mới" name="password">
                                @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Vai Trò</label>
                                <select name="role" class="form-control" required>
                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Quản trị viên</option>
                                    <option value="manager" {{ $user->role == 'manager' ? 'selected' : '' }}>Quản lý</option>
                                    <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>Người dùng</option>
                                </select>
                                @error('role') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Trạng Thái</label>
                                <select name="status" class="form-control" required>
                                    <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                    <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Bị khóa</option>
                                </select>
                                @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Ảnh Đại Diện (Tùy Chọn)</label>
                                <input type="file" class="form-control" name="avatar">
                                @if ($user->avatar)
                                    <br>
                                    <img src="{{ asset('storage/'.$user->avatar) }}" class="img-thumbnail" style="width: 120px; height: 150px;" >
                                @endif
                                @error('avatar') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="text-right">
                                <a class="btn btn-secondary" href="{{ route('users.index') }}">Quay Lại</a>
                                <button type="submit" class="btn btn-primary">Lưu Thông Tin</button>
                            </div>
                        </form>
                    </div>
                </div> 
            </div>
        </div>
    </div>
</section>
@endsection
