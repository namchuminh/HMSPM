@extends('layouts.app')
@section('title', 'Thêm Sản Phẩm')
@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Quản Lý Sản Phẩmn<n/h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Quản Lý Sản Phẩmn</a></li>
                    <li class="breadcrumb-item active">Thêm Sản Phẩmn</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Nhập thông tin sản phẩm</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('products.store') }}">
                            @csrf

                            <div class="form-group">
                                <label>Mã Sản Phẩm</label>
                                <input type="text" class="form-control" name="code" value="{{ old('code') }}" required>
                                @error('code') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Tên Sản Phẩm</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Mô Tả</label>
                                <textarea class="form-control" name="description" rows="4">{{ old('description') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label>Số Lượng</label>
                                <input type="number" class="form-control" name="quantity" min="0" value="{{ old('quantity', 0) }}" required>
                                @error('quantity') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Số Lượng Hết Hạn</label>
                                <input type="number" class="form-control" name="expired_quantity" min="0" value="{{ old('expired_quantity', 0) }}" required>
                                @error('expired_quantity') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Số Lượng Hư Hỏng</label>
                                <input type="number" class="form-control" name="damaged_quantity" min="0" value="{{ old('damaged_quantity', 0) }}" required>
                                @error('damaged_quantity') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Số Lượng Đang Mượn</label>
                                <input type="number" class="form-control" name="borrowed_quantity" min="0" value="{{ old('borrowed_quantity', 0) }}" required>
                                @error('borrowed_quantity') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="text-right">
                                <a class="btn btn-secondary" href="{{ route('products.index') }}">Quay Lại</a>
                                <button type="submit" class="btn btn-primary">Thêm Sản Phẩm</button>
                            </div>
                        </form>
                    </div>
                </div> 
            </div>
        </div>
    </div>
</section>
@endsection 