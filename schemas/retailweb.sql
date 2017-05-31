-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Oct 06, 2015 at 01:43 AM
-- Server version: 5.5.41-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `retailweb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` char(60) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `active`, `created_at`, `updated_at`) VALUES
(1, 'kei', 'hth.tung90@gmail.com', '$2a$08$ObaqMqi6qqcgFsJ99Uu8jO2uOxrBX5EHRKKE.QOFKr.Pj0WC/5r2K', 1, '0000-00-00 00:00:00', '2015-09-23 20:40:51'),
(10, 'admin', 'admin', '$2a$08$UJIfGqV9BbR8qFJt9p19feoBaIC2FgZ2ZQ.ok5iDFIiplkp1kgGES', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE IF NOT EXISTS `banners` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `image` text COLLATE utf8_unicode_ci NOT NULL,
  `link` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `order_no` int(11) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`id`, `image`, `link`, `order_no`, `created_at`, `updated_at`) VALUES
(1, 'images/banners/banner.05-10-15.png', 'banh-mi', 1, '2015-09-30 03:04:28', '2015-10-06 02:41:47'),
(2, 'images/banners/brat-banh-mi-burger.05-10-15.jpg', 'banner02', 1, '2015-09-30 03:05:59', '2015-10-06 02:43:02'),
(3, 'images/banners/5447c8f7826be0b3545e4463_sandwiches-around-the-world-banh-mi-vietnam.05-10-15.jpg', 'banner03', 1, '2015-09-30 03:06:40', '2015-10-06 02:43:42');

-- --------------------------------------------------------

--
-- Table structure for table `configs`
--

