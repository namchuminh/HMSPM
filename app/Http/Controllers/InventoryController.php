<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    /**
     * Hiển thị danh sách nhập kho.
     */
    public function index(Request $request)
    {
        $query = Inventory::with('product', 'user');

        // Tìm kiếm theo sản phẩm
        if ($request->has('search') && $request->search != '') {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%');
            });
        }

        // Lọc theo ngày nhập kho
        if ($request->has('imported_date') && $request->imported_date != '') {
            $query->whereDate('imported_date', $request->imported_date);
        }

        // Lọc theo trạng thái
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Phân trang dữ liệu
        $inventories = $query->orderBy('imported_date', 'desc')->paginate(10);

        // Đảm bảo giữ lại giá trị lọc khi chuyển trang
        $inventories->appends($request->query());

        // Tổng số trang
        $totalPages = $inventories->lastPage();

        return view('inventory.index', compact('inventories', 'totalPages'));
    }


    /**
     * Hiển thị form nhập kho.
     */
    public function create()
    {
        $products = Product::all();
        return view('inventory.create', compact('products'));
    }

    /**
     * Lưu giao dịch nhập kho mới.
     */
    public function store(Request $request)
    {
        // Validate dữ liệu nhập kho
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'status' => 'required|in:new,used,damaged,expired',
            'expiration_date' => 'nullable|date',
            'import_receipt' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:10240',
            'quantity' => 'required|integer|min:1'
        ], [
            'product_id.required' => 'Vui lòng chọn sản phẩm.',
            'status.required' => 'Trạng thái sản phẩm không được để trống.',
            'expiration_date.date' => 'Ngày hết hạn không hợp lệ.',
            'import_receipt.mimes' => 'Chỉ chấp nhận file PDF, JPG, PNG.',
            'import_receipt.max' => 'Dung lượng file tối đa 10MB.',
            'quantity.required' => 'Số lượng không được để trống.',
            'quantity.min' => 'Số lượng phải lớn hơn 0.'
        ]);

        // Xử lý upload file phiếu nhập kho (nếu có)
        $receiptPath = null;
        if ($request->hasFile('import_receipt')) {
            $receiptPath = $request->file('import_receipt')->store('receipts', 'public');
        }

        // Lấy sản phẩm cần cập nhật
        $product = Product::findOrFail($request->product_id);

        // Tạo bản ghi nhập kho
        $inventory = Inventory::create([
            'product_id' => $request->product_id,
            'status' => $request->status,
            'expiration_date' => $request->expiration_date,
            'imported_date' => now(),
            'import_receipt' => $receiptPath,
            'quantity' => $request->quantity,
            'user_id' => Auth::id()
        ]);

        if($request->status == 'expired') {
            $product->expired_quantity += $request->quantity;
        } elseif($request->status == 'damaged') {
            $product->damaged_quantity += $request->quantity;
        } 

        $product->quantity += $request->quantity;

        $product->save();

        return redirect()->route('inventory.index')->with('success', 'Phiếu nhập kho đã được lưu và số lượng sản phẩm đã được cập nhật.');
    }

    /**
     * Hiển thị chi tiết một giao dịch nhập kho.
     */
    public function show($id)
    {
        $inventory = Inventory::with('product', 'user')->findOrFail($id);
        return view('inventory.show', compact('inventory'));
    }

    /**
     * Hiển thị form chỉnh sửa giao dịch nhập kho.
     */
    public function edit($id)
    {
        $inventory = Inventory::findOrFail($id);
        $products = Product::all();
        return view('inventory.edit', compact('inventory', 'products'));
    }

    /**
     * Cập nhật thông tin giao dịch nhập kho.
     */
    public function update(Request $request, $id)
    {
        // $inventory = Inventory::findOrFail($id);

        // $request->validate([
        //     'product_id' => 'required|exists:products,id',
        //     'status' => 'required|in:new,used,damaged,expired',
        //     'expiration_date' => 'nullable|date',
        //     'import_receipt' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:10240',
        //     'quantity' => 'required|integer|min:1'
        // ], [
        //     'product_id.required' => 'Vui lòng chọn sản phẩm.',
        //     'product_id.exists' => 'Sản phẩm không hợp lệ.',
        //     'status.required' => 'Trạng thái sản phẩm không được để trống.',
        //     'status.in' => 'Trạng thái sản phẩm không hợp lệ.',
        //     'expiration_date.date' => 'Ngày hết hạn phải là định dạng ngày hợp lệ.',
        //     'import_receipt.mimes' => 'Chỉ chấp nhận file PDF, JPG, PNG.',
        //     'import_receipt.max' => 'Dung lượng file không được vượt quá 10MB.',
        //     'quantity.required' => 'Số lượng không được để trống.',
        //     'quantity.min' => 'Số lượng phải lớn hơn 0.'
        // ]);

        // // Xử lý cập nhật file phiếu nhập kho (nếu có)
        // if ($request->hasFile('import_receipt')) {
        //     $receiptPath = $request->file('import_receipt')->store('receipts', 'public');
        //     $inventory->import_receipt = $receiptPath;
        // }

        // $product = Product::findOrFail($inventory->product_id);

        // if($request->status == 'expired') {
        //     $product->expired_quantity -= $inventory->quantity;
        //     $product->expired_quantity += $request->quantity;
        //     $product->save();
        // } elseif($request->status == 'damaged') {
        //     $product->damaged_quantity -= $inventory->quantity;
        //     $product->damaged_quantity += $request->quantity;   
        //     $product->save();
        // } 

        // $product->quantity -= $inventory->quantity;
        // $product->save();

        // $product->quantity += $request->quantity;
        // $product->save();

        // $inventory->update([
        //     'product_id' => $request->product_id,
        //     'status' => $request->status,
        //     'expiration_date' => $request->expiration_date,
        //     'quantity' => $request->quantity
        // ]);

        // return redirect()->route('inventory.edit', $id)->with('success', 'Phiếu nhập kho đã được cập nhật.');
    }

    /**
     * Xóa giao dịch nhập kho.
     */
    public function destroy($id)
    {
        $inventory = Inventory::findOrFail($id);
        $inventory->delete();

        return redirect()->route('inventory.index')->with('success', 'Giao dịch nhập kho đã được xóa.');
    }
}
