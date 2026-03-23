<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login E-BLUD RS</title>
    <!-- Gunakan CSS template Bossku (SB Admin 2 biasanya) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-primary">

    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">E-BLUD RUMAH SAKIT</h1>
                                        <p class="mb-4">Silakan login untuk mengakses sistem</p>
                                    </div>

                                    <!-- ALERT ERROR -->
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <!-- ALERT LOGOUT/INFO -->
                                    @if (session('info'))
                                        <div class="alert alert-info">{{ session('info') }}</div>
                                    @endif

                                    <form class="user" action="{{ route('login') }}" method="POST">
                                        @csrf
                                        
                                        <div class="form-group">
                                            <input type="email" name="email" class="form-control form-control-user"
                                                placeholder="Masukkan Email..." value="{{ old('email') }}" required autofocus>
                                        </div>
                                        
                                        <div class="form-group">
                                            <input type="password" name="password" class="form-control form-control-user"
                                                placeholder="Password" required>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            <i class="fas fa-sign-in-alt fa-fw"></i> MASUK SISTEM
                                        </button>
                                    </form>

                                    <hr>
                                    <div class="text-center">
                                        <small class="text-muted">Badan Layanan Umum Daerah &copy; {{ date('Y') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>