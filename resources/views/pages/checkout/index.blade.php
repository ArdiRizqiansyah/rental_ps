@extends('layouts.app')

@section('content')
    <div class="container">
        <form action="{{ route('user.checkout.store', $order->code) }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card mb-3">
                        <h5 class="card-header fw-bold">
                            <i class="fas fa-shopping-cart text-primary me-1 fs-3"></i> Pembelian
                        </h5>
                        <div class="card-body">
                            <div class="row align-items-center">
                                @foreach ($order->orderItems as $item)
                                    <div class="col mb-3">
                                        <h5>{{ $item->metadata->product_name }} ({{ $item->qty }})</h5>
                                        <p class="mb-0">{{ rupiahFormat($item->metadata->product_price) }}</p>
                                    </div>
                            </div>
                            @if (@$item->addon_price)
                                <div class="col">
                                    <h5>Biaya Tambahan Weekend</h5>
                                    <p class="mb-0">{{ rupiahFormat($item->addon_price) }}</p>
                                </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="card">
                        <h5 class="card-header fw-bold">
                            <i class="fas fa-money-check text-primary me-1 fs-3"></i> Pembelian
                        </h5>
                        <div class="card-body">
                            @foreach ($feeMethods as $feeMethod)
                                <div class="row align-items-center mb-3">
                                    <div class="col-9 col-md-10">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_type"
                                                id="payment-{{ $feeMethod->code }}" value="{{ $feeMethod->code }}"
                                                onchange="fee_check(this.value)">
                                            <label class="form-check-label" for="payment-{{ $feeMethod->code }}">
                                                {{ $feeMethod->name }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-3 col-md-2">
                                        <img src="{{ asset('assets/img/payment/' . $feeMethod->code . '.svg') }}"
                                            class="img-fluid" alt="">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <h5 class="card-header fw-bold">
                            <i class="fas fa-shopping-bag text-primary me-1 fs-3"></i> Detail Pembayaran
                        </h5>
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <p>
                                    Total harga
                                </p>
                                <p>
                                    {{ rupiahFormat($order->total) }}
                                </p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p>
                                    Jumlah
                                </p>
                                <p>
                                    {{ $order->orderItems->count() }} item
                                </p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="biaya-name">
                                    Biaya Jasa Pelayanan
                                </p>
                                <p class="biaya-price">
                                    -
                                </p>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <p class="fw-bold">
                                    Total Pembayaran
                                </p>
                                <p class="fw-bold total-payment">
                                    {{ rupiahFormat($order->total) }}
                                </p>
                            </div>
                            <div class="d-grid">
                                <x-button.submit>
                                    Lanjutkan Pembayaran
                                </x-button.submit>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('after-scripts')
        <script>
            let currentTotalBayar = {{ $order->total }};

            function rupiahFormat(angka, prefix) {
                return 'Rp. ' + angka.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
            }

            function fee_check(val) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('user.checkout.fee.check') }}",
                    data: {
                        "val": val,
                        'currentTotal': currentTotalBayar
                    },
                    beforeSend: function() {
                        $(".biaya-name").text('Loading...');
                        $(".biaya-price").text('Loading...');
                    },
                    success: function(data) {
                        var priceLayanan = parseInt(data.price.toFixed());
                        if (data.status == 200) {
                            $(".biaya-name").text('Biaya Jasa Pelayanan ' + data.name);
                            $(".biaya-price").text(rupiahFormat(priceLayanan));
                            $(".total-payment").text(rupiahFormat(currentTotalBayar + priceLayanan));
                        } else {
                            $(".biaya-name").text('Biaya Jasa Pelayanan');
                            $(".biaya-price").text('-');
                            $(".total-payment").text(rupiahFormat(currentTotalBayar));
                        }
                    },
                    error: function(err) {
                        console.log(err)
                        $(".biaya-name").text('Biaya Jasa Pelayanan');
                        $(".biaya-price").text('-');
                    }
                });
            }
        </script>
    @endpush
@endsection
