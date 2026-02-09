import './bootstrap.js';
import '@coreui/coreui/dist/js/coreui.bundle.min.js';

$(function () {
    $('[data-toggle="tooltip"]').tooltip();

    const locale = (document.documentElement.getAttribute('lang') || 'en').slice(0, 2);

    if ($.fn.dataTable && locale === 'id') {
        $.extend(true, $.fn.dataTable.defaults, {
            language: {
                decimal: ',',
                thousands: '.',
                processing: 'Memproses...',
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ entri',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ entri',
                infoEmpty: 'Menampilkan 0 sampai 0 dari 0 entri',
                infoFiltered: '(disaring dari total _MAX_ entri)',
                loadingRecords: 'Memuat...',
                zeroRecords: 'Tidak ada data yang cocok',
                emptyTable: 'Tidak ada data tersedia pada tabel ini',
                paginate: {
                    first: 'Pertama',
                    previous: 'Sebelumnya',
                    next: 'Berikutnya',
                    last: 'Terakhir'
                },
                aria: {
                    sortAscending: ': aktifkan untuk mengurutkan kolom ascending',
                    sortDescending: ': aktifkan untuk mengurutkan kolom descending'
                }
            }
        });
    }
})

