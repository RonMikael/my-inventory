<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
     {
         $perPage = $request->input('perPage', Config::get('pagination.default')); // Get per page from request or use default
         $search = $request->input('search');
         
         $customersQuery = Customer::query(); // Start building the query
     
         // Apply search filter if $search is provided
         if ($search) {
             $customersQuery->where(function ($query) use ($search) {
                 $query->where('name', 'like', '%' . $search . '%')
                       ->orWhere('email', 'like', '%' . $search . '%')
                       ->orWhere('phone', 'like', '%' . $search . '%')
                       ->orWhere('address', 'like', '%' . $search . '%');
             });
         }
     
         // Paginate the query results
         $customers = $customersQuery->paginate($perPage);
     
         return view('customer.index', ['customers' => $customers, 'search' => $search, 'perPage' => $perPage]);
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
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'nullable|email|unique:customers',
            'phone' => 'nullable',
            'address' => 'nullable',
        ]);

        // Create the customer
        $customer = Customer::create($validatedData);

        // Optionally, you can add a success message to the session
        return redirect()->route('customer.index')->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        {
            $customer = Customer::find($id);
            return response()->json($customer);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
        $customer = Customer::findOrFail($id);
        return response()->json($customer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Retrieve the customer by ID
        $customer = Customer::findOrFail($id);

        // Validate the request data
        $request->validate([
            'name' => 'required',
            'email' => 'nullable|email|unique:customers,email,' . $customer->id,
            'phone' => 'nullable',
            'address' => 'nullable',
        ]);

        // Update the customer details
        $customer->update($request->only(['name', 'email', 'phone', 'address']));

        // Redirect back with success message
        return redirect()->back()->with('success', 'Customer details updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return redirect()->route('customer.index')->with('success', 'Customer deleted successfully.');
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="user_template.csv"',
        ];

        $columns = [
            'Name',
            'Email',
            'Phone',
            'Address'
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
                    if ($row[0] == 'Name' && $row[1] == 'Email' && $row[2] == 'Phone' && $row[3] == 'Address') {
                        $headerSkipped = true;
                        continue; // Skip the header row
                    }
                }

                $name = isset($row[0]) ? trim($row[0]) : null;
                $email = isset($row[1]) ? trim($row[1]) : null;
                $phone = isset($row[2]) ? trim($row[2]) : null;
                $address = isset($row[3]) ? trim($row[3]) : null;

                // Check if email is provided and not empty
                if (!empty($email)) {
                    // Check if user with this email already exists
                    $existingCustomer = Customer::where('email', $email)->first();

                    if (!$existingCustomer) {
                        // Create new user
                        $customer = new Customer();
                        $customer->name = $name; // Nullable
                        $customer->email = $email; // Required and unique
                        $customer->phone = $phone; // Nullable
                        $customer->address = $address; // Nullable
                        $customer->save();
                        $imported++;
                    }
                }
            }

            fclose($file);

            return redirect()->route('customer.index')->with('success', 'Imported ' . $imported . ' customers successfully!');
        }

        return redirect()->back()->with('error', 'File not found or invalid.');
    }

    public function export(Request $request)
    {
        $search = $request->input('search');

        // Fetch customers based on search query
        $customerQuery = Customer::query();
        
        // Apply search filter if search term is provided
        if ($search) {
            $customerQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('address', 'like', '%' . $search . '%');
            });
        }

        // Fetch the customers based on the filtered query
        $customers = $customerQuery->get();

        // Define CSV headers
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="customers.csv"',
        ];

        // Prepare CSV data
        $callback = function() use ($customers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Name', 'Email', 'Phone', 'Address']); // CSV header

            foreach ($customers as $customer) {
                fputcsv($file, [$customer->name, $customer->email, $customer->phone, $customer->address]);
            }

            fclose($file);
        };

        // Return the CSV file as a downloadable response
        return Response::stream($callback, 200, $headers);
    }
}
