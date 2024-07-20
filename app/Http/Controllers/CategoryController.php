<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categories = Category::with('products')
            ->when($search, function($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->paginate(10);
        
        return view('category.index', ['categories' => $categories, 'search' => $search]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore(null, 'id')
            ],
        ], [
            'name.unique' => 'The category name is already taken. Please choose a different name.',
        ]);

        // Create a new category
        $category = new Category;
        $category->name = Str::upper($request->name); // Ensure name is uppercase
        $category->save();

        // Redirect back with success message
        return redirect()->back()->with('success', 'Category added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $categories = Category::find($id);
        return response()->json($categories);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $categories = Category::findOrFail($id);
        return response()->json($categories);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate the request data
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($id, 'id')
            ],
        ], [
            'name.unique' => 'The category name is already taken. Please choose a different name.',
        ]);

        // Find the category to update
        $category = Category::findOrFail($id);

        // Update the user details
        $category->update(['name' => Str::upper($request->name)]); // Update and ensure uppercase

        // Redirect back with success message
        return redirect()->back()->with('success', 'Category details updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
    
        // Check if the category is being used by a product
        if ($category->isUsed()) {
            return redirect()->route('category.index')->with('error', 'Category cannot be deleted because it is being used by a product.');
        }
    
        $category->delete();
        return redirect()->route('category.index')->with('success', 'Category deleted successfully.');
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="category_template.csv"',
        ];

        $columns = ['Name'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt',
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->getRealPath();
            $file = fopen($path, 'r');

            $imported = 0;
            while (($row = fgetcsv($file)) !== false) {
                // Skip header row
                if ($row[0] == 'Name') {
                    continue;
                }

                $name = trim($row[0]); // Trim whitespace from name
                $uppercaseName = Str::upper($name); // Convert name to uppercase

                // Check if category with this name already exists
                $existingCategory = Category::where('name', $uppercaseName)->first();

                if (!$existingCategory) {
                    Category::create(['name' => $uppercaseName]);
                    $imported++;
                }
            }

            fclose($file);

            return redirect()->route('category.index')->with('success', 'Imported '.$imported.' categories successfully!');
        }

        return redirect()->back()->with('error', 'File not found or invalid.');
    }

    public function export(Request $request)
    {
        $search = $request->input('search');

        // Fetch categories based on search query
        $categories = Category::when($search, function($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%');
        })->get();

        // Define CSV headers
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="categories.csv"',
        ];

        // Prepare CSV data
        $callback = function() use ($categories) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name']); // CSV header

            foreach ($categories as $category) {
                fputcsv($file, [$category->id, $category->name]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
