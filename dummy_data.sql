-- Isi Tabel Gender
INSERT INTO gender (id, name) VALUES
(1, 'Laki-laki'),
(2, 'Perempuan');

-- Isi Tabel Province
INSERT INTO province (id, name) VALUES
(1, 'DKI Jakarta'),
(2, 'Jawa Barat');

-- Isi Tabel City
INSERT INTO city (id, province_id, name) VALUES
(1, 1, 'Jakarta Selatan'),
(2, 1, 'Jakarta Timur'),
(3, 1, 'Jakarta Pusat'),
(4, 1, 'Jakarta Barat'),
(5, 1, 'Jakarta Utara'),
(6, 2, 'Bogor'),
(7, 2, 'Depok'),
(8, 2, 'Bekasi'),
(9, 2, 'Karawang'),
(10, 2, 'Purwakarta'),
(11, 2, 'Subang'),
(12, 2, 'Indramayu'),
(13, 2, 'Sumedang'),
(14, 2, 'Bandung'),
(15, 2, 'Cimahi'),
(16, 2, 'Bandung Barat'),
(17, 2, 'Pangandaran'),
(18, 2, 'Garut'),
(19, 2, 'Tasikmalaya'),
(20, 2, 'Ciamis'),
(21, 2, 'Kuningan'),
(22, 2, 'Cirebon'),
(23, 2, 'Majalengka'),
(24, 2, 'Kota Bogor'),
(25, 2, 'Kota Sukabumi'),
(26, 2, 'Kota Bandung'),
(27, 2, 'Kota Cirebon'),
(28, 2, 'Kota Bekasi'),
(29, 2, 'Kota Depok'),
(30, 2, 'Kota Cimahi'),
(31, 2, 'Kota Tasikmalaya'),
(32, 2, 'Kota Banjar');

-- Isi Tabel District
INSERT INTO district (id, city_id, name) VALUES
(1, 1, 'Kebayoran Baru'),
(2, 1, 'Kebayoran Lama'),
(3, 1, 'Pesanggrahan'),
(4, 1, 'Cilandak'),
(5, 1, 'Pasar Minggu'),
(6, 1, 'Jagakarsa'),
(7, 1, 'Mampang Prapatan'),
(8, 1, 'Pancoran'),
(9, 1, 'Tebet'),
(10, 1, 'Setiabudi'),
(11, 1, 'Kemayoran'),
(12, 1, 'Senen'),
(13, 1, 'Cempaka Putih'),
(14, 1, 'Menteng'),
(15, 1, 'Tanah Abang'),
(16, 1, 'Johar Baru'),
(17, 1, 'Penjaringan'),
(18, 1, 'Pademangan'),
(19, 1, 'Tanjung Priok'),
(20, 1, 'Koja'),
(21, 1, 'Cilincing'),
(22, 1, 'Kelapa Gading'),
(23, 1, 'Cakung'),
(24, 1, 'Pulo Gadung'),
(25, 1, 'Matraman'),
(26, 1, 'Jatinegara'),
(27, 1, 'Duren Sawit'),
(28, 1, 'Makasar'),
(29, 1, 'Cipayung'),
(30, 1, 'Kramat Jati'),
(31, 1, 'Pasar Rebo'),
(32, 1, 'Ciracas'),
(33, 1, 'Cipayung'),
(34, 1, 'Menteng'),
(35, 1, 'Setu'),
(36, 1, 'Cimanggis'),
(37, 1, 'Beji'),
(38, 1, 'Limo'),
(39, 1, 'Cinere'),
(40, 1, 'Sawangan'),
(41, 1, 'Pancoran Mas');

-- Isi Tabel Sub District
INSERT INTO sub_district (id, district_id, name) VALUES
(1, 1, 'Kramat Pela'),
(2, 1, 'Gandaria Utara'),
(3, 1, 'Gandaria Selatan'),
(4, 1, 'Cipete Utara'),
(5, 1, 'Cipete Selatan'),
(6, 1, 'Pulo'),
(7, 1, 'Petogogan'),
(8, 1, 'Melawai'),
(9, 1, 'Gunung'),
(10, 1, 'Kebayoran Lama Utara'),
(11, 1, 'Kebayoran Lama Selatan'),
(12, 1, 'Cipulir'),
(13, 1, 'Grogol Selatan'),
(14, 1, 'Grogol Utara'),
(15, 1, 'Kebayoran Baru'),
(16, 1, 'Senayan'),
(17, 1, 'Kuningan Barat'),
(18, 1, 'Karet Kuningan'),
(19, 1, 'Karet Semanggi'),
(20, 1, 'Karet'),
(21, 1, 'Menteng Atas'),
(22, 1, 'Setiabudi'),
(23, 1, 'Karet Kuningan'),
(24, 1, 'Karet Semanggi'),
(25, 1, 'Karet'),
(26, 1, 'Menteng Atas'),
(27, 1, 'Setiabudi'),
(28, 1, 'Karet Kuningan'),
(29, 1, 'Karet Semanggi'),
(30, 1, 'Karet'),
(31, 1, 'Menteng Atas'),
(32, 1, 'Setiabudi'),
(33, 1, 'Karet Kuningan'),
(34, 1, 'Karet Semanggi'),
(35, 1, 'Karet'),
(36, 1, 'Menteng Atas'),
(37, 1, 'Setiabudi'),
(38, 1, 'Karet Kuningan'),
(39, 1, 'Karet Semanggi');

