<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;

class EcommerceController extends Controller
{
    //
    public function index(Request $request)
    {
        $category_id = $request->input('category_id');
        $categories = Category::all();

        if ($category_id) {
            $products = Product::with('category', 'images')->where('category_id', $category_id)->get();
        } else {
            $products = Product::with('category', 'images')->get();
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
                "category" => $product->category->name,
                "images" => $product->images->pluck('image_path')->toArray()
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('product.index');
    }

    public function cart()
    {
        $cart = Cart::with('items.product')->where('user_id', auth()->id())->first();
        return view('cart.cart', compact('cart'));
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

    public function updateCart(Request $request, $id)
    {
        $cart = session()->get('cart');

        if ($cart && isset($cart[$id])) {
            if ($request->has('increment')) {
                $cart[$id]['quantity']++;
            } elseif ($request->has('decrement')) {
                $cart[$id]['quantity']--;
                if ($cart[$id]['quantity'] <= 0) {
                    unset($cart[$id]);
                }
            }

            session()->put('cart', $cart);
        }

        return redirect()->route('cart.cart');
    }
}
