<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Loan;
use App\Models\User;
use App\Models\Transaction;

class TransactionController extends Controller
{
    /**
     * Danh sách giao dịch
     */
    public function index(Request $request)
    {
        $query = Transaction::with(['user', 'creator'])
                    ->orderBy('transaction_date', 'desc');

        // Tìm kiếm theo người mượn, người tạo phiếu, loại giao dịch
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhereHas('creator', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhere('transaction_type', 'like', "%{$search}%");
        }

        // Lọc theo loại giao dịch
        if ($request->has('transaction_type') && !empty($request->transaction_type)) {
            $query->where('transaction_type', $request->transaction_type);
        }

        // Lọc theo ngày tạo phiếu
        if ($request->has('transaction_date') && !empty($request->transaction_date)) {
            $query->whereDate('transaction_date', $request->transaction_date);
        }

        // Phân trang
        $transactions = $query->paginate(10);
        $totalPages = $transactions->lastPage();

        return view('transactions.index', compact('transactions', 'totalPages'));
    }

    /**
        * Mượn sản phẩm
     */
    public function create()
    {
        //Lấy toàn bộ user
        $users = User::all();
        return view('transactions.loan', compact('users'));
    }

    /**
     * Lưu thông tin mượn sản phẩm
     */
    public function store(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'details' => 'nullable|string',
        ], [
            'user_id.required' => 'Vui lòng chọn người mượn.',
            'user_id.exists' => 'Người dùng không tồn tại.'
        ]);


        // Lưu thông tin giao dịch
        $transaction = Transaction::create([
            'user_id' => $request->user_id,
            'created_by' => auth()->id(),
            'transaction_type' => "loan",
            'details' => $request->details ?? 'Không có',
        ]);

        return redirect()->route('transactions.show', $transaction->id)
                     ->with('success', 'Tạo phiếu mượn thành công.');
    }


    /**
     * Thông tin chi tiết giao dịch
     */
    public function show($id)
    {
        $transaction = Transaction::findOrFail($id);
        $loans = Loan::with('product')->where('transaction_id', $id)->get();
        $products = Product::all();
        foreach ($products as $product) {
            $product->available_quantity = max(0, $product->quantity - ($product->expired_quantity + $product->damaged_quantity + $product->borrowed_quantity));
        }
        return view('transactions.show', compact('transaction', 'loans', 'products'));
    }
}
