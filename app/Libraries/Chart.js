// Load Chart.js di bagian bawah view
// <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

const ctx = document.getElementById('chartPendapatan').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00'],
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: [150000, 200000, 180000, 300000, 450000, 380000],
            backgroundColor: '#198754', // Warna hijau seperti gambar
            borderRadius: 5,
        }]
    },
    options: {
        indexAxis: 'y', // Membuat bar menjadi horizontal sesuai gambar
        plugins: { legend: { display: false } },
        scales: { x: { grid: { display: false } }, y: { grid: { display: false } } }
    }
});