@extends('layouts.app')

@section('title', 'Face Recognition Attendance')

@section('content')
<div class="min-h-screen bg-[#001932] flex">
    <!-- Left Side -->
    <div class="w-1/2 flex items-center justify-center p-8">
        <div class="w-full max-w-lg text-center">

            <!-- Logo -->
            <div class="mb-8">
                <img src="{{ asset('logo.png') }}" class="w-100 h-auto object-contain">
            </div>

            <h2 class="text-3xl font-bold text-white mb-8">Absen Face Recognition</h2>

            <!-- Camera -->
            <div class="bg-gray-800 rounded-2xl overflow-hidden shadow-2xl mb-6">
                <video id="video" autoplay playsinline class="w-full h-auto" style="transform: scaleX(-1);"></video>
                <canvas id="canvas" class="hidden"></canvas>
            </div>

            <!-- Auto-fill NIK / Name -->
            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-white text-sm mb-2">NIK</label>
                    <input id="nik" readonly class="w-full px-4 py-3 bg-white rounded-lg">
                </div>

                <div>
                    <label class="block text-white text-sm mb-2">Nama</label>
                    <input id="nama" readonly class="w-full px-4 py-3 bg-white rounded-lg">
                </div>
            </div>

            <!-- Submit -->
            <button id="submitBtn" onclick="openSubmitModal()"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-lg shadow-lg transition hover:scale-105">
                SUBMIT
            </button>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-blue-200 hover:text-white text-sm">Admin Login →</a>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE LIST -->
    <div class="w-1/2 bg-white p-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-gray-900">DAFTAR ABSENSI</h2>
            <div class="text-right">
                <div class="text-sm text-gray-500">{{ now()->format('d F Y') }}</div>
                <div id="clock" class="text-4xl font-bold text-gray-900"></div>
            </div>
        </div>

        <!-- Meal Type -->
        <div class="mb-4">
            @if($currentMealType)
                <span class="px-4 py-2 rounded-full text-sm font-bold
                    @if($currentMealType === 'breakfast') bg-yellow-100 text-yellow-800
                    @elseif($currentMealType === 'lunch') bg-blue-100 text-blue-800
                    @else bg-purple-100 text-purple-800
                    @endif">
                    Current: {{ ucfirst($currentMealType) }}
                </span>
            @else
                <span class="px-4 py-2 rounded-full text-sm font-bold bg-red-100 text-red-800">
                    No active meal time
                </span>
            @endif
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto" style="max-height: calc(100vh - 250px);">
                <table class="min-w-full">
                    <thead class="bg-blue-100 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-xs font-bold">No.</th>
                            <th class="px-4 py-3 text-xs font-bold">NIK</th>
                            <th class="px-4 py-3 text-xs font-bold">Nama</th>
                            <th class="px-4 py-3 text-xs font-bold">Tanggal & Waktu</th>
                            <th class="px-4 py-3 text-xs font-bold">Jumlah</th>
                            <th class="px-4 py-3 text-xs font-bold">Kategori</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @foreach($attendances as $i => $a)
                        <tr>
                            <td class="px-4 py-3">{{ $i+1 }}</td>
                            <td class="px-4 py-3">{{ $a->nik }}</td>
                            <td class="px-4 py-3">{{ $a->employee->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $a->attendance_time->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 font-semibold">{{ $a->quantity }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded text-xs font-semibold
                                    @if($a->meal_type==='breakfast') bg-yellow-100 text-yellow-800
                                    @elseif($a->meal_type==='lunch') bg-blue-100 text-blue-800
                                    @else bg-purple-100 text-purple-800 @endif">
                                    {{ ucfirst($a->meal_type) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>

        <div class="mt-6 text-center text-sm text-gray-500">
            © 2025 IT-SIMS. All rights reserved
        </div>
    </div>
</div>

<!-- SUBMIT MODAL -->
<div id="submitModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-md rounded-2xl p-8 shadow-xl">

        <h2 class="text-2xl font-bold text-center mb-4">SUBMIT ABSENSI</h2>

        <label class="block mb-2 font-semibold text-gray-700">Jumlah Makanan</label>
        <input id="modalQuantity" type="number" value="1" min="1" max="10"
            class="w-full px-3 py-3 border rounded-lg mb-4">

        <label class="block mb-2 font-semibold text-gray-700">Saran</label>
        <textarea id="modalRemarks" rows="4"
            class="w-full px-3 py-3 border rounded-lg mb-6"
            placeholder="Opsional"></textarea>

        <button onclick="submitAttendance()"
            class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-bold">
            KIRIM
        </button>

    </div>
</div>

<!-- GENERIC MODAL -->
<div id="modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl">
        <div id="modalContent" class="text-center"></div>
    </div>
</div>

<script>
/* =============================
      CLOCK
============================= */
function updateClock() {
    const now = new Date();
    document.getElementById('clock').textContent =
        now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
}
setInterval(updateClock, 1000); updateClock();

/* =============================
      CAMERA
============================= */
const video = document.getElementById("video");
navigator.mediaDevices.getUserMedia({ video: true })
    .then(stream => video.srcObject = stream);

const canvas = document.getElementById("canvas");
const nikInput = document.getElementById("nik");
const namaInput = document.getElementById("nama");

let isRecognizing = false;
let lastRecognized = null;

/* =============================
      AUTO RECOGNITION
============================= */
setInterval(async () => {
    if (isRecognizing) return;
    isRecognizing = true;

    try {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        const ctx = canvas.getContext("2d");
        ctx.save();
        ctx.scale(-1, 1);
        ctx.drawImage(video, -canvas.width, 0);
        ctx.restore();

        const base64 = canvas.toDataURL("image/jpeg");

        const res = await fetch("{{ route('checkin') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                image: base64,
                recognize_only: true
            })
        });

        const data = await res.json();

        if (data.success && data.nik !== lastRecognized) {
            nikInput.value = data.nik;
            namaInput.value = data.employee_name;
            lastRecognized = data.nik;
        }

    } catch (e) {
        console.log("Recognition error:", e);
    }
    isRecognizing = false;

}, 2000);

/* =============================
      OPEN SUBMIT MODAL
============================= */
function openSubmitModal() {
    if (!nikInput.value) {
        return showModal("error", "Error", "Wajah belum terdeteksi!");
    }
    document.getElementById("submitModal").classList.remove("hidden");
}

/* =============================
      SUBMIT ATTENDANCE
============================= */
function submitAttendance() {
    const quantity = document.getElementById("modalQuantity").value;
    const remarks  = document.getElementById("modalRemarks").value;

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext("2d");
    ctx.save();
    ctx.scale(-1, 1);
    ctx.drawImage(video, -canvas.width, 0);
    ctx.restore();

    const imageData = canvas.toDataURL("image/jpeg");

    fetch("{{ route('checkin') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            image: imageData,
            quantity: quantity,
            remarks: remarks,
            recognize_only: false
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success)
            showModal("success", "Berhasil", data.message, true);
        else
            showModal("error", "Gagal", data.message);
    });

    document.getElementById("submitModal").classList.add("hidden");
}

/* =============================
      GENERIC MODAL
============================= */
function showModal(type, title, message, reload=false) {
    const modal = document.getElementById("modal");
    const html = `
        <h3 class="text-2xl font-bold mb-2">${title}</h3>
        <p class="text-gray-600 mb-6">${message}</p>
        <button onclick="closeModal(${reload})"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">
            OK
        </button>`;
    document.getElementById("modalContent").innerHTML = html;
    modal.classList.remove("hidden");
}

function closeModal(reload=false) {
    document.getElementById("modal").classList.add("hidden");
    if (reload) location.reload();
}
</script>

@endsection
