<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sistem E-BLUD RS">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title>E-BLUD Rumah Sakit</title>

    <!-- Custom fonts for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    
    <!-- CSS Select2 & Theme Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet">
    
    <!-- Custom styles for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
    /* SIPD STYLE OVERRIDE */
    
    /* 1. Sidebar Putih Bersih */
    .sidebar-light-sipd {
        background-color: #ffffff !important;
        border-right: 1px solid #e3e6f0;
    }
    .sidebar-light-sipd .nav-item .nav-link {
        color: #333 !important;
        font-weight: 500;
        /* Default CSS SB Admin ditimpa di poin 5 bawah */
    }
    .sidebar-light-sipd .nav-item.active .nav-link {
        background-color: #eaecf4; /* Abu-abu SIPD */
        color: #000 !important;
        border-left: 4px solid #4e73df; /* Garis biru aktif */
        font-weight: 700 !important;
    }
    .sidebar-brand-text {
        color: #333 !important;
        font-size: 1.2rem;
        font-weight: 800;
    }

    /* 2. Topbar */
    .topbar {
        background-color: #ffffff !important;
        border-bottom: 1px solid #e3e6f0;
    }
    .badge-tahun {
        background-color: #030f2b; /* Biru Gelap SIPD */
        color: white;
        padding: 8px 15px;
        border-radius: 5px;
        font-weight: bold;
        letter-spacing: 1px;
    }

    /* 3. Tombol Biru Bawah Sidebar */
    .btn-sipd {
        background-color: #4099ff;
        color: white;
        width: 90%;
        margin: 0 auto;
        display: block;
        border-radius: 5px;
        text-align: center;
        padding: 10px;
        font-weight: bold;
    }
    .btn-sipd:hover {
        background-color: #2c82e0;
        color: white;
        text-decoration: none;
    }

    /* 4. Background Body Abu-abu */
    #content-wrapper {
        background-color: #f8f9fc !important;
    }

        /* =========================================
        5. FIX SIDEBAR SEJAJAR & RAPAT (SIPD FINAL) 
        ========================================= */
        
        .sidebar .nav-item {
            margin-bottom: 0px !important;
        }

        /* KUNCINYA DISINI: Pakai Flexbox biar Icon & Teks Sejajar Horizontal */
        .sidebar .nav-item .nav-link {
            display: flex !important;          /* Wajib Flex biar sejajar */
            align-items: center !important;    /* Pastikan vertikal di tengah */
            flex-direction: row !important;    /* Paksa arah baris (kiri ke kanan) */
            
            padding-top: 10px !important;      /* Jarak Atas */
            padding-bottom: 10px !important;   /* Jarak Bawah */
            font-size: 0.85rem !important;     /* Ukuran font */
            text-align: left !important;       /* Rata kiri */
            width: 100%;
        }

        /* Atur Icon biar diam di kiri dan tidak loncat */
        .sidebar .nav-item .nav-link i {
            font-size: 1rem !important;        /* Ukuran icon */
            margin-right: 12px !important;     /* Jarak icon ke teks */
            width: 25px !important;            /* Lebar FIX biar teks rata semua */
            text-align: center !important;     /* Icon center di kotaknya */
            flex-shrink: 0 !important;         /* PENTING: Icon dilarang mengecil/gepeng */
        }

        /* Atur Teks Span */
        .sidebar .nav-item .nav-link span {
            line-height: 1.2;                  /* Spasi baris jika teks kepanjangan */
            display: inline-block;             /* Biar rapi */
        }

        /* Khusus Judul Group (TRANSAKSI, DLL) */
        .sidebar-heading {
            margin-top: 10px !important;
            margin-bottom: 5px !important;
            padding-left: 1rem !important;
            font-size: 0.75rem !important;
            font-weight: bold;
            opacity: 0.6;
        }
    </style>
    <!-- SweetAlert2 CSS & JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- 1. PANGGIL SIDEBAR -->
        @include('layouts.sidebar')

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- 2. PANGGIL TOPBAR -->
                @include('layouts.topbar')

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- ALERT GLOBAL (Sukses/Gagal) -->
                   

                    <!-- 3. ISI KONTEN BERUBAH-UBAH DISINI -->
                    @yield('content')

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white shadow-sm border-top">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; E-BLUD RS {{ date('Y') }}</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- 4. MODAL LOGOUT (PENTING!) -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Konfirmasi Logout</h5>
                    <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Apakah Anda yakin ingin mengakhiri sesi ini?</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

        <script>
        document.addEventListener("DOMContentLoaded", function () {
            const triggerTabList = document.querySelectorAll('#settingTabs button');
            triggerTabList.forEach(triggerEl => {
                new bootstrap.Tab(triggerEl);
            });
        });
        </script>

       <!-- ============================= -->
    <!-- JAVASCRIPT SECTION (RAPI FIX) -->
    <!-- ============================= -->

    <!-- 1️⃣ jQuery (HANYA SATU!) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- 2️⃣ Bootstrap 4 (SB Admin 2) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>

    <!-- 3️⃣ jQuery Easing -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>

    <!-- 4️⃣ SB Admin 2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/js/sb-admin-2.min.js"></script>

    <!-- 5️⃣ Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- 6️⃣ Stack Scripts (Halaman Spesifik) -->
    @stack('scripts')

    <!-- ============================= -->
    <!-- SWEETALERT & GLOBAL SCRIPT -->
    <!-- ============================= -->
    <script>
    $(document).ready(function () {

        // ===============================
        // NOTIFIKASI SUKSES
        // ===============================
        @if(session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                iconColor: '#1cc88a',
            });
        @endif

        // ===============================
        // NOTIFIKASI ERROR
        // ===============================
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'GAGAL!',
                text: '{{ session('error') }}',
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#e74a3b',
            });
        @endif

        // ===============================
        // KONFIRMASI HAPUS
        // ===============================
        $(document).on('click', '.btn-delete', function (e) {
            e.preventDefault();
            var form = $(this).closest('form');

            Swal.fire({
                title: 'Yakin hapus data ini?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

    });
    </script>
@stack('scripts')
</body>
</html>
