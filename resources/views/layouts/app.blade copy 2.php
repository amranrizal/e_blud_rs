<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sistem E-BLUD RS">
    <meta name="author" content="">

    <title>E-BLUD Rumah Sakit</title>

    <!-- Custom fonts for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- CSS Select2 & Theme Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
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
    }
    .sidebar-light-sipd .nav-item.active .nav-link {
        background-color: #eaecf4; /* Abu-abu SIPD */
        color: #000 !important;
        border-left: 4px solid #4e73df; /* Garis biru aktif */
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
</style>
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
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <!-- 3. ISI KONTEN BERUBAH-UBAH DISINI -->
                    @yield('content')

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
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
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Yakin ingin keluar?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Klik "Logout" di bawah untuk mengakhiri sesi Anda.</div>
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

    <!-- Bootstrap core JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/js/sb-admin-2.min.js"></script>
    <!-- Taruh sebelum </body> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- 👇 TAMBAHKAN BARIS INI DISINI 👇 -->
    <!-- Ini menyiapkan tempat untuk script tambahan dari halaman lain -->
    @stack('scripts') 
</body>
</html>