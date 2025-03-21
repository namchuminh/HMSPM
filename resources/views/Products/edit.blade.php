@extends('layouts.app')
@section('title', 'Chỉnh Sửa Sản Phẩm')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Chỉnh Sửa Sản Phẩm</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Quản Lý Sản Phẩm</a></li>
                    <li class="breadcrumb-item active">Chỉnh Sửa</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Cập Nhật Thông Tin</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('products.update', $product->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label>Mã Sản Phẩm</label>
                                <input type="text" class="form-control" name="code" value="{{ old('code', $product->code) }}" required>
                                @error('code') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Tên Sản Phẩm</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name', $product->name) }}" required>
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Mô Tả</label>
                                <textarea class="form-control" name="description" rows="4">{{ old('description', $product->description) }}</textarea>
                            </div>

                            <div class="text-right">
                                <a class="btn btn-secondary" href="{{ route('products.index') }}">Quay Lại</a>
                                <button type="submit" class="btn btn-primary">Cập Nhật</button>
                            </div>
                        </form>
                    </div>
                </div> 
            </div>
        </div>
    </div>
</section>
@endsection
