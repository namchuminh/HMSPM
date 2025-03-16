@extends('layouts.app')
@section('title', 'Quản Lý Giao Dịch')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Danh Sách Giao Dịch</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Trang Chủ</a></li>
                    <li class="breadcrumb-item active">Giao Dịch</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <form method="GET" action="{{ route('transactions.index') }}" class="form-inline">
                    <input type="text" name="search" class="form-control mr-2" placeholder="Tìm kiếm người mượn, người tạo" value="{{ request('search') }}">
                    <select name="transaction_type" class="form-control mr-2">
                        <option value="">-- Loại Giao Dịch --</option>
                        <option value="import" {{ request('transaction_type') == 'import' ? 'selected' : '' }}>Nhập Kho</option>
                        <option value="loan" {{ request('transaction_type') == 'loan' ? 'selected' : '' }}>Mượn Sản Phẩm</option>
                        <option value="return" {{ request('transaction_type') == 'return' ? 'selected' : '' }}>Trả Sản Phẩm</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Lọc / Tìm Kiếm</button>
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary ml-2">Xóa Bộ Lọc</a>
                </form>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Người Mượn</th>
                            <th>Người Tạo Phiếu</th>
                            <th>Trạng Thái</th>
                            <th>Ngày Tạo Phiếu</th>
                            <th>Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $index => $transaction)
                            <tr>
                                <td>{{ $transactions->firstItem() + $index }}</td>
                                <td>
                                    <span class="badge badge-primary">
                                        {{ $transaction->user->name ?? 'Không xác định' }} 
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $transaction->creator->name ?? 'Không xác định' }} 
                                    </span>
                                    <span class="badge badge-danger">
                                        {{ $transaction->creator->role == "admin" ? 'Admin' : "Quản Lý" }} 
                                    </span>
                                </td>
                                <td>
                                    @if ($transaction->transaction_type == 'import')
                                        <span class="badge badge-success">Nhập Kho</span>
                                    @elseif ($transaction->transaction_type == 'loan')
                                        <span class="badge badge-warning">Đang Mượn</span>
                                    @else
                                        <span class="badge badge-danger">Đã Thu Hồi</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-dark">
                                        {{ $transaction->transaction_date }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('transactions.show', $transaction->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fa fa-eye"></i> Xem Chi Tiết
                                    </a>
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
                                href="{{ route('transactions.index', ['page' => $i]) }}">
                                {{ $i }}
                            </a>
                        </li>
                    @endfor
                </ul>
            </div>
        </div>
    </div>
</section>
@endsection
