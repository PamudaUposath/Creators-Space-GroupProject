-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 02, 2025 at 03:13 AM
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
-- Table structure for table `ai_analytics`
--

CREATE TABLE `ai_analytics` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `metric_name` varchar(100) NOT NULL,
  `metric_value` decimal(10,2) NOT NULL,
  `metric_count` int(11) DEFAULT 1,
  `additional_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`additional_data`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_conversations`
--

CREATE TABLE `ai_conversations` (
  `id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_message` text NOT NULL,
  `bot_response` text NOT NULL,
  `message_type` enum('normal','suggestion','warning','success') DEFAULT 'normal',
  `intent` varchar(100) DEFAULT NULL,
  `context` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`context`)),
  `processing_time_ms` int(11) DEFAULT NULL,
  `user_feedback` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_conversation_sessions`
--

CREATE TABLE `ai_conversation_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `started_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `message_count` int(11) DEFAULT 0,
  `session_duration_minutes` int(11) DEFAULT 0,
  `user_satisfaction` tinyint(4) DEFAULT NULL,
  `ended_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_knowledge_base`
--

CREATE TABLE `ai_knowledge_base` (
  `id` int(11) NOT NULL,
  `category` varchar(100) NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `keywords` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`keywords`)),
  `usage_count` int(11) DEFAULT 0,
  `effectiveness_score` decimal(3,2) DEFAULT 0.00,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ai_knowledge_base`
--

INSERT INTO `ai_knowledge_base` (`id`, `category`, `question`, `answer`, `keywords`, `usage_count`, `effectiveness_score`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'enrollment', 'How do I enroll in a course?', 'To enroll in a course: 1) Browse our course catalog 2) Click on the course you want 3) Click \"Enroll Now\" 4) Complete payment if required 5) Start learning immediately!', NULL, 0, 0.00, 1, NULL, '2025-09-25 08:47:57', '2025-09-25 08:47:57'),
(2, 'certificates', 'How do I get a certificate?', 'You automatically receive a digital certificate when you complete a course with at least 80% progress. Certificates include a unique verification code and can be shared on LinkedIn.', NULL, 0, 0.00, 1, NULL, '2025-09-25 08:47:57', '2025-09-25 08:47:57'),
(3, 'payment', 'What payment methods are accepted?', 'We accept all major credit cards (Visa, MasterCard, AmEx), PayPal, and bank transfers. Many courses are also available for free!', NULL, 0, 0.00, 1, NULL, '2025-09-25 08:47:57', '2025-09-25 08:47:57');

-- --------------------------------------------------------

--
-- Table structure for table `ai_recommendations`
--

CREATE TABLE `ai_recommendations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_id` int(11) DEFAULT NULL,
  `recommendation_type` enum('course','learning_path','project','resource') NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `item_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`item_data`)),
  `reason` text DEFAULT NULL,
  `user_action` enum('viewed','clicked','enrolled','ignored','dismissed') DEFAULT NULL,
  `relevance_score` decimal(3,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `action_taken_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_user_preferences`
--

CREATE TABLE `ai_user_preferences` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `preferred_learning_style` enum('visual','auditory','kinesthetic','reading') DEFAULT NULL,
  `skill_level` enum('beginner','intermediate','advanced') DEFAULT 'beginner',
  `interests` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`interests`)),
  `learning_goals` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`learning_goals`)),
  `preferred_pace` enum('slow','moderate','fast') DEFAULT 'moderate',
  `notification_preferences` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`notification_preferences`)),
  `language_preference` varchar(10) DEFAULT 'en',
  `timezone` varchar(50) DEFAULT 'UTC',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `instructor_id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `priority` enum('low','normal','high','urgent') DEFAULT 'normal',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `announcement_reads`
--

CREATE TABLE `announcement_reads` (
  `id` int(11) NOT NULL,
  `announcement_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `read_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `excerpt` text DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `featured_image` varchar(500) DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `title`, `slug`, `content`, `excerpt`, `author_id`, `featured_image`, `is_published`, `published_at`, `created_at`, `updated_at`) VALUES
(1, 'Getting Started with Tech Startups', 'getting-started-tech-startups', 'Learn the basics of starting a tech company...', 'A comprehensive guide to launching your tech startup', 2, '/assets/images/techstartup.jpeg', 1, '2025-09-25 08:11:02', '2025-09-25 08:11:02', '2025-09-25 08:11:02'),
(2, 'JavaScript in 30 Days Challenge', 'javascript-30-days-challenge', 'Take on the ultimate JavaScript learning challenge...', 'Master JavaScript programming with our 30-day challenge', 2, '/assets/images/jsin30days.png', 1, '2025-09-25 08:11:02', '2025-09-25 08:11:02', '2025-09-25 08:11:02'),
(3, 'UI/UX Design Best Practices', 'ui-ux-design-best-practices', 'Discover the essential principles of great design...', 'Learn the fundamental principles of effective UI/UX design', 2, '/assets/images/ui-ux.jpeg', 1, '2025-09-25 08:11:02', '2025-09-25 08:11:02', '2025-09-25 08:11:02');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `certificate_code` varchar(100) DEFAULT NULL,
  `issued_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `certificates`
--

INSERT INTO `certificates` (`id`, `user_id`, `course_id`, `certificate_code`, `issued_at`) VALUES
(3, 1, 3, 'CERT-JS30-2024-001', '2025-09-21 21:58:44'),
(4, 2, 1, 'CERT-FSWD-2024-002', '2025-09-23 21:58:44'),
(5, 25, 1, 'CERT-5D163ACF', '2025-09-28 05:53:37');

-- --------------------------------------------------------

--
-- Table structure for table `communication_preferences`
--

CREATE TABLE `communication_preferences` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email_messages` tinyint(1) DEFAULT 1,
  `email_announcements` tinyint(1) DEFAULT 1,
  `email_course_updates` tinyint(1) DEFAULT 1,
  `push_notifications` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `communication_preferences`
