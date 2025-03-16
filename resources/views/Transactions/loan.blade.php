@extends('layouts.app')
@section('title', 'Tạo Phiếu Mượn')

@section('content')
<section class="content-header not_print">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Tạo Phiếu Mượn</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('transactions.index') }}">Quản Lý Giao Dịch</a></li>
                    <li class="breadcrumb-item active">Tạo Phiếu Mượn</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <!-- Form nhập liệu -->
            <div class="col-md-8 not_print">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Nhập Thông Tin Mượn</h3>
                    </div>
                    <div class="card-body">
                        <form id="loanForm" method="POST" action="{{ route('transactions.store') }}">
                            @csrf

                            <div class="form-group">
                                <label><strong>Chọn Người Mượn (<a href="{{ route('users.create') }}">
                                        <i class="fa fa-user-plus"></i> Thêm Người Dùng
                                    </a>)</strong></label>
                                <select name="user_id" id="user_id" class="form-control" required>
                                    <option value="">-- Chọn người mượn --</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" data-name="{{ $user->name }}" data-phone="{{ $user->phone }}">
                                            {{ $user->name }} - {{ $user->phone }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label><strong>Ngày Tạo Phiếu</strong></label>
                                <input type="text" class="form-control" value="{{ now()->format('d/m/Y H:i') }}" disabled>
                            </div>

                            <div class="form-group">
                                <label><strong>Người Tạo Phiếu</strong></label>
                                <input type="text" class="form-control" value="{{ auth()->user()->name }}" required disabled>
                            </div>

                            <div class="form-group">
                                <label><strong>Ghi Chú (Tùy Chọn)</strong></label>
                                <textarea class="form-control" name="details" id="notes" rows="3" placeholder="Ghi chú về việc mượn..."></textarea>
                            </div>

                            <div class="text-right">
                                <a class="btn btn-secondary" href="{{ route('transactions.index') }}">Quay Lại</a>
                                <button type="button" class="btn btn-primary" onclick="preparePrint()">Tạo Phiếu Mượn</button>
                            </div>
                        </form>
                    </div>
                </div> 
            </div>

            <!-- Phiếu mượn xem trước -->
            <div class="col-md-4">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title not_print">Xem Trước Phiếu Mượn</h3>
                    </div>
                    <div class="card-body" style="height: 470px;" id="loan_preview">
                        <h4 class="text-center"><strong>PHIẾU MƯỢN SẢN PHẨM</strong></h4>
                        <hr>
                        <p><strong>Ngày tạo phiếu:</strong> {{ now()->format('d/m/Y H:i') }}</p>
                        <p><strong>Người tạo phiếu:</strong> {{ auth()->user()->name }}</p>
                        <p><strong>Người mượn:</strong> <span id="preview_user">Chưa chọn</span></p>
                        <p><strong>Số điện thoại:</strong> <span id="preview_phone">Chưa chọn</span></p>
                        <p><strong>Ghi chú:</strong> <span id="preview_notes">Không có</span></p>
                        <hr>
                        <div class="row mt-4 d-flex justify-content-between">
                            <div class="col-6 text-left">
                                <p class="ml-1"><strong> Người Tạo Phiếu</strong></p>
                                <p>(Ký & ghi rõ họ tên)</p>
                                <br><br>
                                <p class="font-weight-bold ml-2">{{ auth()->user()->name }}</p>
                            </div>
                            <div class="col-6 text-right">
                                <p class="mr-3"><strong>Người Mượn</strong></p>
                                <p>(Ký & ghi rõ họ tên)</p>
                                <br><br>
                                <p class="font-weight-bold mr-2" id="chukynguoimuon"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<style>
    @media print {
        .not_print {
            display: none;
        }

        footer{
            display: none;
        }
    }
</style>

<script>
    document.querySelector('#user_id').addEventListener('change', function() {
        let selected = this.options[this.selectedIndex];
        document.getElementById('preview_user').innerText = selected.dataset.name || 'Chưa chọn';
        document.getElementById('preview_phone').innerText = selected.dataset.phone || 'Chưa chọn';
        document.getElementById('chukynguoimuon').innerText = selected.dataset.name || '';
    });

    document.querySelector('#notes').addEventListener('input', function() {
        document.getElementById('preview_notes').innerText = this.value || 'Không có';
    });
</script>

<script>
    function preparePrint() {
        window.onafterprint = function() {
            document.getElementById("loanForm").submit(); // Khi in xong thì submit form
        };
        window.print();
    }
</script>

@endsection
