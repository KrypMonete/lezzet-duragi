-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 10 Şub 2026, 07:24:37
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `lezzet_duragi`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `categories`
--

INSERT INTO `categories` (`id`, `name`, `icon`) VALUES
(1, 'Başlangıçlar', 'fa-bowl-rice'),
(2, 'Ana Yemekler', 'fa-utensils'),
(3, 'Tatlılar', 'fa-ice-cream'),
(4, 'İçecekler', 'fa-coffee'),
(5, 'Ara Sıcak', 'fa-fire'),
(7, 'salata', 'fa-carrot');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `comment` text NOT NULL,
  `rating` int(11) DEFAULT 5,
  `is_approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reply` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `comment`, `rating`, `is_approved`, `created_at`, `reply`) VALUES
(26, 1, 'selam', 3, 1, '2025-12-16 11:56:40', NULL),
(27, 1, 'güzel restoran', 5, 1, '2026-02-10 06:18:28', NULL),
(28, 1, 'lezzetli!', 4, 1, '2026-02-10 06:18:45', NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `image_path`, `is_active`) VALUES
(6, 1, 'Mercimek Çorbası', 'Taze sarı mercimek ile hazırlanmış, hafif baharatlarla lezzetlendirilmiş klasik Türk çorbası. Üzerine tereyağı gezdirilmiş, limonla servis edilir.', 100.00, 'uploads/yemek_1764523834.png', 1),
(7, 1, 'Domates Çorbası', 'Fırınlanmış taze domateslerle hazırlanan, kıvamı yoğun ve aroması belirgin bir çorba. Üzeri kaşar peyniri rendesiyle tamamlanır.', 100.00, 'uploads/yemek_1764523873.png', 1),
(8, 1, 'Mantar Çorbası', 'Doğranmış kültür mantarları ve krema ile hazırlanan yumuşak içimli, nefis bir başlangıç. Mantarseverler için hafif ama doyurucu bir seçenek.', 100.00, 'uploads/yemek_1764523911.png', 1),
(9, 1, 'Ezogelin Çorbası', 'Kırmızı mercimek, bulgur ve pirinçle yapılan geleneksel Antep lezzeti. Hafif acılı, aromatik ve tam kıvamında.', 100.00, 'uploads/yemek_1764523967.png', 1),
(10, 1, 'Patates Kızartması', 'Dışı çıtır içi yumuşacık, el yapımı patates kızartması. Yanında özel sosumuzla servis edilir.', 115.00, 'uploads/yemek_1764524158.png', 1),
(11, 1, 'Sigara Böreği', 'İncecik yufkaların beyaz peynir ve maydanozla sarılıp çıtır çıtır kızartılmasıyla hazırlanan klasik başlangıç.\r\nTercihler: Sade peynirli, kaşarlı, patatesli olarak sunulabilir.', 130.00, 'uploads/yemek_1764524601.png', 1),
(12, 1, 'Çıtır Soğan Halkası', 'Altın renginde kızarmış, dışı çıtır içi yumuşak soğan halkaları. Özel baharat karışımıyla lezzeti artırılmıştır.', 60.00, 'uploads/yemek_1764524656.png', 1),
(13, 1, 'Humus', 'Nohut, tahin, limon ve sarımsakla hazırlanan, üzeri zeytinyağı ve kimyonla tamamlanan Orta Doğu klasiği. Kremamsı dokusuyla iştah açar.', 50.00, 'uploads/yemek_1764524701.png', 1),
(14, 1, 'Haydari', 'Süzme yoğurt, sarımsak ve taze otlarla hazırlanan ferahlatıcı bir meze. Yoğun kıvamı ve aromasıyla sofraya hafiflik katar', 50.00, 'uploads/yemek_1764524741.png', 1),
(15, 1, 'Acılı Ezme', 'Taze domates, biber, soğan ve maydanozun incecik kıyılmasıyla hazırlanan acılı, ekşimsi ve leziz bir meze. Et yemeklerinin yanında ideal.', 50.00, 'uploads/yemek_1764524771.png', 1),
(16, 4, 'Coca-Cola (33cl)', 'Klasik sevenler için buz gibi serinletici kola.', 50.00, 'uploads/yemek_1764525550.png', 1),
(17, 4, 'Fanta (33cl)', 'Portakal aroması ve ferahlatıcı gazoz tadıyla ideal bir seçim.', 50.00, 'uploads/yemek_1764525574.png', 1),
(18, 4, 'Sprite (33cl)', 'Limon ve misket limonu aromalı, hafif içimli gazlı içecek.', 50.00, 'uploads/yemek_1764525595.png', 1),
(19, 4, 'Ice Tea – Şeftali', 'Yumuşak şeftali aromasıyla ferahlatan soğuk çay.', 50.00, 'uploads/yemek_1764525651.png', 1),
(20, 4, 'Ice Tea – Limon', 'Hafif ekşi limon aromasıyla yaz-kış içimi kolay soğuk çay.', 50.00, 'uploads/yemek_1764525691.png', 1),
(21, 4, 'Ayran (30cl)', 'Taze yoğurttan yapılan köpüklü, serinletici ayran.', 30.00, 'uploads/yemek_1764525725.png', 1),
(22, 4, 'Soda (200ml)', 'Doğal mineralli, hafif gazlı içecek. Yemeklerden sonra ferahlatır.', 30.00, 'uploads/yemek_1764525751.png', 1),
(23, 4, 'Su (500ml)', 'Doğal kaynak suyu.', 10.00, 'uploads/yemek_1764525775.png', 1),
(24, 4, 'Türk Kahvesi', 'Taze çekilmiş kahve ile hazırlanan, köpüğü bol klasik Türk kahvesi.', 80.00, 'uploads/yemek_1764525817.png', 1),
(25, 4, 'Latte', 'Espresso ve sıcak sütün uyumundan oluşan yumuşak içimli kahve.', 120.00, 'uploads/yemek_1764525852.png', 1),
(26, 5, 'Kaşarlı Mantar', 'Fırında pişirilmiş taze mantarların üzerine erimiş kaşar peyniri eklenerek hazırlanan nefis bir ara sıcak. Hafif ama aroması güçlü.', 100.00, 'uploads/yemek_1764526051.png', 1),
(27, 5, 'Çıtır Tavuk Parçaları', 'Özel baharat karışımıyla harmanlanan tavuk parçaları, çıtır kaplamasıyla altın renginde kızartılır. Barbekü veya ranch sos ile servis edilir.', 225.00, 'uploads/yemek_1764526218.png', 1),
(28, 5, 'Paçanga Böreği', 'Pastırma, kaşar peyniri ve biberle hazırlanan iç harcın yufkaya sarılıp çıtır çıtır kızartılmasıyla yapılan klasik lezzet.', 150.00, 'uploads/yemek_1764526390.png', 1),
(29, 5, 'Karides Güveç', 'Tereyağında sotelenmiş karideslerin kaşar peyniriyle fırınlandığı, aromatik ve sıcak servis edilen nefis bir ara sıcak.', 245.00, 'uploads/yemek_1764526582.png', 1),
(30, 5, 'Mücver', 'Taze rendelenmiş kabak, dereotu ve peynirle hazırlanan hafif kızartılmış geleneksel bir lezzet. Yoğurt eşliğinde servis edilir.', 115.00, 'uploads/yemek_1764526676.png', 1),
(31, 5, 'Patates Kroket', 'Dışı çıtır, içi yumuşacık patates püresinden hazırlanan kroketler. Yanında tatlı-ekşi sos ile servis edilir.', 90.00, 'uploads/yemek_1764526744.png', 1),
(32, 5, 'Avcı Böreği', 'Kıyma, soğan ve baharatlarla hazırlanmış iç harcın yufkaya sarılıp kızartılmasıyla yapılan doyurucu bir ara sıcak.', 165.00, 'uploads/yemek_1764526814.png', 1),
(33, 5, 'Çıtır Kabak Dilimleri', 'İnce dilimlenmiş kabakların hafifçe pane edilip kızartılmasıyla hazırlanır. Yoğurtlu sarımsak sosuyla servis edilir.\r\n', 165.00, 'uploads/yemek_1764526909.png', 1),
(34, 5, 'Peynir Tabağı', 'Ezine, tulum, kaşar ve krem peynirden oluşan bir peynir seçkisi. Ara sıcak olarak hafif ve şık bir seçenek.', 245.00, 'uploads/yemek_1764527177.png', 1),
(35, 5, 'Mini Lahmacun', 'İnce hamur üzerine özel kıyma harcıyla hazırlanan mini boy lahmacunlar. Yumuşak, doyurucu ve sıcak servis edilir.', 200.00, 'uploads/yemek_1764527284.png', 1),
(36, 2, 'Izgara Tavuk', 'Özel marine sosunda dinlendirilmiş tavuk göğsü, ızgarada hafifçe pişirilerek sulu ve yumuşak bir kıvam kazanır. Yanında pilav ve salata ile servis edilir.', 285.00, 'uploads/yemek_1764527479.png', 1),
(37, 2, 'Kremalı Mantarlı Tavuk', 'Tavuk parçaları, taze mantar ve krema ile hazırlanan yoğun aromalı bir sosla buluşur. Makarnayla servis edilen doyurucu ve yumuşak içimli bir tabak.', 320.00, 'uploads/yemek_1764527514.png', 1),
(38, 2, 'Et Sote', 'Taze dana eti, soğan, biber ve domatesle birlikte sotelenerek hazırlanan klasik Türk lezzeti. Pilavla birlikte servis edilir.', 445.00, 'uploads/yemek_1764527680.png', 1),
(39, 2, 'Kaşarlı Köfte', 'Izgarada pişirilmiş dana köftesinin içi akışkan kaşarla doldurulur. Yanında patates ve mevsim yeşillikleriyle servis edilir.', 350.00, 'uploads/yemek_1764527834.png', 1),
(40, 2, 'Karışık Izgara', 'Köfte, tavuk kanat, tavuk göğsü ve sucuktan oluşan zengin bir ızgara tabağı. Bol porsiyon sevenler için ideal.', 550.00, 'uploads/yemek_1764528092.png', 1),
(41, 2, 'Mantı', 'El açması mantılar, sarımsaklı yoğurt ve tereyağında kızdırılmış kırmızı biber sos ile servis edilir. Geleneksel bir lezzeti modern sunumla buluşturur.', 245.00, 'uploads/yemek_1764528330.png', 1),
(42, 2, 'Fırında Kuzu Güveç', 'Sebzelerle birlikte ağır ağır fırında pişirilmiş kuzu eti, yumuşacık dokusu ve aromasıyla sofraya zenginlik katar.', 495.00, 'uploads/yemek_1764528437.png', 1),
(43, 2, 'Sebzeli Et Güveç', 'Dana eti; patlıcan, kabak, biber ve domatesle birlikte fırında pişirilerek hazırlanır. Lezzeti yoğun, porsiyonu doyurucudur.', 465.00, 'uploads/yemek_1764528525.png', 1),
(44, 2, 'Alfredo Makarna', 'Kremalı parmesan sosuyla buluşan fettucini makarna; tereyağı ve sarımsak aromasıyla zenginleşir. Hafif ve yumuşak bir tat arayanlar için ideal.', 215.00, 'uploads/yemek_1764528621.png', 1),
(45, 2, 'Bolonez Spagetti', 'Kıymalı domates sosu ile hazırlanan klasik İtalyan spagettisi. Hafif baharatlarla lezzeti dengelenir ve üzerine parmesan serpilir.', 215.00, 'uploads/yemek_1764528696.png', 1),
(46, 2, 'Tavuk Şinitzel', 'İnce açılmış tavuk göğsü, özel pane harcıyla kaplanarak altın renginde kızartılır. Yanında domates soslu makarna ile servis edilir.', 280.00, 'uploads/yemek_1764528813.png', 1),
(47, 2, 'Et Döner Porsiyon', 'Yaprak yaprak kesilmiş dana döner, tereyağında hafifçe ısıtılarak servis edilir. Yanında patates kızartması ve mevsim yeşillikleriyle sunulur.', 350.00, 'uploads/yemek_1764528990.png', 1),
(48, 3, 'Sufle', 'Dışı hafif kabuklu, içi akışkan çikolata dolu sıcak sufle. Üzeri pudra şekeriyle tamamlanır, isteğe bağlı vanilyalı dondurma ile servis edilebilir.', 135.00, 'uploads/yemek_1764529184.png', 1),
(49, 3, 'Kazandibi', 'Karamelize tabanı ve kadifemsi kıvamıyla hafif bir sütlü tatlı. Geleneksel lezzetleri sevenlere ideal.', 155.00, 'uploads/yemek_1764529360.png', 1),
(50, 3, 'Künefe', 'İncecik tel kadayıf arasına konan dil peyniri ile hazırlanır, şerbeti dengeli ve sıcak servis edilir. Üzerine Antep fıstığı serpilir.', 250.00, 'uploads/yemek_1764529556.png', 1),
(51, 3, 'Fırın Sütlaç', 'Üzeri hafifçe kızarmış, kıvamı tam yerinde klasik sütlaç. Hafif tatlı tercih edenler için ideal.', 160.00, 'uploads/yemek_1764529638.png', 1),
(52, 3, 'İrmik Helvası (Dondurmalı)', 'Tereyağı ile kavrulmuş irmiğin içine vanilyalı dondurma yerleştirilerek hazırlanan sıcak–soğuk dengeli şahane bir tatlı.', 150.00, 'uploads/yemek_1764529753.png', 1),
(53, 3, 'Tiramisu', 'Kahveyle ıslatılmış kedi dili bisküvileri ve mascarpone kremasıyla hazırlanan hafif İtalyan tatlısı. Üzerine kakao serpilerek servis edilir.', 215.00, 'uploads/yemek_1764529875.png', 1),
(54, 3, 'Çikolata Pasta', 'Yoğun çikolatalı pandispanya ve kremanın birleşiminden oluşan zengin ve yumuşacık bir dilim pasta.', 150.00, 'uploads/yemek_1764581125.png', 1),
(55, 3, 'Cheesecake (Frambuazlı)', 'Kremamsı cheesecake tabanı ve üzerine dökülen hafif ekşimsi frambuaz sosuyla ferahlatıcı ve dengeli bir tatlı.', 150.00, 'uploads/yemek_1764530189.png', 1),
(56, 3, 'Magnolia (Çilekli)', 'İnce bisküvi katmanları, krema ve taze çileklerle hazırlanan hafif, modern bir tatlı.', 150.00, 'uploads/yemek_1764530299.png', 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `num_people` varchar(50) DEFAULT NULL,
  `reservation_date` date NOT NULL,
  `reservation_time` time NOT NULL,
  `note` text DEFAULT NULL,
  `status` enum('bekliyor','onaylandi','iptal','tamamlandi') DEFAULT 'bekliyor',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `num_people`, `reservation_date`, `reservation_time`, `note`, `status`, `created_at`) VALUES
