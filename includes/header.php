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
    if (!function_exists('darken_color')) {
        function darken_color($hex, $percent = 15) {
            $hex = ltrim($hex, '#');
            if (strlen($hex) === 3) {
                $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
            }
            if (strlen($hex) !== 6) return '#000000';
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
            $factor = (100 - $percent) / 100;
            $r = max(0, min(255, round($r * $factor)));
            $g = max(0, min(255, round($g * $factor)));
            $b = max(0, min(255, round($b * $factor)));
            return sprintf("#%02x%02x%02x", $r, $g, $b);
        }
    }
    $primary_color_dark = darken_color($primary_color, 15);
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
    <style>:root { --primary: <?= $primary_color ?>; --primary-dark: <?= $primary_color_dark ?>; }</style>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <!-- Main CSS -->
    <link rel="stylesheet" href="<?= $base ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= $base ?>assets/css/about.css">
    <link rel="stylesheet" href="<?= $base ?>assets/css/pages.css">
    <link rel="stylesheet" href="<?= $base ?>assets/css/single-event.css">
    <link rel="stylesheet" href="<?= $base ?>assets/css/ministry.css">
</head>