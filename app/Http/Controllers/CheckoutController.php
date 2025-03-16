<?php

namespace App\Http\Controllers;

use App\Helpers\GeneratingHelper;
use App\Models\FeeMethod;
use App\Models\Order;
use App\Services\Midtrans;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index()
    {
        $order = Order::with(['orderItems'])->isPending()->where('user_id', Auth::user()->id)->latest()->first();
        $feeMethods = FeeMethod::isPublished()->get();

        $data = [
            'order' => $order,
            'feeMethods' => $feeMethods,
        ];
        
        return view('pages.checkout.index', $data);
    }

    public function store(Request $request, $code)
    {
        $request->validate([
            'payment_type' => 'required',
        ]);

        $order = Order::with(['orderItems'])->whereCode($code)->first();

        $midtrans = new Midtrans;
        $response = $midtrans->request($request, $order);

        debug($response);
        
        if (in_array($response['status'], [200, 201])) {
            return redirect(route('user.payment.index', $response['order']->code));
        } else {
            $response['order']->update([
                'code' => GeneratingHelper::setOrderCode(),
            ]);

            sweetalert()->error('warning', $response['message'] . ' - Silakan mencoba metode pembayaran yang lain');
            return back();
        }
    }

    public function feeCheck(Request $request)
    {
        $serviceFee = FeeMethod::whereCode($request->val)->isPublished()->first();

        $totalPayment = $request->currentTotal;

        if ($serviceFee) {
            $additionalPrice = $serviceFee->price;

            if (@$serviceFee->percent) {
                $additionalPrice = $totalPayment * $serviceFee->percent / 100;
            }

            $status = 200;
            $total = $totalPayment + $additionalPrice;
            $price = $additionalPrice;
            $name = $serviceFee->name;

            return [
                'id' => $serviceFee->id,
                'status' => $status,
                'price' => $price,
                'total' => $total,
                'name' => $name
            ];
        }

        return [
            'status' => 404,
            'price' => 0
        ];
    }
}