--

INSERT INTO `communication_preferences` (`id`, `user_id`, `email_messages`, `email_announcements`, `email_course_updates`, `push_notifications`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 1, '2025-09-28 05:59:25', '2025-09-28 05:59:25'),
(2, 2, 1, 1, 1, 1, '2025-09-28 05:59:25', '2025-09-28 05:59:25'),
(3, 14, 1, 1, 1, 1, '2025-09-28 05:59:25', '2025-09-28 05:59:25'),
(4, 15, 1, 1, 1, 1, '2025-09-28 05:59:25', '2025-09-28 05:59:25'),
(5, 16, 1, 1, 1, 1, '2025-09-28 05:59:25', '2025-09-28 05:59:25'),
(6, 17, 1, 1, 1, 1, '2025-09-28 05:59:25', '2025-09-28 05:59:25'),
(7, 18, 1, 1, 1, 1, '2025-09-28 05:59:25', '2025-09-28 05:59:25'),
(8, 19, 1, 1, 1, 1, '2025-09-28 05:59:25', '2025-09-28 05:59:25'),
(9, 20, 1, 1, 1, 1, '2025-09-28 05:59:25', '2025-09-28 05:59:25'),
(10, 21, 1, 1, 1, 1, '2025-09-28 05:59:25', '2025-09-28 05:59:25'),
(11, 22, 1, 1, 1, 1, '2025-09-28 05:59:25', '2025-09-28 05:59:25'),
(12, 23, 1, 1, 1, 1, '2025-09-28 05:59:25', '2025-09-28 05:59:25'),
(13, 24, 1, 1, 1, 1, '2025-09-28 05:59:25', '2025-09-28 05:59:25'),
(14, 25, 1, 1, 1, 1, '2025-09-28 05:59:25', '2025-09-28 05:59:25');

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `id` int(11) NOT NULL,
  `participant_1_id` int(11) NOT NULL,
  `participant_2_id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `last_message_id` int(11) DEFAULT NULL,
  `last_message_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `conversations`
--

