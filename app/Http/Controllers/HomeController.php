<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();

        $bookings = Booking::with(['service'])->where('user_id', $user->id)->get();

        foreach ($bookings as $booking) {
            $events[] = [
                'title' => 'Rental '. $booking->service->name .' '. $booking->total_session .' sesi',
                'start' => $booking->date,
            ];
        }

        $data = [
            'bookings' => $bookings,
            'events' => $events,
        ];

        return view('pages.home', $data);
    }
}
