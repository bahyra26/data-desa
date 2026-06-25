@extends('layouts.app')

@section('title', 'Edit Perangkat')

@section('content')
    <div class="page-header">
        <h1>Edit Perangkat</h1>
        <a href="{{ route('perangkat.index') }}" class="button button-secondary">Kembali</a>
    </div>

    <section class="card form-card">
        <form action="{{ route('perangkat.update', $perangkat) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="field">
                <label for="kecamatan_id">Kecamatan</label>
                <select id="kecamatan_id" name="kecamatan_id">
                    <option value="">Pilih kecamatan</option>
                    @foreach ($kecamatans as $kecamatan)
                        <option value="{{ $kecamatan->id }}" @selected(old('kecamatan_id', $perangkat->wilayah->kecamatan_id) == $kecamatan->id)>{{ $kecamatan->nama }}</option>
                    @endforeach
                </select>
                @error('kecamatan_id') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="wilayah_id">Desa</label>
                <select id="wilayah_id" name="wilayah_id" disabled>
                    <option value="">Pilih desa</option>
                </select>
                <div id="wilayah_warning" class="field-warning">Silakan pilih kecamatan terlebih dahulu sebelum memilih desa.</div>
                @error('wilayah_id') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="jabatan_perangkat_id">Jabatan</label>
                <select id="jabatan_perangkat_id" name="jabatan_perangkat_id" disabled>
                    <option value="">Pilih jabatan</option>
                </select>
                <div id="jabatan_warning" class="field-warning">Silakan pilih desa terlebih dahulu agar pilihan jabatan sesuai jenis wilayah.</div>
                <div id="jabatan_hint" class="field-hint"></div>
                @error('jabatan_perangkat_id') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="nama">Nama</label>
                <input type="text" id="nama" name="nama" value="{{ old('nama', $perangkat->nama) }}">
                @error('nama') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label>Foto Lama</label>
                @if ($perangkat->foto)
                    <img src="{{ asset('storage/'.$perangkat->foto) }}" alt="Foto {{ $perangkat->nama }}" class="photo-preview">
                @else
                    <div class="avatar avatar-placeholder">-</div>
                @endif
            </div>

            <div class="field">
                <label for="foto">Ganti Foto Profil</label>
                <input type="file" id="foto" name="foto" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                @error('foto') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="jenis_kelamin">Jenis Kelamin</label>
                <select id="jenis_kelamin" name="jenis_kelamin">
                    <option value="">Pilih jenis kelamin</option>
                    <option value="L" @selected(old('jenis_kelamin', $perangkat->jenis_kelamin) === 'L')>Laki-laki</option>
                    <option value="P" @selected(old('jenis_kelamin', $perangkat->jenis_kelamin) === 'P')>Perempuan</option>
                </select>
                @error('jenis_kelamin') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="nomor_hp">Nomor HP</label>
                <input type="text" id="nomor_hp" name="nomor_hp" value="{{ old('nomor_hp', $perangkat->nomor_hp) }}">
                @error('nomor_hp') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $perangkat->email) }}">
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="mulai_menjabat">Tanggal Mulai Menjabat</label>
                <input type="date" id="mulai_menjabat" name="mulai_menjabat" value="{{ old('mulai_menjabat', optional($perangkat->mulai_menjabat)->format('Y-m-d')) }}">
                @error('mulai_menjabat') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="akhir_menjabat">Tanggal Akhir Menjabat</label>
                <input type="date" id="akhir_menjabat" name="akhir_menjabat" value="{{ old('akhir_menjabat', optional($perangkat->akhir_menjabat)->format('Y-m-d')) }}">
                @error('akhir_menjabat') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="aktif" @selected(old('status', $perangkat->status) === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected(old('status', $perangkat->status) === 'nonaktif')>Nonaktif</option>
                    <option value="selesai" @selected(old('status', $perangkat->status) === 'selesai')>Selesai</option>
                    <option value="pensiun" @selected(old('status', $perangkat->status) === 'pensiun')>Pensiun</option>
                </select>
                @error('status') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="actions">
                <button type="submit" class="button button-primary">Update</button>
                <a href="{{ route('perangkat.index') }}" class="button button-secondary">Batal</a>
            </div>
        </form>
    </section>
@endsection

@push('scripts')
    <script>
        const wilayahs = @json($wilayahOptions);
        const jabatans = @json($jabatanOptions);
        let selectedKecamatan = '{{ old('kecamatan_id', $perangkat->wilayah->kecamatan_id) }}';
        let selectedWilayah = '{{ old('wilayah_id', $perangkat->wilayah_id) }}';
        let selectedJabatan = '{{ old('jabatan_perangkat_id', $perangkat->jabatan_perangkat_id) }}';
        const kecamatanSelect = document.getElementById('kecamatan_id');
        const wilayahSelect = document.getElementById('wilayah_id');
        const jabatanSelect = document.getElementById('jabatan_perangkat_id');
        const wilayahWarning = document.getElementById('wilayah_warning');
        const jabatanWarning = document.getElementById('jabatan_warning');
        const jabatanHint = document.getElementById('jabatan_hint');

        function refreshWilayahOptions() {
            const kecamatanId = kecamatanSelect.value;
            const pilihanWilayah = wilayahs.filter((wilayah) => String(wilayah.kecamatan_id) === String(kecamatanId));

            wilayahSelect.innerHTML = '<option value="">Pilih desa</option>';
            wilayahSelect.disabled = true;

            if (! kecamatanId) {
                wilayahWarning.textContent = 'Silakan pilih kecamatan terlebih dahulu sebelum memilih desa.';
                wilayahWarning.style.display = 'block';
                refreshJabatanOptions();
                return;
            }

            if (pilihanWilayah.length === 0) {
                wilayahWarning.textContent = 'Belum ada desa pada kecamatan ini. Silakan isi data wilayah/desa terlebih dahulu.';
                wilayahWarning.style.display = 'block';
                refreshJabatanOptions();
                return;
            }

            pilihanWilayah.forEach((wilayah) => {
                const option = new Option(`${wilayah.nama} (${wilayah.jenis})`, wilayah.id);
                wilayahSelect.add(option);
            });

            wilayahSelect.disabled = false;
            wilayahWarning.style.display = 'none';

            if (selectedWilayah && pilihanWilayah.some((wilayah) => String(wilayah.id) === String(selectedWilayah))) {
                wilayahSelect.value = selectedWilayah;
            }

            refreshJabatanOptions();
        }

        function refreshJabatanOptions() {
            const wilayah = wilayahs.find((item) => String(item.id) === String(wilayahSelect.value));
            jabatanSelect.innerHTML = '<option value="">Pilih jabatan</option>';
            jabatanSelect.disabled = true;
            jabatanHint.textContent = '';

            if (! wilayah) {
                jabatanWarning.textContent = 'Silakan pilih desa terlebih dahulu agar pilihan jabatan sesuai jenis wilayah.';
                jabatanWarning.style.display = 'block';
                return;
            }

            const pilihanJabatan = jabatans.filter((jabatan) => jabatan.scope === 'umum' || jabatan.scope === 'desa');

            pilihanJabatan.forEach((jabatan) => {
                const option = new Option(jabatan.nama, jabatan.id);
                jabatanSelect.add(option);
            });

            jabatanSelect.disabled = false;
            jabatanWarning.style.display = 'none';
            jabatanHint.textContent = 'Pilihan jabatan disesuaikan untuk perangkat desa.';

            if (selectedJabatan && pilihanJabatan.some((jabatan) => String(jabatan.id) === String(selectedJabatan))) {
                jabatanSelect.value = selectedJabatan;
            }
        }

        kecamatanSelect.addEventListener('change', () => {
            selectedWilayah = '';
            selectedJabatan = '';
            refreshWilayahOptions();
        });

        wilayahSelect.addEventListener('change', () => {
            selectedJabatan = '';
            refreshJabatanOptions();
        });

        if (selectedKecamatan) {
            kecamatanSelect.value = selectedKecamatan;
        }

        refreshWilayahOptions();
    </script>
@endpush
