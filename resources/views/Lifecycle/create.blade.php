@extends('layouts.app')
@section('title', 'Thêm Vòng Đời Sản Phẩm')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Thêm Vòng Đời Sản Phẩm</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('lifecycle.index') }}">Vòng Đời Sản Phẩm</a></li>
                    <li class="breadcrumb-item active">Thêm Vòng Đời Sản Phẩm</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <!-- Form nhập liệu -->
            <div class="col-md-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Nhập Thông Tin Thay Đổi</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('lifecycle.store') }}">
                            @csrf

                            <div class="form-group">
                                <label><strong>Chọn Sản Phẩm</strong></label>
                                <select name="product_id" id="product_id" class="form-control" required>
                                    <option value="">-- Chọn sản phẩm --</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}"
                                            data-code="{{ $product->code }}"
                                            data-name="{{ $product->name }}"
                                            data-status="{{ $product->status }}"
                                            data-quantity="{{ $product->quantity }}"
                                            data-expired="{{ $product->expired_quantity }}"
                                            data-damaged="{{ $product->damaged_quantity }}"
                                            data-borrowed="{{ $product->borrowed_quantity }}">
                                            {{ $product->code }} - {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Số Lượng Thực Hiện</strong></label>
                                        <input type="number" class="form-control" id="quantity" name="quantity" placeholder="Số lượng">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Người Thực Hiện</strong></label>
                                        <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
                                        <input type="hidden" name="changed_by" value="{{ auth()->id() }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Trạng Thái Cũ</strong></label>
                                        <select name="old_status" id="old_status" class="form-control" required>
                                            <option value="">-- Chọn trạng thái --</option>
                                            <option value="new">Mới</option>
                                            <option value="damaged">Hư hỏng</option>
                                            <option value="expired">Hết hạn</option>
                                        </select>
                                        @error('old_status') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Trạng Thái Mới</strong></label>
                                        <select name="new_status" id="new_status" class="form-control" required>
                                            <option value="">-- Chọn trạng thái mới --</option>
                                            <option value="new">Mới</option>
                                            <option value="damaged">Hư hỏng</option>
                                            <option value="expired">Hết hạn</option>
                                        </select>
                                        @error('new_status') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <a class="btn btn-secondary" href="{{ route('lifecycle.index') }}">Quay Lại</a>
                                <button type="submit" class="btn btn-primary">Xác Nhận Thay Đổi</button>
                            </div>
                        </form>
                    </div>
                </div> 
            </div>

            <!-- Thông tin sản phẩm -->
            <div class="col-md-4">
                <div class="card card-secondary" style="height: 382px;">
                    <div class="card-header">
                        <h3 class="card-title">Thông Tin Sản Phẩm</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Mã Sản Phẩm:</strong> <span id="info_code">Chưa chọn</span></p>
                        <p><strong>Tên Sản Phẩm:</strong> <span id="info_name">Chưa chọn</span></p>
                        <p><strong>Tổng Số Lượng:</strong> <span id="info_quantity">Chưa chọn</span></p>
                        <p><strong>Số Lượng Hết Hạn:</strong> <span id="info_expired">Chưa chọn</span></p>
                        <p><strong>Số Lượng Hư Hỏng:</strong> <span id="info_damaged">Chưa chọn</span></p>
                        <hr>
                        <p><strong>Hành động: </strong> </p>
                        <p><span id="action">Chưa chọn</span></p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const productSelect = document.getElementById("product_id");

        function updateProductInfo() {
            let selected = productSelect.options[productSelect.selectedIndex];

            document.getElementById("info_code").innerText = selected.dataset.code || "Chưa chọn";
            document.getElementById("info_name").innerText = selected.dataset.name || "Chưa chọn";
            document.getElementById("info_quantity").innerText = selected.dataset.quantity + " sản phẩm" || "Chưa chọn";
            document.getElementById("info_expired").innerText = selected.dataset.expired + " sản phẩm" || "Chưa chọn";
            document.getElementById("info_damaged").innerText = selected.dataset.damaged + " sản phẩm" || "Chưa chọn";
        }

        productSelect.addEventListener("change", updateProductInfo);
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const oldStatusSelect = document.getElementById("old_status");
        const newStatusSelect = document.getElementById("new_status");
        const quantityInput = document.getElementById("quantity");
        const actionText = document.getElementById("action");

        const statusLabels = {
            new: "Mới",
            used: "Đã sử dụng",
            damaged: "Hư hỏng",
            expired: "Hết hạn"
        };

        function updateAction() {
            const oldStatus = oldStatusSelect.value;
            const newStatus = newStatusSelect.value;
            const quantity = quantityInput.value || 0;

            if (oldStatus && newStatus && oldStatus !== newStatus && quantity > 0) {
                actionText.innerText = `Thay đổi ${quantity} sản phẩm "${statusLabels[oldStatus]}" ➝ "${statusLabels[newStatus]}"`;
            } else {
                actionText.innerText = "Chưa chọn";
            }
        }

        oldStatusSelect.addEventListener("change", updateAction);
        newStatusSelect.addEventListener("change", updateAction);
        quantityInput.addEventListener("input", updateAction);
    });
</script>
@endsection
