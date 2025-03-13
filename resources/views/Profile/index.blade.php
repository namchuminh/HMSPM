@extends('layouts.app')
@section('title', 'Thông Tin Cá Nhân')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Thông Tin Cá Nhân</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Trang Chủ</a></li>
                    <li class="breadcrumb-item active">Thông Tin Cá Nhân</li>
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
                        <h3 class="card-title">Cập Nhật Thông Tin</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('users.profileUpdate') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label>Họ Tên</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Email (Không thể thay đổi)</label>
                                <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                            </div>

                            <div class="form-group">
                                <label>Số Điện Thoại</label>
                                <input type="text" class="form-control" name="phone" value="{{ old('phone', $user->phone) }}" required>
                                @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <hr>
                            <h5 class="mb-3">Đổi Mật Khẩu</h5>
                            <div class="form-group">
                                <label>Mật Khẩu Hiện Tại</label>
                                <input type="password" class="form-control" name="current_password" placeholder="Nhập mật khẩu hiện tại">
                                @error('current_password') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Mật Khẩu Mới</label>
                                <input type="password" class="form-control" name="new_password" placeholder="Nhập mật khẩu mới">
                                @error('new_password') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Xác Nhận Mật Khẩu</label>
                                <input type="password" class="form-control" name="confirm_password" placeholder="Xác nhận mật khẩu mới">
                                @error('confirm_password') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <hr>
                            <h5 class="mb-3">Ảnh Đại Diện</h5>
                            <div class="form-group">
                                <label>Ảnh Đại Diện (Tùy Chọn)</label>
                                <input type="file" class="form-control" name="avatar">
                                @if ($user->avatar)
                                <br>
                                    <img src="{{ asset('storage/'.$user->avatar) }}" class="img-thumbnail" style="width: 120px; height: 150px;">
                                @endif
                                @error('avatar') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="text-right">
                                <a class="btn btn-secondary" href="{{ route('dashboard') }}">Quay Lại</a>
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
