<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function index(Request $request)
     {
         $perPage = $request->input('perPage', Config::get('pagination.default')); // Get per page from request or use default
         $search = $request->input('search');
         
         $usersQuery = User::query(); // Start building the query
     
         // Apply search filter if $search is provided
         if ($search) {
             $usersQuery->where(function ($query) use ($search) {
                 $query->where('name', 'like', '%' . $search . '%')
                       ->orWhere('role', 'like', '%' . $search . '%')
                       ->orWhere('email', 'like', '%' . $search . '%');
             });
         }
     
         // Paginate the query results
         $users = $usersQuery->paginate($perPage);
     
         return view('user.index', ['users' => $users, 'search' => $search, 'perPage' => $perPage]);
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
        //
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);
    
        // Create the user
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);
    
        // Optionally, you can add a success message to the session
        return redirect()->route('user.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        {
            $user = User::find($id);
            return response()->json($user);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Fetch the user by ID
        $user = User::findOrFail($id);

        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
        ]);

        // Update the user details
        $user->update($request->only('name', 'email'));

        // Redirect back with success message
        return redirect()->back()->with('success', 'User details updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('user.index')->with('success', 'User deleted successfully.');
    }

    public function updaterole(Request $request, $id)
    {
        // Fetch the user by ID
        $user = User::findOrFail($id);

        // Validate the request data
        $request->validate([
            'role' => 'required|string|max:255',
        ]);

        // Update the user details
        $user->update($request->only('role'));

        // Redirect back with success message
        return redirect()->back()->with('success', 'User details updated successfully!');
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
                    if ($row[0] == 'Name' && $row[1] == 'Email') {
                        $headerSkipped = true;
                        continue; // Skip the header row
                    }
                }

                $name = isset($row[0]) ? trim($row[0]) : null;
                $email = isset($row[1]) ? trim($row[1]) : null;

                // Check if email is provided and not empty
                if (!empty($email)) {
                    // Check if user with this email already exists
                    $existingUser = User::where('email', $email)->first();

                    if (!$existingUser) {
                        // Create new user
                        $user = new User();
                        $user->name = $name; // Nullable
                        $user->email = $email; // Required and unique
                        $user->role = 'User'; // Nullable
                        $user->password = Hash::make('password'); // Set default hashed password
                        $user->save();
                        $imported++;
                    }
                }
            }

            fclose($file);

            return redirect()->route('user.index')->with('success', 'Imported ' . $imported . ' users successfully!');
        }

        return redirect()->back()->with('error', 'File not found or invalid.');
    }

    public function export(Request $request)
    {
        $search = $request->input('search');

        // Fetch users based on search query
        $usersQuery = User::query();
        
        // Apply search filter if search term is provided
        if ($search) {
            $usersQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('role', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Fetch the users based on the filtered query
        $users = $usersQuery->get();

        // Define CSV headers
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users.csv"',
        ];

        // Prepare CSV data
        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Name', 'Role', 'Email']); // CSV header

            foreach ($users as $user) {
                fputcsv($file, [$user->name, $user->role, $user->email]);
            }

            fclose($file);
        };

        // Return the CSV file as a downloadable response
        return Response::stream($callback, 200, $headers);
    }
}
