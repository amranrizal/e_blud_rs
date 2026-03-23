@extends('layouts.app') 

@section('content')

{{-- 
    ================================================================
    LOGIC PENGUNCIAN VIEW (LOCKING SYSTEM)
    ================================================================
    User Biasa: Tidak bisa edit jika status 'submitted' atau 'valid'.
    Admin: Bebas edit kapan saja (God Mode).
--}}
@php
    $status = $paguInfo->status_validasi; // draft, submitted, valid, tolak
    $isAdmin = auth()->user()->role == 'admin';
    
    // Terkunci jika: (Status Valid/Submitted) DAN (Bukan Admin)
    $isLocked = ($status == 'valid' || $status == 'submitted') && !$isAdmin;
@endphp

<div class="container-fluid p-0">

    {{-- ALERT JIKA TERKUNCI --}}
    @if($isLocked)
    <div class="alert alert-warning border-start border-5 border-warning shadow-sm mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-lock fa-2x me-3 text-warning"></i>
            <div>
                <h5 class="fw-bold mb-0 text-dark">MODE READ-ONLY</h5>
                <small class="text-dark">
                    Data RKA ini sedang dalam status <strong>{{ strtoupper($status) }}</strong>. 
                    Anda tidak dapat melakukan perubahan data. Hubungi Admin/Verifikator jika ada revisi.
                </small>
            </div>
        </div>
    </div>
    @endif
    
    {{-- ALERT KHUSUS STATUS REVISI --}}
    @if($paguInfo->status_validasi == 'tolak')
    <div class="alert alert-danger border-start border-5 border-danger shadow-sm mb-4">
        <div class="d-flex">
            <i class="fas fa-exclamation-circle fa-2x me-3 mt-1"></i>
            <div>
                <h5 class="fw-bold mb-1">PERLU DIPERBAIKI (REVISI)</h5>
                <p class="mb-0">Admin telah mengembalikan RKA ini dengan catatan:</p>
                <div class="bg-white text-danger p-2 rounded mt-2 border border-danger border-opacity-25 fw-bold">
                    <em>"{{ $paguInfo->catatan_revisi }}"</em>
                </div>
                <small class="text-secondary mt-1 d-block">Silakan edit data di bawah, lalu klik 'Ajukan Validasi' kembali di halaman dashboard.</small>
            </div>
        </div>
    </div>
    @endif

    <!-- A. HEADER PAGU -->
    <div class="bg-primary text-white p-4 rounded-3 mb-4 shadow-sm">
        <div class="d-flex justify-content-between align-items-end">
            <div>
                <small class="text-white-50 text-uppercase fw-bold">Sub Kegiatan</small>
                <h4 class="mb-1 fw-bold">
                    {{ optional($paguInfo->subKegiatan)->nama_program ?? 'Nama Tidak Ditemukan' }}
                </h4>
                <div class="d-flex gap-3 text-white-50">
                    <small>
                        <i class="fas fa-barcode me-1"></i> 
                        {{ optional($paguInfo->programNode)->kode_program ?? '-' }}
                    </small>
                    <small>
                        <i class="fas fa-calendar-alt me-1"></i> Tahun {{ $tahun }}
                    </small>
                    {{-- Badge Status di Header --}}
                    <small>
                        @if($status == 'valid') <span class="badge bg-success border border-white">TERVALIDASI</span>
                        @elseif($status == 'submitted') <span class="badge bg-warning text-dark border border-white">MENUNGGU VERIFIKASI</span>
                        @else <span class="badge bg-secondary border border-white">DRAFT</span>
                        @endif
                    </small>
                </div>
            </div>
            <div class="text-end">
                <small class="text-white-50">Sisa Pagu Tersedia</small>
                <h2 class="fw-bold mb-0" id="sisa-pagu-display">
                    Rp {{ number_format($sisaPagu, 0, ',', '.') }}
                </h2>
                <small class="text-white-50">dari Total Pagu: Rp {{ number_format($paguInfo->pagu_murni ?? $paguInfo->pagu, 0, ',', '.') }}</small>
            </div>
        </div>
    </div>    

    <!-- C. CARD FORM INPUT (LAYOUT HORIZONTAL) -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h6 class="mb-0 fw-bold text-primary"><i class="fas fa-edit me-2"></i>Input Rincian Belanja</h6>
        </div>
        <div class="card-body">
            
            {{-- BLOCK FORM JIKA TERKUNCI --}}
            <fieldset {{ $isLocked ? 'disabled' : '' }}>
                
                <form id="form-belanja" action="{{ route('budget.store') }}" method="POST">
                    @csrf
                    <!-- Hidden Inputs -->
        
                    <input type="hidden" name="unit_id" value="{{ $paguInfo->unit_id }}">
                    <input type="hidden" name="pagu_indikatif_id" value="{{ $paguInfo->id }}">
                    <input type="hidden" name="sub_kegiatan_id" value="{{ $paguInfo->sub_kegiatan_id }}">
                    <input type="hidden" name="tahun" value="{{ $tahun }}">

                    <div class="row g-3 mb-3">
                        <!-- 1. AKUN BELANJA -->
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-muted">AKUN BELANJA (COA)</label>
                            <select class="form-select" name="id_rekening" id="id_rekening" required>
                                <option value=""></option>
                                @foreach($rekenings as $rek)
                                    <option value="{{ $rek->id }}" data-kode="{{ $rek->kode_akun }}">
                                        {{ $rek->kode_akun }} - {{ $rek->nama_akun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 2. SSH / STANDAR HARGA -->
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-muted">
                                CARI BARANG (SSH) <span class="badge bg-info">Auto-Fill</span>
                            </label>
                            <select class="form-select" name="standar_harga_id" id="standar_harga_id" style="width: 100%;">
                                <option value=""></option>
                                @foreach($sshItems as $ssh)
                                    <option value="{{ $ssh->id }}" 
                                            data-harga="{{ $ssh->harga }}" 
                                            data-satuan="{{ $ssh->satuan }}"
                                            data-uraian="{{ $ssh->uraian ?? $ssh->nama_barang }}"
                                            data-kelompok="{{ $ssh->kode_kelompok }}">
                                        {{ $ssh->uraian ?? $ssh->nama_barang }} (Rp {{ number_format($ssh->harga) }})
                                    </option>
                                @endforeach
                            </select>
                                <input type="hidden" name="standar_harga_id_manual" id="standar_harga_id_manual">
                        </div>
                        <div id="sbu-parameter-container"></div>
                    </div>

                    <!-- BARIS 3: URAIAN & OPSI MANUAL -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold text-muted">3. Uraian / Spesifikasi</label>
                            <textarea class="form-control bg-light" name="uraian" id="input-uraian" rows="2" 
                                placeholder="Pilih SSH di atas..." readonly required></textarea>
                            
                            <div id="div-dasar-harga" class="mt-2 d-none">
                                <input type="text" class="form-control form-control-sm border-warning" 
                                    name="keterangan_harga" id="input-dasar-harga" 
                                    placeholder="Masukkan alasan/dasar harga (Wajib untuk Manual)">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Opsi Input</label>
                            <div class="card bg-light border-0">
                                <div class="card-body p-3">
                                    <div class="form-check form-switch mb-1">
                                        <input class="form-check-input" type="checkbox" id="manualCheck" name="is_manual">
                                        <label class="form-check-label fw-bold" for="manualCheck">
                                            Aktifkan Mode Manual
                                        </label>
                                    </div>
                                    <small class="text-muted d-block lh-sm" style="font-size: 0.75rem">
                                        <i class="fas fa-info-circle me-1"></i> Centang opsi ini jika barang tidak ada di SSH.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BARIS 4: VOLUME, HARGA, TOMBOL -->
                    <div class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted">Volume</label>
                            <input type="number" class="form-control" name="volume" id="input-volume" placeholder="0" min="0" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted">Satuan</label>
                            <input type="text" class="form-control bg-light" name="satuan" id="input-satuan" placeholder="Auto" readonly required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Harga Satuan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" class="form-control bg-light" name="harga_satuan" id="input-harga" placeholder="0" readonly required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted">Estimasi Total</label>
                            <div class="fw-bold text-primary mt-1" id="total-display">Rp 0</div>
                        </div>
                        
                        <!-- TOMBOL AKSI FORM (HILANG JIKA LOCKED) -->
                        <div class="col-md-3 d-flex gap-2">
                            @if(!$isLocked)
                                <button type="button" class="btn btn-secondary w-50 d-none" id="btn-batal-edit">Batal</button>
                                <button type="button" class="btn btn-primary w-100 fw-bold btn-simpan-rka">
                                    <i class="fas fa-save me-1"></i> Simpan
                                </button>
                            @else
                                <button type="button" class="btn btn-secondary w-100 fw-bold" disabled>
                                    <i class="fas fa-lock me-1"></i> Input Dikunci
                                </button>
                            @endif
                        </div>
                    </div>
                </form>

            </fieldset> <!-- End Fieldset Disabled -->

        </div>
    </div>


    <!-- D. CARD TABEL RINCIAN (LIST BELANJA) -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-success"><i class="fas fa-list-ul me-2"></i>Daftar Rincian Belanja</h6>
            <span class="badge bg-success-subtle text-success border border-success-subtle">
                Total Item: {{ $rincian->count() }}
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="table-rincian">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th width="40%">Uraian Belanja</th>
                            <th class="text-center" width="15%">Koefisien</th>
                            <th class="text-end" width="15%">Harga Satuan</th>
                            <th class="text-end" width="15%">Total</th>
                            <th class="text-center" width="10%">Aksi</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @php $currentAccount = null; @endphp

                        @forelse($rincian as $index => $item)
                            
                            {{-- LOGIC GROUPING HEADER --}}
                            @if($currentAccount != $item->kode_akun)
                                <tr class="table-secondary border-bottom border-white">
                                    <td colspan="6" class="py-2 ps-3"> 
                                        <div class="d-flex align-items-center">
                                            @if($item->rekening)
                                                <span class="badge bg-primary text-white border border-light shadow-sm me-2" 
                                                      style="font-size: 0.85rem; letter-spacing: 0.5px;">
                                                    {{ $item->rekening->kode_akun }}
                                                </span>
                                                <span class="fw-bold text-dark text-uppercase" style="font-size: 0.85rem;">
                                                    {{ $item->rekening->nama_akun }}
                                                </span>
                                            @else
                                                <span class="badge bg-danger text-white me-2">Error</span>
                                                <span class="text-danger fst-italic small">Rekening Tidak Terdeteksi</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @php $currentAccount = $item->kode_akun; @endphp
                            @endif

                            {{-- BARIS ITEM BELANJA --}}
                            <tr id="row-{{ $item->id }}">
                                <td class="text-center align-middle">{{ $index + 1 }}</td>
                                <td class="align-middle">
                                    <div class="fw-bold text-dark">{{ $item->uraian }}</div>
                                    <div class="mt-1">
                                        @if(is_null($item->standar_harga_id))
                                            <span class="badge bg-warning">Non SSH</span>
                                        @else
                                            <span class="badge bg-success">
                                                {{ $item->standarHarga->kode_kelompok ?? 'Standar' }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- 🔥 PARAMETER SBU --}}
                                    @if(!empty($item->parameter_json))
                                        @php
                                            $params = json_decode($item->parameter_json, true);
                                        @endphp
                                        <div class="mt-2 small text-muted">
                                            @foreach($params as $key => $value)
                                                • {{ ucfirst($key) }}: {{ $value }}<br>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    {{ number_format($item->volume, 0, ',', '.') }} {{ $item->satuan }}
                                </td>
                                <td class="text-end align-middle font-monospace text-muted">
                                    Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                                </td>
                                <td class="text-end align-middle fw-bold text-primary font-monospace">
                                    Rp {{ number_format($item->total_anggaran, 0, ',', '.') }}
                                </td>
                                
                                {{-- LOGIC TOMBOL AKSI TABEL (LOCKING) --}}
                                <td class="text-center align-middle">
                                    @if(!$isLocked)
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-warning btn-edit" 
                                                data-id="{{ $item->id }}" 
                                                data-json="{{ json_encode($item) }}" 
                                                title="Edit">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                            <form action="{{ route('budget.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus item ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-muted" title="Data Terkunci"><i class="fas fa-lock"></i></span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0 fw-bold">Belum ada rincian belanja.</p>
                                    <small>Silakan input data melalui form di atas.</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    console.log('--- Script RKA Dimulai ---');

    // 1. VAR & SELECT2
    const elManual   = $('#manualCheck');
    const elAkun     = $('#id_rekening');
    const elStandar  = $('#standar_harga_id');
    const inpUraian  = $('#input-uraian');
    const inpHarga   = $('#input-harga');
    const inpSatuan  = $('#input-satuan');
    const inpVolume  = $('#input-volume');
    const inpDasar   = $('#div-dasar-harga');
    const elTotal    = $('#total-display');

    elAkun.select2({ theme: 'bootstrap-5', width: '100%', placeholder: '🔍 Pilih Akun Belanja...', allowClear: true });
    elStandar.select2({ theme: 'bootstrap-5', width: '100%', placeholder: '⛔ Pilih Akun Belanja Dahulu...', allowClear: true });
   
    elStandar.prop('disabled', true);

   // 2. LOGIC MANUAL VS AUTO
    elManual.change(function () {

        if ($(this).is(':checked')) {
            // =====================
            // MODE MANUAL (NON SSH)
            // =====================

            // kosongkan SSH tapi JANGAN disable
            elStandar.val(null).trigger('change');

            // sembunyikan visual saja
            elStandar.addClass('d-none').prop('disabled', true);

            inpUraian.prop('readonly', false).removeClass('bg-light').val('');
            inpHarga.prop('readonly', false).removeClass('bg-light').val('');
            inpSatuan.prop('readonly', false).removeClass('bg-light').val('');
            inpDasar.removeClass('d-none');

        } else {
            // =====================
            // MODE SSH
            // =====================

            elStandar.removeClass('d-none');

            // Aktifkan hanya jika akun sudah dipilih
            if (elAkun.val()) {
                elStandar.prop('disabled', false);
            } else {
                elStandar.prop('disabled', true);
            }

            inpUraian.prop('readonly', true).addClass('bg-light').val('');
            inpHarga.prop('readonly', true).addClass('bg-light').val('');
            inpSatuan.prop('readonly', true).addClass('bg-light').val('');
            inpDasar.addClass('d-none');

        }

        elTotal.text('Rp 0');
    });


    // 3. LOGIC PILIH AKUN
    elAkun.on('change', function() {

        if(elManual.is(':checked')) return;
        
        if($(this).val()) { elStandar.prop('disabled', false).val(null).trigger('change'); }
        else { $('#hidden_kode_akun').val(''); elStandar.prop('disabled', true).val(null).trigger('change'); }
    });

    // 4. LOGIC PILIH SSH
    elStandar.off('select2:select').on('select2:select', function(e) {

        let data = e.params.data.element.dataset;
        let id = e.params.data.id;
        let kelompok = data.kelompok;
        console.log("Kelompok:", kelompok);
        console.log("ID:", id);

        // AUTO FILL SSH/SBU dasar
        inpUraian.val(data.uraian);
        inpHarga.val(data.harga).trigger('input');
        inpSatuan.val(data.satuan);
        inpVolume.focus();

        // Bersihkan parameter dulu
        $('#sbu-parameter-container').html('');

        // Jika SBU → load parameter
        if (kelompok === 'SBU' && id) {
            console.log("Load parameter untuk ID:", id);

            $.get("{{ url('/ajax/sbu-parameter') }}/" + id, function (params) {

             console.log("Response parameter:", params);    
            params.forEach(function (param) {

                    let field = `
                        <div class="mb-2">
                            <label class="form-label fw-bold small">
                                ${param.label}
                            </label>
                            <input type="number"
                                name="parameter[${param.kode}]"
                                class="form-control"
                                value="${param.default ? param.default : ''}"
                                ${param.required ? 'required' : ''}>
                        </div>
                    `;

                    $('#sbu-parameter-container').append(field);
                });

            });
        }

    });


    // 5. KALKULATOR
    function hitungTotal() {

    let vol = parseFloat(inpVolume.val()) || 0;
    let hrg = parseFloat(inpHarga.val()) || 0;

    let total = vol * hrg;

    // Jika ada parameter SBU
    $('#sbu-parameter-container input').each(function() {
        let val = parseFloat($(this).val()) || 1;
        total *= val;
    });

    elTotal.text(
        new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(total)
    );
}

// trigger event
$('#input-volume, #input-harga').on('input keyup', hitungTotal);

// trigger saat parameter berubah
$(document).on('input keyup', '#sbu-parameter-container input', hitungTotal);


    // 6. SIMPAN RINCIAN (AJAX)
$(document).off('click', '.btn-simpan-rka').on('click', '.btn-simpan-rka', function(e) {

    e.preventDefault();
        let btn = $(this);
        if (btn.prop('disabled')) return; // 🔥 anti double click
        btn.prop('disabled', true);
        
        let form = $('#form-belanja');
        
        // Validasi Frontend
        let vol = parseFloat(inpVolume.val());
        let hrg = parseFloat(inpHarga.val());
       // if(!elAkun.val()) return Swal.fire('Error', 'Pilih Akun Belanja!', 'warning');
        if(isNaN(vol) || vol <= 0) return Swal.fire('Error', 'Volume wajib diisi!', 'warning');
        if(isNaN(hrg) || hrg <= 0) return Swal.fire('Error', 'Harga masih kosong!', 'warning');

        if (!$('#id_rekening').val()) {
            Swal.fire('Error', 'Akun belanja wajib dipilih!', 'warning');
            btn.prop('disabled', false).html('Simpan');
            return;
        }

        let formData = form.serialize();

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Proses...');

        $.ajax({
            url: form.attr('action'), type: "POST", data: formData, dataType: 'json',
            success: function(res) {
                if(res.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Data Tersimpan', timer: 1000, showConfirmButton: false })
                    .then(() => location.reload());
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                    btn.prop('disabled', false).html('Simpan');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('Simpan');
                let msg = xhr.responseJSON ? (xhr.responseJSON.message || 'Error Server') : 'Error Server';
                Swal.fire('Error', msg, 'error');
            }
        });
    });

    // 7. LOGIC TOMBOL EDIT (POPULATE FORM)
    $(document).on('click', '.btn-edit', function() {
        let item = $(this).data('json');
        
        elAkun.val(item.id_rekening).trigger('change');

        if (item.standar_harga_id) {
            // === ITEM STANDAR ===
            elManual.prop('checked', false).trigger('change');

            setTimeout(() => {
                elStandar
                    .prop('disabled', false)
                    .val(item.standar_harga_id)
                    .trigger('change');
            }, 100);

        } else {
            // === ITEM NON STANDAR (MANUAL) ===
            elManual.prop('checked', true).trigger('change');

            elStandar.val(null).prop('disabled', true);

            inpUraian.val(item.uraian);
            inpSatuan.val(item.satuan);
            inpHarga.val(item.harga_satuan);
        }

        inpVolume.val(item.volume).trigger('input');

        let urlUpdate = "{{ route('budget.update', ':id') }}";
        urlUpdate = urlUpdate.replace(':id', item.id);
        $('#form-belanja').attr('action', urlUpdate);

        if($('#method-put').length === 0) {
            $('#form-belanja').append('<input type="hidden" name="_method" value="PUT" id="method-put">');
        }

        $('.btn-simpan-rka').prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Update').removeClass('btn-primary').addClass('btn-warning');
        $('#btn-batal-edit').removeClass('d-none'); 

        $('html, body').animate({ scrollTop: $("#form-belanja").offset().top - 100 }, 300);
    });

    // 8. LOGIC TOMBOL BATAL EDIT
    $('#btn-batal-edit').click(function() {
        $('#form-belanja')[0].reset();
        elAkun.val(null).trigger('change');
        elStandar.val(null).trigger('change');
        elManual.prop('checked', false).trigger('change');

        $('#form-belanja').attr('action', "{{ route('budget.store') }}");
        $('#method-put').remove(); 

        $('.btn-simpan-rka').prop('disabled', false).html('Simpan').addClass('btn-primary').removeClass('btn-warning');
        $(this).addClass('d-none');
        elTotal.text('Rp 0');
    });

});
</script>
@endpush