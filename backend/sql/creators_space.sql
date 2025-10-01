-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 01, 2025 at 07:47 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `creators_space`
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `instructor_id` int(11) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `duration` varchar(100) DEFAULT NULL,
  `level` enum('beginner','intermediate','advanced') DEFAULT 'beginner',
  `category` varchar(100) DEFAULT 'general',
  `prerequisites` text DEFAULT NULL,
  `learning_objectives` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `featured` tinyint(1) DEFAULT 0,
  `total_lessons` int(11) DEFAULT 0,
  `total_duration_minutes` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`, `slug`, `description`, `instructor_id`, `image_url`, `price`, `duration`, `level`, `category`, `prerequisites`, `learning_objectives`, `is_active`, `featured`, `total_lessons`, `total_duration_minutes`, `created_at`, `updated_at`) VALUES
(1, 'Full Stack Web Development', 'full-stack-web-development', 'Learn complete web development from frontend to backend', 2, 'https://www.w3webschool.com/wp-content/uploads/2024/02/Full-Stack-Web-Development-Course-in-Kolkata.webp', 99.99, '12 weeks', 'intermediate', 'general', NULL, NULL, 1, 0, 0, 0, '2025-09-25 08:11:01', '2025-10-01 16:41:31'),
(2, 'UI/UX Design Fundamentals', 'ui-ux-design-fundamentals', 'Master the fundamentals of user interface and user experience design', 2, './assets/images/courses/uiux.jpeg', 79.99, '8 weeks', 'beginner', 'general', NULL, NULL, 1, 0, 0, 0, '2025-09-25 08:11:01', '2025-10-01 16:28:14'),
(3, 'JavaScript in 30 Days', 'javascript-30-days', 'Master JavaScript programming in 30 days with practical projects', 2, './assets/images/courses/jsin30days.png', 49.99, '4 weeks', 'beginner', 'general', NULL, NULL, 1, 0, 0, 0, '2025-09-25 08:11:01', '2025-10-01 16:33:58'),
(4, 'Java Programming Masterclass', 'java-programming-masterclass', 'Complete Java programming course from basics to advanced concepts. Learn OOP, data structures, algorithms, Spring Framework, and enterprise application development.', 2, './assets/images/courses/java.png', 249.99, '16 weeks', 'intermediate', 'Programming', NULL, NULL, 1, 0, 0, 0, '2025-09-25 17:05:56', '2025-10-01 17:29:20'),
(5, 'C++ Game Development', 'cpp-game-development', 'Learn C++ programming through game development. Build 2D and 3D games using modern C++ techniques, game engines, and graphics programming.', 2, 'https://img.itch.zone/aW1hZ2UyL2phbS8zODY2NjcvMTU3MzY4MTcucG5n/original/tAnW%2BJ.png', 199.99, '12 weeks', 'advanced', 'Programming', NULL, NULL, 1, 0, 0, 0, '2025-09-25 17:05:56', '2025-10-01 16:40:03'),
(8, 'Vue.js Complete Guide', 'vuejs-complete-guide', 'Master Vue.js framework with Composition API, Vuex, Vue Router, and modern development practices. Build scalable single-page applications.', 2, 'https://img-c.udemycdn.com/course/750x422/5157066_66bb.jpg', 159.99, '8 weeks', 'intermediate', 'Web Development', NULL, NULL, 1, 0, 0, 0, '2025-09-25 17:05:56', '2025-10-01 16:42:15'),
(9, 'Angular Enterprise Development', 'angular-enterprise-development', 'Build large-scale enterprise applications with Angular. Advanced patterns, testing, performance optimization, and deployment strategies.', 2, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTQ9EaI_YCZ_fKj1ELOrVVU3miLtpNpLtrjow&s', 279.99, '16 weeks', 'advanced', 'Web Development', NULL, NULL, 1, 0, 0, 0, '2025-09-25 17:05:56', '2025-10-01 16:46:01'),
(10, 'Next.js Full-Stack Development', 'nextjs-fullstack-development', 'Build production-ready applications with Next.js. Server-side rendering, API routes, authentication, and deployment to Vercel.', 2, 'https://codewithmosh.com/_next/image?url=https%3A%2F%2Fuploads.teachablecdn.com%2Fattachments%2F0dKhU49vRbiSSWknbHAR_1920X1357.jpg&w=3840&q=75', 199.99, '10 weeks', 'intermediate', 'Web Development', NULL, NULL, 1, 0, 0, 0, '2025-09-25 17:05:56', '2025-10-01 17:20:27'),
(11, 'GraphQL API Development', 'graphql-api-development', 'Master GraphQL for modern API development. Schema design, resolvers, subscriptions, and integration with popular databases and frameworks.', 2, 'https://d3f1iyfxxz8i1e.cloudfront.net/courses/course_image/da05b2f8cb74.jpg', 189.99, '8 weeks', 'advanced', 'Web Development', NULL, NULL, 1, 0, 0, 0, '2025-09-25 17:05:56', '2025-10-01 17:22:41'),
(26, 'Blockchain Development with Solidity', 'blockchain-development-solidity', 'Build decentralized applications (DApps) and smart contracts using Solidity and Ethereum. Web3 development and cryptocurrency integration.', 2, 'https://d3mxt5v3yxgcsr.cloudfront.net/courses/20837/course_20837_image.png', 349.99, '18 weeks', 'advanced', 'Blockchain', NULL, NULL, 1, 0, 0, 0, '2025-09-25 17:05:56', '2025-10-01 17:23:59'),
(54, 'Design of algorithms', 'design-of-algorithms', 'Students can get basic idea about how algorithms works', 15, 'https://d3f1iyfxxz8i1e.cloudfront.net/courses/course_image/abf95ac29e05.jpg', 25.00, '4 weeks', 'beginner', 'Problem solving', NULL, NULL, 1, 0, 0, 0, '2025-09-28 05:20:06', '2025-10-01 17:26:11'),
(55, 'Python for Beginners', NULL, 'Learn Python programming from scratch', 2, 'https://i.ytimg.com/vi/JJmcL1N2KQs/maxresdefault.jpg', 199.99, '8 weeks', 'beginner', 'general', NULL, NULL, 1, 0, 0, 0, '2025-09-15 05:32:29', '2025-10-01 17:27:24'),
(56, 'React.js Masterclass', NULL, 'Advanced React.js concepts and best practices', 2, 'https://i.ytimg.com/vi/MHn66JJH5zs/hqdefault.jpg', 249.99, '10 weeks', 'advanced', 'general', NULL, NULL, 1, 0, 0, 0, '2025-09-23 05:32:29', '2025-10-01 17:28:06'),
(57, 'Data Science with Python', NULL, 'Learn data analysis and machine learning', 2, 'https://cdn.shopaccino.com/igmguru/products/data-science--with-python-igmguru_176161162_xl.jpg?v=532', 349.99, '16 weeks', 'intermediate', 'general', NULL, NULL, 1, 0, 0, 0, '2025-08-12 05:32:29', '2025-10-01 17:28:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_courses_instructor` (`instructor_id`),
  ADD KEY `idx_courses_level` (`level`),
  ADD KEY `idx_courses_category` (`category`),
  ADD KEY `idx_courses_active` (`is_active`),
  ADD KEY `idx_courses_featured` (`featured`),
  ADD KEY `idx_courses_price` (`price`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
