@extends('layouts.app')
@section('title', 'Phiếu Nhập Kho')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Phiếu Nhập Kho</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Quản Lý Nhập Kho</a></li>
                    <li class="breadcrumb-item active">Tạo Phiếu Nhập Kho</li>
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
                        <h3 class="card-title">Nhập thông tin nhập kho</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('inventory.store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <!-- Cột trái -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Mã Sản Phẩm</strong></label>
                                        <select name="product_id" class="form-control" required>
                                            <option value="">-- Chọn MSP --</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->code }}</option>
                                            @endforeach
                                        </select>
                                        @error('product_id') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="form-group">
                                        <label><strong>Trạng Thái</strong></label>
                                        <select name="status" class="form-control" required>
                                            <option value="new">Sản Phẩm Mới</option>
                                            <option value="used">Đã qua sử dụng</option>
                                            <option value="damaged">Hư Hỏng</option>
                                            <option value="expired">Hết Hạn</option>
                                        </select>
                                        @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="form-group">
                                        <label><strong>Ngày Hết Hạn (nếu có)</strong></label>
                                        <input type="date" class="form-control" name="expiration_date">
                                        @error('expiration_date') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <!-- Cột phải -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Ngày Nhập Kho</strong></label>
                                        <input type="text" class="form-control" value="{{ now()->format('d/m/Y H:i') }}" disabled>
                                    </div>

                                    <div class="form-group">
                                        <label><strong>Số Lượng Nhập</strong></label>
                                        <input type="number" class="form-control" value="0" name="quantity" required>
                                        @error('quantity') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="form-group">
                                        <label><strong>Phiếu Nhập (PDF, JPG, PNG)</strong></label>
                                        <input type="file" class="form-control" name="import_receipt">
                                        @error('import_receipt') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="text-right mt-3">
                                <a class="btn btn-secondary" href="{{ route('inventory.index') }}">Quay Lại</a>
                                <button type="submit" class="btn btn-primary">Lưu Phiếu Nhập</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Phiếu nhập kho dạng hiển thị -->
                <div class="card mt-3">
                    <div class="card-header bg-secondary">
                        <h3 class="card-title">Xem Trước Phiếu Nhập Kho</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Mã Sản Phẩm:</strong> <span id="preview_product"></span></p>
                        <p><strong>Trạng Thái:</strong> <span id="preview_status"></span></p>
                        <p><strong>Ngày Hết Hạn:</strong> <span id="preview_expiration"></span></p>
                        <p><strong>Ngày Nhập Kho:</strong> <span>{{ now()->format('d/m/Y H:i') }}</span></p>
                        <p><strong>Số Lượng Nhập:</strong> <span id="preview_quantity">0 sản phẩm</span></p>
                        <p><strong>Phiếu Nhập:</strong> <span id="preview_receipt">Chưa có</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // Cập nhật xem trước thông tin phiếu nhập
    document.querySelector('select[name="product_id"]').addEventListener('change', function() {
        let selectedOption = this.options[this.selectedIndex].text;
        document.getElementById('preview_product').innerText = selectedOption;
    });

    document.querySelector('select[name="status"]').addEventListener('change', function() {
        if (this.value == 'new') {
            document.getElementById('preview_status').innerText = 'Mới';
        } else if(this.value == 'used') {
            document.getElementById('preview_status').innerText = 'Đã sử dụng';
        } else if(this.value == 'damaged') {
            document.getElementById('preview_status').innerText = 'Hư hỏng';
        } else {
            document.getElementById('preview_status').innerText = 'Hết hạn';
        }
    });

    document.querySelector('input[name="expiration_date"]').addEventListener('input', function() {
        document.getElementById('preview_expiration').innerText = this.value || 'Không có';
    });

    document.querySelector('input[name="import_receipt"]').addEventListener('change', function(event) {
        let fileName = event.target.files.length ? event.target.files[0].name : 'Chưa có';
        document.getElementById('preview_receipt').innerText = fileName;
    });

    document.querySelector('input[name="quantity"]').addEventListener('change', function(event) {
        document.getElementById('preview_quantity').innerText = this.value + ' sản phẩm' || '0 sản phẩm';
    });
</script>

@endsection
