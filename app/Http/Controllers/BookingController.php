<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatusEnum;
use App\Http\Requests\BookingRequest;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function create()
    {
        $services = Service::all();

        $data = [
            'services' => $services,
        ];

        return view('pages.booking.create', $data);
    }

    public function store(BookingRequest $request)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $user = Auth::user();

            // get services
            $service = Service::findOrFail($data['service_id']);

            $data['total_price'] = $service->price * $data['total_session'];

            // check weekend
            if (in_array(date('N', strtotime($data['date'])), [6, 7])) {
                // add price 50000
                $data['total_price'] += 50000;
                $data['addon_price'] = 50000;
            }

            // create order
            $order = Order::create([
                'user_id' => $user->id,
                'service_id' => $data['service_id'],
                'status' => OrderStatusEnum::PENDING,
                'price' => $service->price,
                'qty' => $data['total_session'],
                'total' => $data['total_price'],
                'user_metadata' => [
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                ]
            ]);

            // create order item
            $order->orderItems()->create([
                'productable_type' => get_class($service),
                'productable_id' => $service->id,
                'metadata' => [
                    'booking_date' => $data['date'],
                    'product_name' => $service->name,
                    'product_price' => $service->price,
                ],
                'price' => $service->price,
                'addon_price' => @$data['addon_price'] ?? 0,
                'qty' => $data['total_session'],
                'total' => $data['total_price'],
            ]);

            DB::commit();

            sweetalert()->success('Berhasil memesan layanan, silahkan lanjutkan pembayaran');
            return redirect()->route('user.checkout.index');
        } catch (\Throwable $th) {
            DB::rollBack();
            
            if (app()->environment('local')) {
                throw $th;
            }

            sweetalert()->error('Terjadi kesalahan saat memesan layanan, silahkan coba lagi');
            return back();
        }
    }
}
