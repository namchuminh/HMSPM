@extends('layouts.app')
@section('title', 'Quản Lý Sản Phẩm')
@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Quản Lý Sản Phẩm</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Trang Chủ</a></li>
                    <li class="breadcrumb-item active">Quản Lý Sản Phẩm</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- /.row -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <form class="form-inline" method="GET" action="{{ route('products.index') }}">
                            <div class="col-md-2">
                                <input type="text" class="form-control w-100" name="search" placeholder="Tìm kiếm mã/tên" value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control w-100" name="expired_quantity" placeholder="Số lượng hết hạn" value="{{ request('expired_quantity') }}">
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control w-100" name="damaged_quantity" placeholder="Số lượng hư hỏng" value="{{ request('damaged_quantity') }}">
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control w-100" name="borrowed_quantity" placeholder="Số lượng đang mượn" value="{{ request('borrowed_quantity') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-control w-100">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>Mới</option>
                                    <option value="used" {{ request('status') == 'used' ? 'selected' : '' }}>Đã qua sử dụng</option>
                                    <option value="damaged" {{ request('status') == 'damaged' ? 'selected' : '' }}>Hư hỏng</option>
                                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Hết hạn</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary" type="submit">Lọc / Tìm Kiếm</button>
                                <a href="{{ route('products.index') }}" class="btn btn-secondary">Xóa Bộ Lọc</a>
                            </div>
                        </form>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Mã Sản Phẩm</th>
                                <th>Tên Sản Phẩm</th>
                                <th>Số lượng</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="badge badge-primary"><i>{{ $product->code }}</i></span></td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->quantity }} sản phẩm</td>
                                    <td>
                                        @php
                                            $status_labels = [
                                                'new' => 'Sản Phẩm Mới',
                                                'used' => 'Đã Qua Sử Dụng',
                                                'damaged' => 'Hư Hỏng',
                                                'expired' => 'Hết Hạn'
                                            ];
                                        @endphp
                                        <span class="badge badge-secondary">{{ $status_labels[$product->status] ?? 'Không xác định' }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning"><i class="fa-solid fa-pen-to-square"></i> Sửa</a>
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Xóa sản phẩm này?')"><i class="fa-solid fa-trash"></i> Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        </table>
                    </div>
                    <div class="card-footer clearfix">
                        <ul class="pagination pagination-sm m-0 float-right">
                            @for ($i = 1; $i <= $totalPages; $i++)
                                <li class="page-item">
                                    <a class="page-link"
                                        href="{{ route('products.index', ['page' => $i]) }}">
                                        {{ $i }}
                                    </a>
                                </li>
                            @endfor
                        </ul>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
@endsection