-- MySQL dump 10.13  Distrib 8.0.42, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: recipe_network
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `recipe_id` (`recipe_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (44,5,11,'πολύ ωραία συνταγή','2025-05-16 01:32:21'),(63,5,10,'nice','2025-05-16 01:45:28'),(64,5,10,'very good','2025-05-16 01:45:34');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `likes`
--

DROP TABLE IF EXISTS `likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `likes` (
  `user_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`recipe_id`),
  KEY `recipe_id` (`recipe_id`),
  CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `likes`
--

LOCK TABLES `likes` WRITE;
/*!40000 ALTER TABLE `likes` DISABLE KEYS */;
INSERT INTO `likes` VALUES (5,11);
/*!40000 ALTER TABLE `likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recipes`
--

DROP TABLE IF EXISTS `recipes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recipes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `ingredients` text NOT NULL,
  `instructions` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recipes`
--

LOCK TABLES `recipes` WRITE;
/*!40000 ALTER TABLE `recipes` DISABLE KEYS */;
INSERT INTO `recipes` VALUES (8,5,'κοτόπουλο μπουτάκια με πατάτες στο φούρνο','2 μπουτάκια κοτόπουλου\r\n4 μεγάλες πατάτες κομμένες σε κύβους\r\n4 σκελίδες σκόρδο\r\n2 κουταλιές της σούπας ελαιόλαδο\r\nΧυμός από 1 λεμόνι\r\n1 κουταλάκι ρίγανη\r\nΑλάτι\r\nΠιπέρι','2 μπουτάκια κοτόπουλου\r\n4 μεγάλες πατάτες κομμένες σε κύβους\r\n4 σκελίδες σκόρδο\r\n2 κουταλιές της σούπας ελαιόλαδο\r\nΧυμός από 1 λεμόνι\r\n1 κουταλάκι ρίγανη\r\nΑλάτι\r\nΠιπέρι','assets/uploads/recipe_68266b61a3c955.52456062_Screenshot_2025-05-16_012230.png','2025-05-15 22:32:01'),(10,5,'Μακαρόνια με κιμά και τριμμένο τυρί','500γρ μακαρόνια (σπαγγέτι)\r\n400γρ κιμάς μοσχαρίσιος\r\n1 ξερό κρεμμύδι ψιλοκομμένο\r\n2 σκελίδες σκόρδο λιωμένες\r\n1 κονσέρβα συμπυκνωμένος χυμός ντομάτας\r\n1 κουταλιά της σούπας ελαιόλαδο\r\nΑλάτι\r\nΠιπέρι\r\n1 πρέζα κανέλα (προαιρετικά)\r\nΤριμμένο τυρί (π.χ. κεφαλοτύρι ή παρμεζάνα)','1. Βράζουμε τα μακαρόνια σε αλατισμένο νερό σύμφωνα με τις οδηγίες του πακέτου.\r\n2. Σε βαθύ τηγάνι ζεσταίνουμε το ελαιόλαδο και σοτάρουμε το κρεμμύδι και το σκόρδο.\r\n3. Προσθέτουμε τον κιμά και ανακατεύουμε μέχρι να πάρει χρώμα και να \"στεγνώσει\".\r\n4. Ρίχνουμε τον χυμό ντομάτας, αλατοπιπερώνουμε και προσθέτουμε (αν θέλουμε) λίγη κανέλα.\r\n5. Αφήνουμε τη σάλτσα να σιγοβράσει για 15-20 λεπτά μέχρι να δέσει.\r\n6. Σουρώνουμε τα μακαρόνια και τα ανακατεύουμε με τη σάλτσα.\r\n7. Σερβίρουμε ζεστά με μπόλικο τριμμένο τυρί από πάνω.','assets/uploads/recipe_6826927764ccf5.23122599_Screenshot_2025-05-16_041835.png','2025-05-16 01:18:47'),(11,6,'Μουσακάς παραδοσιακός με μελιτζάνες και κιμά','3 μελιτζάνες φλάσκες\r\n3 πατάτες\r\n500γρ κιμάς μοσχαρίσιος\r\n1 κρεμμύδι ψιλοκομμένο\r\n2 σκελίδες σκόρδο\r\n1 κονσέρβα ντομάτας ψιλοκομμένη\r\n1/2 φλ. λευκό κρασί (προαιρετικά)\r\n1/2 φλ. ελαιόλαδο\r\nΑλάτι, πιπέρι, μοσχοκάρυδο, κανέλα\r\nΤριμμένο κεφαλοτύρι (για επικάλυψη)\r\n\r\nΓια την μπεσαμέλ:\r\n4 κουταλιές βούτυρο\r\n5 κουταλιές αλεύρι\r\n750ml φρέσκο γάλα\r\n2 αυγά\r\n1/2 φλ. τριμμένο τυρί\r\nΑλάτι, μοσχοκάρυδο','1. Κόβουμε τις μελιτζάνες και τις πατάτες σε φέτες. Τηγανίζουμε ελαφρά ή ψήνουμε στον φούρνο με λίγο λάδι.\r\n2. Σε κατσαρόλα σωτάρουμε το κρεμμύδι και το σκόρδο με ελαιόλαδο. Προσθέτουμε τον κιμά και ανακατεύουμε μέχρι να αλλάξει χρώμα.\r\n3. Σβήνουμε με κρασί (αν χρησιμοποιούμε), ρίχνουμε ντομάτα, κανέλα, αλάτι, πιπέρι και σιγοβράζουμε για 20 λεπτά.\r\n4. Ετοιμάζουμε τη μπεσαμέλ: Λιώνουμε το βούτυρο, προσθέτουμε το αλεύρι και ανακατεύουμε. Προσθέτουμε σταδιακά το γάλα ανακατεύοντας μέχρι να πήξει. Κατεβάζουμε από τη φωτιά και προσθέτουμε αυγά, τυρί, αλάτι και μοσχοκάρυδο.\r\n5. Σε ταψί στρώνουμε πατάτες → μελιτζάνες → κιμά → επαναλαμβάνουμε αν έχουμε υλικά.\r\n6. Ρίχνουμε από πάνω τη μπεσαμέλ, πασπαλίζουμε με τυρί.\r\n7. Ψήνουμε στους 180°C για 40-45 λεπτά μέχρι να ροδίσει.\r\n8. Αφήνουμε να σταθεί για 20 λεπτά πριν κόψουμε.','assets/uploads/recipe_6826930b52ccd0.32588742_Screenshot_2025-05-16_042103.png','2025-05-16 01:21:15');
/*!40000 ALTER TABLE `recipes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `bio` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (5,'elpidoforos','moisiadis.2004.em@gmail.com','$2y$10$ILKo/TWPJ1lwxlprksjkU.yZvvS9iM2CHV8wnlf.F3XzSGrkQZHAG',NULL,'2025-04-17 00:46:48'),(6,'kwstas','moisiadi.2004.em@gmail.com','$2y$10$0C8mrP1JJJX0C39DOvwKRO0Sn3gFSx.r6pTDo0NzsTnSgwBSv.pIS',NULL,'2025-05-16 01:19:51');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-01 23:53:16
