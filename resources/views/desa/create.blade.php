@extends('layouts.app')

@section('title', 'Tambah Desa')

@section('content')
    <div class="page-header">
        <h1>Tambah Desa</h1>
        <a href="{{ route('desa.index') }}" class="button button-secondary">Kembali</a>
    </div>

    <section class="card form-card">
        <form action="{{ route('desa.store') }}" method="POST">
            @csrf

            <div class="field">
                <label for="kecamatan_id">Kecamatan</label>
                <select id="kecamatan_id" name="kecamatan_id">
                    <option value="">Pilih kecamatan</option>
                    @foreach ($kecamatans as $kecamatan)
                        <option value="{{ $kecamatan->id }}" @selected(old('kecamatan_id') == $kecamatan->id)>{{ $kecamatan->nama }}</option>
                    @endforeach
                </select>
                @error('kecamatan_id') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="wilayah_id">Desa</label>
                <select id="wilayah_id" name="wilayah_id">
                    <option value="">Pilih desa</option>
                </select>
                @error('wilayah_id') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="kepala_desa">Kepala Desa</label>
                <input type="text" id="kepala_desa" name="kepala_desa" value="{{ old('kepala_desa') }}">
                @error('kepala_desa') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="alamat_kantor">Alamat Kantor</label>
                <textarea id="alamat_kantor" name="alamat_kantor" placeholder="Contoh: Kantor Desa ..., Kecamatan ..., Kabupaten Pasuruan">{{ old('alamat_kantor') }}</textarea>
                <div class="help-text">Lokasi akan dibuka dari alamat kantor ini lewat Google Maps.</div>
                @error('alamat_kantor') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="jumlah_penduduk">Jumlah Penduduk</label>
                <input type="number" id="jumlah_penduduk" name="jumlah_penduduk" min="0" value="{{ old('jumlah_penduduk') }}">
                @error('jumlah_penduduk') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="luas_wilayah">Luas Wilayah (km2)</label>
                <input type="number" step="0.01" id="luas_wilayah" name="luas_wilayah" min="0" value="{{ old('luas_wilayah') }}">
                @error('luas_wilayah') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="actions">
                <button type="submit" class="button button-primary">Simpan</button>
                <a href="{{ route('desa.index') }}" class="button button-secondary">Batal</a>
            </div>
        </form>
    </section>
@endsection

@push('scripts')
    <script>
        const wilayahs = @json($wilayahOptions);
        const selectedKecamatan = '{{ old('kecamatan_id') }}';
        const selectedWilayah = '{{ old('wilayah_id') }}';
        const kecamatanSelect = document.getElementById('kecamatan_id');
        const wilayahSelect = document.getElementById('wilayah_id');

        function refreshWilayahOptions() {
            const kecamatanId = kecamatanSelect.value;

            wilayahSelect.innerHTML = '<option value="">Pilih desa</option>';
            wilayahSelect.disabled = true;

            if (! kecamatanId) return;

            const filtered = wilayahs.filter((w) => String(w.kecamatan_id) === String(kecamatanId));
            filtered.forEach((w) => {
                const opt = document.createElement('option');
                opt.value = w.id;
                opt.textContent = `${w.nama} (${w.jenis})`;
                wilayahSelect.appendChild(opt);
            });

            wilayahSelect.disabled = false;

            if (selectedWilayah && filtered.some((w) => String(w.id) === String(selectedWilayah))) {
                wilayahSelect.value = selectedWilayah;
            }
        }

        kecamatanSelect.addEventListener('change', refreshWilayahOptions);

        if (selectedKecamatan) {
            kecamatanSelect.value = selectedKecamatan;
            refreshWilayahOptions();
        }
    </script>
@endpush