(11, 1, '1', '2025-12-25', '16:00:00', 'mergaba', 'bekliyor', '2025-12-02 07:12:07');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `site_title` varchar(100) DEFAULT 'Restoran Adı',
  `logo_path` varchar(255) DEFAULT NULL,
  `hero_title` varchar(255) DEFAULT 'Enfes Lezzetleri Keşfedin',
  `hero_subtitle` varchar(255) DEFAULT 'Menümüze bir göz atın',
  `hero_image` varchar(255) DEFAULT NULL,
  `about_title` varchar(255) DEFAULT 'Biz Kimiz?',
  `about_text` text DEFAULT NULL,
  `chef_title` varchar(100) DEFAULT 'Şefin Önerisi',
  `chef_dish_name` varchar(100) DEFAULT NULL,
  `chef_dish_desc` text DEFAULT NULL,
  `chef_dish_image` varchar(255) DEFAULT NULL,
  `chef_dish_price` decimal(10,2) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `working_hours` varchar(100) DEFAULT '09:00 - 23:00',
  `footer_text` varchar(255) DEFAULT NULL,
  `theme_color` varchar(20) DEFAULT '#ff6347',
  `font_family` varchar(50) DEFAULT 'Poppins',
  `bg_color` varchar(20) DEFAULT '#ffffff',
  `menu_title` varchar(255) DEFAULT 'Menümüz',
  `menu_subtitle` varchar(255) DEFAULT 'Usta şeflerimizin ellerinden çıkan eşsiz lezzetleri keşfedin.',
  `reservation_title` varchar(255) DEFAULT 'Masayı Ayırt',
  `reservation_desc` text DEFAULT NULL,
  `about_whatsapp` varchar(50) DEFAULT '905550000000',
  `announcement_text` varchar(255) DEFAULT 'Bugüne özel %20 indirim fırsatını kaçırmayın!',
  `announcement_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `settings`
--

INSERT INTO `settings` (`id`, `site_title`, `logo_path`, `hero_title`, `hero_subtitle`, `hero_image`, `about_title`, `about_text`, `chef_title`, `chef_dish_name`, `chef_dish_desc`, `chef_dish_image`, `chef_dish_price`, `address`, `phone`, `email`, `working_hours`, `footer_text`, `theme_color`, `font_family`, `bg_color`, `menu_title`, `menu_subtitle`, `reservation_title`, `reservation_desc`, `about_whatsapp`, `announcement_text`, `announcement_active`) VALUES
(1, 'Lezzet Durağı', 'uploads/1764447338_logo.png', 'Lezzet Durağı', 'Gerçek lezzetin adresi.', 'uploads/hero_1764450045_gune-baslarken.jpg', 'Biz Kimiz?', 'Lezzet Durağı Restaurant, 2025 yılında yeni ve farklı bir hizmet oluşturmak için bölgesindeki en iyi ustaları ve ürünleri bir araya getirerek mükemmeli yakalamayı hedefliyor. Bölgemizin yöresel lezzetleri ve Karadeniz\'in eşsiz balık popülasyonunu usta şeflerimizin elinden tatmalısınız. Mükemmel mutfağımızda yöremizin tatlarını mutlaka denemelisiniz. Karadeniz eşliğinde denize sıfır Giresun manzaralı, tertemiz havası ile güler yüzlü hizmetimiz sizi ve misafirlerinizi daima memnun edecektir. Bizler hem şehrimizi hem bölgemizi temsil edecek lezzetler ile sizleri daima memnun etmek için bekliyoruz.', 'Şefin Önerisi', NULL, NULL, NULL, NULL, 'Adres', '0555 555 55 55', 'busraornek@mail.com', '09:00 - 23:00', NULL, '#ff6347', 'Poppins', '#ffffff', 'Menümüz', 'Usta şeflerimizin ellerinden çıkan eşsiz lezzetleri keşfedin.', 'Masayı Ayırt', 'Özel günleriniz ve keyifli akşam yemekleriniz için şimdiden yerinizi ayırtın.', '905555555555', 'Öğrencilere özel şubemizde indirim vardır!', 0);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `slider_images`
--

CREATE TABLE `slider_images` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `display_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','personel','uye') DEFAULT 'uye',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role`, `created_at`) VALUES
(1, 'Yönetici', 'admin@admin.com', 'e10adc3949ba59abbe56e057f20f883e', '0555 555 55 55', 'admin', '2025-11-29 11:43:11');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Tablo için indeksler `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `slider_images`
--
ALTER TABLE `slider_images`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Tablo için AUTO_INCREMENT değeri `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Tablo için AUTO_INCREMENT değeri `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- Tablo için AUTO_INCREMENT değeri `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Tablo için AUTO_INCREMENT değeri `slider_images`
--
ALTER TABLE `slider_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Tablo kısıtlamaları `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
