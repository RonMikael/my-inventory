<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Category;
use App\Models\Stock;
use App\Models\ProductImage;
use App\Models\StockRecord;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', Config::get('pagination.default')); // Get per page from request or use default
        $search = $request->input('search');
        
        $productsQuery = Product::query()->with('category', 'stocks', 'images'); // Start building the query

        // Apply search filter if $search is provided
        if ($search) {
            $productsQuery->where(function ($query) use ($search) {
                $query->where('size', 'like', '%' . $search . '%')
                    ->orWhere('reference_number', 'like', '%' . $search . '%')
                    ->orWhere('price', 'like', '%' . $search . '%')
                    ->orWhere('brand', 'like', '%' . $search . '%')
                    ->orWhereHas('category', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('stocks', function ($query) use ($search) {
                        $query->where('stock_room', 'like', '%' . $search . '%')
                                ->orWhere('location', 'like', '%' . $search . '%');
                    });
            });
        }

        // Paginate the query results
        $products = $productsQuery->paginate($perPage);

        // Calculate the total stock quantity for each product
        foreach ($products as $product) {
            $product->total_stock_quantity = $product->stocks->sum('quantity');
        }

        $categories = Category::all();

        return view('product.index', compact('products', 'categories', 'search', 'perPage'));
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
                // Skip saving if quantity, stock_room, and location are all null
                if (is_null($stockData['quantity']) && is_null($stockData['stock_room']) && is_null($stockData['location'])) {
                    continue;
                }
        
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
        
        $product->stock_records()->delete();
        $product->stocks()->delete(); // Delete associated stocks
        $product->delete(); // Delete the product itself
        
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    public function deleteStock(Request $request, $id)
    {
        $stock = Stock::find($id);
    
        if (!$stock) {
            return response()->json(['success' => false, 'message' => 'Stock not found.'], 404);
        }
    
        $stockRecords = StockRecord::where('stock_id', $id)->get();
    
        $stock->delete();
        foreach ($stockRecords as $stockRecord) {
            $stockRecord->delete();
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Product Stock deleted successfully.'
        ]);
    }  
    
    public function export(Request $request)
    {
        $search = $request->input('search');

        // Start building the query to fetch products
        $productsQuery = Product::query()->with('category', 'stocks');

        // Apply search filter if search term is provided
        if ($search) {
            $productsQuery->where(function ($query) use ($search) {
                $query->where('size', 'like', '%' . $search . '%')
                    ->orWhere('reference_number', 'like', '%' . $search . '%')
                    ->orWhere('price', 'like', '%' . $search . '%')
                    ->orWhere('brand', 'like', '%' . $search . '%')
                    ->orWhereHas('category', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('stocks', function ($query) use ($search) {
                        $query->where('stock_room', 'like', '%' . $search . '%')
                                ->orWhere('location', 'like', '%' . $search . '%');
                    });
            });
        }

        // Fetch the products based on the filtered query
        $products = $productsQuery->get();

        // Define CSV headers
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="products.csv"',
        ];

        // Prepare CSV data
        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Size', 'Reference Number', 'Price', 'Brand', 'Category', 'Total Stock Quantity']); // CSV header

            foreach ($products as $product) {
                // Calculate total stock quantity for each product
                $totalStockQuantity = $product->stocks->sum('quantity');

                fputcsv($file, [
                    $product->id,
                    $product->size,
                    $product->reference_number,
                    $product->price,
                    $product->brand,
                    $product->category->name, // Assuming category relationship is defined
                    $totalStockQuantity,
                ]);
            }

            fclose($file);
        };

        // Return the CSV file as a downloadable response
        return Response::stream($callback, 200, $headers);
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="product_template.csv"',
        ];

        $columns = [
            'Size',
            'Reference Number',
            'Price',
            'Brand',
        ];

        // Callback to generate the CSV content
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        if ($request->hasFile('file')) {
            $path = $request->file('file')->getRealPath();
            $file = fopen($path, 'r');

            $imported = 0;
            $headerSkipped = false; // Flag to track if header row has been skipped

            while (($row = fgetcsv($file)) !== false) {
                if (!$headerSkipped) {
                    // Skip the header row
                    if ($row[0] == 'Size' && $row[1] == 'Reference Number' && $row[2] == 'Price' && $row[3] == 'Brand') {
                        $headerSkipped = true;
                        continue; // Skip the header row
                    }
                }

                $size = isset($row[0]) ? trim($row[0]) : null;
                $referenceNumber = isset($row[1]) ? trim($row[1]) : null;
                $price = isset($row[2]) ? trim($row[2]) : null;
                $brand = isset($row[3]) ? trim($row[3]) : null;

                // Validate required fields (you can add more validation as needed)
                if (!empty($size) && !empty($brand)) {
                    // Check if product with this reference number already exists
                    $existingProduct = Product::where('reference_number', $referenceNumber)->first();

                    if (!$existingProduct) {
                        // Create new product
                        $product = new Product();
                        $product->size = $size;
                        $product->reference_number = $referenceNumber;
                        $product->price = $price;
                        $product->brand = $brand;
                        // Assuming category_id needs to be set based on your business logic
                        // You may need to adjust this part based on how you handle categories in import
                        $product->category_id = 1; // Example: set category_id to 1 as default

                        $product->save();
                        $imported++;
                    }
                }
            }

            fclose($file);

            return redirect()->route('products.index')->with('success', 'Imported ' . $imported . ' products successfully!');
        }

        return redirect()->back()->with('error', 'File not found or invalid.');
    }
}
