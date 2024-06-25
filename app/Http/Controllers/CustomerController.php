<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Response;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $customers = Customer::all();
        return view('Customer.index', ['customers' => $customers]);
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
}
