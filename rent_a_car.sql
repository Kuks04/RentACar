-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 30, 2023 at 02:18 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rent_a_car`
--

-- --------------------------------------------------------

--
-- Table structure for table `automobil`
--

CREATE TABLE `automobil` (
  `id` int(3) NOT NULL,
  `marka` varchar(30) NOT NULL,
  `opis` text NOT NULL,
  `slika` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `automobil`
--

INSERT INTO `automobil` (`id`, `marka`, `opis`, `slika`) VALUES
(1, 'BMW M3 E46', 'The BMW M3 E46 Coupé took the hearts of real sports car fans by storm when it launched in 2000. With a low weight construction and the high-rev concept of its inline 6-cylinder engine, for many it embodied a return to the virtues of the iconic BMW M3 E30. The following year, a convertible followed, combining a sporty experience and the luxury of open-top driving in an unprecedented way.', '1692284990_6efdf7d0-bmw-m3-e46-.jpg'),
(2, 'BMW M5 CS', 'The BMW M5 CS is an exceptional high-performance model with a unique combination of power, driving dynamics, exclusivity and emotional design. As a four-door sedan, it can be used in all situations and is at the same time predestined for fast laps on racetracks like the Nürburgring Nordschleife, on which the BMW M engineers did the fine tuning for their suspension set-up.', '1692285032_BMW M5 CS front slide.jpg'),
(3, 'BMW M3 F80', 'The effects on the response characteristics of the M3 and M4 models are correspondingly clear: 431hp and a maximum of 7,600rpm indicate the potential of the 3.0 litre motor. With a maximum torque of 550Nm – from 1,850rpm upwards – the motor catapults the new M automobiles into new dimensions of performance. Quick sprints are performed much faster than ever before and the acceleration from 0 to 100kmh now takes just 4.1 seconds.', '1692285706_download.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `datumi`
--

CREATE TABLE `datumi` (
  `id` int(11) NOT NULL,
  `datum` date NOT NULL,
  `id_rezervacije` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `korisnik`
--

CREATE TABLE `korisnik` (
  `id` int(3) NOT NULL,
  `ime` varchar(30) NOT NULL,
  `prezime` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `lozinka` varchar(50) NOT NULL,
  `slika` varchar(100) NOT NULL DEFAULT 'user.svg',
  `id_korisnika` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `korisnik`
--

INSERT INTO `korisnik` (`id`, `ime`, `prezime`, `email`, `lozinka`, `slika`, `id_korisnika`) VALUES
(1, 'Ognjen', 'Kukalj', 'ognjenkuks@admin.com', 'admin123', '1692284941_me.jpeg', 2),
(2, 'Aleksa', 'Mitic', 'aleksamitic@gmail.com', 'aleksamitic', 'user.svg', 1),
(3, 'Pera', 'Peric', 'peraperic@gmail.com', 'peraperic', 'user.svg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `newsletter`
--

CREATE TABLE `newsletter` (
  `id` int(11) NOT NULL,
  `ime` varchar(30) NOT NULL,
  `prezime` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `newsletter`
--

INSERT INTO `newsletter` (`id`, `ime`, `prezime`, `email`) VALUES
(1, 'Aleksa', 'Mitic', 'aleksamitic@gmail.com'),
(2, 'Pera', 'Peric', 'peraperic@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `rezervacija`
--

CREATE TABLE `rezervacija` (
  `id_rezervacije` int(11) NOT NULL,
  `id_korisnika` int(11) NOT NULL,
  `id_automobila` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `automobil`
--
ALTER TABLE `automobil`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `datumi`
--
ALTER TABLE `datumi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_rezervacije` (`id_rezervacije`);

--
-- Indexes for table `korisnik`
--
ALTER TABLE `korisnik`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `newsletter`
--
ALTER TABLE `newsletter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rezervacija`
--
ALTER TABLE `rezervacija`
  ADD PRIMARY KEY (`id_rezervacije`),
  ADD KEY `id_korisnika` (`id_korisnika`),
  ADD KEY `id_automobila` (`id_automobila`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `automobil`
--
ALTER TABLE `automobil`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `datumi`
--
ALTER TABLE `datumi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `korisnik`
--
ALTER TABLE `korisnik`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `newsletter`
--
ALTER TABLE `newsletter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rezervacija`
--
ALTER TABLE `rezervacija`
  MODIFY `id_rezervacije` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `datumi`
--
ALTER TABLE `datumi`
  ADD CONSTRAINT `datumi_ibfk_1` FOREIGN KEY (`id_rezervacije`) REFERENCES `rezervacija` (`id_rezervacije`);

--
-- Constraints for table `rezervacija`
--
ALTER TABLE `rezervacija`
  ADD CONSTRAINT `rezervacija_ibfk_1` FOREIGN KEY (`id_korisnika`) REFERENCES `korisnik` (`id`),
  ADD CONSTRAINT `rezervacija_ibfk_2` FOREIGN KEY (`id_automobila`) REFERENCES `automobil` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
