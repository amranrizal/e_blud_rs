<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow-sm" style="height: 70px;">

    <!-- Sidebar Toggle -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- KOTAK TAHUN (KIRI) -->
    <div class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100">
        <span class="badge-tahun">
            TAHUN : {{ date('Y') }}
        </span>
    </div>

    <!-- USER INFO (KANAN) -->
    <ul class="navbar-nav ml-auto align-items-center">

        <!-- Lonceng Notifikasi -->
        <li class="nav-item mx-1">
            <a class="nav-link" href="#">
                <i class="fas fa-bell fa-fw text-gray-400"></i>
            </a>
        </li>

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- User Profile -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                
                <div class="text-right mr-3 d-none d-lg-block">
                    <!-- Nama Kabupaten/RS -->
                    <div class="text-xs font-weight-bold text-gray-800 text-uppercase">
                        Kab. Kepulauan Selayar
                    </div>
                    <!-- Nama User -->
<span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name ?? 'Admin' }}</span>                </div>

                <!-- Logo Pemda / RS (Ganti src dengan logo asli) -->
                <img class="img-profile rounded-circle"
                    src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/768px-User_icon_2.svg.png"
                    style="border: 1px solid #ddd;">
            </a>
            
            <!-- Dropdown Menu -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                <a class="dropdown-item" href="#">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profile
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>

    </ul>

</nav>