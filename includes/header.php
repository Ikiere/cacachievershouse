<!DOCTYPE html>
<html lang="en-NG">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="<?= $primary_color ?? '#f97316' ?>">

    <?php
    // Load DB connection + get_setting() helper
    require_once __DIR__ . '/config.php';
    $site_name  = htmlspecialchars(get_setting('site_name', 'CAC Achievers House'));
    $site_tagline = htmlspecialchars(get_setting('site_tagline', 'Where Faith Meets Destiny'));
    $page_title = isset($seo['title'])
        ? htmlspecialchars($seo['title']) . ' — ' . $site_name
        : $site_name . ' — ' . $site_tagline;
    // Inject primary color CSS variable from DB
    $primary_color = htmlspecialchars(get_setting('primary_color', '#f97316'));
    ?>
    <title><?= $page_title ?></title>

    <?php include_once __DIR__ . '/seo.php'; ?>

    <!-- JSON-LD Schema: Church -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Church",
        "name": "<?= addslashes(get_setting('site_name', 'CAC Achievers House')) ?>",
        "url": "<?= 'http://' . ($_SERVER['HTTP_HOST'] ?? 'cacachievers.com') ?>",
        "logo": "<?= 'http://' . ($_SERVER['HTTP_HOST'] ?? 'cacachievers.com') ?>/<?= get_setting('logo_path', 'assets/logo/cac-logo.png') ?>",
        "description": "<?= addslashes(get_setting('site_name', 'CAC Achievers House')) ?> — <?= addslashes(get_setting('site_tagline', 'Where Faith Meets Destiny')) ?>. A vibrant Spirit-filled church community.",
        "telephone": "<?= addslashes(get_setting('contact_phone', '')) ?>",
        "email": "<?= addslashes(get_setting('contact_email', '')) ?>",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "<?= addslashes(get_setting('contact_address', '')) ?>",
            "addressCountry": "NG"
        },
        "openingHoursSpecification": [
            { "@type": "OpeningHoursSpecification", "dayOfWeek": "Sunday",    "opens": "08:00", "closes": "11:00" },
            { "@type": "OpeningHoursSpecification", "dayOfWeek": "Wednesday", "opens": "18:00", "closes": "20:00" },
            { "@type": "OpeningHoursSpecification", "dayOfWeek": "Friday",    "opens": "19:00", "closes": "21:00" }
        ],
        "sameAs": [
            "<?= addslashes(get_setting('facebook_url', '')) ?>",
            "<?= addslashes(get_setting('youtube_url', '')) ?>",
            "<?= addslashes(get_setting('instagram_url', '')) ?>"
        ]
    }
    </script>

    <!-- Dynamic primary color from Site Settings -->
    <style>:root { --primary: <?= $primary_color ?>; --primary-dark: color-mix(in srgb, <?= $primary_color ?> 85%, #000); }</style>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/about.css">
    <link rel="stylesheet" href="assets/css/pages.css">
    <link rel="stylesheet" href="assets/css/single-event.css">
</head>