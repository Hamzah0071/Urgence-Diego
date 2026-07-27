# mon sql 
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 26 juil. 2026 à 14:09
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `urgences_antsiranana`
--

-- --------------------------------------------------------

--
-- Structure de la table `article`
--

CREATE TABLE `article` (
  `id_article` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `contenu` text NOT NULL,
  `lien_source` varchar(500) DEFAULT NULL,
  `id_auteur` int(11) DEFAULT NULL,
  `id_source` int(11) DEFAULT NULL,
  `statut` enum('brouillon','publie','archive') NOT NULL DEFAULT 'publie',
  `date_publication` timestamp NOT NULL DEFAULT current_timestamp(),
  `derniere_modification` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `article`
--

INSERT INTO `article` (`id_article`, `titre`, `contenu`, `lien_source`, `id_auteur`, `id_source`, `statut`, `date_publication`, `derniere_modification`) VALUES
(1, 'SOAMIAVY 17 JOLAY 2026', '<div><img src=\"https://scontent-sea5-1.xx.fbcdn.net/v/t15.5256-10/750459364_1319369316602565_812508501711479115_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=107&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=R7jI9N7zruYQ7kNvwE8mafg&_nc_oc=AdrqqnYqqGtax6pT0GZn55aoekXxKnub4KR4L7G_njy82-kfTnGq-kP3TmA6cKH6gJw&_nc_zt=23&_nc_ht=scontent-sea5-1.xx&_nc_gid=kc4LqY0hjlBier-xvkJ80g&_nc_ss=7b289&oh=00_AQBGfIrooquX0g9dcUer02UvEwYix1ABJKgTSD1NvtHXIg&oe=6A5FC6F6\" style=\"width: 100%;\" /><div>SOAMIAVY 17 JOLAY 2026</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/2211774073009750/', NULL, 2, 'publie', '2026-07-17 02:02:15', '2026-07-17 07:57:59'),
(2, 'VAOVAO AN-TSARY 19H30 - 16 JOLAY 2026', '<div><img src=\"https://scontent-sea5-1.xx.fbcdn.net/v/t15.5256-10/749952836_1010470611777475_7956773578778809967_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=108&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=8sxdB71ifscQ7kNvwELfp-k&_nc_oc=AdqcZ6JXn7TgiDXUUKoUoWHhVM_36MHwwDe3WH4fz84EfkAS0_GxuJvpAD8tpoOdPXQ&_nc_zt=23&_nc_ht=scontent-sea5-1.xx&_nc_gid=kc4LqY0hjlBier-xvkJ80g&_nc_ss=7b289&oh=00_AQBo4SrlMM8Jas9PmNVg2WHpuUqcRp4ayj1n9M-fatAb0A&oe=6A5FAE87\" style=\"width: 100%;\" /><div>VAOVAO AN-TSARY 19H30 - 16 JOLAY 2026</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/2125075735099728/', NULL, 2, 'publie', '2026-07-16 15:29:58', '2026-07-17 07:57:59'),
(3, 'RUSSAMADA - 16 JUILLET 2026', '<div><img src=\"https://scontent-sea1-1.xx.fbcdn.net/v/t15.5256-10/748978317_1034402165906769_7388227845723774352_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=101&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=kHOOzJYnNKkQ7kNvwFr73mJ&_nc_oc=AdpEk1XeR45bmX6nPbOcSxkqC0gsqK3A2zkK4qKvjdc9ZK-m_kyAxBJH8kWA_YRJYjc&_nc_zt=23&_nc_ht=scontent-sea1-1.xx&_nc_gid=kc4LqY0hjlBier-xvkJ80g&_nc_ss=7b289&oh=00_AQDMcmsh5Ezs3KF0eW3v9YFaVDtd-a816MYEeh1A9QB-XA&oe=6A5FAED3\" style=\"width: 100%;\" /><div>RUSSAMADA - 16 JUILLET 2026</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/1058150207111344/', NULL, 2, 'publie', '2026-07-16 15:00:44', '2026-07-17 07:57:59'),
(4, 'KAROKA SY KETRIKA  16 JOLAY 2026', '<div><img src=\"https://scontent-sea5-1.xx.fbcdn.net/v/t15.5256-10/743876838_1072238651863398_2902852999856386320_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=105&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=kTi9DgsZFvoQ7kNvwFEzcrW&_nc_oc=Adr8FigsRY5GTxMl0XiMrm4mhgCOdiwB7u5R-9wYaR7xA2Lv9C82RqKM9DBgV_CXlX4&_nc_zt=23&_nc_ht=scontent-sea5-1.xx&_nc_gid=kc4LqY0hjlBier-xvkJ80g&_nc_ss=7b289&oh=00_AQBuL6JscKm8RpKzVmLRGZPkoGWqOHdl-kfQ1FipfwBh0w&oe=6A5FBE82\" style=\"width: 100%;\" /><div>KAROKA SY KETRIKA  16 JOLAY 2026</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/3453727878142731/', NULL, 2, 'publie', '2026-07-16 14:00:12', '2026-07-17 07:57:59'),
(5, 'VAOVAO AN-TSARY 1 ORA ATOANDRO', '<div><img src=\"https://scontent-sea5-1.xx.fbcdn.net/v/t15.5256-10/730040200_1773590923811081_995320107852927954_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=105&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=1iBSFydzVlYQ7kNvwEJCgY3&_nc_oc=AdofJAElrtf6Er-TJM7DsB64PyaVBOHL5Om-QQxEE63bzrIiBpi0C6ajju1A9XP3nrE&_nc_zt=23&_nc_ht=scontent-sea5-1.xx&_nc_gid=kc4LqY0hjlBier-xvkJ80g&_nc_ss=7b289&oh=00_AQAgIddRsFn5j1OUou4HwotKlHeDUc11FyZ60oYCpz_yhA&oe=6A5FA045\" style=\"width: 100%;\" /><div>VAOVAO AN-TSARY 1 ORA ATOANDRO</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/1016811340944581/', NULL, 2, 'publie', '2026-07-16 08:59:34', '2026-07-17 07:57:59'),
(11, 'SOAMIAVY 21 JUILLET 2026', '<div><img src=\"https://scontent-ams2-1.xx.fbcdn.net/v/t15.5256-10/752708189_1047512124881720_7050477248133087371_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=103&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=BDzhIWtrScgQ7kNvwFB8U0t&_nc_oc=AdoySx-RHzJxIZR3_T6csXivUFwuK7EmvdUCnjzFI17d4fR8pSbdliaNnQYOyM3Tgt4&_nc_zt=23&_nc_ht=scontent-ams2-1.xx&_nc_gid=T7PlZya0Kgw6zCUClDpo5Q&_nc_ss=7b289&oh=00_AQA8ZeCUWEX41hGuDhqsZMV5USuvW8DOdGQnKy2eSE0aEQ&oe=6A6503AA\" style=\"width: 100%;\" /><div>SOAMIAVY 21 JUILLET 2026</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/1853104182321226/', NULL, 2, 'publie', '2026-07-21 02:07:50', '2026-07-21 08:59:32'),
(12, 'VAOVAO AN-TSARY 19H30 - 20 JUILLET 2026', '<div><img src=\"https://scontent-ams2-1.xx.fbcdn.net/v/t15.5256-10/749440525_1530827277953405_1863124758560736122_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=102&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=niqztXQ_aFgQ7kNvwFoMBHc&_nc_oc=AdoMMgZS0ZgCpiNO_UbROR0L7JU8B1RUveuHgV6EfjTecx8LAQoy1qhTl1ypJ8yIDtQ&_nc_zt=23&_nc_ht=scontent-ams2-1.xx&_nc_gid=T7PlZya0Kgw6zCUClDpo5Q&_nc_ss=7b289&oh=00_AQB41eIxtf3onfhsQThePkckA0PIP_ynVBkIVNq8BusZoA&oe=6A64FEDF\" style=\"width: 100%;\" /><div>VAOVAO AN-TSARY 19H30 - 20 JUILLET 2026</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/1977603049574294/', NULL, 2, 'publie', '2026-07-20 15:31:32', '2026-07-21 08:59:32'),
(13, 'VAOVAO AN-TSARY 13H00 - 20 JUILLET 2026', '<div><img src=\"https://scontent-ams2-1.xx.fbcdn.net/v/t15.5256-10/750273972_2081984442673794_4216019571728154329_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=103&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=V7-KLNQ5mHoQ7kNvwE7UWAz&_nc_oc=Ado_42-RluVsfakfKGuYVrimrcMkx0dqvTAcyKcpy26vEjjfgT5FJIlLBsYr1jW-ZTs&_nc_zt=23&_nc_ht=scontent-ams2-1.xx&_nc_gid=T7PlZya0Kgw6zCUClDpo5Q&_nc_ss=7b289&oh=00_AQA2ohxoWJoyF0nEsI_rZdurchoOsaK0_TLyNzpK1yB4dA&oe=6A64FCB4\" style=\"width: 100%;\" /><div>VAOVAO AN-TSARY 13H00 - 20 JUILLET 2026</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/863449899894690/', NULL, 2, 'publie', '2026-07-20 09:00:38', '2026-07-21 08:59:32'),
(14, 'SOAMIAVY 20 JOLAY 2026', '<div><img src=\"https://scontent-ams2-1.xx.fbcdn.net/v/t15.5256-10/748623633_1474126001148746_5855812094513668693_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=102&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=tNE8osECiTUQ7kNvwFnGDkP&_nc_oc=AdrWRP4mysXDObvOEwNfNvHcj5j6zZMOEJsDOWTmgMjH5-oRXmGJQBCY-Nq6fbmNmGM&_nc_zt=23&_nc_ht=scontent-ams2-1.xx&_nc_gid=T7PlZya0Kgw6zCUClDpo5Q&_nc_ss=7b289&oh=00_AQBDnFERowpdXKeLA4FtVIOLUrsHflZm6pH4qp8DPXorfA&oe=6A64F2F0\" style=\"width: 100%;\" /><div>SOAMIAVY 20 JOLAY 2026</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/980030421726167/', NULL, 2, 'publie', '2026-07-20 02:05:30', '2026-07-21 08:59:32'),
(15, 'FINAL RESUME', '<div><img src=\"https://scontent-ams2-1.xx.fbcdn.net/v/t15.5256-10/751549666_1852400786170134_9081013287418015657_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=105&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=43G2uv6-p-wQ7kNvwHzvIxV&_nc_oc=AdoMmTgEeocYf37UWgpiQm_5EvPyv4XqUajb7rGnp2_-sdn_INGchJJjZKJO73YKDJU&_nc_zt=23&_nc_ht=scontent-ams2-1.xx&_nc_gid=T7PlZya0Kgw6zCUClDpo5Q&_nc_ss=7b289&oh=00_AQAs3hwusgE0SuGQGv_jt6B99sd-svYVH5NmsxKifXfReg&oe=6A64ED71\" style=\"width: 100%;\" /><div>FINAL RESUME</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/1996143491039075/', NULL, 2, 'publie', '2026-07-19 21:51:50', '2026-07-21 08:59:32'),
(16, 'ESPAGNE # ARGENTINE SUITE', '<div><img src=\"https://scontent-ams2-1.xx.fbcdn.net/v/t15.5256-10/752972437_1332303042315686_7002269238918937108_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=106&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=bY4xgDCI0S8Q7kNvwGrCdfV&_nc_oc=Adqa5nM6Tkb7ce3leuQdupGc7j9oFuGAsc9IMWI_ZUpE15eHYsZHIjPbKU7aZbyCt1MQdYpKfbvRLWT3jJL-Hh2a&_nc_zt=23&_nc_ht=scontent-ams2-1.xx&_nc_gid=WpUMjF56Af80yToBtSVWpA&_nc_ss=7b289&oh=00_AQC8--rsTmacmoJk8XpUVOyyzzUgzH9qKzA6eTaTETeBvw&oe=6A64B959\" style=\"width: 100%;\" /><div>ESPAGNE # ARGENTINE SUITE</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/1745644166444221/', NULL, 2, 'publie', '2026-07-19 21:06:40', '2026-07-21 08:59:32'),
(17, 'ANIMATION FINALE COUPE DU MONDE 2026 - 19 JUILLET 2026', '<div><img src=\"https://scontent-lga3-2.xx.fbcdn.net/v/t15.5256-10/752658758_1086351270383161_2889238808038587078_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=107&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=S_4dtwqAFMwQ7kNvwGIKDps&_nc_oc=AdqefMCHpoZFVFmp0i0DDyWQiSikBkxBpXG_pa9LfN3jCKQwSkx3oSq9o8Jhr6RXaos&_nc_zt=23&_nc_ht=scontent-lga3-2.xx&_nc_gid=HSn9AZytqZTcGRdHkccQOg&_nc_ss=7b289&oh=00_AQBk4ixwb67088OrPZLudJPo2Bj2ORukFD94UziGdAdUtw&oe=6A640E9C\" style=\"width: 100%;\" /><div>ANIMATION FINALE COUPE DU MONDE 2026 - 19 JUILLET 2026</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/1847277446437553/', NULL, 2, 'publie', '2026-07-19 16:26:38', '2026-07-21 08:59:32'),
(18, 'VAOVAO AN-TSARY 19H30 - 19 JOLAY 2026', '<div><img src=\"https://scontent-ams2-1.xx.fbcdn.net/v/t15.5256-10/748763461_1042954748593876_2207888479711061659_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=101&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=j6G0aUp0VPUQ7kNvwEVXvC4&_nc_oc=Adrlu4KuCORwHM998FH_oC6WTK8Db-D4L-G9omOeWedjZv7erbXzC461FNX3lzio4DC73GnH5hIaJ7NAevmqj3Lf&_nc_zt=23&_nc_ht=scontent-ams2-1.xx&_nc_gid=VvG8JMBN9wEcToXdOBDIVw&_nc_ss=7b289&oh=00_AQCAPaLvGtOH_BCDpGbgcdrl8IlAKNPem2YvuW1bMsIH3w&oe=6A63BCB4\" style=\"width: 100%;\" /><div>VAOVAO AN-TSARY 19H30 - 19 JOLAY 2026</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/1038082311928995/', NULL, 2, 'publie', '2026-07-19 15:30:10', '2026-07-21 08:59:32'),
(19, 'JOURNAL TELEVISE EN VERSION FRANÇAISE - 19 JUILLET 2026', '<div><img src=\"https://scontent-ams2-1.xx.fbcdn.net/v/t15.5256-10/752524531_1338912117907774_6455196138807614367_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=100&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=3fimnpKu6ksQ7kNvwHhLnXM&_nc_oc=AdrHjokytD9N9yfgahCDx_B5W8zE-dNhtKqUulJI8jyxySmcIsGC-xwOV_D3VhQFXWbdqHcvLkjT11cJJj-omqpw&_nc_zt=23&_nc_ht=scontent-ams2-1.xx&_nc_gid=-iv0ghrH9F5hWvHDUARe3w&_nc_ss=7b289&oh=00_AQD9GP55IknDBRPezkKggL5W3D1P6LvVENd0eXy_5HTZEw&oe=6A63552A\" style=\"width: 100%;\" /><div>JOURNAL TELEVISE EN VERSION FRANÇAISE - 19 JUILLET 2026</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/2099033414043194/', NULL, 2, 'publie', '2026-07-19 14:51:16', '2026-07-21 08:59:32'),
(20, 'E-SEE MAGAZINE - 19 JUILLET 2026', '<div><img src=\"https://scontent-ams2-1.xx.fbcdn.net/v/t15.5256-10/750941985_1415772623937483_1638618658383480423_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=102&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=0KwGfNGp05UQ7kNvwF0iTmL&_nc_oc=Adr3n_-KsrvdZyBGEF56OyoQqWBSNsRCbgHTITjA49YunDi73IYskuOyviHycSX5N6JR9vduw5S34t2d1scDwnZS&_nc_zt=23&_nc_ht=scontent-ams2-1.xx&_nc_gid=o4MJrfbpKDoUlwKlwo0wAg&_nc_ss=7b289&oh=00_AQCI0N1Eyqc_KAkprrcGfr0G4b71HnjMWlfep_eC5wbx6w&oe=6A632967\" style=\"width: 100%;\" /><div>E-SEE MAGAZINE - 19 JUILLET 2026</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/1569662984682929/', NULL, 2, 'publie', '2026-07-19 13:44:52', '2026-07-21 08:59:32'),
(21, 'VAOVAO 19 JOLAY 2026 13H', '<div><img src=\"https://scontent-ams2-1.xx.fbcdn.net/v/t15.5256-10/752854658_1049325630796142_771983704094635084_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=102&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=ntD4yZDcBwMQ7kNvwHc3p_r&_nc_oc=AdriTWhKNmlqYmutHVK2Zn3JxDPfqIEoFpdFXBtoA_PmLR6q4bQZODSI-OFociUCM5TQyCY4sp9fTGfuAO3Xb74n&_nc_zt=23&_nc_ht=scontent-ams2-1.xx&_nc_gid=o4MJrfbpKDoUlwKlwo0wAg&_nc_ss=7b289&oh=00_AQBr91o-64vLUBmEH3aXfzFA-_0MySUVBpxPrvgYVE9PfQ&oe=6A631038\" style=\"width: 100%;\" /><div>VAOVAO 19 JOLAY 2026 13H</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/1021946530766281/', NULL, 2, 'publie', '2026-07-19 09:00:08', '2026-07-21 08:59:32'),
(22, 'VAOVAOM-PIANGONANA 19 JUILLET 2026', '<div><img src=\"https://scontent-cdg6-1.xx.fbcdn.net/v/t15.5256-10/751870907_1565484271905989_6396588143644947940_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=103&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=TUi8wW_ON8AQ7kNvwGI19_v&_nc_oc=AdoHMY0gvwm-QXt2RZNE8E2i1egkCCdiWtDn_JcEq8N7lJ1BTN4uiUvyNYCZLeL2EJg&_nc_zt=23&_nc_ht=scontent-cdg6-1.xx&_nc_gid=tlkGykvWNaRRFuAjeJu2ww&_nc_ss=7b289&oh=00_AQA8TxbSIL5Vj_ppP6u3HIoY59oJVHDhZfgj2RIFbuFK5A&oe=6A62CE0E\" style=\"width: 100%;\" /><div>VAOVAOM-PIANGONANA 19 JUILLET 2026</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/1040848511765159/', NULL, 2, 'publie', '2026-07-19 03:00:01', '2026-07-21 08:59:32'),
(23, 'FRAN # ANG RESUME', '<div><img src=\"https://scontent-ams2-1.xx.fbcdn.net/v/t15.5256-10/749878471_2104941990129566_4499465657623779602_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=107&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=2J33eVbfhBkQ7kNvwErkLtH&_nc_oc=AdrjWkBSuN5xKPYqtc-CATbdrbarXLQq42LIo4skzAJXTi-0c2OKo_xagDKlTtEVATiUBaI9MYJSGpDHUEN47Keg&_nc_zt=23&_nc_ht=scontent-ams2-1.xx&_nc_gid=Vw5CmM7BC-FnRpLzOmCBew&_nc_ss=7b289&oh=00_AQBPviYXSrRHV106Ih5cMjjOtMbBIa9cRrWfj1YgvlzskQ&oe=6A62B88D\" style=\"width: 100%;\" /><div>FRAN # ANG RESUME</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/1347070614263447/', NULL, 2, 'publie', '2026-07-18 22:06:21', '2026-07-21 08:59:32'),
(24, 'FRANCE # ANGLETERRE SUITE', '<div><img src=\"https://scontent-ams2-1.xx.fbcdn.net/v/t15.5256-10/751001236_1041842894893914_6542646810582659758_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=104&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=WZTYmNjWAtgQ7kNvwHQPRag&_nc_oc=AdrOAl1s_XkFKTyG3UkOdaaFQWZHANxh-AoZOj0rW-UKCHeVKyhwlxJIVDzBQJaoxe1AuTIb0CjtWQ2sxR0gPaBN&_nc_zt=23&_nc_ht=scontent-ams2-1.xx&_nc_gid=Vw5CmM7BC-FnRpLzOmCBew&_nc_ss=7b289&oh=00_AQCnsbWSEMmrXYjQR3602V8up3cUX1351E8ql7UkztIERQ&oe=6A62C6DE\" style=\"width: 100%;\" /><div>FRANCE # ANGLETERRE SUITE</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/1263852369031970/', NULL, 2, 'publie', '2026-07-18 20:58:22', '2026-07-21 08:59:32'),
(25, 'EMISSION MONDIAL FRANCE # ANGLETERRE', '<div><img src=\"https://scontent-ams2-1.xx.fbcdn.net/v/t15.5256-10/748418937_1015886451156980_3294003599595235091_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=105&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=TzbcjWKBes8Q7kNvwEF0Lho&_nc_oc=AdpjLUsNkZCvpX8ArvARYi-DmVcB59xvL_TvhrppbBgPntZpcB48sIiaSMvIxFKkaVHgwqF1VXbjgBE_zL0BUX59&_nc_zt=23&_nc_ht=scontent-ams2-1.xx&_nc_gid=7SOLdk3SXj4g_DBb7G9K1w&_nc_ss=7b289&oh=00_AQBz8cCLBnvV6aegmd6DqAqOnPE5CAA-YvTQBcrnsMNqXA&oe=6A629CF5\" style=\"width: 100%;\" /><div>EMISSION MONDIAL FRANCE # ANGLETERRE</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/2579194209198951/', NULL, 2, 'publie', '2026-07-18 19:21:00', '2026-07-21 08:59:32'),
(26, 'FANDAHARANA MANOKANA - IMBEH', '<div><img src=\"https://scontent-ams2-1.xx.fbcdn.net/v/t15.5256-10/751622696_1035190369440418_1533174507227413506_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=108&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=cG_fxubKQLsQ7kNvwEblQkI&_nc_oc=AdrSzqWTB5Wnh7cmvljugINioXLQvnQ2v9r2BhbmnaGIXqUFngNDv8wEUyJRE6pWeWrP3xiB7Hfro1HfRzohI4um&_nc_zt=23&_nc_ht=scontent-ams2-1.xx&_nc_gid=NguPB5IQwrn5vaNC0SmSKA&_nc_ss=7b289&oh=00_AQBk0Y9BtBL6xpBOiB-IikPBs_FMN0HykgwdTR87jp-cKA&oe=6A62518A\" style=\"width: 100%;\" /><div>FANDAHARANA MANOKANA - IMBEH</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/1401291245198463/', NULL, 2, 'publie', '2026-07-18 16:34:57', '2026-07-21 08:59:32'),
(27, 'VAOVAO AN-TSARY 19H30 - 18 JOLAY 2026', '<div><img src=\"https://scontent-lga3-2.xx.fbcdn.net/v/t15.5256-10/751819087_972852599137156_309308678863214882_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=100&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=OJiN_vxypS0Q7kNvwFFjRAB&_nc_oc=Adr9ic8H3lB-QcBniqBMURsjp-Qv-bEMUME1CHmQofRcScEvptTN1xVKVeG1ijWjMEc&_nc_zt=23&_nc_ht=scontent-lga3-2.xx&_nc_gid=uqpW4eMlEd-_Qi4hnvxCIA&_nc_ss=7b289&oh=00_AQCCq4pH-gZmOBywcX5JekdZPQcN_n_0dgRud_2GoPf7ew&oe=6A61FE77\" style=\"width: 100%;\" /><div>VAOVAO AN-TSARY 19H30 - 18 JOLAY 2026</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/1018373904342560/', NULL, 2, 'publie', '2026-07-18 15:30:45', '2026-07-21 08:59:32'),
(28, 'FANDAHARANA SORADIHY 18 JOLAY 2026', '<div><img src=\"https://scontent-ams2-1.xx.fbcdn.net/v/t15.5256-10/751281748_2105863000312816_7803352831086225037_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=104&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=lbwsNxWdVLIQ7kNvwH_Ds2y&_nc_oc=AdqGPqNto5sgiInxzTpaUKnlj1tkfg5ETnolsxbX0qPHWfxZzw6upGPyXyLnf5MRQRzEDlo7nDlEyKO6g4Rzpdqn&_nc_zt=23&_nc_ht=scontent-ams2-1.xx&_nc_gid=bkpKsjmdP0BZ2rqxz0tQtw&_nc_ss=7b289&oh=00_AQB1RgAvV23Rmc0PhfqIleawQliPe2YmlXYpJ82m2ART3A&oe=6A61D3FC\" style=\"width: 100%;\" /><div>FANDAHARANA SORADIHY 18 JOLAY 2026</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/1066295922752249/', NULL, 2, 'publie', '2026-07-18 13:01:45', '2026-07-21 08:59:32'),
(29, 'VAOVAO AN-TSARY 13H00 - 18 JUILLET 2026', '<div><img src=\"https://scontent-bcn1-1.xx.fbcdn.net/v/t15.5256-10/750785180_917339013957363_3114683307678497706_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=100&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=6VF4o6jWh14Q7kNvwHQwPcO&_nc_oc=Adr5-BN4vbuO2h7khfje4FSF8Dxb-1udU4i04_gP63fHr2vKHT3pLhNqS5v0DWqdNsg&_nc_zt=23&_nc_ht=scontent-bcn1-1.xx&_nc_gid=cT5uH0ulH88UbifzDU85YA&_nc_ss=7b289&oh=00_AQCcnuqaSiLSqwkA0N3wp-Ev_SmOMFnqhMi-sI4fPgaHZA&oe=6A61A739\" style=\"width: 100%;\" /><div>VAOVAO AN-TSARY 13H00 - 18 JUILLET 2026</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/1570851334699322/', NULL, 2, 'publie', '2026-07-18 09:00:05', '2026-07-21 08:59:32'),
(30, 'GASY MAFOAKA Samedi 18  Juillet 2026 - Dr Veterinaire RAZANANORO Erline - ILRI Kenya Nairobi', '<div><img src=\"https://scontent-lga3-3.xx.fbcdn.net/v/t15.5256-10/741402322_1939823090030901_9006050723977607194_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=108&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=RtJ_qNvE29QQ7kNvwG7zThm&_nc_oc=Adoql2mR48E2P1dpnIP0mlJnmKPUpPYOWxgNo-xxL_diCVrRqhVZ9yg9bNRkHUlwgtc&_nc_zt=23&_nc_ht=scontent-lga3-3.xx&_nc_gid=O4wCbRlgpAXrxxkst0n1qg&_nc_ss=7b289&oh=00_AQDgH4FLsz1m7SWTaIVEkn4-6bX4qktqmL5Yp__OrCrBKw&oe=6A61A414\" style=\"width: 100%;\" /><div>GASY MAFOAKA Samedi 18  Juillet 2026 - Dr Veterinaire RAZANANORO Erline - ILRI Kenya Nairobi</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/1043691028007674/', NULL, 2, 'publie', '2026-07-18 08:00:51', '2026-07-21 08:59:32'),
(31, 'SOAMIAVY 18 JOLAY 2026', '<div><img src=\"https://scontent-ams2-1.xx.fbcdn.net/v/t15.5256-10/750615630_898766756604309_5656315656225818745_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=100&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=upK2jSxpi24Q7kNvwHZPiMP&_nc_oc=Adp2W53J0sKjgvrrkwjdjx8bHsSDlvZ3Rm4EG-6gbuSoCRMk-RPV5ZfoxEEcV2QajdxmdVlq9RkzkIiAqWQoP9Qi&_nc_zt=23&_nc_ht=scontent-ams2-1.xx&_nc_gid=rBEnblf7an3EkVMQaevubQ&_nc_ss=7b289&oh=00_AQA3sOf0av6N1bLrWFlFF_uYp9xUN00ZSHD1PE2KQauBuQ&oe=6A61A030\" style=\"width: 100%;\" /><div>SOAMIAVY 18 JOLAY 2026</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/1396990215667856/', NULL, 2, 'publie', '2026-07-18 02:01:48', '2026-07-21 08:59:32'),
(32, 'VAOVAO AN-TSARY 19H30 - 17 JOLAY 2026', '<div><img src=\"https://scontent-lga3-3.xx.fbcdn.net/v/t15.5256-10/750814955_1026358976794987_4327782728899994652_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=102&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=yguz23ENYIgQ7kNvwFX5B6G&_nc_oc=Adr5XkvRuTExX0GlviR3f5xgnEnznDyGcLUWazwuI68A9VHfZzQE5dK5Q38n-DGNjEA&_nc_zt=23&_nc_ht=scontent-lga3-3.xx&_nc_gid=oDlD7zDf_dtbpu-Oqw924Q&_nc_ss=7b289&oh=00_AQBPIOUFjyk4yt6rKvT_nSY5uAD9zueyeKhapYSUsOZ-Mw&oe=6A615EEE\" style=\"width: 100%;\" /><div>VAOVAO AN-TSARY 19H30 - 17 JOLAY 2026</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/1031708052948448/', NULL, 2, 'publie', '2026-07-17 15:29:28', '2026-07-21 08:59:32'),
(33, 'FANDAHARANA NAMAKO GRIKA 17 JOLAY 2026', '<div><img src=\"https://scontent-ams2-1.xx.fbcdn.net/v/t15.5256-10/749788815_1026136506811112_1338874552998763184_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=104&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=Fyuclvw2i-0Q7kNvwFHlWXC&_nc_oc=AdrpcicJNck8EbiRy3uL54wrQVV2MC1fbtIixPF47g4lZDRTUJNZzqnSJGroPAMg4D89orA5I4_-uBRs2aoMJdPn&_nc_zt=23&_nc_ht=scontent-ams2-1.xx&_nc_gid=AWQ_kZ4nTg_BK64sondwLA&_nc_ss=7b289&oh=00_AQCaHQ-qnMegyg8EdKPBddKCa0jcNVP1s1RVeFFB4dGJJw&oe=6A614C32\" style=\"width: 100%;\" /><div>FANDAHARANA NAMAKO GRIKA 17 JOLAY 2026</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/1341196260966046/', NULL, 2, 'publie', '2026-07-17 14:05:08', '2026-07-21 08:59:32'),
(34, 'VAOVAO AN-TSARY 13H 00 - 17 JUILLET 2026', '<div><img src=\"https://scontent-cdg4-1.xx.fbcdn.net/v/t15.5256-10/750843755_2187576995352977_2241518512770227988_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=102&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=TXkpxuG1GpcQ7kNvwE1ZupR&_nc_oc=AdqiGF6IE-mSDzg7XzmUGHC7b0oGV8tKCOMSomcY_Bi9XPY75tF68c-vjbKJSXM3qaA&_nc_zt=23&_nc_ht=scontent-cdg4-1.xx&_nc_gid=UPHZUG29CesqTgdTkZKidw&_nc_ss=7b289&oh=00_AQDocZE4eX6VSY6Mg5AigD_FiWZkHKo0PjU2mxyo6FLomg&oe=6A6126C1\" style=\"width: 100%;\" /><div>VAOVAO AN-TSARY 13H 00 - 17 JUILLET 2026</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/879788748533431/', NULL, 2, 'publie', '2026-07-17 09:02:55', '2026-07-21 08:59:32'),
(35, '🟥🟥 FANDAHARANA MANOKANA 🟥🟥 Ho vahinin\'ny fandaharana manokan\'ny TVM rahampitso Sabotsy 18 jolay 2026 andriamatoa Ser...', '<div><img src=\"https://scontent-cdg4-3.xx.fbcdn.net/v/t39.30808-6/748088598_1372023821807117_822546920639098538_n.jpg?stp=dst-jpg_p843x403_tt6&_nc_cat=111&ccb=1-7&_nc_sid=2e5b1e&_nc_ohc=8NG4YVFDTnAQ7kNvwH2Q8yg&_nc_oc=Adow1bvOdL6oVVzZVc1OyPq-t5A7LlbC8b9NvzwyyogwYJTU8fWvN8D0_eodKHpJsCE&_nc_zt=23&_nc_ht=scontent-cdg4-3.xx&_nc_gid=UPHZUG29CesqTgdTkZKidw&_nc_ss=7b289&oh=00_AQAPSc9ktSRacuBaZZ5ZCkKZ2AvfOes2-61VVy0kQbNwEA&oe=6A610CE6\" style=\"width: 100%;\" /><div>🟥🟥 FANDAHARANA MANOKANA 🟥🟥<br> Ho vahinin\'ny fandaharana manokan\'ny TVM rahampitso Sabotsy 18 jolay 2026 andriamatoa Serge Jovial IMBEH, Administrateur Judiciaire Groupe Sodiat.<br> Ny manodidina ny fampiodinana ity orinasa ity no lohahevitra ho resahina ao anatin\'izany.</div></div>', 'https://m.facebook.com/televizionamalagasy/posts/pfbid02CvLZ14XGHyk6mJdh9s6Uyue9LyZLtKFHoJXEfEH6LywnMhhRfE2QAdksSdAK1qzTl', NULL, 2, 'publie', '2026-07-17 07:52:33', '2026-07-21 08:59:32'),
(36, '🔥 Mitohy hatrany ny asa fanasoavana ny vahoaka eto amin\'ny Commune Urbaine de Diego Suarez ! Na eo aza ny trépidations...', '<div><img src=\"https://scontent-den2-1.xx.fbcdn.net/v/t39.30808-6/557945524_122157968798757215_1890457134295151826_n.jpg?stp=dst-jpg_s1080x2048_tt6&_nc_cat=101&ccb=1-7&_nc_sid=e21142&_nc_ohc=Qs_V__SQKc0Q7kNvwEmEwc5&_nc_oc=AdqEHywBie-o0B8Cgl6nwoD1jOo7e8bs33HD4xaoeSVozjiPkS2cj-oNdaYwbBGaeDw&_nc_zt=23&_nc_ht=scontent-den2-1.xx&_nc_gid=GzaOljq2uiINig74IE5BFw&_nc_ss=7b289&oh=00_AQDZMLu-lUyTuxuW1FDh1huYUosIIpydBhkbS2NU72xvJg&oe=6A650D61\" style=\"width: 100%;\" /><div>🔥 Mitohy hatrany ny asa fanasoavana ny vahoaka eto amin\'ny Commune Urbaine de Diego Suarez !<br> Na eo aza ny trépidations sy turbulence samihafa mianjady amin\'ny firenena, tsy miato ny ezaka atao amin\'ny  fitantanan-draharaha tarihin\'ny PDS Tina Edmond sy ny ekipany.<br> Fanadiovana, fanamboaran-dalana, fanatsarana ny tontolo iainana — asa mivaingana sy hita maso eny ifotony, ho fanomezana endrika madio, mirindra ary mahasarika ho an\'ny tanànan\'i Diego Suarez .<br> 🎯 Tanjona : famerenana ny fahatokisan\'ny vahoaka amin\'ny fitantanana ny tanàn-dehibe, fanamafisana ny firaisankina ary fanentanana ny rehetra handray anjara amin\'ny fanpandrosoan\'ny tanàna.<br> 💪 Ny asa tsy mitady resabe, fa porofo fo .<br> Eo ambany fitarihan\'ny PDS Tina Edmond , dia manohy miasa amim-pahendrena, amin\'ny fahaiza-mitantana ary amin\'ny finiavana ny ekipan\'ny Commune Urbaine de Diego Suarez — ho an\'ny vahoaka, ho an\'ny tanàna, ho an\'ny ho avy. 🌿</div></div>', 'https://m.facebook.com/permalink.php?id=61572716470944&story_fbid=pfbid0RHXLXNxuXTYUPCKT7CUG8g5ppx1zTc1bXJ9bL6PWhGFNBvYpY88oNL7X3DzfSvdtl', NULL, 3, 'publie', '2025-10-09 09:00:57', '2026-07-21 08:59:33'),
(37, 'Ny commune urbaine de Diégo Suarez tarihin\'ny PDS TINA Edmond dia miarahaba ny Praiministra vaovao jeneraly Zafisambo Ru...', '<div><img src=\"https://scontent-den2-1.xx.fbcdn.net/v/t39.30808-6/559200446_122157830048757215_7139467130063556990_n.jpg?_nc_cat=102&ccb=1-7&_nc_sid=2e5b1e&_nc_ohc=ARyvOSlKXaQQ7kNvwFU8J4M&_nc_oc=AdoRH92hfb5rcujr6qtwm9YRBHhCI_DBP_ibck4txE04oStgGSTGzKMAogAnsj83N6A&_nc_zt=23&_nc_ht=scontent-den2-1.xx&_nc_gid=GzaOljq2uiINig74IE5BFw&_nc_ss=7b289&oh=00_AQAzm5InG2n2HEbblroiI18sAsrO9cvfKkAWn9Ya1LA5-w&oe=6A64F095\" style=\"width: 100%;\" /><div>Ny commune urbaine de Diégo Suarez tarihin\'ny PDS TINA Edmond dia miarahaba ny Praiministra vaovao jeneraly Zafisambo Ruphin Fortunat ary mankasitraka ny Praiministra teo aloha NTSAY Christian.<br> Miandrandra ny mèva hatrany ny mponin\'Antsiranana.</div></div>', 'https://m.facebook.com/permalink.php?id=61572716470944&story_fbid=pfbid02Wmm7yy17CpyqtsEBzd7yY913F8GUXsatFyxSyULbx6mnHpbTDvUHoRQ2oHLqLp4Gl', NULL, 3, 'publie', '2025-10-08 07:50:50', '2026-07-21 08:59:33'),
(38, 'Fagnadiovagna Canal: Mitohy ny asan\'ny CUDS  Miroso amin\'ny fagnadiovagna irô canal tsentsigny ny fako ny CUDS.Hitsintso...', '<div><img src=\"https://scontent-den2-1.xx.fbcdn.net/v/t39.30808-6/543409455_122153099924757215_2489307292549896403_n.jpg?stp=dst-jpg_s1080x2048_tt6&_nc_cat=107&ccb=1-7&_nc_sid=e21142&_nc_ohc=rQDSkNlC-RIQ7kNvwFMZSqk&_nc_oc=Adpz2pd8_Gqizn1mO305SsR48EMKmOWqd3d3CG7d8JRFPzkJTIyte9VpZGa28x1jtbk&_nc_zt=23&_nc_ht=scontent-den2-1.xx&_nc_gid=GzaOljq2uiINig74IE5BFw&_nc_ss=7b289&oh=00_AQA1rNvBMhvztqwGTGTx9p4i4Kw02PXa618H_pbVTBxS9g&oe=6A651400\" style=\"width: 100%;\" /><div>Fagnadiovagna Canal: Mitohy ny asan\'ny CUDS <br> Miroso amin\'ny fagnadiovagna irô canal tsentsigny ny fako ny CUDS.Hitsintsovagna ny dobon-drano amin\'ny fotoam-pahavaratra no isany kinendrin\'ny PDS TINA Edmond.</div></div>', 'https://m.facebook.com/permalink.php?id=61572716470944&story_fbid=pfbid02b925GibNFmXrQnk8V7EybPyNA6fqGBWFZWB8zU9NiqNrGpuX5HbVhsRmR3yNXQdzl', NULL, 3, 'publie', '2025-09-06 04:33:47', '2026-07-21 08:59:33'),
(39, '🔥🇲🇬 Finale BAREA vs Maroc 🇲🇦🔥 Eto amin’ny Lapan’ny Tanàna Diego, olona maro no miara-mijery mivantana sy manohana...', '<div><img src=\"https://scontent-den2-1.xx.fbcdn.net/v/t39.30808-6/539796776_122152033592757215_9069982499633765094_n.jpg?stp=dst-jpg_s720x720_tt6&_nc_cat=102&ccb=1-7&_nc_sid=e21142&_nc_ohc=N_jxqvdzDfsQ7kNvwEXJqbP&_nc_oc=Adp_Zs-kdtm1D9RVM2FQQ8kga73u2u_AZDhomHtXEyp-oHREWNk6vFWTnV8EUvZHURg&_nc_zt=23&_nc_ht=scontent-den2-1.xx&_nc_gid=GzaOljq2uiINig74IE5BFw&_nc_ss=7b289&oh=00_AQDtdi37-1QwGc4_0RW3DQIyDpWI3_hj-w8tWK0zPqneDQ&oe=6A64E82A\" style=\"width: 100%;\" /><div>🔥🇲🇬 Finale BAREA vs Maroc 🇲🇦🔥<br> Eto amin’ny Lapan’ny Tanàna Diego, olona maro no miara-mijery mivantana sy manohana mafy ny BAREA!<br> ⚽ Isa vonjimaika: 2 – 2 (mbola mitohy ny lalao)<br> Avia miaraka hanohagna: BAREA EEE! 💚🤍❤️</div></div>', 'https://m.facebook.com/permalink.php?id=61572716470944&story_fbid=pfbid02y6dMYEK4u81imrqyw3jCzkTph9UX84KdQQUqPfhGa1Gq9v6J9rawbYAYmjw5tH7zl', NULL, 3, 'publie', '2025-08-30 15:33:11', '2026-07-21 08:59:33'),
(40, 'FANAMAFISANA FAHAIZA-MANAO HO AN’NY BEN’NY TANÀNA SY MPANOLOTSAINA TATY AMIN\'NY FARITRA DIANA  Teto Antsiranana no namar...', '<div><img src=\"https://scontent-den2-1.xx.fbcdn.net/v/t39.30808-6/537251466_122132225948885016_847140689108843161_n.jpg?stp=dst-jpg_p843x403_tt6&_nc_cat=108&ccb=1-7&_nc_sid=e21142&_nc_ohc=ZJUtWA_vigUQ7kNvwEFpc63&_nc_oc=AdrfMthi76Jc5I9JRAYHNtl-6udtgEgu8ArBqal3mgkwFxWQE77YHTCNl2gn4BdUTZI&_nc_zt=23&_nc_ht=scontent-den2-1.xx&_nc_gid=GzaOljq2uiINig74IE5BFw&_nc_ss=7b289&oh=00_AQAom4GlMbyvJCTgLQMvLllksEikoSssHe_Fs6HUk5J8qQ&oe=6A650EFF\" style=\"width: 100%;\" /><div>FANAMAFISANA FAHAIZA-MANAO HO AN’NY BEN’NY TANÀNA SY MPANOLOTSAINA TATY AMIN\'NY FARITRA DIANA <br> Teto Antsiranana no namaranana ny fiofanana manokana ho an’ireo Ben’ny Tanàna sy Mpanolotsaina, mikasika ny andraikitra sy anjara asany amin’ny fampandrosoana ny tanàna, araka ny lalàna 2014-020.<br> ✨ Nizara fahaiza-manao Atoa RAOSY, manampahaizana momba ny fitsinjaram-pahefana sy fampandrosoana ifotony, ary notohanan’ny Loholon’i Madagasikara sady Kestora #MINO_Seramila, izay nanasongadina fa ilaina hatrany ny fanohanana ireo mpitantana eny anivon’ny Kaominina.<br> 📌 Tonga nanotrona fiofanana izao ny tenako noho <br> fitiavako manampy sy hanatsara ny fahaizan’ireo Ben’ny Tanàna amin’ny fitantanana sy ny famolavolana paikady ho an’ny Kaominina tsirairay. Mahatsapa tokoa isika fa mila fanohanana sy fandraisana anjara rehetra ny fampandrosoana ny Kaominana. <br> 💬 Nambaran’ireo mpandray fitenenana rehetra fa tena ilaina sy fototra ny fiofanana toy izao, satria manampy ireo mpitantana hahafehy bebe kokoa ny asany, ary manome lanja ny fandraisana anjaran’ny vahoaka amin’ny fampandrosoana ifotony.</div></div>', 'https://m.facebook.com/permalink.php?id=61572716470944&story_fbid=pfbid0nnbPLQeGjuHmPYCbG3gvdmSWAABFdBjHpsPivkM7QWURoc5KKiddukquok4tv7PQl', NULL, 3, 'publie', '2025-08-24 17:48:08', '2026-07-21 08:59:33'),
(71, 'Passation DG SECREN', '<div><img src=\"https://scontent-ams2-1.xx.fbcdn.net/v/t15.5256-10/749119616_27567775346222525_1201261488592970146_n.jpg?_nc_cat=104&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=xNgAQ5A1Wm4Q7kNvwEcbQb0&_nc_oc=AdpqkdEuCEio3lBg-o_NkqJY681OfBoFj57BSu8bcIVlOBlSSEBNbGgxQJKndkgGEB2Fsy-_3AWJkrF-KIrC5wG0&_nc_zt=23&_nc_ht=scontent-ams2-1.xx&_nc_gid=lcvkks7Blk823QnoZoxABQ&_nc_ss=7b289&oh=00_AQBo0dCjYNrFKmfMIO3agAvX_Kjv8ZmgjIu9ozRbg635Wg&oe=6A64F025\" style=\"width: 100%;\" /><div>Passation DG SECREN</div></div>', 'https://www.facebook.com/reel/1312023561013483/', NULL, 5, 'publie', '2026-07-16 12:55:46', '2026-07-21 09:01:47'),
(72, 'TVM Antsiranana  《Télévision Varatraza 》 Posted', 'TVM Antsiranana  《Télévision Varatraza 》 Posted', 'https://m.facebook.com/permalink.php?id=100057582064106&story_fbid=pfbid02KLdvA8cUJgHU5Lov93uzFPFfRjFWCvdJDaxXvFteKR9187myjvUzuyj4SmN8VCYAl', NULL, 5, 'publie', '2026-07-15 10:40:31', '2026-07-21 09:01:47'),
(73, 'FILAZANA MANOKANA Ny Radio Télévision Varatraza Antsiranana dia mampandre fa tsy maintsy efa nampandrenesina ny Polisy n...', '<div>FILAZANA MANOKANA<br> Ny Radio Télévision Varatraza Antsiranana dia mampandre fa tsy maintsy efa nampandrenesina ny Polisy na Zandary sy ny Fokontany alohan’ny handefasana filazana olona very.<br> Alefaso aty aminay ny sary na kopian’ny PV na taratasy fanamarinana, miaraka amin’ny lahatsoratra tianareo havoaka, amin’ny message privé.<br> Misaotra amin’ny fahatakarana sy ny fiaraha-miasa.</div>', 'https://m.facebook.com/permalink.php?id=100057582064106&story_fbid=pfbid0fpQb1a1UghQUWyJxxUcdu7ZgkcMUXG2WpoAqEVEdu6vAjR2wfky9nADr2YpiJ1K7l', NULL, 5, 'publie', '2026-07-09 18:45:41', '2026-07-21 09:01:47'),
(74, 'TVM Antsiranana  《Télévision Varatraza 》 Posted', 'TVM Antsiranana  《Télévision Varatraza 》 Posted', 'https://m.facebook.com/permalink.php?id=100057582064106&story_fbid=pfbid029qzx4HEoTsdG48HWtJTRoZqU1HZNSKhyotZJ6fP848MTP3NPDf7DnpGVqREE7ensl', NULL, 5, 'publie', '2026-07-08 16:17:14', '2026-07-21 09:01:47'),
(75, '🔵 SECREN SA : Velom-panantenana ny mpiasa, miandry ny fiovana entin\'i Felicien Milson Fanantenana vaovao no entin\'ny fa...', '<div><img src=\"https://scontent-ams2-1.xx.fbcdn.net/v/t39.30808-6/738774050_1494346142494774_4577624748585562772_n.jpg?_nc_cat=100&ccb=1-7&_nc_sid=e21142&_nc_ohc=yK7oqhMB5E4Q7kNvwEfEQ1c&_nc_oc=Adq67ZzEWxhiQ62nGgouNhG7yCRu2WAt3n1wxTjrMKC0Ld4IBqvZO087Fl7EZ0QMfsWuNAjznvRPA5r0v1n0Om5L&_nc_zt=23&_nc_ht=scontent-ams2-1.xx&_nc_gid=lcvkks7Blk823QnoZoxABQ&_nc_ss=7b289&oh=00_AQBCu4KtZLP9GdZT0pHpSezlxhaIFkD9adawiChVlzmxrQ&oe=6A650E79\" style=\"width: 100%;\" /><div>🔵 SECREN SA : Velom-panantenana ny mpiasa, miandry ny fiovana entin\'i Felicien Milson<br> Fanantenana vaovao no entin\'ny fahatongavan\'i Felicien Milson eo amin\'ny fitantanana ny SECREN SA. Matoky ny mpiasa fa ny traikefany sy ny fahaizany ny orinasa no hanokatra pejy vaovao ho an\'ny fanarenana.<br> Nambaran\'ny Tale Jeneraly fa laharam-pahamehana ny fanarenana ny orinasa sy ny fanatsarana ny fari-piainan\'ny mpiasa. Anisan\'ny vinavinany ny fandoavana indray mandeha ny karama enim-bolana mbola tsy voaloa, raha vantany vao tonga ny fanohanan\'ny fitondram-panjakana. Eo ihany koa ny fahatongavan\'ireo mpiara-miombon\'antoka sy sambo hokarakaraina ao amin\'ny orinasa, izay antenaina hanomboka amin\'ity tapaky ny volana Jolay ity.<br> Na dia velom-panantenana aza ny mpiasa, dia mazava ny hafatr\'izy ireo: tsy ny teny no andrasana, fa ny vokatra. Ny fiverenan\'ny asa, ny fahatongavan\'ny mpanjifa ary ny fandoavana ara-potoana ny karama no ho porofo fa tena miroso amin\'ny fanarenana ny SECREN SA.<br> @à la une</div></div>', 'https://m.facebook.com/permalink.php?id=100057582064106&story_fbid=pfbid02huQq88zhj4h9yVnoV2rs6BQRLRyXgL4qCJ8Jqqo1XZAkvwBzXebTRzey4tmxPBaml', NULL, 5, 'publie', '2026-07-06 12:32:34', '2026-07-21 09:01:47'),
(111, 'VAOVAO AN-TSARY 13H - 23 JUILLET 2026', '<div><img src=\"https://scontent-lga3-2.xx.fbcdn.net/v/t15.5256-10/743730469_836803219516647_8735672934659048489_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=107&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=eJj6t0yWeQYQ7kNvwE6UmxQ&_nc_oc=AdrVZqVJ2DAofIu8xLa-XYEd8O79RC-wjmLGMunNaU6t_yNMRtGODHIGgQ6s26XWAk8&_nc_zt=23&_nc_ht=scontent-lga3-2.xx&_nc_gid=wg6YerBXngLHLawqtTH5Kg&_nc_ss=7b289&oh=00_AQB78jg_RFOVA9qltawpmTu2uk2JdMK4Fj9NANptXIh04Q&oe=6A680679\" style=\"width: 100%;\" /><div>VAOVAO AN-TSARY 13H - 23 JUILLET 2026</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/1661085794959929/', NULL, 2, 'publie', '2026-07-23 09:00:06', '2026-07-23 15:31:47'),
(112, '🟥🟥 FLASH INFO 🟥🟥 FOTOAM-PIVAVAHANA IRAISAM-PINOANA LEHIBE Ho fangataham-pitahiana ho an\'ny firenena Zoma 24 Jolay 20...', '<div><img src=\"https://scontent-lga3-2.xx.fbcdn.net/v/t39.30808-6/752881034_1376810814661751_6111465759291358295_n.jpg?stp=dst-jpg_p843x403_tt6&_nc_cat=105&ccb=1-7&_nc_sid=2e5b1e&_nc_ohc=pELjmWabT7wQ7kNvwGEU6_z&_nc_oc=AdpUTJ5--0XpS7P0Qg6wGLZimwj7Q3sYk7I8kmrAA-T5WGbsLNeEDfZfjM1IIryh1NM&_nc_zt=23&_nc_ht=scontent-lga3-2.xx&_nc_gid=wg6YerBXngLHLawqtTH5Kg&_nc_ss=7b289&oh=00_AQAk6BMtIy2UnRTxSctpHWFuN15ksSPzU7GfJnzs9V5RKg&oe=6A680389\" style=\"width: 100%;\" /><div>🟥🟥 FLASH INFO 🟥🟥<br> FOTOAM-PIVAVAHANA IRAISAM-PINOANA LEHIBE<br> Ho fangataham-pitahiana ho an\'ny firenena<br> Zoma 24 Jolay 2026<br> 9h30 maraina<br> Parvis Analakely</div></div>', 'https://m.facebook.com/televizionamalagasy/posts/pfbid02SBHkovjDfR7xwvqEydTWyrKhVqKnQZ4JfuD5hAXkL6wEPo4iPyvmsJvqfCZbjDPMl', NULL, 2, 'publie', '2026-07-23 05:09:50', '2026-07-23 15:31:47'),
(113, 'Cérémonie de clôture du concours Médias  #PourLesAiresProtégées 3e édition - 22 JUILLET 2026', '<div><img src=\"https://scontent-lga3-3.xx.fbcdn.net/v/t15.5256-10/754714970_1048179110996455_8679122844588024871_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=108&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=JUuoTsvLjZ4Q7kNvwH_nnXN&_nc_oc=Adpf8RHHJTx18UGJUv87oRVB3xOJMi525NMjhE_FvDDzfFWwVJXgb7VLPUac_zI4iF8&_nc_zt=23&_nc_ht=scontent-lga3-3.xx&_nc_gid=wg6YerBXngLHLawqtTH5Kg&_nc_ss=7b289&oh=00_AQBVFdvpEICz0r1CH4duowcGMfdiIEEK_P9AQb-Zo6pEXg&oe=6A6814F1\" style=\"width: 100%;\" /><div>Cérémonie de clôture du concours Médias  #PourLesAiresProtégées 3e édition - 22 JUILLET 2026</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/28190849250513011/', NULL, 2, 'publie', '2026-07-22 16:42:10', '2026-07-23 15:31:47'),
(114, 'VAOVAO AN-TSARY 19H30 - 22 JOLAY 2026', '<div><img src=\"https://scontent-lga3-1.xx.fbcdn.net/v/t15.5256-10/752466205_1997249700956804_4349412240506126340_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=111&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=Gk1XPE-ffjwQ7kNvwFNaj-Q&_nc_oc=AdrE5zQ08SkTsEOTj8PK8jeaDiKSjbuUEFWezLaqruadgw3ZJbMO73ZWnJloxBjZC5U&_nc_zt=23&_nc_ht=scontent-lga3-1.xx&_nc_gid=wg6YerBXngLHLawqtTH5Kg&_nc_ss=7b289&oh=00_AQDBfmx3EcxXuKWSSfV0UU3inqbuxr4EjrHUxuEJnhSw5A&oe=6A6800E8\" style=\"width: 100%;\" /><div>VAOVAO AN-TSARY 19H30 - 22 JOLAY 2026</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/2113834326225257/', NULL, 2, 'publie', '2026-07-22 15:29:59', '2026-07-23 15:31:47'),
(115, 'VAOVAO AN-TSARY 13H 00 - 22 JUILLET 2026', '<div><img src=\"https://scontent-lga3-1.xx.fbcdn.net/v/t15.5256-10/754307047_1712183750112404_6861156521704472945_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=111&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=anCdkOl3bS0Q7kNvwH6Ec18&_nc_oc=AdpUHHgQXuFO0TCoBlGz6fQBSvTCiwwIx8crrghAtGKIsuhKXsTskcVaF0m82klxtfg&_nc_zt=23&_nc_ht=scontent-lga3-1.xx&_nc_gid=wg6YerBXngLHLawqtTH5Kg&_nc_ss=7b289&oh=00_AQDY6H8JjWg9y4-fUl9Poh3QeqzluW3-mKVLLbwaQa7kAg&oe=6A68177B\" style=\"width: 100%;\" /><div>VAOVAO AN-TSARY 13H 00 - 22 JUILLET 2026</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/1607768537443218/', NULL, 2, 'publie', '2026-07-22 09:04:25', '2026-07-23 15:31:47'),
(116, '\"Médias Pour Les Aires Protégées\" andiany faha 3: Lalonana fankasitrahana ireo Mpanao Gazety nandray anjara tamin’ny fif...', '<div><img src=\"https://scontent-ams2-1.xx.fbcdn.net/v/t15.5256-10/754247992_1849394326041794_3521217667456964462_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=101&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=pADCYOBve0cQ7kNvwHhHJRf&_nc_oc=Adqh98I_ndTIwI_xn7wplxSfqplJ1e-FyRigCEsz3hRSQT2-Ead3-LkCsZcDTGjzUeyUQi3idXr-61_PajgTaVwV&_nc_zt=23&_nc_ht=scontent-ams2-1.xx&_nc_gid=MQnt0Dbvegy3vgsbC5TaOQ&_nc_ss=7b289&oh=00_AQBhIc0LfsiAgvJbGXIOriyFuFAo_8BcxLMpbYySVsdcnQ&oe=6A67A03D\" style=\"width: 100%;\" /><div>\"Médias Pour Les Aires Protégées\" andiany faha 3: Lalonana fankasitrahana ireo Mpanao Gazety nandray anjara tamin’ny fifaninana<br> Nanana Mpanao Gazety roa nandray anjara ny teto anivon’ny ORTM <br> - Philippine RASOAFARA (RNM)<br> - Aina Rabetany, (TVM)<br> -----------------<br> Cérémonie de clôture du concours Médias #PourLesAiresProtégées 3ème édition<br>Cérémonie de clôture du concours Médias #PourLesAiresProtégées 3ème édition</div></div>', 'https://m.facebook.com/televizionamalagasy/posts/pfbid02ExcedCNtbdH9BuMCB4bgQUfiR91NwnFaQkSczSJP4p6W78h9LFgn36frKSLcsvsyl', NULL, 2, 'publie', '2026-07-22 06:22:26', '2026-07-23 15:31:47'),
(117, 'SOAMIAVY 22 JUILLET 2026', '<div><img src=\"https://scontent-cdg4-1.xx.fbcdn.net/v/t15.5256-10/753865349_1782417176277276_7680126881835564724_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=108&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=X2ZyA_xD4JkQ7kNvwHjUxPN&_nc_oc=AdooDjNR24tmZBGnw95qrpAt5ix5jryxaQ1GQ6koWhWqaHCtdxeP97A2p9H0uL3DKF0&_nc_zt=23&_nc_ht=scontent-cdg4-1.xx&_nc_gid=hT7ZYzlGZn8BxLBB8vhJOQ&_nc_ss=7b289&oh=00_AQARH2ohRBgz1KYJ4xZy6HdLt3BGyIE3DdisXN_0uBBvqw&oe=6A677CD4\" style=\"width: 100%;\" /><div>SOAMIAVY 22 JUILLET 2026</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/3070414086484906/', NULL, 2, 'publie', '2026-07-22 02:24:03', '2026-07-23 15:31:47'),
(118, 'VAOVAO AN-TSARY 19H30 - 21 JOLAY 2026', '<div><img src=\"https://scontent-cdg6-1.xx.fbcdn.net/v/t15.5256-10/753772977_1341669404745258_3593447451875805570_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=102&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=5aFrzPE-HhgQ7kNvwGCrIJu&_nc_oc=AdpuaRrNuWVmyomq9XykvuypgA8HRQgfMLoEZQ4Bd5LYs3UOyyHHsWordeLdoKTia6Q&_nc_zt=23&_nc_ht=scontent-cdg6-1.xx&_nc_gid=Bjn0gD2iVIgqVLVvo8FBHQ&_nc_ss=7b289&oh=00_AQCSLI8a7aL5IWpcRmbg7rh1Ig3uUkAlr7vREcVWG66t_Q&oe=6A66C1E6\" style=\"width: 100%;\" /><div>VAOVAO AN-TSARY 19H30 - 21 JOLAY 2026</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/1368649551860609/', NULL, 2, 'publie', '2026-07-21 15:29:51', '2026-07-23 15:31:47'),
(119, 'FANDAHARANA MANOKANA  BNGRC Tamatave -20 JUILLET 2026', '<div><img src=\"https://scontent-lga3-1.xx.fbcdn.net/v/t15.5256-10/753192626_2040098690226483_2891853688902543040_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=103&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=3Rc0xfzZNpEQ7kNvwFaNt2Z&_nc_oc=Ado7ej73Zfa-eN5qa910JJx9TOwkySYNVphEPXlaAGSf05s_bxELLGAZncVr45PWnMc&_nc_zt=23&_nc_ht=scontent-lga3-1.xx&_nc_gid=j3OO0MPN0-GOx90ih0oLZA&_nc_ss=7b289&oh=00_AQDHZRkZxlFoNaFlDCKD-gQ3lQoUXgJ4Kc10V2MoZa1TSg&oe=6A66AA76\" style=\"width: 100%;\" /><div>FANDAHARANA MANOKANA  BNGRC Tamatave -20 JUILLET 2026</div></div>', 'https://www.facebook.com/reel/2045709083497874/', NULL, 2, 'publie', '2026-07-21 15:09:47', '2026-07-23 15:31:47'),
(120, 'VAOVAO AN-TSARY 13H00 - 21 JUILLET 2026', '<div><img src=\"https://scontent-ams2-1.xx.fbcdn.net/v/t15.5256-10/753052746_2108852986681073_1103499602173911477_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=106&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=212qGQNAC7UQ7kNvwEjIKBI&_nc_oc=Ado_H_0Gx7ig2zSXtQCXX3RqzKpr44y4FzwTdGuVbzUQsA8CNYvvuMyBreU52LcXXjY7H75HWUkeCFDMZXBGC5LN&_nc_zt=23&_nc_ht=scontent-ams2-1.xx&_nc_gid=uZUoxE9Y08LuAKhqKOglVQ&_nc_ss=7b289&oh=00_AQAtndn4oyjOSm2prPQ8a_lmWzIufrKdIXz5XLhtmootmw&oe=6A664F9D\" style=\"width: 100%;\" /><div>VAOVAO AN-TSARY 13H00 - 21 JUILLET 2026</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/1266334545435917/', NULL, 2, 'publie', '2026-07-21 08:59:50', '2026-07-23 15:31:47'),
(216, 'SOAMIAVY 24 JUILLET 2026', '<div><img src=\"https://scontent-ams2-1.xx.fbcdn.net/v/t15.5256-10/754248002_1074040721680985_4756116112470245359_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=105&ccb=1-7&_nc_sid=1a6f08&_nc_ohc=lMw1shOwbgwQ7kNvwEYh7aV&_nc_oc=AdrwW0SmxlJl2Q62wEp9NugNb8wAKq0-GCGJh5pKVoDgqSZ_0GGssKKNo1MNAeAWIlmP8kBC54G4tdli7NuBGrah&_nc_zt=23&_nc_ht=scontent-ams2-1.xx&_nc_gid=6iopkSfpPCA-71-iDacA8g&_nc_ss=7b289&oh=00_AQC-9mxHZuYIr_6wwjSqzFYwUYN9iCI5X24QD08Md6W3bg&oe=6A68C75F\" style=\"width: 100%;\" /><div>SOAMIAVY 24 JUILLET 2026</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/2167220890809377/', NULL, 2, 'publie', '2026-07-24 02:21:29', '2026-07-24 04:39:47'),
(217, 'VAOVAO AN-TSARY 19H30 DU 23 JOLAY 2026', '<div><img src=\"https://scontent-ams2-1.xx.fbcdn.net/v/t15.5256-10/755734310_1057084979997478_7131372455706244219_n.jpg?stp=dst-jpg_p565x565_tt6&_nc_cat=105&ccb=1-7&_nc_sid=3a9e82&_nc_ohc=ZtX9dH-3Z7YQ7kNvwGAOhza&_nc_oc=AdqFJI4xWpshLQ3D-ZtJrTm4pxiPx6OJrLdF7Y5h1pbT6URl9jUbxZ8Ns1QTjUSo0RGnewQ-1ZzJkQtRY_yGgIKF&_nc_zt=23&_nc_ht=scontent-ams2-1.xx&_nc_gid=6iopkSfpPCA-71-iDacA8g&_nc_ss=7b289&oh=00_AQCDTKfycdficJuxuzc_kGq7wXpZ4HWjPDs9ZKIRO9_Sig&oe=6A68C202\" style=\"width: 100%;\" /><div>VAOVAO AN-TSARY 19H30 DU 23 JOLAY 2026</div></div>', 'https://m.facebook.com/televizionamalagasy/videos/2421710898355934/', NULL, 2, 'publie', '2026-07-23 15:30:32', '2026-07-24 04:39:47'),
(246, '24 Juillet 2026: Fisaonam- pirenena.', '<div><img src=\"https://scontent-ams2-1.xx.fbcdn.net/v/t39.30808-6/754971244_1508189117777143_8526065595515650764_n.jpg?_nc_cat=111&ccb=1-7&_nc_sid=2e5b1e&_nc_ohc=Q04bqou2X2AQ7kNvwGTrZIl&_nc_oc=Adp4JQCqu3ggPiH12yWyGhNUtPJa52u3jNFa2G6iwQ6UiWBo-UWBdSYgrju_TvQ8G802CBHxDAG-LowSmbSalbUK&_nc_zt=23&_nc_ht=scontent-ams2-1.xx&_nc_gid=eSnG3SgcJhPcTxHHcSfTVA&_nc_ss=7b289&oh=00_AQAuS44wNYFjCbs8x7aF09sIfhI87wVnFsY6WC6gOClW4A&oe=6A68A745\" style=\"width: 100%;\" /><div>24 Juillet 2026: Fisaonam- pirenena.</div></div>', 'https://m.facebook.com/permalink.php?id=100057582064106&story_fbid=pfbid0ppyk33LNTCS4C7vNKDRzbk5S5UqjSqBbpY7ekZDMa324fM4VNGRWkR8Tvi2xJzu2l', NULL, 5, 'publie', '2026-07-23 14:38:49', '2026-07-24 04:39:48');

-- --------------------------------------------------------

--
-- Structure de la table `garde`
--

CREATE TABLE `garde` (
  `id_garde` int(11) NOT NULL,
  `id_service` int(11) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `est_exceptionnel` tinyint(1) DEFAULT 0,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `garde`
--

INSERT INTO `garde` (`id_garde`, `id_service`, `date_debut`, `date_fin`, `est_exceptionnel`, `notes`) VALUES
(1, 5, '2026-01-03', '2026-01-10', 0, NULL),
(2, 6, '2026-01-10', '2026-01-17', 0, NULL),
(3, 3, '2026-01-17', '2026-01-24', 0, NULL),
(4, 2, '2026-01-24', '2026-01-31', 0, NULL),
(5, 8, '2026-01-31', '2026-02-07', 0, NULL),
(6, 1, '2026-02-07', '2026-02-14', 0, NULL),
(8, 7, '2026-02-21', '2026-02-28', 0, NULL),
(9, 5, '2026-02-28', '2026-03-07', 0, NULL),
(10, 6, '2026-03-07', '2026-03-14', 0, NULL),
(11, 3, '2026-03-14', '2026-03-21', 0, NULL),
(12, 2, '2026-03-21', '2026-03-28', 0, NULL),
(13, 8, '2026-03-28', '2026-04-04', 0, NULL),
(14, 1, '2026-04-04', '2026-04-11', 0, NULL),
(16, 7, '2026-04-18', '2026-04-25', 0, NULL),
(17, 5, '2026-04-25', '2026-05-02', 0, NULL),
(18, 6, '2026-05-02', '2026-05-09', 0, NULL),
(19, 3, '2026-05-09', '2026-05-16', 0, NULL),
(20, 2, '2026-05-16', '2026-05-23', 0, NULL),
(21, 8, '2026-05-23', '2026-05-30', 0, NULL),
(22, 1, '2026-05-30', '2026-06-06', 0, NULL),
(24, 7, '2026-06-13', '2026-06-20', 0, NULL),
(25, 5, '2026-06-20', '2026-06-27', 0, NULL),
(26, 6, '2026-06-27', '2026-07-04', 0, NULL),
(27, 3, '2026-07-04', '2026-07-11', 0, NULL),
(28, 2, '2026-07-11', '2026-07-18', 0, NULL),
(29, 8, '2026-07-18', '2026-07-25', 0, NULL),
(30, 1, '2026-07-25', '2026-08-01', 0, NULL),
(32, 7, '2026-08-08', '2026-08-15', 0, NULL),
(33, 5, '2026-08-15', '2026-08-22', 0, NULL),
(34, 6, '2026-08-22', '2026-08-29', 0, NULL),
(35, 3, '2026-08-29', '2026-09-05', 0, NULL),
(36, 2, '2026-09-05', '2026-09-12', 0, NULL),
(37, 8, '2026-09-12', '2026-09-19', 0, NULL),
(38, 1, '2026-09-19', '2026-09-26', 0, NULL),
(40, 7, '2026-10-03', '2026-10-10', 0, NULL),
(41, 5, '2026-10-10', '2026-10-17', 0, NULL),
(42, 6, '2026-10-17', '2026-10-24', 0, NULL),
(43, 3, '2026-10-24', '2026-10-31', 0, NULL),
(44, 2, '2026-10-31', '2026-11-07', 0, NULL),
(45, 8, '2026-11-07', '2026-11-14', 0, NULL),
(46, 1, '2026-11-14', '2026-11-21', 0, NULL),
(48, 7, '2026-11-28', '2026-12-05', 0, NULL),
(49, 5, '2026-12-05', '2026-12-12', 0, NULL),
(50, 6, '2026-12-12', '2026-12-19', 0, NULL),
(51, 3, '2026-12-19', '2026-12-26', 0, NULL),
(52, 2, '2026-12-26', '2027-01-02', 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `log`
--

CREATE TABLE `log` (
  `id_log` int(11) NOT NULL,
  `type_log` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `date_log` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_utilisateur` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `quartier`
--

CREATE TABLE `quartier` (
  `id_quartier` int(11) NOT NULL,
  `nom_quartier` varchar(100) NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `quartier`
--

INSERT INTO `quartier` (`id_quartier`, `nom_quartier`, `latitude`, `longitude`) VALUES
(1, 'Place Kabary', -12.2697217, 49.2917480),
(2, 'Avenir', -12.2813217, 49.2914384),
(3, 'SCAMA', -12.3212863, 49.2968186),
(4, 'Lazaret Nord', -12.2807425, 49.3018896),
(5, 'Lazaret Sud', -12.2888907, 49.3021674),
(6, 'Grand Pavois', -12.2925245, 49.2873116),
(7, 'Tanambao V', -12.2881553, 49.2854104),
(8, 'Ambalavola', -12.3185580, 49.2816082),
(9, 'Soafeno', -12.2959698, 49.2904805),
(10, 'Morafeno', -12.3019370, 49.2980863),
(11, 'Mahatsara', -12.2937004, 49.2778063),
(12, 'Cité Ouvrière', -12.2977853, 49.3006218),
(13, 'Tsaramandroso', -12.2958125, 49.2858594),
(14, 'Bazar Kely', NULL, NULL),
(15, 'Manongalaza', -12.3233380, 49.2905003),
(16, 'Tanambao Nord', -12.2865206, 49.2939664),
(17, 'Tanambao Sud', -12.2902052, 49.2936494),
(18, 'Centre-ville', -12.2851300, 49.2936700),
(19, 'Tanambao IV', NULL, NULL),
(20, 'Tanambao Tsena', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `role`
--

CREATE TABLE `role` (
  `id_role` int(11) NOT NULL,
  `nom_role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `role`
--

INSERT INTO `role` (`id_role`, `nom_role`) VALUES
(3, 'Administrateur'),
(2, 'Redacteur'),
(1, 'Visiteur');

-- --------------------------------------------------------

--
-- Structure de la table `service`
--

CREATE TABLE `service` (
  `id_service` int(11) NOT NULL,
  `libelle` varchar(255) NOT NULL,
  `telephone` varchar(50) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `id_quartier` int(11) NOT NULL,
  `id_type` int(11) NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_modification` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `service`
--

INSERT INTO `service` (`id_service`, `libelle`, `telephone`, `adresse`, `latitude`, `longitude`, `id_quartier`, `id_type`, `actif`, `description`, `date_creation`, `date_modification`) VALUES
(1, 'Pharmacie Issa', '020 82 227 15', 'P76V+99R, Rte De L\'Ankarana, Antsiranana 201', NULL, NULL, 17, 1, 1, 'Située a cote de BOA Tanabao Sud', '2026-07-18 09:19:12', '2026-07-24 19:22:55'),
(2, 'Pharmacie Mahasoa', '032 09 219 27', 'P75P+FQ, Grand Pavois, Lot 181, Antsiranana 201', NULL, NULL, 6, 1, 1, NULL, '2026-07-18 09:19:12', '2026-07-24 19:22:55'),
(3, 'Pharmacie Mora', '032 78 826 04', 'P7HR+5XW, Place Kabary, Antsiranana 201', -12.2719700, 49.2923800, 1, 1, 1, NULL, '2026-07-18 09:19:12', '2026-07-24 19:48:47'),
(5, 'Pharmacie Henintsoa', '034 06 480 98', 'P76R+62, Lot 201, 206 Rue Justin Bezara, Antsiranana 201', NULL, NULL, 19, 1, 1, NULL, '2026-07-18 09:19:12', '2026-07-24 19:22:55'),
(6, 'Pharmacie Avenir', '000 00 000 00', 'P79R+FH, 32 Rue Lafayette, Antsiranana 201', NULL, NULL, 2, 1, 1, NULL, '2026-07-18 09:19:12', '2026-07-20 09:47:18'),
(7, 'Pharmacie Olga', '032 04 207 88', 'P7JV+3F, 2 Rue Mangin, Antsiranana 201', -12.2697500, NULL, 1, 1, 1, NULL, '2026-07-18 09:19:12', '2026-07-24 19:48:47'),
(8, 'Pharmacie Mahavavy', '020 82 223 15', 'Tanambao Tsena, P75R+HVV, Lot 3 016 10, Rue Point Six, Antsiranana 201', -12.2800000, 49.2900000, 20, 1, 1, NULL, '2026-07-18 09:19:12', '2026-07-24 19:48:47'),
(9, 'Pompier Antsiranana', '032 63 505 56', 'P72R+VFW, Soafeno, Antsiranana 201', NULL, NULL, 9, 2, 1, NULL, '2026-07-18 09:19:12', '2026-07-20 09:47:18'),
(10, 'hopitale Homi', '034 14 586 41', ' Antsiranana 201', -12.0000000, 49.0000000, 18, 4, 1, NULL, '2026-07-18 09:19:12', '2026-07-24 19:54:57'),
(11, 'Ambulance Hopitale BE', '032 03 088 10', 'P7JW+9RR, Antsiranana 201', -12.2686500, 49.2971000, 1, 4, 1, NULL, '2026-07-18 09:19:12', '2026-07-24 19:48:47'),
(12, 'Ambulance Policlinique', '034 49 110 11', 'M8J2+6H, Antsiranana 201', NULL, NULL, 3, 4, 1, NULL, '2026-07-18 09:19:12', '2026-07-24 19:22:55'),
(14, 'FIP', '034 05 998 60', 'P75Q+WW, Tanambao IV, Antsiranana 201', -12.2901900, 49.2897800, 19, 3, 1, NULL, '2026-07-18 09:19:12', '2026-07-24 19:48:47'),
(15, 'Police Manogalaza', '034 05 440 66', 'Quartier Manongalaza', NULL, NULL, 15, 3, 1, NULL, '2026-07-18 09:19:12', '2026-07-18 09:39:44'),
(16, 'Police Centrale', '032 86 772 25', 'P77V+QRV, RN6, Antsiranana 201 -12.2855600  49.2945300', -12.2855600, 49.2945300, 18, 3, 1, NULL, '2026-07-18 09:19:12', '2026-07-24 19:52:00'),
(17, 'Police Tanambao', '034 99 443 44', 'RCV4+3X4, Rue De Farafaty, Toamasina 501', NULL, NULL, 17, 3, 1, NULL, '2026-07-18 09:19:12', '2026-07-20 09:47:18'),
(18, 'Hôpital Manara-Penitra', '+230 57 376 759', ' Avenue Pasteur, Antsiranana 201 ', -12.2855600, 49.2945300, 16, 4, 1, NULL, '2026-07-20 09:47:18', '2026-07-24 19:52:54'),
(20, 'Homéopharma Diego', '034 49 150 40', 'M7XP+JQ3, Route d\'Anamakia, Antsiranana 201', NULL, NULL, 1, 1, 1, 'Pharmacie sans tour de garde', '2026-07-20 09:47:18', '2026-07-20 09:47:18'),
(24, 'Pharmacie SCAMA', '000 00 000 00', 'M7PV+2X, SCAMA, Antsiranana 201', NULL, NULL, 3, 1, 1, NULL, '2026-07-24 19:22:55', '2026-07-24 19:22:55');

-- --------------------------------------------------------

--
-- Structure de la table `sources_articles`
--

CREATE TABLE `sources_articles` (
  `id_source` int(11) NOT NULL,
  `nom_source` varchar(150) NOT NULL,
  `type_source` enum('rss','reseau_social') NOT NULL DEFAULT 'rss',
  `url_flux` varchar(500) NOT NULL,
  `identifiant_page` varchar(150) DEFAULT NULL,
  `url_instance_bridge` varchar(255) DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `date_ajout` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `sources_articles`
--

INSERT INTO `sources_articles` (`id_source`, `nom_source`, `type_source`, `url_flux`, `identifiant_page`, `url_instance_bridge`, `actif`, `date_ajout`) VALUES
(2, 'TVM - Télévision Malagasy', 'rss', 'https://rss.app/feeds/YWDnoHaTjFT5BLvE.xml', NULL, NULL, 1, '2026-07-17 10:51:44'),
(3, 'Commune Urbaine de Diego-Suarez Officiel Page', 'rss', 'https://rss.app/feeds/la9wSCpXRhfVMaIn.xml', NULL, NULL, 1, '2026-07-21 11:50:03'),
(5, 'TVM Antsiranana 《Télévision Varatraza 》', 'rss', 'https://rss.app/feeds/kjzhHtJWKZM1wF1c.xml', NULL, NULL, 1, '2026-07-21 12:00:16');

-- --------------------------------------------------------

--
-- Structure de la table `type_service`
--

CREATE TABLE `type_service` (
  `id_type` int(11) NOT NULL,
  `nom_type` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `type_service`
--

INSERT INTO `type_service` (`id_type`, `nom_type`) VALUES
(3, 'Force de l\'ordre'),
(4, 'Hôpital'),
(1, 'Pharmacie'),
(2, 'Pompier');

-- --------------------------------------------------------

--
-- Structure de la table `type_vehicule`
--

CREATE TABLE `type_vehicule` (
  `id_type_vehicule` int(11) NOT NULL,
  `nom_type` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `type_vehicule`
--

INSERT INTO `type_vehicule` (`id_type_vehicule`, `nom_type`) VALUES
(1, 'Ambulance'),
(4, 'Camion'),
(3, 'Fourgon'),
(2, 'Véhicule de livraison');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id_utilisateur` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `date_naissance` date DEFAULT NULL,
  `id_quartier` int(11) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `id_role` int(11) NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_utilisateur`, `nom`, `prenom`, `date_naissance`, `id_quartier`, `email`, `mot_de_passe`, `id_role`, `actif`, `date_creation`) VALUES
(5, 'Hamzah', 'Nasser', '2005-07-15', 5, 'nasserhazmah@gmail.com', '15e2b0d3c33891ebb0f1ef609ec419420c20e320ce94c65fbc8c3312448eb225', 3, 1, '2026-07-09 12:11:48'),
(9, 'Hamzah', 'Misizara', '2005-07-12', 5, 'hamzah@gmail.com', '15e2b0d3c33891ebb0f1ef609ec419420c20e320ce94c65fbc8c3312448eb225', 2, 1, '2026-07-18 10:29:26'),
(11, 'Razakaria', 'Brenda Anissa', '2006-05-26', 4, 'brendarazakaria5@gmail.com', '15e2b0d3c33891ebb0f1ef609ec419420c20e320ce94c65fbc8c3312448eb225', 1, 1, '2026-07-20 10:16:37');

-- --------------------------------------------------------

--
-- Structure de la table `vehicule`
--

CREATE TABLE `vehicule` (
  `id_vehicule` int(11) NOT NULL,
  `id_service` int(11) NOT NULL,
  `id_type_vehicule` int(11) NOT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `vehicule`
--

INSERT INTO `vehicule` (`id_vehicule`, `id_service`, `id_type_vehicule`, `nom`, `telephone`, `actif`) VALUES
(1, 10, 1, 'Ambulance Homi', NULL, 1),
(2, 11, 1, 'Ambulance Hopitale BE', NULL, 1),
(3, 12, 1, 'Ambulance Policlinique', NULL, 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `article`
--
ALTER TABLE `article`
  ADD PRIMARY KEY (`id_article`),
  ADD UNIQUE KEY `lien_source_unique` (`lien_source`),
  ADD KEY `id_auteur` (`id_auteur`),
  ADD KEY `articles_ibfk_2` (`id_source`);

--
-- Index pour la table `garde`
--
ALTER TABLE `garde`
  ADD PRIMARY KEY (`id_garde`),
  ADD KEY `id_service` (`id_service`);

--
-- Index pour la table `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `quartier`
--
ALTER TABLE `quartier`
  ADD PRIMARY KEY (`id_quartier`),
  ADD UNIQUE KEY `nom_quartier` (`nom_quartier`);

--
-- Index pour la table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id_role`),
  ADD UNIQUE KEY `nom_role` (`nom_role`);

--
-- Index pour la table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id_service`),
  ADD KEY `id_quartier` (`id_quartier`),
  ADD KEY `id_type` (`id_type`);

--
-- Index pour la table `sources_articles`
--
ALTER TABLE `sources_articles`
  ADD PRIMARY KEY (`id_source`);

--
-- Index pour la table `type_service`
--
ALTER TABLE `type_service`
  ADD PRIMARY KEY (`id_type`),
  ADD UNIQUE KEY `nom_type` (`nom_type`);

--
-- Index pour la table `type_vehicule`
--
ALTER TABLE `type_vehicule`
  ADD PRIMARY KEY (`id_type_vehicule`),
  ADD UNIQUE KEY `nom_type` (`nom_type`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id_utilisateur`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_role` (`id_role`),
  ADD KEY `id_quartier` (`id_quartier`);

--
-- Index pour la table `vehicule`
--
ALTER TABLE `vehicule`
  ADD PRIMARY KEY (`id_vehicule`),
  ADD KEY `id_service` (`id_service`),
  ADD KEY `id_type_vehicule` (`id_type_vehicule`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `article`
--
ALTER TABLE `article`
  MODIFY `id_article` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=360;

--
-- AUTO_INCREMENT pour la table `garde`
--
ALTER TABLE `garde`
  MODIFY `id_garde` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT pour la table `log`
--
ALTER TABLE `log`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `quartier`
--
ALTER TABLE `quartier`
  MODIFY `id_quartier` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `role`
--
ALTER TABLE `role`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `service`
--
ALTER TABLE `service`
  MODIFY `id_service` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT pour la table `sources_articles`
--
ALTER TABLE `sources_articles`
  MODIFY `id_source` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `type_service`
--
ALTER TABLE `type_service`
  MODIFY `id_type` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `type_vehicule`
--
ALTER TABLE `type_vehicule`
  MODIFY `id_type_vehicule` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `vehicule`
--
ALTER TABLE `vehicule`
  MODIFY `id_vehicule` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `article`
--
ALTER TABLE `article`
  ADD CONSTRAINT `article_ibfk_1` FOREIGN KEY (`id_auteur`) REFERENCES `utilisateur` (`id_utilisateur`),
  ADD CONSTRAINT `article_ibfk_2` FOREIGN KEY (`id_source`) REFERENCES `sources_articles` (`id_source`) ON DELETE SET NULL;

--
-- Contraintes pour la table `garde`
--
ALTER TABLE `garde`
  ADD CONSTRAINT `garde_ibfk_1` FOREIGN KEY (`id_service`) REFERENCES `service` (`id_service`) ON DELETE CASCADE;

--
-- Contraintes pour la table `log`
--
ALTER TABLE `log`
  ADD CONSTRAINT `log_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`);

--
-- Contraintes pour la table `service`
--
ALTER TABLE `service`
  ADD CONSTRAINT `service_ibfk_1` FOREIGN KEY (`id_quartier`) REFERENCES `quartier` (`id_quartier`),
  ADD CONSTRAINT `service_ibfk_2` FOREIGN KEY (`id_type`) REFERENCES `type_service` (`id_type`);

--
-- Contraintes pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `utilisateur_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role` (`id_role`),
  ADD CONSTRAINT `utilisateur_ibfk_2` FOREIGN KEY (`id_quartier`) REFERENCES `quartier` (`id_quartier`);

--
-- Contraintes pour la table `vehicule`
--
ALTER TABLE `vehicule`
  ADD CONSTRAINT `vehicule_ibfk_1` FOREIGN KEY (`id_service`) REFERENCES `service` (`id_service`) ON DELETE CASCADE,
  ADD CONSTRAINT `vehicule_ibfk_2` FOREIGN KEY (`id_type_vehicule`) REFERENCES `type_vehicule` (`id_type_vehicule`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


# gemini ma propose de cree une nouvele table 
-- Base de données : `urgences_antsiranana`

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Structure de la table `lieu`
--

CREATE TABLE IF NOT EXISTS `lieu` (
  `id_lieu` int(11) NOT NULL AUTO_INCREMENT,
  `google_place_id` varchar(255) DEFAULT NULL,
  `nom` varchar(255) NOT NULL,
  `categorie` enum('hopital','police','pompier','pharmacie','mairie','autre') NOT NULL DEFAULT 'autre',
  `quartier` varchar(100) NOT NULL COMMENT 'Fokontany / Quartier à Antsiranana',
  `adresse` varchar(500) DEFAULT NULL,
  `latitude` decimal(10,8) NOT NULL COMMENT 'Coordonnées réelles Google Maps',
  `longitude` decimal(11,8) NOT NULL COMMENT 'Coordonnées réelles Google Maps',
  `telephone` varchar(50) DEFAULT NULL,
  `url_google_maps` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id_lieu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des services d'urgence réels à Antsiranana
--

INSERT INTO `lieu` (`id_lieu`, `google_place_id`, `nom`, `categorie`, `quartier`, `adresse`, `latitude`, `longitude`, `telephone`, `url_google_maps`) VALUES

-- SANTÉ / HÔPITAUX / CLINIQUES
(1, 'ChIJnS4pP01-1i8Rt4y-3V59B8g', 'Centre Hospitalier Régional de Référence (CHRR) Tanambao', 'hopital', 'Tanambao III', 'Boulevard Etienne, Antsiranana 201', -12.28541210, 49.29215430, '+261 32 02 451 12', 'https://maps.google.com/?q=-12.28541210,49.29215430'),
(2, 'ChIJ_X7s90R-1i8RdM_wJ4c2V7E', 'Hôpital d\'Instruction des Armées (HIA) / Hôpital Militaire', 'hopital', 'Place Kabary', 'Rue Lally Tollendal, Antsiranana 201', -12.27114250, 49.29132100, '+261 20 82 221 04', 'https://maps.google.com/?q=-12.27114250,49.29132100'),
(3, 'ChIJY8m_vUh-1i8RbA8z_5u_12a', 'Clinique Brivet (Centre Médical)', 'hopital', 'Bazar Kely', 'Rue Brivet, Antsiranana 201', -12.27981000, 49.29153000, '+261 34 02 211 50', 'https://maps.google.com/?q=-12.27981000,49.29153000'),

-- SÉCURITÉ / POLICE / GENDARMERIE
(4, 'ChIJLz91-Eh-1i8RQ3aK9jL8s3c', 'Commissariat Central de Police', 'police', 'Ville Basse', 'Rue Colbert / Rue Baste, Antsiranana 201', -12.27312000, 49.29184000, '+261 34 05 998 20', 'https://maps.google.com/?q=-12.27312000,49.29184000'),
(5, 'ChIJs2X4x0p-1i8RfM3b9A7y61k', 'Groupement de Gendarmerie Nationale DIANA', 'police', 'Tanambao I', 'Route de la Pyrotechnie, Antsiranana 201', -12.28115000, 49.28912000, '+261 34 14 010 35', 'https://maps.google.com/?q=-12.28115000,49.28912000'),

-- SECOURS / POMPIERS
(6, 'ChIJ81m2cUh-1i8Rk1m3X4P998a', 'Caserne des Sapeurs-Pompiers (CUDS)', 'pompier', 'Soafeno', 'Avenue Sadi Carnot, Antsiranana 201', -12.27785000, 49.28990000, '18', 'https://maps.google.com/?q=-12.27785000,49.28990000'),

-- INSTITUTIONS / MAIRIE
(7, 'ChIJk18AZEh-1i8R55GjY3WvM8M', 'Hôtel de Ville / Lapan\'ny Tanàna (Commune Urbaine)', 'mairie', 'Place Kabary', 'Rue Colbert, Antsiranana 201', -12.26821500, 49.29198000, '+261 20 82 212 11', 'https://maps.google.com/?q=-12.26821500,49.29198000'),

-- PHARMACIES
(8, 'ChIJg_v_O0h-1i8Rp019V8x_L12', 'Pharmacie du Progrès', 'pharmacie', 'Place Kabary', 'Rue Colbert, Antsiranana 201', -12.26945000, 49.29178000, '+261 20 82 222 10', 'https://maps.google.com/?q=-12.26945000,49.29178000'),
(9, 'ChIJb02_-Uh-1i8Rq99_3L12M21', 'Pharmacie d\'Antsiranana (Tanambao)', 'pharmacie', 'Tanambao II', 'Boulevard Etienne, Antsiranana 201', -12.28391000, 49.29230000, '+261 20 82 214 05', 'https://maps.google.com/?q=-12.28391000,49.29230000');

COMMIT;

Récapitulatif des coordonnées géographique réelles à Antsiranana :ServiceQuartier / FokontanyLatitudeLongitudeCHRR TanambaoTanambao III-12.2854121049.29215430Hôpital MilitairePlace Kabary-12.2711425049.29132100Commissariat CentralVille Basse / Centre-12.2731200049.29184000Sapeurs-PompiersSoafeno-12.2778500049.28990000Hôtel de VillePlace Kabary-12.2682150049.29198000GendarmerieTanambao I-12.2811500049.28912000Remarque technique :Les coordonnées d'Antsiranana commencent toutes par une latitude négative (-12.xxxx) car la ville se situe dans l'hémisphère sud, et une longitude positive (49.xxxx) dans l'hémisphère est.Ce format SQL permet une intégration directe dans Leaflet, Google Maps JavaScript API ou OpenStreetMap.