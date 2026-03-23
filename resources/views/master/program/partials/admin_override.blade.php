{{-- ========================================================= --}}
{{-- ADMIN OVERRIDE — PROGRAM / ANGGARAN                        --}}
{{-- HANYA UNTUK ADMIN                                         --}}
{{-- ========================================================= --}}

{{-- @if(auth()->check() && auth()->user()->role === 'admin') --}}

@can('force', $anggaran)

<div class="card border-danger mt-4">
    <div class="card-header bg-danger text-white">
        <strong>⚠ ADMIN OVERRIDE STATUS RKA</strong>
    </div>

    <div class="card-body">
        <p class="text-danger mb-3">
            Tindakan ini <strong>mengabaikan workflow normal</strong> dan
            <strong>akan tercatat di audit log</strong>.
            Gunakan hanya dalam kondisi khusus.
        </p>

        <form method="POST"
              action="{{ route('admin.anggaran.force-status', $anggaran) }}"
              onsubmit="return confirm('Yakin melakukan ADMIN OVERRIDE? Aksi ini tercatat di audit.');">
            @csrf

            {{-- STATUS BARU --}}
            <div class="mb-3">
                <label class="form-label fw-bold">
                    Status Baru <span class="text-danger">*</span>
                </label>

                <select name="status"
                        class="form-select @error('status') is-invalid @enderror"
                        required>
                    <option value="">-- pilih status --</option>

                    <option value="Draft" @selected(old('status') === 'Draft')>
                        Draft
                    </option>

                    <option value="Diajukan" @selected(old('status') === 'Diajukan')>
                        Diajukan
                    </option>

                    <option value="Divalidasi" @selected(old('status') === 'Divalidasi')>
                        Divalidasi
                    </option>

                    <option value="Disetujui" @selected(old('status') === 'Disetujui')>
                        Disetujui
                    </option>

                    <option value="Ditolak" @selected(old('status') === 'Ditolak')>
                        Ditolak
                    </option>
                </select>

                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- ALASAN OVERRIDE --}}
            <div class="mb-3">
                <label class="form-label fw-bold">
                    Alasan Override <span class="text-danger">*</span>
                </label>

                <textarea name="reason"
                          rows="3"
                          class="form-control @error('reason') is-invalid @enderror"
                          placeholder="Wajib diisi. Minimal 10 karakter."
                          required>{{ old('reason') }}</textarea>

                @error('reason')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- ACTION --}}
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-danger">
                    🔐 ADMIN OVERRIDE
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

{{-- @endif --}}
