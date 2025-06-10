$(document).ready(function () {
    console.log('Script delete-handler.js berhasil dimuat.');

    // Event delegation untuk tombol hapus
    $(document).on('click', '.delete-button', function (e) {
        e.preventDefault(); // Mencegah aksi default tombol
        console.log('Tombol hapus diklik.');

        const form = $(this).closest('.delete-form'); // Cari form terdekat
        if (form.length > 0) {
            const confirmation = confirm('Apakah Anda yakin ingin menghapus data ini?');
            console.log('Konfirmasi: ', confirmation);
            if (confirmation) {
                form.submit(); // Kirim form jika user menekan "OK"
            }
        } else {
            console.log('Form tidak ditemukan.');
        }
    });
});
