<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Hiển thị danh sách sản phẩm.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Tìm kiếm theo từ khóa (mã hoặc tên sản phẩm)
        if ($request->has('search') && $request->search !== '') {
            $query->where(function ($q) use ($request) {
                $q->where('code', 'LIKE', '%' . $request->search . '%')
                ->orWhere('name', 'LIKE', '%' . $request->search . '%');
            });
        }

        // Lọc theo trạng thái sản phẩm
        if ($request->has('status') && $request->status != '') {
            if ($request->status == 'expired') {
                $query->where('expired_quantity', '>=', 1);
            } elseif ($request->status == 'damaged') {
                $query->where('damaged_quantity', '>=', 1);
            }
        }

        // Phân trang kết quả
        $products = $query->orderBy('created_at', 'desc')->paginate(10);

        $totalPages = $products->lastPage();

        return view('products.index', compact('products', 'totalPages'));
    }

    /**
     * Hiển thị form thêm sản phẩm.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Lưu sản phẩm mới.
     */
    public function store(Request $request)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'code' => 'required|unique:products,code|max:50',
            'name' => 'required|max:255',
            'description' => 'nullable|string'
        ], [
            'code.required' => 'Mã sản phẩm không được để trống.',
            'code.unique' => 'Mã sản phẩm đã tồn tại.',
            'name.required' => 'Tên sản phẩm không được để trống.',
        ]);

        // Lưu sản phẩm vào database
        Product::create($request->all());

        return redirect()->route('products.index')->with('success', 'Sản phẩm đã được thêm.');
    }

    /**
     * Hiển thị form chỉnh sửa sản phẩm.
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('products.edit', compact('product'));
    }

    /**
     * Cập nhật sản phẩm.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'code' => 'required|max:50|unique:products,code,' . $id,
            'name' => 'required|max:255',
            'description' => 'nullable|string'
        ], [
            'code.required' => 'Mã sản phẩm không được để trống.',
            'code.unique' => 'Mã sản phẩm đã tồn tại.',
            'code.max' => 'Mã sản phẩm không được vượt quá 50 ký tự.',
            'name.required' => 'Tên sản phẩm không được để trống.',
            'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự.',
            'description.string' => 'Mô tả sản phẩm phải là chuỗi ký tự.'
        ]);

        $product->update($request->all());

        return redirect()->route('products.edit', $id)->with('success', 'Sản phẩm đã được cập nhật.');
    }

    /**
     * Xóa sản phẩm.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Sản phẩm đã bị xóa.');
    }
}
