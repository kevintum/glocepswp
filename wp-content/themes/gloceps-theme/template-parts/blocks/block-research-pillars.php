<?php
/**
 * Block: Research Pillars Grid
 * 
 * Displays the 5 research pillars in a grid
 * 
 * @package GLOCEPS
 */

$eyebrow = get_sub_field('eyebrow') ?: 'RESEARCH PILLARS';
$title = get_sub_field('title') ?: 'Areas of Focus';
$description = get_sub_field('description');

// Define the pillars with their data
$pillars = array(
    array(
        'title' => 'Foreign Policy',
        'slug' => 'foreign-policy',
        'description' => 'Examining the global interplay of balance of power, great power politics, diplomatic networks, and strategic interests.',
        'color' => '#1e3a5f',
        'icon' => '<svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418"/></svg>',
    ),
    array(
        'title' => 'Security & Defence',
        'slug' => 'security-defence',
        'description' => 'Analyzing regional security dynamics, defense cooperation, peacekeeping, and emerging security challenges.',
        'color' => '#8B4513',
        'icon' => '<svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>',
    ),
    array(
        'title' => 'Governance & Ethics',
        'slug' => 'governance-ethics',
        'description' => 'Promoting accountable institutions, ethical leadership, and strengthening democratic governance structures.',
        'color' => '#2F4F4F',
        'icon' => '<svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 01-2.031.352 5.988 5.988 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.97zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 01-2.031.352 5.989 5.989 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.97z"/></svg>',
    ),
    array(
        'title' => 'Development',
        'slug' => 'development',
        'description' => 'Advancing sustainable development strategies, economic policy, and regional integration frameworks.',
        'color' => '#228B22',
        'icon' => '<svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>',
    ),
    array(
        'title' => 'Transnational Organised Crimes',
        'slug' => 'transnational-organised-crimes',
        'description' => 'Combating cross-border criminal networks, terrorism financing, and emerging threat landscapes.',
        'color' => '#8B0000',
        'icon' => '<svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>',
    ),
);
?>

<section class="section pillars" id="pillars">
    <div class="container">
        <div class="section-header section-header--center reveal">
            <?php if ($eyebrow) : ?>
                <div class="section-header__eyebrow">
                    <span class="eyebrow"><?php echo esc_html($eyebrow); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($title) : ?>
                <h2 class="section-header__title"><?php echo wp_kses_post($title); ?></h2>
            <?php endif; ?>

            <?php if ($description) : ?>
                <p class="section-header__description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </div>

        <div class="pillars__grid reveal stagger-children">
            <?php foreach ($pillars as $pillar) : ?>
                <a href="<?php echo esc_url(home_url('/research/' . $pillar['slug'] . '/')); ?>" class="pillar-card">
                    <div class="pillar-card__icon">
                        <?php echo $pillar['icon']; ?>
                    </div>
                    <h3 class="pillar-card__title"><?php echo esc_html($pillar['title']); ?></h3>
                    <p class="pillar-card__description"><?php echo esc_html($pillar['description']); ?></p>
                    <span class="pillar-card__link">
                        Explore Pillar
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

