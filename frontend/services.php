<?php
// Set page-specific variables
$pageTitle = "Services";
$pageDescription = "Discover our comprehensive services including consulting, training, and development solutions.";
$additionalCSS = ['./src/css/services.css'];
$additionalJS = ['./src/js/services.js'];

// Include header
include './includes/header.php';
// Load services data (server-side) from JSON so page works without client-side DB/ajax
$servicesJsonPath = __DIR__ . '/src/data/services.json';
$services = [];
if (file_exists($servicesJsonPath)) {
    $raw = file_get_contents($servicesJsonPath);
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) $services = $decoded;
}
?>
<!-- Main Content Container -->
<div class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Our Professional Services</h1>
        <p class="page-subtitle">Empowering your journey with comprehensive career and technical guidance.</p>
    </div>

    <!-- Services Cards -->
    <section class="section">
        <div class="offerings-grid services-grid" id="services-list">
            <?php if (!empty($services)): ?>
                <?php foreach ($services as $item): ?>
                    <div class="service-card" style="box-shadow:0 4px 16px rgba(0,0,0,0.08);">
                        <div class="card-content">
                            <?php if (!empty($item['icon'])): ?>
                                <div style="text-align:center; margin-bottom:0.8rem;"><img src="<?php echo htmlspecialchars($item['icon']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" style="width:64px; height:64px; object-fit:contain;" /></div>
                            <?php endif; ?>
                            <h3 style="font-weight:700; margin-bottom:0.5rem;"><?php echo htmlspecialchars($item['title']); ?></h3>
                            <p class="service-description" style="font-size:1.08rem; margin-bottom:0.7rem;"><?php echo htmlspecialchars($item['description']); ?></p>
                            <?php if (!empty($item['features']) && is_array($item['features'])): ?>
                                <div class="features" style="margin-top:0.8rem;">
                                    <h4 style="margin-bottom:0.4rem; font-weight:600;">What's Included</h4>
                                    <ul style="margin:0; padding-left:1.1rem;">
                                        <?php foreach ($item['features'] as $feature): ?>
                                            <li style="font-weight:500; margin-bottom:0.2rem;"><?php echo htmlspecialchars($feature); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($item['ctaText'])): ?>
                                <div style="margin-top:1rem; display:flex; gap:0.6rem;">
                                    <span class="hero-btn" style="background: #6A5ACD; color:#000; font-weight:600; cursor:default;"><?php echo htmlspecialchars($item['ctaText']); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; color: rgba(0, 0, 0, 0.8); padding: 2rem;">
                    <h3>No Services Found</h3>
                    <p>We don't have any public services listed right now. Please check back later.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php
// Include footer
include './includes/footer.php';
?>