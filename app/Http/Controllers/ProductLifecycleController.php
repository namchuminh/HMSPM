<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductLifecycle;
use App\Models\Product;


class ProductLifecycleController extends Controller
{
    /**
     * Danh sách vòng đời sản phẩm
     */
    public function index(Request $request)
    {
        $query = ProductLifecycle::with(['product', 'user']) // Lấy dữ liệu kèm quan hệ sản phẩm & người thực hiện
                    ->orderBy('id', 'desc');

        // Tìm kiếm theo mã sản phẩm hoặc tên sản phẩm
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Lọc theo trạng thái cũ -> trạng thái mới
        if ($request->has('status_filter') && !empty($request->status_filter)) {
            $allowedFilters = [
                'new-used', 'new-damaged', 'new-expired',
                'damaged-new', 'damaged-expired',
                'expired-new', 'expired-damaged'
            ];
            if (in_array($request->status_filter, $allowedFilters)) {
                [$oldStatus, $newStatus] = explode('-', $request->status_filter);
                $query->where('previous_status', $oldStatus)
                    ->where('new_status', $newStatus);
            }
        }

        $lifecycles = $query->paginate(10);
        $totalPages = $lifecycles->lastPage();

        return view('lifecycle.index', compact('lifecycles', 'totalPages'));
    }


    /**
     * Tạo vòng đời sản phẩm
     */
    public function create()
    {
        $products = Product::all();
        return view('lifecycle.create', compact('products'));
    }

    /**
     * Lưu vòng đời sản phẩm
     */
    public function store(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'old_status' => 'required|in:new,used,damaged,expired',
            'new_status' => 'required|in:new,used,damaged,expired',
        ], [
            'product_id.required' => 'Vui lòng chọn sản phẩm.',
            'product_id.exists' => 'Sản phẩm không tồn tại.',
            'quantity.required' => 'Vui lòng nhập số lượng.',
            'quantity.integer' => 'Số lượng phải là số nguyên.',
            'quantity.min' => 'Số lượng phải lớn hơn 0.',
            'old_status.required' => 'Vui lòng chọn trạng thái cũ.',
            'new_status.required' => 'Vui lòng chọn trạng thái mới.',
            'old_status.in' => 'Trạng thái cũ không hợp lệ.',
            'new_status.in' => 'Trạng thái mới không hợp lệ.',
        ]);

        // Lấy thông tin sản phẩm
        $product = Product::findOrFail($request->product_id);

        // Kiểm tra số lượng tồn kho hợp lệ
        if ($product->quantity < $request->quantity) {
            return redirect()->back()->with('error', 'Số lượng sản phẩm không đủ để thay đổi trạng thái.');
        }

        // Cập nhật số lượng trong bảng products
        if ($request->old_status == 'new' && $request->new_status == 'damaged') {
            // Kiểm tra số lượng tồn kho hợp lệ
            if (($product->quantity - ($product->expired_quantity + $product->damaged_quantity)) < $request->quantity) {
                return redirect()->back()->with('error', 'Số lượng sản phẩm không đủ để thay đổi trạng thái.');
            }

            $product->increment('damaged_quantity', $request->quantity);
        } 
        
        if ($request->old_status == 'new' && $request->new_status == 'expired') {
            // Kiểm tra số lượng tồn kho hợp lệ
            if (($product->quantity - ($product->expired_quantity + $product->damaged_quantity)) < $request->quantity) {
                return redirect()->back()->with('error', 'Số lượng sản phẩm không đủ để thay đổi trạng thái.');
            }

            $product->increment('expired_quantity', $request->quantity);
        } 

        if ($request->old_status == 'damaged' && $request->new_status == 'new') {
            // Kiểm tra số lượng tồn kho hợp lệ
            if ($product->damaged_quantity < $request->quantity) {
                return redirect()->back()->with('error', 'Số lượng sản phẩm Hư hỏng không đủ để thay đổi trạng thái.');
            }

            $product->decrement('damaged_quantity', $request->quantity);
        } 

        if ($request->old_status == 'damaged' && $request->new_status == 'expired') {
            // Kiểm tra số lượng tồn kho hợp lệ
            if ($product->damaged_quantity < $request->quantity) {
                return redirect()->back()->with('error', 'Số lượng sản phẩm Hư hỏng không đủ để thay đổi trạng thái.');
            }

            $product->decrement('damaged_quantity', $request->quantity);
            $product->increment('expired_quantity', $request->quantity);
        } 

        if ($request->old_status == 'expired' && $request->new_status == 'new') {
            // Kiểm tra số lượng tồn kho hợp lệ
            if ($product->expired_quantity < $request->quantity) {
                return redirect()->back()->with('error', 'Số lượng sản phẩm Hết hạn không đủ để thay đổi trạng thái.');
            }

            $product->decrement('expired_quantity', $request->quantity);
        } 

        if ($request->old_status == 'expired' && $request->new_status == 'damaged') {
            // Kiểm tra số lượng tồn kho hợp lệ
            if ($product->expired_quantity < $request->quantity) {
                return redirect()->back()->with('error', 'Số lượng sản phẩm Hết hạn không đủ để thay đổi trạng thái.');
            }

            $product->decrement('expired_quantity', $request->quantity);
            $product->increment('damaged_quantity', $request->quantity);
        } 

        // Lưu lịch sử thay đổi trạng thái
        ProductLifecycle::create([
            'product_id' => $request->product_id,
            'previous_status' => $request->old_status,
            'new_status' => $request->new_status,
            'quantity' => $request->quantity,
            'changed_by' => auth()->id(),
        ]);

        return redirect()->route('lifecycle.index', [
            'search' => $product->code
        ])->with('success', 'Thay đổi trạng thái sản phẩm thành công.');
    }
}
