@extends('layouts.app')
@section('title', 'Chi Tiết Giao Dịch')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Chi Tiết Giao Dịch</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Trang Chủ</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('transactions.index') }}">Quản Lý Giao Dịch</a></li>
                        <li class="breadcrumb-item active">Chi Tiết Giao Dịch</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Thông tin phiếu mượn -->
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Thông Tin Phiếu Mượn</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Mã Giao Dịch:</th>
                                    <td>
                                        <span class="badge badge-info">{{ $transaction->id }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Người Mượn:</th>
                                    <td>
                                        <span class="badge badge-secondary">{{ $transaction->user->name }}
                                            ({{ $transaction->user->phone }})</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ngày Mượn:</th>
                                    <td>
                                        <span class="badge badge-dark">{{ $transaction->transaction_date }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Người Tạo Phiếu:</th>
                                    <td>
                                        @php
                                            $creator = DB::table('users')->where('id', $transaction->created_by)->first();
                                        @endphp
                                        <span class="badge badge-primary">
                                            {{ $creator->name ?? 'Không xác định' }}
                                        </span>
                                        <span class="badge badge-success">
                                            @if ($creator && $creator->role == 'admin')
                                                Quản Trị Viên
                                            @elseif ($creator && $creator->role == 'manager')
                                                Quản Lý
                                            @else
                                                Nhân Viên
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Trạng Thái:</th>
                                    <td>
                                        @if($transaction->transaction_type == 'loan')
                                            <span class="badge badge-warning">Đang Cho Mượn</span>
                                        @elseif($transaction->transaction_type == 'return')
                                            <span class="badge badge-success">Đã Thu Hồi</span>
                                        @else
                                            <span class="badge badge-primary">Nhập Kho</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ghi Chú:</th>
                                    <td>{{ $transaction->details ?? 'Không có' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Lịch sử mượn của sản phẩm -->

            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Danh Sách Sản Phẩm</h3>
                        </div>
                        <div class="card-body">
                            <form id="bulkActionForm" method="POST" action="{{ route('loans.bulkAction') }}">
                                <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">
                                @csrf
                                <!-- Nút Xác nhận Trả, Xóa Tất Cả, Thêm Sản Phẩm -->
                                <div class="mb-3 text-right">
                                    @if ($transaction->transaction_type == 'loan')
                                        <button type="button" class="btn btn-primary" data-toggle="modal"
                                            data-target="#addProductModal">
                                            <i class="fa-solid fa-plus"></i> Thêm Sản Phẩm
                                        </button>
                                        <button type="submit" id="bulkReturnBtn" class="btn btn-success" name="action"
                                        value="return" disabled>
                                            <i class="fa-solid fa-check"></i> Xác Nhận Thu Hồi
                                        </button>
                                        <button type="submit" id="bulkDeleteBtn" class="btn btn-danger" name="action" value="delete"
                                            disabled>
                                            <i class="fa-solid fa-trash"></i> Xóa Đã Chọn
                                        </button>
                                    @endif
                                </div>
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="selectAll"></th>
                                            <th>STT</th>
                                            <th>Mã Sản Phẩm</th>
                                            <th>Tên Sản Phẩm</th>
                                            <th>Số Lượng Mượn</th>
                                            <th>Ngày Trả</th>
                                            <th>Trạng Thái</th>
                                            <th>Ghi Chú</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($loans as $loan)
                                            <tr>
                                                <td><input type="checkbox" class="loan-checkbox" name="loan_ids[]"
                                                        value="{{ $loan->id }}"></td>
                                                <td>{{ $loop->iteration }}</td>
                                                <td><span class="badge badge-primary">{{ $loan->product->code }}</span></td>
                                                <td>{{ $loan->product->name }}</td>
                                                <td>{{ $loan->quantity }} sản phẩm</td>
                                                <td>{{ $loan->return_date }}</td>
                                                <td>
                                                    @php
                                                        $currentDate = now()->toDateString(); // Lấy ngày hiện tại (YYYY-MM-DD)

                                                        // Kiểm tra nếu quá hạn mà chưa trả thì cập nhật trạng thái
                                                        if ($loan->return_date && $loan->return_date < $currentDate && $loan->status != 'returned') {
                                                            $loan->status = 'overdue';
                                                            $loan->save(); // Cập nhật trạng thái trong DB
                                                        }
                                                    @endphp

                                                    @if ($loan->status == 'borrowed')
                                                        <span class="badge badge-warning">Đang Mượn</span>
                                                    @elseif ($loan->status == 'returned')
                                                        <span class="badge badge-success">Đã Trả</span>
                                                    @elseif ($loan->status == 'overdue')
                                                        <span class="badge badge-danger">Quá Hạn</span>
                                                    @endif
                                                </td>
                                                <td>{{ $loan->notes ?? 'Không có' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </form>
                        </div>

                    </div>
                </div>

                <!-- Lịch sử mượn của sản phẩm -->

            </div>
        </div>
    </section>
    <div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="POST" action="{{ route('loans.store') }}">
                @csrf
                <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addProductModalLabel">Thêm Sản Phẩm Mượn</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Chọn Sản Phẩm</label>
                            <select name="product_id" class="form-control" required>
                                <option value="">-- Chọn sản phẩm --</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->code }} | {{ $product->name }} | Mượn Tối Đa:
                                        {{ $product->available_quantity }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Số Lượng Mượn</label>
                            <input type="number" class="form-control" name="quantity" min="1" required>
                        </div>

                        <div class="form-group">
                            <label>Ngày Trả Dự Kiến</label>
                            <input type="date" class="form-control" name="return_date" required>
                        </div>

                        <div class="form-group">
                            <label>Ghi Chú (Tùy Chọn)</label>
                            <textarea class="form-control" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Thêm Sản Phẩm</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('script')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const checkboxes = document.querySelectorAll('.loan-checkbox');
            const bulkReturnBtn = document.getElementById('bulkReturnBtn');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            const selectAll = document.getElementById('selectAll');

            function updateButtons() {
                let hasChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
                bulkReturnBtn.disabled = !hasChecked;
                bulkDeleteBtn.disabled = !hasChecked;
            }

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateButtons);
            });

            selectAll.addEventListener('change', function () {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAll.checked;
                });
                updateButtons();
            });
        });
    </script>
@endsection