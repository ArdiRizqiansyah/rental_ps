@extends('layouts.app')

@section('content')
    @include('includes.vendor.fulltime-calender')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col">
                <div class="card bg-white">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">
                            Buat Jadwal Rental
                        </h5>
                        <x-button.back :url="route('home')" />
                    </div>

                    <div class="card-body">
                        <form action="{{ route('user.booking.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <x-form.input-label :required="true">
                                    Tanggal Booking
                                </x-form.input-label>
                                <x-form.input type="date" name="date" :value="old('date')" required />
                                <x-form.input-error name="date" />
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <x-form.input-label :required="true">
                                        Rental Layanan
                                    </x-form.input-label>
                                    <x-form.input-select name="service_id" required>
                                        <option value="">Pilih Rental Layanan</option>
                                        @foreach ($services as $service)
                                            <option value="{{ $service->id }}" data-price="{{ $service->price }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                                        @endforeach
                                    </x-form.input-select>
                                    <x-form.input-error name="service_id" />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <x-form.input-label :required="true">
                                        Total Sesi
                                    </x-form.input-label>
                                    <x-form.input type="number" name="total_session" placeholder="masukkan total sesi" :value="old('total_session')" required />
                                    <x-form.input-error name="date" />
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <x-form.input-label :required="true">
                                        Harga Sewa
                                    </x-form.input-label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">Rp</span>
                                        <x-form.input type="text" name="price" id="price" placeholder="0" :value="old('price')" readonly />
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <x-form.input-label :required="true">
                                        Total Harga
                                    </x-form.input-label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">Rp</span>
                                        <x-form.input type="text" name="total_price" id="total_price" placeholder="0" :value="old('total_price')" readonly />
                                    </div>
                                    <x-form.input-error name="total_price" />
                                </div>
                            </div>

                            <div class="d-grid">
                                <x-button.submit>
                                    Buat Jadwal
                                </x-button.submit>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('after-scripts')
        <script>
            $(document).ready(function() {
                function updatePrice() {
                    var service_id = $('select[name="service_id"]').val();
                    var price = $('option[value="'+service_id+'"]').data('price');
                    
                    if(price) {
                        // Format price with thousand separator
                        var formatted_price = price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        $('input[name="price"]').val(formatted_price);
                    } else {
                        $('input[name="price"]').val('');
                    }
                }

                function calculateTotal() {
                    var service_id = $('select[name="service_id"]').val();
                    var price = $('option[value="'+service_id+'"]').data('price');
                    var total_session = $('input[name="total_session"]').val();
                    var date = $('input[name="date"]').val();
                    
                    if(price && total_session && date) {
                        var total = price * total_session;
                        
                        // Check if selected date is weekend
                        var selectedDate = new Date(date);
                        var day = selectedDate.getDay();
                        
                        // Add weekend surcharge (day 6 is Saturday, 0 is Sunday)
                        if(day === 6 || day === 0) {
                            total += 50000;
                        }
                        
                        // Format to thousand separator
                        var formatted_total = total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        $('input[name="total_price"]').val(formatted_total);
                    } else {
                        $('input[name="total_price"]').val('');
                    }
                }

                // Update price and calculate total when service changes
                $('select[name="service_id"]').change(function() {
                    updatePrice();
                    calculateTotal();
                });
                
                // Calculate when total session changes
                $('input[name="total_session"]').change(calculateTotal);
                $('input[name="total_session"]').keyup(calculateTotal);
                
                // Calculate when date changes
                $('input[name="date"]').change(calculateTotal);

                // Initial price update
                updatePrice();
            });
        </script>
    @endpush
@endsection
