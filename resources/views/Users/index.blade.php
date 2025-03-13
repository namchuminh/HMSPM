@extends('layouts.app')
@section('title', 'Quản Lý Người Dùng')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Quản Lý Người Dùng</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Trang Chủ</a></li>
                    <li class="breadcrumb-item active">Quản Lý Người Dùng</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <form class="form-inline" method="GET" action="{{ route('users.index') }}">
                            <div class="col-md-2">
                                <input type="text" class="form-control w-100" name="search" placeholder="Tìm theo tên/email" value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <input type="text" class="form-control w-100" name="phone" placeholder="Số điện thoại" value="{{ request('phone') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="role" class="form-control w-100">
                                    <option value="">Tất cả vai trò</option>
                                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="manager" {{ request('role') == 'manager' ? 'selected' : '' }}>Quản lý</option>
                                    <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>Người dùng</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-control w-100">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Bị khóa</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary" type="submit">Lọc / Tìm Kiếm</button>
                                <a href="{{ route('users.index') }}" class="btn btn-secondary">Xóa Bộ Lọc</a>
                            </div>
                        </form>
                    </div>

                    <!-- Danh sách người dùng -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Ảnh Đại Diện</th>
                                <th>Họ Tên</th>
                                <th>Email</th>
                                <th>Số Điện Thoại</th>
                                <th>Vai Trò</th>
                                <th>Trạng Thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <img src="{{ $user->avatar ? asset('storage/'.$user->avatar) : asset('default-avatar.png') }}" class="img-thumbnail" style="width: 120px; height: 150px;">
                                    </td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ ucfirst($user->role) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $user->status == 'active' ? 'badge-success' : 'badge-danger' }}">
                                            {{ $user->status == 'active' ? 'Hoạt động' : 'Bị khóa' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning"><i class="fa-solid fa-pen-to-square"></i> Sửa</a>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Xóa người dùng này?')"><i class="fa-solid fa-trash"></i> Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        </table>
                    </div>

                    <!-- Phân trang -->
                    <div class="card-footer clearfix">
                        <ul class="pagination pagination-sm m-0 float-right">
                            @for ($i = 1; $i <= $totalPages; $i++)
                                <li class="page-item">
                                    <a class="page-link"
                                        href="{{ route('users.index', ['page' => $i]) }}">
                                        {{ $i }}
                                    </a>
                                </li>
                            @endfor
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
