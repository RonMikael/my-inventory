<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Customer;

class EcommerceController extends Controller
{
    //
    public function index(Request $request)
    {
        $category_id = $request->input('category_id');
        $categories = Category::all();

        if ($category_id) {
            $products = Product::with(['category', 'images', 'stocks'])->where('category_id', $category_id)->get();
        } else {
            $products = Product::with(['category', 'images', 'stocks'])->get();
        }

        return view('Product.product', compact('products', 'categories', 'category_id'));
    }

    public function addToCart(Request $request, $productId)
    {
        $cart = session()->get('cart', []);

        $product = Product::with('images')->find($productId);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->price,
                "size" => $product->size,
                "brand" => $product->brand,
                "category" => $product->category->name,
                "images" => $product->images->pluck('image_path')->toArray()
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('product.index');
    }

    public function checkout()
    {
        $cart = session()->get('cart');
        if (!$cart) {
            return redirect()->route('products.index')->with('error', 'Your cart is empty.');
        }

        $user = auth()->user();
        $userCart = Cart::create(['user_id' => $user->id]);

        foreach ($cart as $productId => $details) {
            CartItem::create([
                'cart_id' => $userCart->id,
                'product_id' => $productId,
                'quantity' => $details['quantity'],
            ]);
        }

        session()->forget('cart');

        return redirect()->route('product.index')->with('success', 'Checkout successful!');
    }

    public function cart()
    {
        $cart = session()->get('cart', []); // Retrieve cart from session or set it to an empty array
        $customers = Customer::all(); // Retrieve all customers

        $total = 0;
        foreach ($cart as $id => $details) {
            $total += $details['price'] * $details['quantity'];
        }

        return view('cart.cart', compact('cart', 'customers', 'total'));
    }

    public function updateCart(Request $request, $id)
    {
        // Retrieve cart from session
        $cart = session()->get('cart');

        // Check if the cart and product exist
        if ($cart && isset($cart[$id])) {
            // Get the product from the database
            $product = Product::find($id);

            if (!$product) {
                // If product is not found, redirect back with an error
                return redirect()->route('cart.cart')->with('error', 'Product not found.');
            }

            // Get the total available quantity of the product
            $availableStock = $product->stocks->sum('quantity');

            if ($request->has('increment')) {
                // Check if adding to the cart exceeds available stock
                if ($cart[$id]['quantity'] < $availableStock) {
                    $cart[$id]['quantity']++;
                    session()->put('cart', $cart);
                } else {
                    // Redirect with error if stock is exceeded
                    return redirect()->route('cart.cart')->with('error', 'Cannot add more. Exceeds available stock.');
                }
            } elseif ($request->has('decrement')) {
                // Decrease the quantity
                $cart[$id]['quantity']--;
                if ($cart[$id]['quantity'] <= 0) {
                    unset($cart[$id]);
                }
                session()->put('cart', $cart);
            }

            // Redirect with success message
            return redirect()->route('cart.cart')->with('success', 'Cart updated successfully.');
        }

        // Redirect with error if product is not in cart
        return redirect()->route('cart.cart')->with('error', 'Product not in cart.');
    }

    public function selectCustomer(Request $request)
    {
        $customerId = $request->input('customer_id');
        // Add logic to save selected customer to the session or database
        session()->put('selected_customer', $customerId);
        return response()->json(['success' => true]);
    }

    public function selectPaymentMethod(Request $request)
    {
        $paymentMethod = $request->input('payment_method');
        // Save payment method to session or database
        session()->put('selected_payment_method', $paymentMethod);
        return response()->json(['success' => true]);
    }

    public function updatePrice(Request $request, $id)
    {
        // Retrieve cart from session
        $cart = session()->get('cart');

        // Check if the cart and product exist
        if ($cart && isset($cart[$id])) {
            // Update the price in the cart
            $cart[$id]['price'] = $request->input('price');
            session()->put('cart', $cart);

            // Redirect with success message
            return redirect()->route('cart.cart')->with('success', 'Price updated successfully.');
        }

        // Redirect with error if product is not in cart
        return redirect()->route('cart.cart')->with('error', 'Product not in cart.');
    }

}
