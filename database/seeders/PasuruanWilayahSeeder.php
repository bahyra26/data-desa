<?php

namespace Database\Seeders;

use App\Models\Kecamatan;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;

class PasuruanWilayahSeeder extends Seeder
{
    /**
     * Seed the Kabupaten Pasuruan master wilayah data.
     */
    public function run(): void
    {
        $kecamatans = [
            [
                'kode_kemendagri' => '35.14.14',
                'nama' => 'Bangil',
                'kode_pos' => '67153',
                'desa' => ['Manaruwi', 'Masangan', 'Raci', 'Tambakan'],
            ],
            [
                'kode_kemendagri' => '35.14.13',
                'nama' => 'Beji',
                'kode_pos' => '67154',
                'desa' => ['Baujeng', 'Beji', 'Cangkringmalang', 'Gajahbendo', 'Gununggangsir', 'Gunungsari', 'Kedungboto', 'Kedungringin', 'Kenep', 'Ngembe', 'Sidowayah', 'Wonokoyo'],
            ],
            [
                'kode_kemendagri' => '35.14.12',
                'nama' => 'Gempol',
                'kode_pos' => '67155',
                'desa' => ['Bulusari', 'Carat', 'Gempol', 'Jerukpurut', 'Karangrejo', 'Kejapanan', 'Kepulungan', 'Legok', 'Ngerong', 'Randupitu', 'Sumbersuko', 'Watukosek', 'Winong', 'Wonosari', 'Wonosunyo'],
            ],
            [
                'kode_kemendagri' => '35.14.18',
                'nama' => 'Gondang Wetan',
                'kode_pos' => '67174',
                'desa' => ['Bajangan', 'Bayeman', 'Brambang', 'Gayam', 'Gondangrejo', 'Grogol', 'Kalirejo', 'Karangsentul', 'Keboncandi', 'Kersikan', 'Lajuk', 'Pateguhan', 'Pekangkungan', 'Ranggeh', 'Sekarputih', 'Tebas', 'Tenggilisrejo', 'Wonojati', 'Wonosari'],
            ],
            [
                'kode_kemendagri' => '35.14.20',
                'nama' => 'Grati',
                'kode_pos' => '67184',
                'desa' => ['Cukurgondang', 'Kalipang', 'Kambinganrejo', 'Karangkliwon', 'Karanglo', 'Kebonrejo', 'Kedawung Kulon', 'Kedawung Wetan', 'Plososari', 'Ranuklindungan', 'Rebalas', 'Sumberagung', 'Sumberdawesari', 'Trewung'],
            ],
            [
                'kode_kemendagri' => '35.14.06',
                'nama' => 'Kejayan',
                'kode_pos' => '67172',
                'desa' => ['Ambal Ambil', 'Benerwojo', 'Cubanjoyo', 'Kedemungan', 'Kedungpengaron', 'Kepuh', 'Ketangirejo', 'Klangrong', 'Klinter', 'Kurung', 'Linggo', 'Lorokan', 'Luwuk', 'Oro-Oro Pule', 'Pacarkeling', 'Patebon', 'Randugong', 'Sladi', 'Sumberbanteng', 'Sumbersuko', 'Tanggulangin', 'Tundosoro', 'Wangkalwetan', 'Wrati'],
            ],
            [
                'kode_kemendagri' => '35.14.16',
                'nama' => 'Kraton',
                'kode_pos' => '67151',
                'desa' => ['Asem Kandang', 'Bendungan', 'Curahdukuh', 'Dhompo', 'Gambirkuning', 'Gerongan', 'Jeruk', 'Kalirejo', 'Karanganyar', 'Kebotohan', 'Klampisrejo', 'Kraton', 'Mulyorejo', 'Ngabar', 'Ngempit', 'Plinggisan', 'Pukul', 'Pulokerto', 'Rejosari', 'Selotambak', 'Semare', 'Sidogiri', 'Slambrit', 'Tambakrejo', 'Tambaksari'],
            ],
            [
                'kode_kemendagri' => '35.14.22',
                'nama' => 'Lekok',
                'kode_pos' => '67186',
                'desa' => ['Alastlogo', 'Balunganyar', 'Branang', 'Gejugjati', 'Jatirejo', 'Pasinan', 'Rowogempol', 'Semedusari', 'Tambaklekok', 'Tampung', 'Wates'],
            ],
            [
                'kode_kemendagri' => '35.14.04',
                'nama' => 'Lumbang',
                'kode_pos' => '67183',
                'desa' => ['Banjarimbo', 'Bulukandang', 'Cukurguling', 'Karang Asem', 'Karang Jati', 'Kronto', 'Lumbang', 'Pancur', 'Panditan', 'Watu Lumbung', 'Welulang', 'Wonorejo'],
            ],
            [
                'kode_kemendagri' => '35.14.21',
                'nama' => 'Nguling',
                'kode_pos' => '67185',
                'desa' => ['Dadanggendis', 'Kapasan', 'Kedawang', 'Mlaten', 'Nguling', 'Penunggul', 'Randuati', 'Sebalong', 'Sedarum', 'Senganom', 'Sudimulyo', 'Sumberanyar', 'Watestani', 'Watuprapat', 'Wotgalih'],
            ],
            [
                'kode_kemendagri' => '35.14.11',
                'nama' => 'Pandaan',
                'kode_pos' => '67156',
                'desa' => ['Banjarsari', 'Banjar Kejen', 'Durensewu', 'Karang Jati', 'Kebon Waris', 'Kemiri Sewu', 'Nogosari', 'Plintahan', 'Sebani', 'Sumber Gedang', 'Sumber Rejo', 'Tawang Rejo', 'Tunggul Wulung', 'Wedoro'],
            ],
            [
                'kode_kemendagri' => '35.14.05',
                'nama' => 'Pasrepan',
                'kode_pos' => '67175',
                'desa' => ['Ampelsari', 'Cengkrong', 'Galih', 'Jogorepuh', 'Klakah', 'Lemahbang', 'Mangguan', 'Ngantungan', 'Pasrepan', 'Petung', 'Pohgading', 'Pohgedang', 'Rejosalam', 'Sapulante', 'Sibon', 'Tambakrejo', 'Tempuran'],
            ],
            [
                'kode_kemendagri' => '35.14.10',
                'nama' => 'Prigen',
                'kode_pos' => '67157',
                'desa' => ['Bulukandang', 'Candi Wates', 'Dayurejo', 'Gambiran', 'Jatiarjo', 'Ketanireng', 'Lumbangrejo', 'Sekarjoho', 'Sukolilo', 'Sukoreno', 'Watuagung'],
            ],
            [
                'kode_kemendagri' => '35.14.17',
                'nama' => 'Pohjentrek',
                'kode_pos' => '67171',
                'desa' => ['Logowok', 'Parasrejo', 'Pleret', 'Sukorejo', 'Sungi Kulon', 'Sungi Wetan', 'Susukanrejo', 'Tidu', 'Warungdowo'],
            ],
            [
                'kode_kemendagri' => '35.14.01',
                'nama' => 'Purwodadi',
                'kode_pos' => '67163',
                'desa' => ['Capang', 'Cowek', 'Dawuhan Sengon', 'Gajahrejo', 'Gerbo', 'Jati Sari', 'Lebak Rejo', 'Parerejo', 'Pucang Sari', 'Purwodadi', 'Semut', 'Sentul', 'Tambak Sari'],
            ],
            [
                'kode_kemendagri' => '35.14.08',
                'nama' => 'Purwosari',
                'kode_pos' => '67162',
                'desa' => ['Bakalan', 'Cendono', 'Karangrejo', 'Kayoman', 'Kertosari', 'Martopuro', 'Pager', 'Pucang Sari', 'Sekarmojo', 'Sengonagung', 'Sukodermo', 'Sumberrejo', 'Sumbersuko', 'Tejowangi'],
            ],
            [
                'kode_kemendagri' => '35.14.03',
                'nama' => 'Puspo',
                'kode_pos' => '67176',
                'desa' => ['Jangjangwulung', 'Jimbaran', 'Keduwung', 'Kemiri', 'Palang Sari', 'Puspo', 'Pusung Malang'],
            ],
            [
                'kode_kemendagri' => '35.14.23',
                'nama' => 'Rejoso',
                'kode_pos' => '67181',
                'desa' => ['Arjosari', 'Jarangan', 'Karangpandan', 'Kawisrejo', 'Kedungbako', 'Kemantrenrejo', 'Ketegan', 'Manikrejo', 'Pandanrejo', 'Patuguran', 'Rejoso Kidul', 'Rejoso Lor', 'Sadengrejo', 'Sambirejo', 'Segoropuro', 'Toyaning'],
            ],
            [
                'kode_kemendagri' => '35.14.15',
                'nama' => 'Rembang',
                'kode_pos' => '67152',
                'desa' => ['Genengwaru', 'Kalisat', 'Kanigoro', 'Kedungbanteng', 'Krengih', 'Mojoparon', 'Orobulu', 'Oro-Oro Ombo Kulon', 'Oro-Oro Ombo Wetan', 'Pajaran', 'Pandean', 'Pejangkungan', 'Pekoren', 'Rembang', 'Siyar', 'Sumberglagah', 'Tampung'],
            ],
            [
                'kode_kemendagri' => '35.14.09',
                'nama' => 'Sukorejo',
                'kode_pos' => '67161',
                'desa' => ['Candibinangun', 'Curahrejo', 'Dukuhsari', 'Glagahsari', 'Gunting', 'Kalirejo', 'Karangsono', 'Kenduruan', 'Lecari', 'Lemahbang', 'Mojotengah', 'Ngadimulyo', 'Pakukerto', 'Sebandung', 'Sukorame', 'Sukorejo', 'Suwayuwo', 'Tanjungarum', 'Wonokerto'],
            ],
            [
                'kode_kemendagri' => '35.14.24',
                'nama' => 'Tosari',
                'kode_pos' => '67177',
                'desa' => ['Baledono', 'Kandangan', 'Mororejo', 'Ngadiwono', 'Podokoyo', 'Sedaeng', 'Tosari', 'Wonokitri'],
            ],
            [
                'kode_kemendagri' => '35.14.02',
                'nama' => 'Tutur',
                'kode_pos' => '67165',
                'desa' => ['Andono Sari', 'Blarang', 'Gendro', 'Kali Pucang', 'Kayu Kebek', 'Ngadirejo', 'Ngembal', 'Pungging', 'Sumberpitu', 'Tlogosari', 'Tutur', 'Wonosari'],
            ],
            [
                'kode_kemendagri' => '35.14.19',
                'nama' => 'Winongan',
                'kode_pos' => '67182',
                'desa' => ['Bandaran', 'Gading', 'Jeladri', 'Kandung', 'Karang Tengah', 'Kedung Rejo', 'Lebak', 'Mendalan', 'Menyarik', 'Minggir', 'Penataan', 'Prodo', 'Sidepan', 'Sruwi', 'Sumber Rejo', 'Umbulan', 'Winongan Kidul', 'Winongan Lor'],
            ],
            [
                'kode_kemendagri' => '35.14.07',
                'nama' => 'Wonorejo',
                'kode_pos' => '67173',
                'desa' => ['Cobanblimbing', 'Jatigunting', 'Karangasem', 'Karangjatianyar', 'Karangmenggah', 'Karangsono', 'Kendangdukuh', 'Kluwut', 'Lebaksari', 'Pakijangan', 'Rebono', 'Sambisirah', 'Tamansari', 'Wonorejo', 'Wonosari'],
            ],
        ];

        foreach ($kecamatans as $dataKecamatan) {
            $kecamatan = Kecamatan::updateOrCreate([
                'kode_kemendagri' => $dataKecamatan['kode_kemendagri'],
            ], [
                'nama' => $dataKecamatan['nama'],
                'kode_pos' => $dataKecamatan['kode_pos'],
            ]);

            foreach ($dataKecamatan['desa'] as $namaWilayah) {
                Wilayah::updateOrCreate([
                    'kecamatan_id' => $kecamatan->id,
                    'nama' => $namaWilayah,
                ], [
                    'jenis' => 'desa',
                ]);
            }
        }
    }
}
