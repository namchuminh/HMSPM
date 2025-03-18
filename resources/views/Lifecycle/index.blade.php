@extends('layouts.app')
@section('title', 'Vòng Đời Sản Phẩm')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Vòng Đời Sản Phẩm</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Trang Chủ</a></li>
                    <li class="breadcrumb-item active">Vòng Đời Sản Phẩm</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <!-- Bảng dữ liệu vòng đời sản phẩm -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <form method="GET" action="{{ route('lifecycle.index') }}" class="form-inline">
                            <input type="text" name="search" class="form-control col-md-2 mr-2"
                                placeholder="Nhập mã hoặc tên sản phẩm" value="{{ request('search') }}">

                            <select name="status_filter" class="form-control col-md-2 mr-2">
                                <option value="">-- Chọn trạng thái thay đổi --</option>
                                <option value="new-damaged" {{ request('status_filter') == 'new-damaged' ? 'selected' : '' }}>Mới → Hư hỏng</option>
                                <option value="new-expired" {{ request('status_filter') == 'new-expired' ? 'selected' : '' }}>Mới → Hết hạn</option>
                                <option value="damaged-new" {{ request('status_filter') == 'damaged-new' ? 'selected' : '' }}>Hư hỏng → Mới</option>
                                <option value="damaged-expired" {{ request('status_filter') == 'damaged-expired' ? 'selected' : '' }}>Hư hỏng → Hết hạn</option>
                                <option value="expired-new" {{ request('status_filter') == 'expired-new' ? 'selected' : '' }}>Hết hạn → Mới</option>
                                <option value="expired-damaged" {{ request('status_filter') == 'expired-damaged' ? 'selected' : '' }}>Hết hạn → Hư hỏng</option>
                            </select>

                            <button type="submit" class="btn btn-primary">Lọc / Tìm Kiếm</button>
                            <a href="{{ route('lifecycle.index') }}" class="btn btn-secondary ml-2">Xóa Bộ Lọc</a>
                        </form>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Mã Sản Phẩm</th>
                                    <th>Trạng Thái Cũ</th>
                                    <th>Trạng Thái Mới</th>
                                    <th>Số Lượng Thay Đổi</th>
                                    <th>Người Thực Hiện</th>
                                    <th>Thời Gian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lifecycles as $lifecycle)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><span class="badge badge-primary">{{ $lifecycle->product->code }}</span></td>
                                        <td>
                                            <span class="badge 
                                                {{ $lifecycle->previous_status == 'new' ? 'badge-warning' : 
                                                   ($lifecycle->previous_status == 'damaged' ? 'badge-danger' : 
                                                   ($lifecycle->previous_status == 'expired' ? 'badge-dark' : 'badge-secondary')) }}">
                                                {{ $lifecycle->previous_status == 'new' ? 'Mới' : 
                                                   ($lifecycle->previous_status == 'damaged' ? 'Hư hỏng' : 
                                                   ($lifecycle->previous_status == 'expired' ? 'Hết hạn' : 'Đã sử dụng')) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge 
                                                {{ $lifecycle->new_status == 'new' ? 'badge-warning' : 
                                                   ($lifecycle->new_status == 'damaged' ? 'badge-danger' : 
                                                   ($lifecycle->new_status == 'expired' ? 'badge-dark' : 'badge-secondary')) }}">
                                                {{ $lifecycle->new_status == 'new' ? 'Mới' : 
                                                   ($lifecycle->new_status == 'damaged' ? 'Hư hỏng' : 
                                                   ($lifecycle->new_status == 'expired' ? 'Hết hạn' : 'Đã sử dụng')) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-primary"> 
                                                {{ $lifecycle->quantity ?? 'Không rõ' }} sản phẩm
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-info"> 
                                                {{ $lifecycle->user->name }}
                                            </span>
                                            <span class="badge badge-danger"> 
                                                {{ $lifecycle->user->role }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-dark"> 
                                                {{ $lifecycle->created_at }}
                                            </span>
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
                                        href="{{ route('lifecycle.index', ['page' => $i]) }}">
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
