@extends('layouts.app')
@section('title', 'Quản Lý Nhập Kho')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Quản Lý Nhập Kho</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Trang Chủ</a></li>
                    <li class="breadcrumb-item active">Quản Lý Nhập Kho</li>
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
                        <form class="form-inline" method="GET" action="{{ route('inventory.index') }}">
                            <div class="col-md-3">
                                <input type="text" class="form-control w-100" name="search" placeholder="Tìm theo sản phẩm" value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control w-100" name="imported_date" placeholder="Ngày nhập kho" value="{{ request('imported_date') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-control w-100">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>Mới</option>
                                    <option value="used" {{ request('status') == 'used' ? 'selected' : '' }}>Đã qua sử dụng</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-primary" type="submit">Lọc / Tìm Kiếm</button>
                                <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Xóa Bộ Lọc</a>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Danh sách nhập kho -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Mã Sản Phẩm</th>
                                    <th>Ngày Hết Hạn</th>
                                    <th>Ngày Nhập Kho</th>
                                    <th>Trạng Thái</th>
                                    <th>Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inventories as $inventory)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><span class="badge badge-primary"><i>{{ $inventory->product->code }}</i></span></td>
                                        <td>{{ $inventory->expiration_date ?? 'Không có' }}</td>
                                        <td>{{ $inventory->imported_date ?? 'Không xác định'}}</td>
                                        <td>
                                            @php
                                                $status_labels = [
                                                    'new' => 'Sản Phẩm Mới',
                                                    'used' => 'Đã Qua Sử Dụng'
                                                ];
                                            @endphp
                                            <span class="badge badge-secondary">{{ $status_labels[$inventory->status] ?? 'Không xác định' }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('inventory.edit', $inventory->id) }}" class="btn btn-warning"><i class="fa-solid fa-file"></i> Xem</a>
                                            <form action="{{ route('inventory.destroy', $inventory->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Xóa giao dịch này?')"><i class="fa-solid fa-trash"></i> Xóa</button>
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
                                        href="{{ route('inventory.index', ['page' => $i]) }}">
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