-- Isi Tabel Post Code
INSERT INTO post_code (id, province, city, district, sub_district, postal_code) VALUES
(1, 'DKI Jakarta', 'Jakarta Selatan', 'Kebayoran Baru', 'Kramat Pela', '12130'),
(2, 'DKI Jakarta', 'Jakarta Selatan', 'Kebayoran Baru', 'Gandaria Utara', '12140'),
(3, 'DKI Jakarta', 'Jakarta Selatan', 'Kebayoran Baru', 'Gandaria Selatan', '12140'),
(4, 'DKI Jakarta', 'Jakarta Selatan', 'Kebayoran Baru', 'Cipete Utara', '12150'),
(5, 'DKI Jakarta', 'Jakarta Selatan', 'Kebayoran Baru', 'Cipete Selatan', '12150'),
(6, 'DKI Jakarta', 'Jakarta Selatan', 'Kebayoran Baru', 'Pulo', '12160'),
(7, 'DKI Jakarta', 'Jakarta Selatan', 'Kebayoran Baru', 'Petogogan', '12170'),
(8, 'DKI Jakarta', 'Jakarta Selatan', 'Kebayoran Baru', 'Melawai', '12160'),
(9, 'DKI Jakarta', 'Jakarta Selatan', 'Kebayoran Baru', 'Gunung', '12120'),
(10, 'DKI Jakarta', 'Jakarta Selatan', 'Kebayoran Lama', 'Kebayoran Lama Utara', '12210'),
(11, 'DKI Jakarta', 'Jakarta Selatan', 'Kebayoran Lama', 'Kebayoran Lama Selatan', '12210'),
(12, 'DKI Jakarta', 'Jakarta Selatan', 'Kebayoran Lama', 'Cipulir', '12230'),
(13, 'DKI Jakarta', 'Jakarta Selatan', 'Kebayoran Lama', 'Grogol Selatan', '12220'),
(14, 'DKI Jakarta', 'Jakarta Selatan', 'Kebayoran Lama', 'Grogol Utara', '12210'),
(15, 'DKI Jakarta', 'Jakarta Selatan', 'Kebayoran Baru', 'Senayan', '12190');

-- Isi Tabel Type of Home
INSERT INTO type_of_home (id, name) VALUES
(1, 'Rumah Tinggal'),
(2, 'Rumah Usaha'),
(3, 'Rumah Sewa');

-- Isi Tabel Type of Livestock
INSERT INTO type_of_livestock (id, name) VALUES
(1, 'Sapi'),
(2, 'Kambing');

-- Isi Tabel Breed of Livestock
INSERT INTO breed_of_livestock (id, name) VALUES
(1, 'Bali'),
(2, 'Madura');

-- Isi Tabel Maintenance
INSERT INTO maintenance (id, name) VALUES
(1, 'Kandang'),
(2, 'Gembala'),
(3, 'Campuran');

-- Isi Tabel Source
INSERT INTO source (id, name) VALUES
(1, 'Sejak Lahir'),
(2, 'Bantuan Pemerintah'),
(3, 'Beli'),
(4, 'Beli dari Dalam Kelompok'),
(5, 'Beli dari Luar Kelompok'),
(6, 'Inseminasi Buatan'),
(7, 'Kawin Alam'),
(8, 'Tidak Tau');


-- Isi Tabel Ownership Status
INSERT INTO ownership_status (id, name) VALUES
(1, 'Milik Sendiri'),
(2, 'Milik Kelompok'),
(3, 'Titipan');

-- Isi Tabel Reproduction
INSERT INTO reproduction (id, name) VALUES
(1, 'Tidak Bunting'),
(2, 'Bunting < 1 Bulan'),
(3, 'Bunting 1 Bulan'),
(4, 'Bunting 2 Bulan'),
(5, 'Bunting 3 Bulan'),
(6, 'Bunting 4 Bulan'),
(7, 'Bunting 5 Bulan'),
(8, 'Bunting 6 Bulan'),
(9, 'Bunting 7 Bulan'),
(10, 'Bunting 8 Bulan'),
(11, 'Bunting 9 Bulan'),
(12, 'Bunting 10 Bulan'),
(13, 'Bunting 11 Bulan'),
(14, 'Bunting 12 Bulan'),
(15, 'Bunting 13 Bulan'),
(16, 'Bunting 14 Bulan'),
(17, 'Bunting 15 Bulan');

-- Isi Tabel Purpose
INSERT INTO purpose (id, name) VALUES
(1, 'Indukan'),
(2, 'Penggemukan'),
(3, 'Tabungan'),
(4, 'Belum Tau');

-- Isi Tabel User Role
INSERT IGNORE INTO user_role (id, name) VALUES
(1, 'Peternak'),
(2, 'Admin'),
(3, 'Customer');