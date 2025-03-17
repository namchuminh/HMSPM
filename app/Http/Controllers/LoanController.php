<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\Loan;
use App\Models\Transaction;

class LoanController extends Controller
{

    /**
     * Lưu thông tin mượn sản phẩm
     */
    public function store(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'return_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string',
        ], [
            'transaction_id.required' => 'Giao dịch không hợp lệ.',
            'product_id.required' => 'Vui lòng chọn sản phẩm.',
            'quantity.required' => 'Số lượng không được để trống.',
            'quantity.min' => 'Số lượng phải lớn hơn 0.',
            'return_date.required' => 'Vui lòng chọn ngày trả.',
            'return_date.after_or_equal' => 'Ngày trả không hợp lệ.',
        ]);
    
        // Lấy thông tin sản phẩm
        $product = Product::findOrFail($request->product_id);
    
        // Tính số lượng sản phẩm có thể cho mượn
        $availableQuantity = $product->quantity - ($product->expired_quantity + $product->damaged_quantity + $product->borrowed_quantity);
    
        // Kiểm tra sản phẩm còn đủ số lượng để mượn không
        if ($availableQuantity < $request->quantity) {
            return redirect()->back()->with('error', 'Số lượng sản phẩm hợp lệ không đủ để mượn.');
        }
        
        $quanlityLoan = $request->quantity;

        // Cập nhật số lượng sản phẩm
        $product->increment('borrowed_quantity', $request->quantity);
        $product->save();
        
    
        // Tạo khoản mượn mới
        $loan = Loan::create([
            'transaction_id' => $request->transaction_id,
            'product_id' => $request->product_id,
            'loan_date' => now(),
            'quantity' => $quanlityLoan,
            'return_date' => $request->return_date,
            'status' => 'borrowed',
            'notes' => $request->notes,
        ]);

        return redirect()->route('transactions.show', $request->transaction_id)
                        ->with('success', 'Sản phẩm đã được thêm vào phiếu mượn.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'loan_ids' => 'required|array',
            'loan_ids.*' => 'exists:loans,id',
            'action' => 'required|in:return,delete'
        ], [
            'loan_ids.required' => 'Vui lòng chọn ít nhất một mục để thực hiện hành động.',
            'loan_ids.*.exists' => 'Một số mục không hợp lệ.',
            'action.required' => 'Hành động không hợp lệ.',
            'action.in' => 'Hành động không hợp lệ.'
        ]);

        $loans = Loan::whereIn('id', $request->loan_ids)->get();

        foreach ($loans as $loan) {
            $product = Product::find($loan->product_id);
            if (!$product) continue;

            if ($request->action === 'return') {
                // Cập nhật trạng thái mượn
                $loan->update(['status' => 'returned']);

                // Giảm số lượng đang mượn của sản phẩm
                $product->decrement('borrowed_quantity', $loan->quantity);
                $product->save();

                // Kiểm tra xem tất cả các khoản mượn của giao dịch đã được trả chưa
                $transaction = Transaction::findOrFail($request->transaction_id);
                $remainingLoans = Loan::where('transaction_id', $transaction->id)
                                        ->where('status', 'borrowed')
                                        ->count();

                if ($remainingLoans === 0) {
                    $transaction->update(['transaction_type' => 'return']);
                }
            } elseif ($request->action === 'delete') {
                if ($loan->status === 'borrowed') {
                    $product->decrement('borrowed_quantity', $loan->quantity);
                    $product->save();
                    // Xóa khoản mượn
                    $loan->delete();
                }
            }
        }

        return redirect()->route('transactions.show', $request->transaction_id)
                        ->with('success', 'Thao tác đã được thực hiện thành công.');
    }

}
