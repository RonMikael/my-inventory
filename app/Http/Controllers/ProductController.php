<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Category;
use App\Models\Stock;
use App\Models\ProductImage;
use App\Models\StockRecord;

use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('category', 'stocks', 'images')->get();
        $categories = Category::all();

        // Calculate the total stock quantity for each product
        foreach ($products as $product) {
            $product->total_stock_quantity = $product->stocks->sum('quantity');
        }

        return view('Product.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $categories = Category::all();
        return view('Product.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'size' => 'required',
            'reference_number' => 'nullable',
            'price' => 'nullable|numeric',
            'brand' => 'required',
            'category_id' => 'required|exists:categories,id',
            'stocks' => 'nullable|array',
            'stocks.*.stock_room' => 'nullable|string',
            'stocks.*.location' => 'nullable|string',
            'stocks.*.quantity' => 'nullable|integer',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $product = Product::create([
            'size' => $validatedData['size'],
            'reference_number' => $validatedData['reference_number'],
            'price' => $validatedData['price'],
            'brand' => $validatedData['brand'],
            'category_id' => $validatedData['category_id']
        ]);

        if (!empty($validatedData['stocks'])) {
            foreach ($validatedData['stocks'] as $stock) {
                // Create stock entry only if necessary fields are provided
                if (isset($stock['stock_room']) && isset($stock['location']) && isset($stock['quantity'])) {
                    // Create stock record
                    $newStock = $product->stocks()->create($stock);

                    // Log stock addition in stock_records table
                    StockRecord::create([
                        'product_id' => $product->id,
                        'stock_id' => $newStock->id,
                        'quantity_change' => $stock['quantity'],
                        'action' => '+',
                        'stock_room' => $stock['stock_room'],
                        'location' => $stock['location']
                    ]);
                }
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('product_images', 'public');
                $product->images()->create(['image_path' => $path]);
            }
        }

        return redirect()->route('products.index')->with('success', 'Product added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
        $categories = Category::all();
        $product->load('stocks', 'images');
        return response()->json(['product' => $product, 'categories' => $categories]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'size' => 'required',
            'reference_number' => 'nullable',
            'price' => 'nullable|numeric',
            'brand' => 'required',
            'category_id' => 'required|exists:categories,id',
            'stocks' => 'nullable|array',
            'stocks.*.id' => 'nullable|integer|exists:stocks,id',
            'stocks.*.stock_room' => 'nullable|string',
            'stocks.*.location' => 'nullable|string',
            'stocks.*.quantity' => 'nullable|integer',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'deleted_stocks' => 'nullable|array',
            'deleted_stocks.*' => 'nullable|integer|exists:stocks,id',
            'deleted_images' => 'nullable|array',
            'deleted_images.*' => 'nullable|integer|exists:product_images,id',
        ]);
    
        // Update the product details
        $product->update([
            'size' => $validatedData['size'],
            'reference_number' => $validatedData['reference_number'],
            'price' => $validatedData['price'],
            'brand' => $validatedData['brand'],
            'category_id' => $validatedData['category_id']
        ]);
    
        // Update existing stocks and log changes
        if (isset($validatedData['stocks'])) {
            foreach ($validatedData['stocks'] as $stockData) {
                if (isset($stockData['id'])) {
                    // Update existing stock
                    $stock = Stock::findOrFail($stockData['id']);
                    $previousQuantity = $stock->quantity;
    
                    // Update stock details
                    $stock->update([
                        'stock_room' => $stockData['stock_room'],
                        'location' => $stockData['location'],
                        'quantity' => $stockData['quantity'],
                    ]);
    
                    // Determine action for stock record
                    if ($stockData['quantity'] > $previousQuantity) {
                        // Quantity increased
                        $quantityChange = $stockData['quantity'] - $previousQuantity;
                        $action = '+';
                    } elseif ($stockData['quantity'] < $previousQuantity) {
                        // Quantity decreased
                        $quantityChange = $previousQuantity - $stockData['quantity'];
                        $action = '-';
                    } else {
                        // Quantity unchanged
                        continue; // Skip logging if quantity hasn't changed
                    }
    
                    // Log stock change in stock_records table
                    StockRecord::create([
                        'product_id' => $product->id,
                        'stock_id' => $stock->id,
                        'quantity_change' => $quantityChange,
                        'action' => $action,
                        'stock_room' => $stockData['stock_room'],
                        'location' => $stockData['location']
                    ]);
                } else {
                    // Handle addition of new stocks
                    $newStock = $product->stocks()->create($stockData);
    
                    // Log stock addition in stock_records table
                    StockRecord::create([
                        'product_id' => $product->id,
                        'stock_id' => $newStock->id,
                        'quantity_change' => $stockData['quantity'],
                        'action' => '+',
                        'stock_room' => $stockData['stock_room'],
                        'location' => $stockData['location']
                    ]);
                }
            }
        }
    
        // Handle deleted stocks
        if (isset($validatedData['deleted_stocks'])) {
            Stock::destroy($validatedData['deleted_stocks']);
        }
    
        // Handle deleted images
        if (isset($validatedData['deleted_images'])) {
            foreach ($validatedData['deleted_images'] as $deletedImageId) {
                $deletedImage = ProductImage::find($deletedImageId);
                if ($deletedImage) {
                    Storage::disk('public')->delete($deletedImage->image_path);
                    $deletedImage->delete();
                }
            }
        }
    
        // Handle uploaded images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('product_images', 'public');
                $product->images()->create(['image_path' => $path]);
            }
        }
    
        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        foreach ($product->images as $image) {
            // Check if the image exists in both storage locations and delete accordingly
            if (Storage::disk('public')->exists('product_images/' . $image->image_path)) {
                Storage::disk('public')->delete('product_images/' . $image->image_path);
            }
            if (Storage::disk('local')->exists('product_images/' . $image->image_path)) {
                Storage::disk('local')->delete('product_images/' . $image->image_path);
            }

            $image->delete();
        }
        
        $product->stocks()->delete(); // Delete associated stocks
        $product->delete(); // Delete the product itself
        
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
