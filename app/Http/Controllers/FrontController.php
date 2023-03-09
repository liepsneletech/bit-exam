<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Review;
use App\Models\Meal;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            if (auth()->user()->role == User::ROLES['admin']) {
                return redirect()->route('admin-dashboard');
            }
        }

        return redirect()->route('home');
    }

    public function home()
    {
        return view('pages.front.home');
    }

    public function restaurants(Restaurant $restaurant)
    {
        $restaurants = Restaurant::all();
        return view('pages.front.restaurants', compact('restaurants', 'restaurant'));
    }

    public function meals(Request $request, Meal $meal, Restaurant $restaurant)
    {
        if (!$request->s) {
            if ($request->provider_id && $request->provider_id != 'Pasirinkite teikėją') {
                $meals = Meal::where('restaurant_id', $request->restaurant_id)->get();
            } else {
                $meals = Meal::where('restaurant_id', '>', '0')->get();
            }

            $meals = match ($request->sort ?? '') {
                'asc_price' => $meals->sortBy('price'),
                'dsc_price' => $meals->sortByDesc('price'),
                default => $meals,
            };
        } else {
            $meals = Meal::search($request->s)->get();
        }

        $sortSelect = Meal::SORT;
        $sortShow = isset(Meal::SORT[$request->sort]) ? $request->sort : '';

        $restaurants = Restaurant::all();
        $restaurantshow = $request->restaurant_id ? $request->restaurant_id : '';

        $searchTerm = $request->s;

        return view('pages.front.meals', compact('meal', 'meals', 'restaurant', 'restaurants', 'restaurantshow', 'sortSelect', 'sortShow', 'searchTerm'));
    }

    public function search($searchTerm)
    {
        $meals = Meal::search($searchTerm)->get();
        return $meals;
    }

    public function singleMeal(Meal $meal, Restaurant $restaurant)
    {
        $restaurants = Restaurant::all();
        $meals = Meal::all();

        return view('pages.front.single-meal', compact('meal', 'meals', 'restaurant', 'restaurants'));
    }

    public function checkout(Meal $meal, Restaurant $restaurant)
    {
        $meals = Meal::all();
        $restaurants = Restaurant::all();

        return view('pages.front.checkout', compact('meal', 'meals', 'restaurant', 'restaurants'));
    }

    public function makeOrder(Request $request, Order $order, Meal $meal)
    {
        $incomingFields = $request->validate([
            'name' => ['required'],
            'surname' => ['required'],
            'email' => ['required'],
        ], [
            'name.required' => 'Vardo laukelis yra privalomas',
            'surname.required' => 'Pavardės laukelis yra privalomas',
            'email.required' => 'El. pašto laukelis yra privalomas',
        ]);

        $order->name = $request->name;
        $order->surname = $request->surname;
        $order->email = $request->email;
        $incomingFields['user_id'] = Auth::user()->id;
        $incomingFields['meal_id'] = $meal->id;

        Order::create($incomingFields);

        return redirect()->route('order-success');
    }

    public function orderSuccess()
    {
        return view('pages.front.order-success');
    }

    public function userOrders()
    {
        $user_id = Auth::user()->id;

        $orders = Order::where('user_id', '=', $user_id)->get();

        return view('pages.front.orders', compact('orders', 'user_id'));
    }

    public function makeReview(Request $request, Meal $meal, Review $review)
    {
        $incomingFields = $request->validate([
            'reviewer' => ['required'],
            'rate' => ['required'],
        ], [
            'reviewer.required' => 'Vardo laukelis yra privalomas',
            'rate.required' => 'Įvertinimo laukelis yra privalomas',
        ]);

        $review->reviewer = $request->reviewer;
        $review->rate = $request->rate;
        $review->review_text = $request->review_text;
        $incomingFields['meal_id'] = $meal->id;

        Review::create($incomingFields);

        return redirect()->route('make-review');
    }
}