INSERT INTO `conversations` (`id`, `participant_1_id`, `participant_2_id`, `course_id`, `last_message_id`, `last_message_at`, `created_at`) VALUES
(4, 2, 14, NULL, 12, '2025-09-28 08:45:43', '2025-09-28 08:42:51');

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
(1, 'Full Stack Web Development', 'full-stack-web-development', 'Learn complete web development from frontend to backend', 2, 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/Full-Stack-Web-Development-Course-in-Kolkata.webp', 99.99, '12 weeks', 'intermediate', 'general', NULL, NULL, 1, 0, 0, 0, '2025-09-25 08:11:01', '2025-10-02 01:07:28'),
(2, 'UI/UX Design Fundamentals', 'ui-ux-design-fundamentals', 'Master the fundamentals of user interface and user experience design', 2, 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/uiux.jpeg', 79.99, '8 weeks', 'beginner', 'general', NULL, NULL, 1, 0, 0, 0, '2025-09-25 08:11:01', '2025-10-02 01:11:02'),
(3, 'JavaScript in 30 Days', 'javascript-30-days', 'Master JavaScript programming in 30 days with practical projects', 2, 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/jsin30days.png', 49.99, '4 weeks', 'beginner', 'general', NULL, NULL, 1, 0, 0, 0, '2025-09-25 08:11:01', '2025-10-02 01:09:49'),
(4, 'Java Programming Masterclass', 'java-programming-masterclass', 'Complete Java programming course from basics to advanced concepts. Learn OOP, data structures, algorithms, Spring Framework, and enterprise application development.', 2, 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/java.png', 249.99, '16 weeks', 'intermediate', 'Programming', NULL, NULL, 1, 0, 0, 0, '2025-09-25 17:05:56', '2025-10-02 01:09:38'),
(5, 'C++ Game Development', 'cpp-game-development', 'Learn C++ programming through game development. Build 2D and 3D games using modern C++ techniques, game engines, and graphics programming.', 2, 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/gamedev.png', 199.99, '12 weeks', 'advanced', 'Programming', NULL, NULL, 1, 0, 0, 0, '2025-09-25 17:05:56', '2025-10-02 01:09:03'),
(8, 'Vue.js Complete Guide', 'vuejs-complete-guide', 'Master Vue.js framework with Composition API, Vuex, Vue Router, and modern development practices. Build scalable single-page applications.', 2, 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/Vuejs.jpg', 159.99, '8 weeks', 'intermediate', 'Web Development', NULL, NULL, 1, 0, 0, 0, '2025-09-25 17:05:56', '2025-10-02 01:11:37'),
(9, 'Angular Enterprise Development', 'angular-enterprise-development', 'Build large-scale enterprise applications with Angular. Advanced patterns, testing, performance optimization, and deployment strategies.', 2, 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/angular.png', 279.99, '16 weeks', 'advanced', 'Web Development', NULL, NULL, 1, 0, 0, 0, '2025-09-25 17:05:56', '2025-10-02 01:08:02'),
(10, 'Next.js Full-Stack Development', 'nextjs-fullstack-development', 'Build production-ready applications with Next.js. Server-side rendering, API routes, authentication, and deployment to Vercel.', 2, 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/nextjs.webp', 199.99, '10 weeks', 'intermediate', 'Web Development', NULL, NULL, 1, 0, 0, 0, '2025-09-25 17:05:56', '2025-10-02 01:10:07'),
(11, 'GraphQL API Development', 'graphql-api-development', 'Master GraphQL for modern API development. Schema design, resolvers, subscriptions, and integration with popular databases and frameworks.', 2, 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/graphql.jpg', 189.99, '8 weeks', 'advanced', 'Web Development', NULL, NULL, 1, 0, 0, 0, '2025-09-25 17:05:56', '2025-10-02 01:09:20'),
(26, 'Blockchain Development with Solidity', 'blockchain-development-solidity', 'Build decentralized applications (DApps) and smart contracts using Solidity and Ethereum. Web3 development and cryptocurrency integration.', 2, 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/blockchain.png', 349.99, '18 weeks', 'advanced', 'Blockchain', NULL, NULL, 1, 0, 0, 0, '2025-09-25 17:05:56', '2025-10-02 01:08:21'),
(54, 'Design of algorithms', 'design-of-algorithms', 'Students can get basic idea about how algorithms works', 15, 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/designOfAlgorithm.jpg', 25.00, '4 weeks', 'beginner', 'Problem solving', NULL, NULL, 1, 0, 0, 0, '2025-09-28 05:20:06', '2025-10-02 01:08:46'),
(55, 'Python for Beginners', 'python for beginners', 'Learn Python programming from scratch', 2, 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/Pythonforbeginners.jpg', 199.99, '8 weeks', 'beginner', 'general', NULL, NULL, 1, 0, 0, 0, '2025-09-15 05:32:29', '2025-10-02 01:10:20'),
(56, 'React.js Masterclass', 'react.js masterclass', 'Advanced React.js concepts and best practices', 2, 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/reactjs.jpg', 249.99, '10 weeks', 'advanced', 'general', NULL, NULL, 1, 0, 0, 0, '2025-09-23 05:32:29', '2025-10-02 01:10:46'),
(57, 'Data Science with Python', 'data science with python', 'Learn data analysis and machine learning', 2, 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/datasciencewithpython.jpg', 349.99, '16 weeks', 'intermediate', 'general', NULL, NULL, 1, 0, 0, 0, '2025-08-12 05:32:29', '2025-10-02 01:08:32');

-- --------------------------------------------------------

--
-- Table structure for table `course_requests`
--

CREATE TABLE `course_requests` (
  `id` int(11) NOT NULL,
  `instructor_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `duration` varchar(100) DEFAULT NULL,
  `level` enum('Beginner','Intermediate','Advanced') DEFAULT 'Beginner',
  `category` varchar(100) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_requests`
--

INSERT INTO `course_requests` (`id`, `instructor_id`, `title`, `description`, `price`, `duration`, `level`, `category`, `status`, `admin_notes`, `requested_at`, `reviewed_at`, `reviewed_by`, `course_id`) VALUES
(1, 15, 'Design of algorithms', 'Students can get basic idea about how algorithms works', 25.00, '4 weeks', 'Beginner', 'Problem solving', 'approved', 'Good work.', '2025-09-28 05:18:45', '2025-09-28 05:20:06', 1, 54);

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `last_accessed` timestamp NULL DEFAULT NULL,
  `progress` decimal(5,2) DEFAULT 0.00,
  `current_lesson_id` int(11) DEFAULT NULL,
  `status` enum('active','completed','paused','cancelled') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `user_id`, `course_id`, `enrolled_at`, `completed_at`, `last_accessed`, `progress`, `current_lesson_id`, `status`) VALUES
(201, 21, 11, '2025-08-27 05:32:29', NULL, NULL, 18.00, NULL, 'active'),
(202, 17, 10, '2025-08-26 05:32:29', NULL, NULL, 22.00, NULL, 'active'),
(203, 20, 57, '2025-08-21 05:32:29', NULL, NULL, 60.00, NULL, 'active'),
(204, 18, 1, '2025-09-07 05:32:29', NULL, NULL, 54.00, NULL, 'active'),
(205, 25, 57, '2025-09-15 05:32:29', NULL, NULL, 86.00, NULL, 'active'),
(206, 24, 57, '2025-09-10 05:32:29', NULL, NULL, 45.00, NULL, 'active'),
(207, 25, 1, '2025-09-26 05:32:29', '2025-09-28 05:53:37', NULL, 100.00, NULL, 'completed'),
(208, 17, 8, '2025-09-10 05:32:29', NULL, NULL, 32.00, NULL, 'active'),
(209, 21, 8, '2025-09-12 05:32:29', NULL, NULL, 88.00, NULL, 'active'),
(210, 25, 11, '2025-09-14 05:32:29', NULL, NULL, 97.00, NULL, 'active'),
(211, 25, 9, '2025-08-20 05:32:29', NULL, NULL, 48.00, NULL, 'active'),
(212, 21, 3, '2025-08-24 05:32:29', NULL, NULL, 51.00, NULL, 'active'),
(213, 21, 57, '2025-09-20 05:32:29', NULL, NULL, 37.00, NULL, 'active'),
(214, 22, 8, '2025-09-14 05:32:29', NULL, NULL, 50.00, NULL, 'active'),
(215, 16, 55, '2025-09-04 05:32:29', NULL, NULL, 24.00, NULL, 'active'),
(216, 18, 11, '2025-09-03 05:32:29', NULL, NULL, 5.00, NULL, 'active'),
(217, 17, 1, '2025-09-02 05:32:29', NULL, NULL, 80.00, NULL, 'active'),
(218, 24, 26, '2025-09-18 05:32:29', NULL, NULL, 77.00, NULL, 'active'),
(219, 16, 1, '2025-09-20 05:32:29', NULL, NULL, 57.00, NULL, 'active'),
(220, 14, 4, '2025-09-15 05:32:29', NULL, NULL, 84.00, NULL, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `internships`
--

CREATE TABLE `internships` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `is_remote` tinyint(1) DEFAULT 0,
  `application_deadline` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `internships`
--

INSERT INTO `internships` (`id`, `title`, `company`, `description`, `requirements`, `duration`, `location`, `is_remote`, `application_deadline`, `is_active`, `created_at`) VALUES
(1, 'Frontend Developer Intern', 'TechCorp', 'Work on exciting frontend projects using React and modern web technologies', 'Knowledge of HTML, CSS, JavaScript, React', '3 months', 'Remote', 1, '2025-12-31', 1, '2025-09-25 08:11:02'),
(2, 'Backend Developer Intern', 'WebSolutions', 'Develop scalable backend systems using Node.js and databases', 'Knowledge of Node.js, databases, API development', '6 months', 'New York', 0, '2025-11-30', 1, '2025-09-25 08:11:02'),
(3, 'UI/UX Design Intern', 'DesignStudio', 'Create beautiful and user-friendly interfaces for web and mobile apps', 'Portfolio showcasing UI/UX work, Figma knowledge', '4 months', 'Remote', 1, '2025-10-15', 1, '2025-09-25 08:11:02'),
(4, 'Full Stack Software Engineer Intern', 'Microsoft', 'Work on cloud-native applications using cutting-edge technologies. Collaborate with senior engineers on Azure services and contribute to products used by millions of users worldwide.', 'Strong programming skills in C#, JavaScript, or Python. Experience with cloud platforms, web frameworks, and agile development. Academic excellence in Computer Science or related field.', '12 weeks', 'Seattle, WA', 0, '2025-07-15', 1, '2025-09-25 17:06:43'),
(5, 'Software Development Engineer Intern', 'Amazon', 'Join one of our engineering teams working on e-commerce, AWS, or Alexa. Build scalable systems that handle massive traffic and learn from world-class engineers.', 'Proficiency in Java, Python, or C++. Strong problem-solving skills, data structures knowledge, and system design understanding. Previous internship or project experience preferred.', '16 weeks', 'San Francisco, CA', 0, '2025-06-30', 1, '2025-09-25 17:06:43'),
(6, 'Frontend Engineering Intern', 'Meta (Facebook)', 'Build user interfaces for billions of users across Facebook, Instagram, and WhatsApp. Work with React, GraphQL, and modern web technologies in a fast-paced environment.', 'Expert knowledge of JavaScript, React, HTML5, CSS3. Understanding of web performance, accessibility, and mobile-first design. Portfolio of impressive frontend projects.', '12 weeks', 'Menlo Park, CA', 1, '2025-08-01', 1, '2025-09-25 17:06:43'),
(7, 'Backend Systems Intern', 'Google', 'Work on large-scale distributed systems that power Google Search, Gmail, YouTube, or Google Cloud. Learn about system architecture, performance optimization, and reliability engineering.', 'Strong background in algorithms, data structures, and system design. Proficiency in C++, Java, Python, or Go. Experience with distributed systems and database technologies.', '14 weeks', 'Mountain View, CA', 0, '2025-07-01', 1, '2025-09-25 17:06:43'),
(8, 'iOS Developer Intern', 'Apple', 'Contribute to iOS, macOS, or watchOS applications. Work with Swift, SwiftUI, and Apple\'s frameworks to create seamless user experiences across the Apple ecosystem.', 'Expertise in Swift programming, iOS SDK, and Apple Human Interface Guidelines. Strong understanding of MVC/MVVM patterns and Core Data. Published apps preferred.', '12 weeks', 'Cupertino, CA', 0, '2025-06-15', 1, '2025-09-25 17:06:43'),
(9, 'Android Development Intern', 'Uber', 'Build features for Uber\'s rider and driver mobile applications. Work with Kotlin, Android SDK, and modern architecture patterns to improve user experience and app performance.', 'Proficiency in Kotlin/Java, Android SDK, and Material Design. Experience with MVVM architecture, dependency injection, and testing frameworks. Strong problem-solving skills.', '10 weeks', 'San Francisco, CA', 1, '2025-07-30', 1, '2025-09-25 17:06:43'),
(10, 'Cross-Platform Mobile Intern', 'Airbnb', 'Develop mobile features using React Native for both iOS and Android platforms. Work on booking flows, host tools, and user engagement features for millions of travelers.', 'Experience with React Native, JavaScript/TypeScript, and mobile app development. Understanding of navigation, state management, and performance optimization. Portfolio required.', '12 weeks', 'San Francisco, CA', 1, '2025-08-15', 1, '2025-09-25 17:06:43'),
(11, 'Machine Learning Engineer Intern', 'Tesla', 'Work on Autopilot and Full Self-Driving technology. Develop and optimize neural networks for computer vision, path planning, and autonomous vehicle decision making.', 'Strong background in machine learning, deep learning, and computer vision. Proficiency in Python, TensorFlow/PyTorch, and C++. Experience with autonomous systems preferred.', '16 weeks', 'Palo Alto, CA', 0, '2025-06-01', 1, '2025-09-25 17:06:43'),
(12, 'Data Scientist Intern', 'Netflix', 'Analyze user behavior and content performance to improve recommendation algorithms. Work with big data technologies and A/B testing to drive content strategy decisions.', 'Strong statistics and analytics background. Proficiency in Python/R, SQL, and data visualization tools. Experience with machine learning and experimentation methodologies.', '12 weeks', 'Los Gatos, CA', 1, '2025-07-20', 1, '2025-09-25 17:06:43'),
(13, 'AI Research Intern', 'OpenAI', 'Contribute to cutting-edge artificial intelligence research. Work on large language models, reinforcement learning, or AI safety research alongside world-renowned researchers.', 'PhD or Masters in Computer Science, Machine Learning, or related field. Publications in top AI conferences. Expertise in deep learning frameworks and research methodologies.', '20 weeks', 'San Francisco, CA', 0, '2025-05-15', 1, '2025-09-25 17:06:43'),
(14, 'UX Design Intern', 'Adobe', 'Design user experiences for Creative Cloud applications used by millions of creators worldwide. Conduct user research, create prototypes, and collaborate with product teams.', 'Portfolio demonstrating UX design process and thinking. Proficiency in Figma, Sketch, and Adobe Creative Suite. Understanding of design systems and accessibility principles.', '12 weeks', 'San Jose, CA', 1, '2025-06-20', 1, '2025-09-25 17:06:43'),
(15, 'Product Design Intern', 'Spotify', 'Design music discovery and listening experiences across mobile, desktop, and emerging platforms. Work on features that connect artists and fans through innovative design.', 'Strong design portfolio with mobile and web projects. Experience with user-centered design process. Skills in Figma, prototyping, and user testing. Music industry knowledge a plus.', '10 weeks', 'New York, NY', 1, '2025-08-05', 1, '2025-09-25 17:06:43'),
(16, 'Site Reliability Engineer Intern', 'Dropbox', 'Ensure high availability and performance of cloud storage systems. Work with Kubernetes, monitoring tools, and automation to maintain 99.99% uptime for millions of users.', 'Experience with Linux, scripting languages, and containerization. Understanding of distributed systems, monitoring, and incident response. Strong troubleshooting skills required.', '14 weeks', 'San Francisco, CA', 0, '2025-07-10', 1, '2025-09-25 17:06:43'),
(17, 'Cloud Engineer Intern', 'Salesforce', 'Build and maintain cloud infrastructure supporting millions of CRM users. Work with AWS, microservices architecture, and infrastructure as code to scale enterprise applications.', 'Knowledge of cloud platforms (AWS/Azure/GCP), infrastructure automation, and CI/CD pipelines. Programming skills in Python, Go, or Java. Docker and Kubernetes experience preferred.', '12 weeks', 'San Francisco, CA', 1, '2025-06-25', 1, '2025-09-25 17:06:43'),
(18, 'Security Engineer Intern', 'Cloudflare', 'Protect internet infrastructure and help build a better internet. Work on DDoS mitigation, web application firewalls, and security research to defend against cyber threats.', 'Strong understanding of network security, cryptography, and security protocols. Programming skills in multiple languages. Ethical hacking or security research experience preferred.', '12 weeks', 'San Francisco, CA', 1, '2025-07-05', 1, '2025-09-25 17:06:43'),
(19, 'Full Stack Developer Intern', 'Stripe', 'Build payment processing systems that power online commerce globally. Work on developer APIs, dashboard interfaces, and financial infrastructure used by millions of businesses.', 'Full stack development experience with modern frameworks. Strong API design and database skills. Understanding of financial systems and security best practices helpful.', '12 weeks', 'San Francisco, CA', 1, '2025-06-10', 1, '2025-09-25 17:06:43'),
(20, 'Blockchain Developer Intern', 'Coinbase', 'Develop cryptocurrency exchange and wallet applications. Work with blockchain technologies, smart contracts, and financial APIs to build the future of digital currency.', 'Understanding of blockchain technology, cryptocurrencies, and smart contracts. Programming skills in JavaScript, Python, or Solidity. Interest in decentralized finance (DeFi).', '10 weeks', 'San Francisco, CA', 1, '2025-08-20', 1, '2025-09-25 17:06:43'),
(21, 'Game Developer Intern', 'Epic Games', 'Work on Fortnite, Unreal Engine, or Epic Games Store. Develop gameplay features, tools, or engine improvements that impact millions of players and developers worldwide.', 'Strong C++ programming skills and game development experience. Familiarity with Unreal Engine, 3D mathematics, and game design principles. Portfolio of game projects required.', '14 weeks', 'Cary, NC', 0, '2025-06-05', 1, '2025-09-25 17:06:43'),
(22, 'Technical Artist Intern', 'Riot Games', 'Bridge art and programming for League of Legends and Valorant. Create shaders, optimize art pipelines, and develop tools that empower artists to create amazing game visuals.', 'Combination of artistic and technical skills. Experience with 3D software, shaders, and scripting languages. Understanding of game art pipelines and optimization techniques.', '12 weeks', 'Los Angeles, CA', 0, '2025-07-25', 1, '2025-09-25 17:06:43');

-- --------------------------------------------------------

--
-- Table structure for table `internship_applications`
--

CREATE TABLE `internship_applications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `internship_id` int(11) NOT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `video_url` varchar(500) DEFAULT NULL,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments`)),
  `position` int(11) DEFAULT 0,
  `duration` varchar(50) DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT 0,
  `is_free` tinyint(1) DEFAULT 0,
  `is_published` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `is_deleted_by_sender` tinyint(1) DEFAULT 0,
  `is_deleted_by_receiver` tinyint(1) DEFAULT 0,
  `reply_to_message_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `read_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `course_id`, `subject`, `message`, `is_read`, `is_deleted_by_sender`, `is_deleted_by_receiver`, `reply_to_message_id`, `created_at`, `read_at`) VALUES
(8, 2, 14, NULL, '', 'Hi', 1, 0, 0, NULL, '2025-09-28 08:42:51', '2025-09-28 08:43:09'),
(9, 14, 2, NULL, '', 'hello', 1, 0, 0, NULL, '2025-09-28 08:43:22', '2025-09-28 08:45:10'),
(10, 2, 14, NULL, '', 'whats going on', 1, 0, 0, NULL, '2025-09-28 08:45:24', '2025-09-28 08:46:09'),
(11, 2, 14, NULL, '', 'is it okay', 1, 0, 0, NULL, '2025-09-28 08:45:30', '2025-09-28 08:46:09'),
(12, 2, 14, NULL, '', 'Are you facing any problem?', 1, 0, 0, NULL, '2025-09-28 08:45:43', '2025-09-28 08:46:09');

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscriptions`
--

CREATE TABLE `newsletter_subscriptions` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `subscribed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('message','announcement','course_update','certificate','general') NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `related_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `read_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `content`, `related_id`, `is_read`, `created_at`, `read_at`) VALUES
(5, 14, 'message', 'New message from John', 'Hi', 1, 0, '2025-09-28 07:19:48', NULL),
(6, 14, 'message', 'New message from John', 'how are you', 2, 0, '2025-09-28 07:19:55', NULL),
(7, 2, 'message', 'New message from Test', 'Hi', 5, 0, '2025-09-28 08:16:04', NULL),
(8, 14, 'message', 'New message from John', '3gre', 6, 0, '2025-09-28 08:31:15', NULL),
(9, 14, 'message', 'New message from John', 'ewvf', 7, 0, '2025-09-28 08:31:27', NULL),
(10, 14, 'message', 'New message from John', 'Hi', 8, 0, '2025-09-28 08:42:51', NULL),
(11, 2, 'message', 'New message from Test', 'hello', 9, 0, '2025-09-28 08:43:22', NULL),
(12, 14, 'message', 'New message from John', 'whats going on', 10, 0, '2025-09-28 08:45:24', NULL),
(13, 14, 'message', 'New message from John', 'is it okay', 11, 0, '2025-09-28 08:45:30', NULL),
(14, 14, 'message', 'New message from John', 'Are you facing any problem?', 12, 0, '2025-09-28 08:45:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` varchar(100) NOT NULL,
  `payment_id` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) DEFAULT 'LKR',
  `status` enum('pending','completed','failed','canceled','chargedback') NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `card_holder_name` varchar(100) DEFAULT NULL,
  `card_no` varchar(20) DEFAULT NULL,
  `card_expiry` varchar(10) DEFAULT NULL,
  `status_message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `title`, `description`, `icon`, `is_active`, `created_at`) VALUES
(1, 'Career Guidance', 'Get expert advice on your career path and professional development', 'career-guidance.svg', 1, '2025-09-25 08:11:02'),
(2, 'Mock Interviews', 'Practice interviews with industry professionals', 'mock-interviews.svg', 1, '2025-09-25 08:11:02'),
(3, 'Resume Tips', 'Professional resume writing and optimization tips', 'resume-tips.svg', 1, '2025-09-25 08:11:02'),
(4, 'Project Learning', 'Hands-on learning through real-world projects', 'project-learning.svg', 1, '2025-09-25 08:11:02'),
(5, 'Open Source Contribution', 'Learn to contribute to open source projects', 'opensource.svg', 1, '2025-09-25 08:11:02');

-- --------------------------------------------------------

--
-- Table structure for table `student_reports`
--

CREATE TABLE `student_reports` (
  `id` int(11) NOT NULL,
  `instructor_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `report_type` enum('academic_concern','behavior_issue','attendance_problem','inappropriate_conduct','plagiarism','other') NOT NULL DEFAULT 'other',
  `subject` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `severity` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `status` enum('pending','under_review','resolved','dismissed') NOT NULL DEFAULT 'pending',
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `resolution_action` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_reports`
--

INSERT INTO `student_reports` (`id`, `instructor_id`, `student_id`, `course_id`, `report_type`, `subject`, `description`, `severity`, `status`, `submitted_at`, `reviewed_by`, `reviewed_at`, `admin_notes`, `resolution_action`) VALUES
(3, 2, 14, 2, 'academic_concern', 'Consistent Low Performance', 'Student has been consistently underperforming despite multiple attempts to provide assistance. Shows lack of engagement with course materials.', 'medium', 'pending', '2025-09-28 09:21:48', NULL, NULL, NULL, NULL),
(4, 2, 16, 3, 'behavior_issue', 'Disruptive Behavior', 'Student has been showing disruptive behavior during online sessions and in course discussions.', 'high', 'pending', '2025-09-28 09:21:48', NULL, NULL, NULL, NULL),
(5, 2, 14, 1, 'academic_concern', 'Test Report via API', 'This is a test report submitted via API to verify functionality.', 'medium', 'pending', '2025-09-28 09:32:48', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('user','instructor','admin') DEFAULT 'user',
  `is_active` tinyint(1) DEFAULT 1,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `profile_image` varchar(500) DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `username`, `password_hash`, `role`, `is_active`, `reset_token`, `reset_expires`, `profile_image`, `skills`, `bio`, `phone`, `date_of_birth`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'User', 'admin@creatorsspace.local', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-25 08:11:01', '2025-09-25 08:11:01'),
(2, 'John', 'Instructor', 'instructor@creatorsspace.local', 'instructor', '$2y$10$uNK/Z/zvG.nV7sP1vlknsuFKozLn0gQXksoRUBo604mwBByZQRDze', 'instructor', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-25 08:11:01', '2025-09-27 19:22:09'),
(14, 'Test', 'User', 'test@gmail.com', '', '$2y$10$6DZQgqLeUYWcBgn0.gHOWe/4nsOUJJFxUszD7bCACzSqWvlKG4dPq', 'user', 1, NULL, NULL, './assets/images/profiles/profile_14_1758833534.png', '', '', '', '0000-00-00', '2025-09-25 08:16:03', '2025-09-25 20:52:14'),
(15, 'Test', 'User2', 'testuser@gmail.com', NULL, '$2y$10$4bXShBAF7x7YOIu4u7gprenfZk.x9KMlMaTcLQ..WSxSUs/yFqZu.', 'instructor', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-26 21:23:49', '2025-09-26 21:47:10'),
(16, 'Test', 'User', 'test@example.com', 'testuser123', '$2y$10$nVGWBIfhFWuld4FwW2pTiOYEUSh.9q.SDGJtDbyMt.Uo6412NEs62', 'user', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-26 22:26:09', '2025-09-26 22:26:09'),
(17, 'Test', 'User', 'testuser3@gmail.com', NULL, '$2y$10$9FAt44CcARqOJXOFLXfhDuGEUIJ3uf82h7hUPEYpcRuKvxgtdjssW', 'user', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-26 22:27:47', '2025-09-26 22:27:47'),
(18, 'Alice', 'Johnson', 'alice.johnson@example.com', 'alice.johnson', '$2y$10$SVI/9UTIr9NEiTBjiDA7fuEh6GbytLRk3wUkTa9o9deJ.t.HfzTg.', 'user', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-17 05:32:27', '2025-09-28 05:32:27'),
(19, 'Bob', 'Smith', 'bob.smith@example.com', 'bob.smith', '$2y$10$eQSJ/zhSCm1UmljzWS0KyeeETUAGMKYPR3VIYXjpbVEDL2ecohEDK', 'user', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-17 05:32:27', '2025-09-28 05:32:27'),
(20, 'Carol', 'Davis', 'carol.davis@example.com', 'carol.davis', '$2y$10$.6ZwM1/.SkFy/CWDVzoeweic2wc8j1Mz//UDbyO/kRInmwThR.HyO', 'user', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-05 05:32:28', '2025-09-28 05:32:28'),
(21, 'David', 'Wilson', 'david.wilson@example.com', 'david.wilson', '$2y$10$rLTmrC.Zz1Kql6sAd3sTFeQThiBeOG5VWAYoSF8jlL1LR9vTBQsXm', 'user', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-07 05:32:28', '2025-09-28 05:32:28'),
(22, 'Emma', 'Brown', 'emma.brown@example.com', 'emma.brown', '$2y$10$8qiBsqgM9eBTXnpuSlyi8eQZp0juOevIbezJIkYWKQu583Qbsi142', 'user', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-19 05:32:28', '2025-09-28 05:32:28'),
(23, 'Frank', 'Miller', 'frank.miller@example.com', 'frank.miller', '$2y$10$J8g9NrJ9.UaspNi4DLxNCOp68OjJ.BBTrP5iHHoh6eA3Mc6NYwqSu', 'user', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-15 05:32:28', '2025-09-28 05:32:28'),
(24, 'Grace', 'Taylor', 'grace.taylor@example.com', 'grace.taylor', '$2y$10$IfI.icuoB6Oin7R4ibaqr.QXZtMoO1TEqkjc6edaT5DoMDcBWTvR2', 'user', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-20 05:32:29', '2025-09-28 05:32:29'),
(25, 'Henry', 'Anderson', 'henry.anderson@example.com', 'henry.anderson', '$2y$10$WYuxnmydDnrsrBgtplxRouLaVgMChXX0CX0j1Wr7IclXb3zXGVFfS', 'user', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-28 05:32:29', '2025-09-28 05:32:29'),
(26, 'Pamuda', 'U de A Goonatilake', 'pamuda@mailinator.com', NULL, '$2y$10$.NbN0pL4SMl0JUzXKsZpiuZwSjRaUVtDTRvg5LKWQi4LiUR0lIGjq', 'user', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-29 05:18:43', '2025-09-29 05:18:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ai_analytics`
--
ALTER TABLE `ai_analytics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_daily_metric` (`date`,`metric_name`),
  ADD KEY `idx_ai_analytics_date` (`date`),
  ADD KEY `idx_ai_analytics_metric` (`metric_name`),
  ADD KEY `idx_ai_analytics_value` (`metric_value`);

--
-- Indexes for table `ai_conversations`
--
ALTER TABLE `ai_conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ai_conversations_session` (`session_id`),
  ADD KEY `idx_ai_conversations_user` (`user_id`),
  ADD KEY `idx_ai_conversations_date` (`created_at`),
  ADD KEY `idx_ai_conversations_intent` (`intent`),
  ADD KEY `idx_ai_conversations_type` (`message_type`);

--
-- Indexes for table `ai_conversation_sessions`
--
ALTER TABLE `ai_conversation_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ai_sessions_user` (`user_id`),
  ADD KEY `idx_ai_sessions_date` (`started_at`),
  ADD KEY `idx_ai_sessions_activity` (`last_activity`);

--
-- Indexes for table `ai_knowledge_base`
--
ALTER TABLE `ai_knowledge_base`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_ai_kb_category` (`category`),
  ADD KEY `idx_ai_kb_active` (`is_active`),
  ADD KEY `idx_ai_kb_usage` (`usage_count`),
  ADD KEY `idx_ai_kb_score` (`effectiveness_score`);

--
-- Indexes for table `ai_recommendations`
--
ALTER TABLE `ai_recommendations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `idx_ai_recs_user` (`user_id`),
  ADD KEY `idx_ai_recs_type` (`recommendation_type`),
  ADD KEY `idx_ai_recs_date` (`created_at`),
  ADD KEY `idx_ai_recs_action` (`user_action`),
  ADD KEY `idx_ai_recs_score` (`relevance_score`);

--
-- Indexes for table `ai_user_preferences`
--
ALTER TABLE `ai_user_preferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_ai_prefs_user` (`user_id`),
  ADD KEY `idx_ai_prefs_level` (`skill_level`),
  ADD KEY `idx_ai_prefs_style` (`preferred_learning_style`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_instructor` (`instructor_id`),
  ADD KEY `idx_course` (`course_id`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `announcement_reads`
--
ALTER TABLE `announcement_reads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_read` (`announcement_id`,`user_id`),
  ADD KEY `idx_announcement` (`announcement_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_blog_posts_author` (`author_id`),
  ADD KEY `idx_blog_posts_slug` (`slug`),
  ADD KEY `idx_blog_posts_published` (`is_published`),
  ADD KEY `idx_blog_posts_published_at` (`published_at`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_course` (`user_id`,`course_id`),
  ADD KEY `idx_cart_user_id` (`user_id`),
  ADD KEY `idx_cart_course_id` (`course_id`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `certificate_code` (`certificate_code`),
  ADD KEY `idx_certificates_user` (`user_id`),
  ADD KEY `idx_certificates_course` (`course_id`),
  ADD KEY `idx_certificates_code` (`certificate_code`);

--
-- Indexes for table `communication_preferences`
--
ALTER TABLE `communication_preferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_prefs` (`user_id`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_conversation` (`participant_1_id`,`participant_2_id`,`course_id`),
  ADD KEY `participant_2_id` (`participant_2_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `last_message_id` (`last_message_id`),
  ADD KEY `idx_participants` (`participant_1_id`,`participant_2_id`),
  ADD KEY `idx_last_message` (`last_message_at`);

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
-- Indexes for table `course_requests`
--
ALTER TABLE `course_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviewed_by` (`reviewed_by`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `idx_course_requests_status` (`status`),
  ADD KEY `idx_course_requests_instructor` (`instructor_id`),
  ADD KEY `idx_course_requests_requested_at` (`requested_at`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`course_id`),
  ADD KEY `current_lesson_id` (`current_lesson_id`),
  ADD KEY `idx_enrollments_user` (`user_id`),
  ADD KEY `idx_enrollments_course` (`course_id`),
  ADD KEY `idx_enrollments_status` (`status`),
  ADD KEY `idx_enrollments_progress` (`progress`);

--
-- Indexes for table `internships`
--
ALTER TABLE `internships`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_internships_company` (`company`),
  ADD KEY `idx_internships_location` (`location`),
  ADD KEY `idx_internships_remote` (`is_remote`),
  ADD KEY `idx_internships_active` (`is_active`),
  ADD KEY `idx_internships_deadline` (`application_deadline`);

--
-- Indexes for table `internship_applications`
--
ALTER TABLE `internship_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`internship_id`),
  ADD KEY `idx_internship_apps_user` (`user_id`),
  ADD KEY `idx_internship_apps_internship` (`internship_id`),
  ADD KEY `idx_internship_apps_status` (`status`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_lessons_course` (`course_id`),
  ADD KEY `idx_lessons_position` (`position`),
  ADD KEY `idx_lessons_free` (`is_free`),
  ADD KEY `idx_lessons_published` (`is_published`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sender` (`sender_id`),
  ADD KEY `idx_receiver` (`receiver_id`),
  ADD KEY `idx_course` (`course_id`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_unread` (`receiver_id`,`is_read`),
  ADD KEY `idx_thread` (`reply_to_message_id`);

--
-- Indexes for table `newsletter_subscriptions`
--
ALTER TABLE `newsletter_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_newsletter_email` (`email`),
  ADD KEY `idx_newsletter_active` (`is_active`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_unread` (`user_id`,`is_read`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_payment_id` (`payment_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_services_active` (`is_active`);

--
-- Indexes for table `student_reports`
--
ALTER TABLE `student_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `reviewed_by` (`reviewed_by`),
  ADD KEY `idx_instructor_reports` (`instructor_id`),
  ADD KEY `idx_student_reports` (`student_id`),
  ADD KEY `idx_report_status` (`status`),
  ADD KEY `idx_report_date` (`submitted_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_users_email` (`email`),
  ADD KEY `idx_users_username` (`username`),
  ADD KEY `idx_users_role` (`role`),
  ADD KEY `idx_users_active` (`is_active`),
  ADD KEY `idx_users_skills` (`skills`(768));

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ai_analytics`
--
ALTER TABLE `ai_analytics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_conversations`
--
ALTER TABLE `ai_conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_conversation_sessions`
--
ALTER TABLE `ai_conversation_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_knowledge_base`
--
ALTER TABLE `ai_knowledge_base`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ai_recommendations`
--
ALTER TABLE `ai_recommendations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_user_preferences`
--
ALTER TABLE `ai_user_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `announcement_reads`
--
ALTER TABLE `announcement_reads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `communication_preferences`
--
ALTER TABLE `communication_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `course_requests`
--
ALTER TABLE `course_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=221;

--
-- AUTO_INCREMENT for table `internships`
--
ALTER TABLE `internships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `internship_applications`
--
ALTER TABLE `internship_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `newsletter_subscriptions`
--
ALTER TABLE `newsletter_subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_reports`
--
ALTER TABLE `student_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ai_conversations`
--
ALTER TABLE `ai_conversations`
  ADD CONSTRAINT `ai_conversations_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `ai_conversation_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ai_conversations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ai_conversation_sessions`
--
ALTER TABLE `ai_conversation_sessions`
  ADD CONSTRAINT `ai_conversation_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ai_knowledge_base`
--
ALTER TABLE `ai_knowledge_base`
  ADD CONSTRAINT `ai_knowledge_base_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ai_recommendations`
--
ALTER TABLE `ai_recommendations`
  ADD CONSTRAINT `ai_recommendations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ai_recommendations_ibfk_2` FOREIGN KEY (`session_id`) REFERENCES `ai_conversation_sessions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ai_user_preferences`
--
ALTER TABLE `ai_user_preferences`
  ADD CONSTRAINT `ai_user_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `announcements_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `announcement_reads`
--
ALTER TABLE `announcement_reads`
  ADD CONSTRAINT `announcement_reads_ibfk_1` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `announcement_reads_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD CONSTRAINT `blog_posts_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `certificates_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `communication_preferences`
--
ALTER TABLE `communication_preferences`
  ADD CONSTRAINT `communication_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `conversations_ibfk_1` FOREIGN KEY (`participant_1_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversations_ibfk_2` FOREIGN KEY (`participant_2_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversations_ibfk_3` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `conversations_ibfk_4` FOREIGN KEY (`last_message_id`) REFERENCES `messages` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `course_requests`
--
ALTER TABLE `course_requests`
  ADD CONSTRAINT `course_requests_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_requests_ibfk_2` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `course_requests_ibfk_3` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_3` FOREIGN KEY (`current_lesson_id`) REFERENCES `lessons` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `internship_applications`
--
ALTER TABLE `internship_applications`
  ADD CONSTRAINT `internship_applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `internship_applications_ibfk_2` FOREIGN KEY (`internship_id`) REFERENCES `internships` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `messages_ibfk_4` FOREIGN KEY (`reply_to_message_id`) REFERENCES `messages` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_reports`
--
ALTER TABLE `student_reports`
  ADD CONSTRAINT `student_reports_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_reports_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_reports_ibfk_3` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `student_reports_ibfk_4` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
