<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan Tiket - {{ $film->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .seat-available { background-color: #10B981; }
        .seat-selected { background-color: #3B82F6; }
        .seat-sweetbox { background-color: #F59E0B; }
        .seat-occupied { background-color: #EF4444; cursor: not-allowed; }
    </style>
</head>
<body class="bg-gray-900 text-white">

    <!-- Header -->
    <header class="bg-black py-4 border-b border-yellow-500">
        <div class="container mx-auto px-6">
            <div class="flex justify-between items-center">
                <div class="text-2xl font-bold">
                    <span class="text-yellow-400">CINEMA</span><span class="text-white">XXI</span>
                </div>
                <a href="{{ route('home') }}" class="text-yellow-400 hover:text-yellow-300">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-6 py-8">
        <!-- Film Info -->
        <div class="bg-gray-800 rounded-xl p-6 mb-8">
            <div class="flex items-center space-x-6">
                @if($film->image)
                <img src="{{ asset('storage/' . $film->image) }}" alt="{{ $film->title }}" class="w-32 h-48 object-cover rounded-lg">
                @else
                <div class="w-32 h-48 bg-gray-700 rounded-lg flex items-center justify-center">
                    <i class="fas fa-film text-gray-500 text-4xl"></i>
                </div>
                @endif
                <div>
                    <h1 class="text-3xl font-bold text-yellow-400">{{ $film->title }}</h1>
                    <p class="text-gray-300 mt-2">{{ $film->genre }}</p>
                    <p class="text-gray-300">{{ $film->duration }}</p>
                    <p class="text-2xl font-bold text-green-400 mt-4">Rp {{ number_format($film->price, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Booking Form -->
        <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
            @csrf
            <input type="hidden" name="film_id" value="{{ $film->id }}">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Studio & Date Selection -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Studio Selection -->
                    <div class="bg-gray-800 rounded-xl p-6">
                        <h2 class="text-xl font-bold text-yellow-400 mb-4">Pilih Studio</h2>
                        <div class="space-y-3">
                            @foreach($studios as $studio)
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="radio" name="studio_id" value="{{ $studio->id }}"
                                       class="studio-radio hidden"
                                       {{ $loop->first ? 'checked' : '' }}>
                                <div class="w-6 h-6 rounded-full border-2 border-gray-400 flex items-center justify-center">
                                    <div class="w-3 h-3 rounded-full bg-yellow-400 hidden"></div>
                                </div>
                                <span class="text-white font-semibold">{{ $studio->name }}</span>
                                <span class="text-gray-400 text-sm">({{ $studio->total_seats }} kursi)</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Date Selection -->
                    <div class="bg-gray-800 rounded-xl p-6">
                        <h2 class="text-xl font-bold text-yellow-400 mb-4">Pilih Tanggal</h2>
                        <input type="date" name="show_date"
                               class="w-full p-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-yellow-500"
                               min="{{ date('Y-m-d') }}"
                               value="{{ date('Y-m-d') }}">
                    </div>

                    <!-- Time Selection -->
                    <div class="bg-gray-800 rounded-xl p-6">
                        <h2 class="text-xl font-bold text-yellow-400 mb-4">Pilih Jam Tayang</h2>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($showTimes as $time)
                            <label class="cursor-pointer">
                                <input type="radio" name="show_time" value="{{ $time }}"
                                       class="hidden time-radio" {{ $loop->first ? 'checked' : '' }}>
                                <div class="w-full p-3 bg-gray-700 border border-gray-600 rounded-lg text-center hover:bg-gray-600 transition time-slot">
                                    <span class="text-white font-semibold">{{ $time }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Right Column - Seat Selection -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-800 rounded-xl p-6">
                        <h2 class="text-xl font-bold text-yellow-400 mb-6">Pilih Kursi</h2>

                        <!-- Screen -->
                        <div class="bg-gray-700 py-4 rounded-lg text-center mb-8">
                            <span class="text-white font-bold text-lg">LAYAR</span>
                        </div>

                        <!-- Seat Map -->
                        <div id="seatMap" class="flex flex-col items-center space-y-2 mb-8">
                            <div class="text-gray-400">Memuat kursi...</div>
                        </div>

                        <!-- Seat Legend -->
                        <div class="flex justify-center space-x-6 text-sm mb-6">
                            <div class="flex items-center space-x-2">
                                <div class="w-6 h-6 bg-green-500 rounded"></div>
                                <span class="text-gray-300">Tersedia</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-6 h-6 bg-blue-500 rounded"></div>
                                <span class="text-gray-300">Dipilih</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-6 h-6 bg-yellow-500 rounded"></div>
                                <span class="text-gray-300">Sweetbox</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-6 h-6 bg-red-500 rounded"></div>
                                <span class="text-gray-300">Terisi</span>
                            </div>
                        </div>

                        <!-- Selected Seats & Total -->
                        <div class="mt-8 p-4 bg-gray-700 rounded-lg">
                            <h3 class="text-lg font-bold text-yellow-400 mb-3">Kursi Dipilih</h3>
                            <div id="selectedSeats" class="text-gray-300 mb-3">Belum ada kursi dipilih</div>
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="text-gray-300">Total: </span>
                                    <span id="totalPrice" class="text-2xl font-bold text-green-400">Rp 0</span>
                                </div>
                                <button type="submit"
                                        class="bg-yellow-500 text-black px-8 py-3 rounded-lg font-bold hover:bg-yellow-400 transition disabled:opacity-50"
                                        id="bookButton" disabled>
                                    <i class="fas fa-ticket-alt mr-2"></i>PESAN SEKARANG
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🎬 Booking page loaded!');

            const studioRadios = document.querySelectorAll('.studio-radio');
            const seatMap = document.getElementById('seatMap');
            const selectedSeatsDiv = document.getElementById('selectedSeats');
            const totalPriceSpan = document.getElementById('totalPrice');
            const bookButton = document.getElementById('bookButton');
            const filmPrice = {{ $film->price }};

            let selectedSeats = [];
            let currentStudioId = null;

            // Load seats when studio is selected
            studioRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.checked) {
                        currentStudioId = this.value;
                        loadSeats(this.value);
                    }
                });
            });

            // Load initial seats for first studio
            const initialStudio = document.querySelector('.studio-radio:checked');
            if (initialStudio) {
                currentStudioId = initialStudio.value;
                loadSeats(initialStudio.value);
            }

            function loadSeats(studioId) {
                console.log('Loading seats for studio:', studioId);
                fetch(`/studios/${studioId}/seats`)
                    .then(response => response.json())
                    .then(data => {
                        renderSeatMap(data);
                        updateSelectedSeats();
                    })
                    .catch(error => {
                        console.error('Error loading seats:', error);
                        seatMap.innerHTML = '<div class="text-red-400">Error memuat kursi</div>';
                    });
            }

            function renderSeatMap(seatsByRow) {
                console.log('Rendering seat map with data:', seatsByRow);
                seatMap.innerHTML = '';

                Object.keys(seatsByRow).sort().forEach(row => {
                    const rowDiv = document.createElement('div');
                    rowDiv.className = 'flex items-center space-x-2';

                    // Row label
                    const rowLabel = document.createElement('div');
                    rowLabel.className = 'w-8 text-center font-bold text-yellow-400';
                    rowLabel.textContent = row;
                    rowDiv.appendChild(rowLabel);

                    // Seats
                    seatsByRow[row].forEach(seat => {
                        const seatDiv = document.createElement('div');
                        let seatClass = 'w-8 h-8 rounded flex items-center justify-center text-xs font-bold cursor-pointer transition-all ';

                        // Check if seat is selected
                        const isSelected = selectedSeats.find(s => s.id === seat.id);

                        if (!seat.is_available) {
                            seatClass += 'seat-occupied cursor-not-allowed';
                        } else if (isSelected) {
                            seatClass += 'seat-selected';
                        } else if (seat.type === 'sweetbox') {
                            seatClass += 'seat-sweetbox hover:bg-yellow-400';
                        } else {
                            seatClass += 'seat-available hover:bg-green-400';
                        }

                        seatDiv.className = seatClass;
                        seatDiv.textContent = seat.number;
                        seatDiv.title = `Kursi ${seat.seat_code}`;
                        seatDiv.dataset.seatId = seat.id;

                        if (seat.is_available) {
                            seatDiv.addEventListener('click', function() {
                                console.log('Clicked seat:', seat.seat_code);
                                toggleSeat(seat);
                            });
                        }

                        rowDiv.appendChild(seatDiv);
                    });

                    seatMap.appendChild(rowDiv);
                });
            }

            function toggleSeat(seat) {
                console.log('Toggling seat:', seat.seat_code);
                console.log('Current selected seats before:', selectedSeats.map(s => s.seat_code));

                const seatIndex = selectedSeats.findIndex(s => s.id === seat.id);

                if (seatIndex > -1) {
                    // Remove seat
                    selectedSeats.splice(seatIndex, 1);
                    console.log('Removed seat:', seat.seat_code);
                } else {
                    // Add seat
                    selectedSeats.push(seat);
                    console.log('Added seat:', seat.seat_code);
                }

                console.log('Current selected seats after:', selectedSeats.map(s => s.seat_code));
                updateSelectedSeats();

                // Re-render seat map to update colors without losing event listeners
                if (currentStudioId) {
                    fetch(`/studios/${currentStudioId}/seats`)
                        .then(response => response.json())
                        .then(data => {
                            renderSeatMap(data);
                        });
                }
            }

            function updateSelectedSeats() {
                console.log('Updating selected seats display. Count:', selectedSeats.length);

                // Update selected seats display
                if (selectedSeats.length === 0) {
                    selectedSeatsDiv.innerHTML = 'Belum ada kursi dipilih';
                    totalPriceSpan.textContent = 'Rp 0';
                    bookButton.disabled = true;
                    console.log('No seats selected - disabling button');
                } else {
                    const seatCodes = selectedSeats.map(seat => {
                        const extra = seat.type === 'sweetbox' ? ' (Sweetbox)' : '';
                        return seat.seat_code + extra;
                    });
                    selectedSeatsDiv.innerHTML = seatCodes.join(', ');

                    // Calculate total price
                    let total = 0;
                    selectedSeats.forEach(seat => {
                        let price = filmPrice;
                        if (seat.type === 'sweetbox') {
                            price += 20000; // Sweetbox extra charge
                        }
                        total += price;
                    });

                    totalPriceSpan.textContent = 'Rp ' + total.toLocaleString('id-ID');
                    bookButton.disabled = false;
                    console.log('Seats selected - enabling button. Total:', total);
                }

                // Update hidden inputs for seats
                document.querySelectorAll('input[name="seats[]"]').forEach(input => input.remove());

                selectedSeats.forEach(seat => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'seats[]';
                    input.value = seat.id;
                    document.getElementById('bookingForm').appendChild(input);
                });

                console.log('Created hidden inputs:', document.querySelectorAll('input[name="seats[]"]').length);
            }

            // Update radio button visuals
            document.querySelectorAll('.studio-radio').forEach(radio => {
                radio.addEventListener('change', function() {
                    document.querySelectorAll('.studio-radio').forEach(r => {
                        const visual = r.parentElement.querySelector('div > div');
                        visual.classList.toggle('hidden', !r.checked);
                    });
                });
            });

            // Update time selection visuals
            document.querySelectorAll('.time-radio').forEach(radio => {
                radio.addEventListener('change', function() {
                    document.querySelectorAll('.time-slot').forEach(slot => {
                        slot.classList.remove('bg-yellow-500', 'text-black');
                        slot.classList.add('bg-gray-700', 'text-white');
                    });

                    const selectedSlot = this.parentElement.querySelector('.time-slot');
                    selectedSlot.classList.remove('bg-gray-700', 'text-white');
                    selectedSlot.classList.add('bg-yellow-500', 'text-black');
                });
            });

            // Initialize time slot visuals
            document.querySelectorAll('.time-radio:checked').forEach(radio => {
                const selectedSlot = radio.parentElement.querySelector('.time-slot');
                selectedSlot.classList.remove('bg-gray-700', 'text-white');
                selectedSlot.classList.add('bg-yellow-500', 'text-black');
            });
        });
    </script>
</body>
</html>