CREATE TABLE IF NOT EXISTS `configs` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `cf_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cf_value` text COLLATE utf8_unicode_ci,
  `status` tinyint(1) DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `cf_key_2` (`cf_key`),
  KEY `cf_key` (`cf_key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `configs`
--

INSERT INTO `configs` (`id`, `cf_key`, `cf_value`, `status`, `created_at`, `updated_at`) VALUES
(1, 'about_footer', '<p>Products above may not appear exactly as shown. Product, pricing and participation may vary by location. Delivery areas and charges may vary by location and minimum delivery order conditions may apply. All taxes and delivery charges extra. Please note that for the purpose of completing your order, Pizza Hut and/or independent Pizza Hut franchisees must collect customer and order information.This information may be retained by Pizza Hut and/or franchisees to help serve you better in the future. View our Privacy Policy.</p>\r\n        <p>® Reg. TM/MD Pizza Hut International, LLC; Used under license.</p>\r\n        <p>Gluten Free Crust Pizza is made in an environment that contains wheat. Our tomato sauce, meats, cheeses and vegetable toppings are gluten free as well! For more information go to ''Nutrition'' at the bottom of our website.</p>\r\n        <p>Pepsi® and PepsiCo Inc. related companies'' marks are used under license. Brisk® and Lipton® are Unilever BRANDS and related marks are used under license. Dole® and Dole Food Company, Inc.''s related marks are used under license. The HERSHEY’S® and CHIPITS® trademarks are used under license. HERSHEY''S® Chocolate Dunkers include white chocolate and HERSHEY''S® milk chocolate.</p>\r\n        <p>®Reg. TM/MD PH Yum! Franchise I LP; Used under license. © 2015 PH Canada Company. All rights reserved.</p>\r\n        <p>Powered by Tillster®</p>', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, 'option_group', '{"1":"VEGGIE","2":"MEAT","3":"CHEESE","4":"SIZE","5":"OTHER"}', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, 'list_unit', '[{"name":"Unit","data":["Piece","Loaf","Part"]},{"name":"Weight","data":["Kg","Grams","Grains","Pounds","Ounces"]},{"name":"Size","data":["Inches","Feet","Cm","Sq.in","Sq.ft","Sq.cm"]}]', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `failed_logins`
--

CREATE TABLE IF NOT EXISTS `failed_logins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(10) unsigned DEFAULT NULL,
  `ip_address` char(15) NOT NULL,
  `attempted` smallint(5) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `failed_logins`
--

INSERT INTO `failed_logins` (`id`, `admin_id`, `ip_address`, `attempted`, `created_at`, `updated_at`) VALUES
(1, 0, '14.169.115.98', 65535, '2015-09-28 07:53:19', '2015-09-28 07:53:19'),
(2, 0, '14.169.115.98', 65535, '2015-09-28 07:54:56', '2015-09-28 07:54:56'),
(3, 10, '115.73.33.87', 65535, '2015-09-29 13:08:51', '2015-09-29 13:08:51'),
(4, 10, '115.73.33.87', 65535, '2015-09-29 13:08:55', '2015-09-29 13:08:55'),
(5, 10, '14.169.115.98', 65535, '2015-10-06 01:19:03', '2015-10-06 01:19:03');

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `short_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `summary` text COLLATE utf8_unicode_ci,
  `content` text COLLATE utf8_unicode_ci,
  `category_id` int(11) DEFAULT NULL,
  `order_no` int(10) NOT NULL DEFAULT '1',
  `image` text COLLATE utf8_unicode_ci,
  `meta_title` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `meta_desciption` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_2` (`name`),
  KEY `name` (`name`,`short_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=16 ;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `name`, `short_name`, `summary`, `content`, `category_id`, `order_no`, `image`, `meta_title`, `meta_desciption`, `created_at`, `updated_at`) VALUES
(1, 'World Hunger', 'world-hunger', NULL, '<ul class="subnav" style="outline: none; margin-right: 0px; margin-bottom: 1.25em; margin-left: 0px; padding: 0px; direction: ltr; font-size: 16px; line-height: 1.6; list-style-position: outside; font-family: Roboto, Arial, sans-serif; color: rgb(34, 34, 34); background-color: rgb(37, 37, 35);"><li ng-repeat="menuItem in footerMenus[0].posts" class="ng-scope" style="outline: none; margin: 0px 0px 0.7em; padding: 0px; direction: ltr; border-color: rgba(255, 255, 255, 0.298039); line-height: 1.1em; color: rgb(255, 255, 255); font-size: 0.95em; list-style: none;"><a href="https://www.pizzahut.ca/#!/page/92" target="" class="ng-binding" style="outline: none; color: rgb(255, 255, 255); line-height: inherit; font-size: 0.95em;">World Hunger</a></li></ul>', 6, 2, NULL, '', NULL, '2015-10-04 19:08:02', '2015-10-04 19:10:27'),
(2, 'Our Story', 'our-story', NULL, '<div class="main_wrap" style="background:none;">\n	<h1>Our Story</h1>\n	<h2>Since 1958</h2>\n	<div class="white_block rounded-5">\n		<div class="block row">\n			<div class="col-md-8 col-xs-8">\n				<h2 style="color:#222;text-align: left">The Legacy of Pizza Hut began in 1958,</h2>\n	<h3 style="color:#222;text-align: left;font-size: 22px;">when two college students from Wichita, Kansas, Frank and Dan Carney; were approached by a family friend with the idea of opening a pizza parlor.</h3>\n			</div>\n			<div class="col-md-4 col-xs-4 text-right">\n				<img src="http://pos.banhmisub.com/images/products/test%20011.02-10-15.jpg" alt="" style="max-height: 120px;">\n			</div>\n		</div>\n	</div>\n	<div class="grey_block rounded-5" style="margin-top: -10px;">\n		<h2 style="color:#222;text-align: left">Although the concept was relatively new to many Americans at that time, the brothers quickly saw the potential of this new enterprise.</h2>\n		<p>After borrowing $600(US) from their mother, they purchased some second-hand equipment and rented a small building on a busy intersection in their home town. The result of their entrepreneurial efforts was the first Pizza Hut® restaurant, and the foundation for what would become the largest and most successful pizza restaurant in the world.</p>\n		<p>Pizza Hut® is a division of YUM! Brands Inc. and has more than 300 units in Canada, 7,200 units in the U.S. and 3,000 units in more than 86 other countries.</p>\n		<p>YUM! Brands Inc. is the parent company to two other segment leaders, Taco Bell and KFC. When combined with Pizza Hut®, these organizations make up the world’s largest restaurant group.</p>\n		<p>For more information about YUM!, visit <a href="www.yum.com">www.yum.com</a>.</p>\n		<p>Pizza Hut gift cards are available in any denominations and are reloadable at participating Pizza Hut Canada locations.</p>\n	</div>\n</div>', 6, 1, NULL, '', NULL, '2015-10-04 19:09:23', '2015-10-04 19:10:49'),
(3, 'Careers', 'careers', NULL, '', 6, 3, NULL, '', NULL, '2015-10-04 19:14:42', '2015-10-04 19:14:42'),
(4, 'Create an Account', 'create-an-account', NULL, '', 7, 1, NULL, '', NULL, '2015-10-06 01:35:52', '2015-10-06 01:35:52'),
(5, 'Sign In', 'sign-in', NULL, '', 7, 2, NULL, '', NULL, '2015-10-06 01:38:42', '2015-10-06 01:38:42'),
(6, 'Contact Us', 'contact-us', NULL, '', 8, 1, NULL, '', NULL, '2015-10-06 01:43:42', '2015-10-06 01:43:42'),
(7, 'Find a BanhMiSub', 'find-a-banhmisub', NULL, '', 8, 1, NULL, '', NULL, '2015-10-06 01:45:55', '2015-10-06 01:45:55'),
(8, 'Catering', 'catering', NULL, '', 8, 1, NULL, '', NULL, '2015-10-06 01:57:37', '2015-10-06 01:57:37'),
(9, 'Terms of Use', 'terms-of-use', NULL, '', 9, 1, NULL, '', NULL, '2015-10-06 01:58:13', '2015-10-06 01:58:13'),
(10, 'Privacy Policy', 'privacy-policy', NULL, '', 9, 1, NULL, '', NULL, '2015-10-06 01:58:29', '2015-10-06 01:58:29'),
(11, 'Nutrition Information', 'nutrition-information', NULL, '', 10, 1, NULL, '', NULL, '2015-10-06 01:58:55', '2015-10-06 01:58:55'),
(12, 'Ingredient Listing', 'ingredient-listing', '', '', 10, 1, NULL, '', NULL, '2015-10-06 01:59:15', '2015-10-06 01:59:15'),
(13, 'Make It Great', 'make-it-great', NULL, '', 11, 1, NULL, '', NULL, '2015-10-06 01:59:58', '2015-10-06 01:59:58'),
(14, 'Why Us', 'why-us', NULL, '', 11, 1, NULL, '', NULL, '2015-10-06 02:00:28', '2015-10-06 02:00:28'),
(15, 'FAQ', 'faq', NULL, '', 11, 1, NULL, '', NULL, '2015-10-06 02:00:41', '2015-10-06 02:00:41');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `short_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `category_id` int(10) NOT NULL,
  `image` text COLLATE utf8_unicode_ci,
  `meta_title` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8_unicode_ci,
  `price` float DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `short_name_2` (`short_name`),
  KEY `category_id` (`category_id`),
  KEY `short_name` (`short_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `short_name`, `description`, `category_id`, `image`, `meta_title`, `meta_description`, `price`, `created_at`, `updated_at`) VALUES
(1, 'Bánh mì dac biet', 'banh-mi-dac-biet', 'Bánh mì ngon va re nhat tren the gioi', 3, 'images/products/test 011.02-10-15.jpg', '', '', 6.8, '2015-09-29 04:48:45', '2015-10-05 08:20:46'),
(2, 'Traditional Banh Mi', 'traditional-banh-mi', 'Type flavored traditional cake from many years ago. The shape and materials of all kinds are in the traditional way.', 1, 'images/products/l_518856232_banh-mi-xa-xiu_1444097456.png', '', '', 5.5, '2015-10-02 13:15:44', '2015-10-06 02:10:56'),
(3, 'Pepsi', 'pepsi', '', 4, NULL, '', '', 1.5, '2015-10-04 21:35:31', '2015-10-05 08:21:33'),
(4, 'Banh Mi custom', 'banh-mi-custom', 'Create your ideal Banh mi', 2, 'images/products/banhmi_1444098369.png', '', '', 0, '2015-10-06 02:12:59', '2015-10-06 02:26:09');

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE IF NOT EXISTS `product_categories` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `short_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image` text COLLATE utf8_unicode_ci,
  `meta_title` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8_unicode_ci,
  `parent_id` int(11) DEFAULT NULL,
  `position` tinyint(4) DEFAULT '1',
  `order_no` int(11) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `short_name` (`short_name`),
  KEY `short_name_2` (`short_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=13 ;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`id`, `name`, `short_name`, `description`, `image`, `meta_title`, `meta_description`, `parent_id`, `position`, `order_no`, `created_at`, `updated_at`) VALUES
(1, 'Feature Promo', 'feature-promo', 'Meat Bread', 'images/product-categories/michael-symons-banh-mi-sliders_recipe_1000x400_1417815108038.05-10-15.jpg', '', '', 0, 1, 1, '2015-09-29 13:17:18', '2015-10-06 02:57:31'),
(2, 'Banh Mi SUBS', 'banh-mi-subs', 'Bread Soup', 'images/product-categories/thuc-don-tang-can-buoi-sang-2.02-10-15.jpg', '', '', NULL, 1, 1, '2015-09-29 23:35:41', '2015-10-02 15:15:41'),
(3, 'Sides', 'sides', 'Original Bread', 'images/product-categories/maxresdefault.02-10-15.jpg', 'Original Bread', 'Original Bread', NULL, 1, 1, '2015-09-29 23:36:45', '2015-10-02 14:29:15'),
(4, 'Drinks', 'drinks', 'Egg bread', 'images/product-categories/bundaberg__brewed__drinks__range.02-10-15.jpg', 'Egg bread', 'Egg bread', NULL, 1, 1, '2015-09-29 23:38:11', '2015-10-02 15:55:52'),
(5, 'Catering', 'catering', 'Drinks', 'images/product-categories/drinks-_wallpaper.02-10-15.jpg', 'Drinks', 'Drinks', 0, 1, 1, '2015-09-29 23:39:05', '2015-10-04 14:37:59'),
(6, 'About Us', 'about-us', '', NULL, '', '', 0, 2, 1, '2015-10-04 15:51:19', '2015-10-04 15:57:15'),
(7, 'My Account', 'my-account', '', NULL, '', '', 0, 2, 1, '2015-10-04 15:59:47', '2015-10-04 16:00:27'),
(8, 'Customer Service', 'customer-service', '', NULL, '', '', 0, 2, 2, '2015-10-04 16:06:07', '2015-10-04 16:06:07'),
(9, 'Policies', 'policies', '', NULL, '', '', 0, 2, 2, '2015-10-04 16:14:24', '2015-10-04 17:09:02'),
(10, 'Nutrition', 'nutrition', '', NULL, '', '', 0, 2, 3, '2015-10-04 17:06:37', '2015-10-04 17:06:53'),
(11, 'Become a Franchisee', 'become-a-franchisee', '', NULL, '', '', 0, 2, 3, '2015-10-04 17:09:16', '2015-10-04 17:35:30'),
(12, 'Our Story', 'our-story', '', NULL, '', '', 0, 0, 1, '2015-10-04 17:17:15', '2015-10-05 15:20:22');

-- --------------------------------------------------------

--
-- Table structure for table `success_logins`
--

CREATE TABLE IF NOT EXISTS `success_logins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(10) unsigned NOT NULL,
  `ip_address` char(15) NOT NULL,
  `user_agent` varchar(120) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=61 ;

--
-- Dumping data for table `success_logins`
--

INSERT INTO `success_logins` (`id`, `admin_id`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, 1, '::1', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.93 Safari/537.36', '2015-09-21 21:12:35', '2015-09-21 21:12:35'),
(2, 1, '::1', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.93 Safari/537.36', '2015-09-22 03:07:08', '2015-09-22 03:07:08'),
(3, 1, '::1', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.93 Safari/537.36', '2015-09-23 02:47:56', '2015-09-23 02:47:56'),
(4, 1, '::1', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.93 Safari/537.36', '2015-09-23 20:14:33', '2015-09-23 20:14:33'),
(5, 1, '::1', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.93 Safari/537.36', '2015-09-24 03:16:13', '2015-09-24 03:16:13'),
(6, 1, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.99 Safari/537.36', '2015-09-28 07:15:32', '2015-09-28 07:15:32'),
(7, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.99 Safari/537.36', '2015-09-28 07:57:32', '2015-09-28 07:57:32'),
(8, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-09-28 08:17:51', '2015-09-28 08:17:51'),
(9, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-09-29 01:32:55', '2015-09-29 01:32:55'),
(10, 10, '174.0.26.72', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.93 Safari/537.36', '2015-09-29 03:25:49', '2015-09-29 03:25:49'),
(11, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-09-29 03:53:32', '2015-09-29 03:53:32'),
(12, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-09-29 03:53:33', '2015-09-29 03:53:33'),
(13, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-09-29 03:53:57', '2015-09-29 03:53:57'),
(14, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-09-29 03:54:04', '2015-09-29 03:54:04'),
(15, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-09-29 03:54:04', '2015-09-29 03:54:04'),
(16, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-09-29 03:54:04', '2015-09-29 03:54:04'),
(17, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-09-29 03:54:05', '2015-09-29 03:54:05'),
(18, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-09-29 03:54:05', '2015-09-29 03:54:05'),
(19, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-09-29 03:54:05', '2015-09-29 03:54:05'),
(20, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-09-29 03:55:15', '2015-09-29 03:55:15'),
(21, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-09-29 03:55:16', '2015-09-29 03:55:16'),
(22, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-09-29 03:55:17', '2015-09-29 03:55:17'),
(23, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-09-29 03:55:17', '2015-09-29 03:55:17'),
(24, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-09-29 03:55:17', '2015-09-29 03:55:17'),
(25, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-09-29 03:55:17', '2015-09-29 03:55:17'),
(26, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-09-29 04:11:31', '2015-09-29 04:11:31'),
(27, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-09-29 04:12:27', '2015-09-29 04:12:27'),
(28, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-09-29 07:08:59', '2015-09-29 07:08:59'),
(29, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-09-29 08:48:04', '2015-09-29 08:48:04'),
(30, 10, '115.73.33.87', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-09-29 13:09:23', '2015-09-29 13:09:23'),
(31, 10, '115.73.33.87', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-09-29 13:50:27', '2015-09-29 13:50:27'),
(32, 10, '205.206.177.129', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.99 Safari/537.36', '2015-09-29 21:49:59', '2015-09-29 21:49:59'),
(33, 10, '115.73.33.87', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-09-29 23:21:04', '2015-09-29 23:21:04'),
(34, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-09-30 01:01:55', '2015-09-30 01:01:55'),
(35, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-09-30 03:04:07', '2015-09-30 03:04:07'),
(36, 10, '115.73.8.122', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-10-01 14:08:24', '2015-10-01 14:08:24'),
(37, 10, '205.206.177.129', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-10-01 17:03:42', '2015-10-01 17:03:42'),
(38, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-10-02 01:20:10', '2015-10-02 01:20:10'),
(39, 10, '174.0.26.72', 'Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-10-02 13:10:12', '2015-10-02 13:10:12'),
(40, 10, '115.73.128.218', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-10-02 14:11:01', '2015-10-02 14:11:01'),
(41, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-10-03 01:24:48', '2015-10-03 01:24:48'),
(42, 10, '115.73.138.167', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-10-03 15:37:05', '2015-10-03 15:37:05'),
(43, 10, '205.206.177.129', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-10-04 01:40:45', '2015-10-04 01:40:45'),
(44, 10, '115.73.138.167', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-10-04 08:19:11', '2015-10-04 08:19:11'),
(45, 10, '115.73.138.167', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-10-04 09:47:02', '2015-10-04 09:47:02'),
(46, 10, '115.73.138.167', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-10-04 09:47:03', '2015-10-04 09:47:03'),
(47, 10, '115.73.34.91', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-10-04 14:04:51', '2015-10-04 14:04:51'),
(48, 10, '115.73.34.91', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-10-04 15:27:36', '2015-10-04 15:27:36'),
(49, 10, '115.73.34.91', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-10-04 17:05:37', '2015-10-04 17:05:37'),
(50, 10, '205.206.177.129', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-10-04 17:08:35', '2015-10-04 17:08:35'),
(51, 10, '205.206.177.129', 'Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.135 Safari/537.36 Edge/12.10240', '2015-10-04 21:34:47', '2015-10-04 21:34:47'),
(52, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-10-05 02:25:28', '2015-10-05 02:25:28'),
(53, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-10-05 03:53:23', '2015-10-05 03:53:23'),
(54, 1, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-10-05 04:22:37', '2015-10-05 04:22:37'),
(55, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-10-05 08:14:14', '2015-10-05 08:14:14'),
(56, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-10-05 09:25:57', '2015-10-05 09:25:57'),
(57, 10, '115.73.41.179', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-10-05 15:19:50', '2015-10-05 15:19:50'),
(58, 10, '115.73.41.179', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-10-05 17:13:38', '2015-10-05 17:13:38'),
(59, 10, '115.73.41.179', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-10-05 17:13:39', '2015-10-05 17:13:39'),
(60, 10, '14.169.115.98', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36', '2015-10-06 01:19:15', '2015-10-06 01:19:15');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `email` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `password` text COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `birthday` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `subscribe` tinyint(4) NOT NULL DEFAULT '0',
  `active` tinyint(4) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `email_2` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `first_name`, `last_name`, `birthday`, `subscribe`, `active`, `created_at`, `updated_at`) VALUES
(1, 'nmtri44@gmail.com', '$2a$08$2uTGoS6QwqhIG7oseiZ3ROj24TV7XsKM5zLeABCkkSFNmtz..9o3i', 'Kami', 'Tori', '4-4', 0, 1, '2015-10-01 04:35:02', '2015-10-01 04:35:02'),
(4, 'test@anvy.com', '$2a$08$WjfOucCHxaQYpc1WlJ8pKuv/Y3qt7T5OPcAdhwwYdzsV9eD9lNCeq', 'Test', 'Test', '1-5', 1, 1, '2015-10-02 09:59:00', '2015-10-02 09:59:00');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
