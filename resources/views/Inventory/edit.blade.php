@extends('layouts.app')
@section('title', 'Chỉnh Sửa Phiếu Nhập Kho')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Chỉnh Sửa Phiếu Nhập Kho</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Quản Lý Nhập Kho</a></li>
                    <li class="breadcrumb-item active">Chỉnh Sửa Phiếu Nhập Kho</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8 not_print">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Thông Tin Phiếu Nhập Kho</h3>
                    </div>
                    <div class="card-body">
                        <form enctype="multipart/form-data" method="POST">
                            <div class="row">
                                <!-- Cột trái -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Mã Sản Phẩm</strong></label>
                                        <select name="product_id" class="form-control" disabled required>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}" {{ $product->id == $inventory->product_id ? 'selected' : '' }}>
                                                    {{ $product->code }} - {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('product_id') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="form-group">
                                        <label><strong>Trạng Thái</strong></label>
                                        <select name="status" class="form-control" disabled required>
                                            <option value="new" {{ $inventory->status == 'new' ? 'selected' : '' }}>Sản Phẩm Mới</option>
                                            <option value="used" {{ $inventory->status == 'used' ? 'selected' : '' }}>Đã qua sử dụng</option>
                                            <option value="damaged" {{ $inventory->status == 'damaged' ? 'selected' : '' }}>Hư Hỏng</option>
                                            <option value="expired" {{ $inventory->status == 'expired' ? 'selected' : '' }}>Hết Hạn</option>
                                        </select>
                                        @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="form-group">
                                        <label><strong>Ngày Hết Hạn (nếu có)</strong></label>
                                        <input type="date" class="form-control" name="expiration_date" value="{{ $inventory->expiration_date }}" disabled>
                                        @error('expiration_date') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <!-- Cột phải -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Ngày Nhập Kho</strong></label>
                                        <input type="text" class="form-control" value="{{ $inventory->imported_date }}" disabled>
                                    </div>

                                    <div class="form-group">
                                        <label><strong>Số Lượng Nhập</strong></label>
                                        <input type="number" class="form-control" name="quantity" value="{{ $inventory->quantity }}" required disabled>
                                        @error('quantity') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="form-group">
                                        <label><strong>Phiếu Nhập (PDF, JPG, PNG)</strong></label>
                                        <input type="file" class="form-control" name="import_receipt" disabled>
                                        @if ($inventory->import_receipt)
                                            <p>File hiện tại: <a href="{{ asset('storage/'.$inventory->import_receipt) }}" target="_blank">Xem file</a></p>
                                        @endif
                                        @error('import_receipt') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="text-right mt-3">
                                <a class="btn btn-secondary not_print" href="{{ route('inventory.index') }}">Quay Lại</a>
                                <button type="button" class="btn btn-primary not_print" onclick="window.print()" >In Phiếu Nhập</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title not_print">Xem Trước Phiếu Nhập</h3>
        </div>
        <div class="card-body" style="height: 520px;" id="loan_preview">
            <h4 class="text-center"><strong>PHIẾU NHẬP SẢN PHẨM</strong></h4>
            <hr>
            <p><strong>Mã Sản Phẩm:</strong> <span id="preview_product">{{ $inventory->product->code }} - {{ $inventory->product->name }}</span></p>
            <p><strong>Ngày Nhập Kho:</strong> <span>{{ $inventory->imported_date }}</span></p>
            <div class="row">
                <!-- Cột 1 -->
                <div class="col-6">
                    <p><strong>Trạng Thái:</strong> <span id="preview_status">
                        @if ($inventory->status == 'new')
                            Mới
                        @elseif ($inventory->status == 'used') 
                            Đã qua sử dụng  
                        @elseif ($inventory->status == 'damaged')
                            Hư hỏng
                        @else
                            Hết hạn
                        @endif
                    </span></p>
                    <p><strong>Ngày Hết Hạn:</strong> <span id="preview_expiration">{{ $inventory->expiration_date ?? 'Không có' }}</span></p>
                    
                </div>
                <!-- Cột 2 -->
                <div class="col-6">
                    <p><strong>Số Lượng Nhập:</strong> <span id="preview_quantity">{{ $inventory->quantity }} sản phẩm</span></p>
                    <p><strong>Người Nhập:</strong> <span id="preview_importer">{{ $inventory->user->name }}</span></p>
                </div>
                <div class="col-12">
                <p><strong>Phiếu Nhập:</strong> <span id="preview_receipt">{{ $inventory->import_receipt ? $inventory->import_receipt : 'Chưa có' }}</span></p>

                </div>
            </div>

            <hr>
            <div class="row mt-4 d-flex justify-content-between">
                <div class="col-6 text-left">
                    <p class="ml-3"><strong>Người Nhập</strong></p>
                    <p>(Ký & ghi rõ họ tên)</p>
                    <br><br>
                    <p class="font-weight-bold ml-3">{{ auth()->user()->name }}</p>
                </div>
                <div class="col-6 text-right">
                    <p class="mr-3"><strong>Bên Cung Cấp</strong></p>
                    <p>(Ký & ghi rõ họ tên)</p>
                    <br><br>
                    <p class="font-weight-bold" id="chukynguoimuon"></p>
                </div>
            </div>
        </div>
    </div>
</div>


        </div>
    </div>
</section>

<style>
    .form-control:disabled, .form-control[readonly] {
        background-color: white;
        opacity: 1;
    }

    @media print {
        .not_print {
            display: none;
        }

        .content-header, footer, header, nav{
            display: none;
        }

        .card-header{
            border-bottom: unset;
        }
    }

</style>

<script>
    document.querySelector('select[name="product_id"]').addEventListener('change', function() {
        let selectedOption = this.options[this.selectedIndex].text;
        document.getElementById('preview_product').innerText = selectedOption;
    });

    document.querySelector('input[name="expiration_date"]').addEventListener('input', function() {
        document.getElementById('preview_expiration').innerText = this.value || 'Không có';
    });

    document.querySelector('input[name="import_receipt"]').addEventListener('change', function(event) {
        let fileName = event.target.files.length ? event.target.files[0].name : 'Chưa có';
        document.getElementById('preview_receipt').innerText = fileName;
    });

    document.querySelector('input[name="quantity"]').addEventListener('input', function(event) {
        document.getElementById('preview_quantity').innerText = this.value + ' sản phẩm';
    });
</script>

@endsection
