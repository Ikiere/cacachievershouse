<?php
// ── SEO Helper — include BEFORE <head> closes ──
// Usage: set $seo array before including header.php, e.g.:
// $seo = ['title' => 'Page Title', 'description' => '...', 'image' => '...', 'url' => '...'];

$site_name    = 'CAC Achievers House';
$site_url     = 'http://' . ($_SERVER['HTTP_HOST'] ?? 'cacachievers.com');
$default_img  = $site_url . '/assets/images/bg.jpg';

$seo_title       = isset($seo['title'])       ? htmlspecialchars($seo['title']) . ' — ' . $site_name : $site_name . ' — Where Faith Meets Destiny';
$seo_description = isset($seo['description']) ? htmlspecialchars($seo['description']) : 'CAC Achievers House is a vibrant Spirit-filled church community in Nigeria. Join us for Sunday services, Bible study, prayer meetings and life-changing ministries.';
$seo_keywords    = isset($seo['keywords'])    ? htmlspecialchars($seo['keywords'])    : 'CAC Achievers House, church, faith, Sunday service, ministries, prayer, Nigeria';
$seo_image       = isset($seo['image'])       ? htmlspecialchars($seo['image'])       : $default_img;
$seo_url         = isset($seo['url'])         ? htmlspecialchars($seo['url'])         : $site_url . '/' . basename($_SERVER['PHP_SELF'] ?? '');
$seo_type        = isset($seo['type'])        ? htmlspecialchars($seo['type'])        : 'website';
?>
    <meta name="description" content="<?= $seo_description ?>">
    <meta name="keywords"    content="<?= $seo_keywords ?>">
    <meta name="author"      content="CAC Achievers House">
    <meta name="robots"      content="index, follow">
    <link rel="canonical"    href="<?= $seo_url ?>">

    <!-- Open Graph -->
    <meta property="og:type"        content="<?= $seo_type ?>">
    <meta property="og:title"       content="<?= $seo_title ?>">
    <meta property="og:description" content="<?= $seo_description ?>">
    <meta property="og:image"       content="<?= $seo_image ?>">
    <meta property="og:url"         content="<?= $seo_url ?>">
    <meta property="og:site_name"   content="<?= $site_name ?>">
    <meta property="og:locale"      content="en_NG">

    <!-- Twitter Card -->
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="<?= $seo_title ?>">
    <meta name="twitter:description" content="<?= $seo_description ?>">
    <meta name="twitter:image"       content="<?= $seo_image ?>">
