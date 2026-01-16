<?php
/**
 * Flexible Content Block: Institutional Subscriptions Section
 *
 * @package GLOCEPS
 */

// Always render this block - no early returns
$eyebrow = get_sub_field('eyebrow') ?: 'For Organisations';
$title = get_sub_field('title') ?: 'Institutional Subscriptions';
$description = get_sub_field('description') ?: 'Universities, government agencies, and development organisations can access our full catalogue through institutional licensing.';
$details = get_sub_field('details') ?: 'Institutional subscribers receive advance access to upcoming publications, priority event invitations, quarterly briefings with GLOCEPS researchers, and custom research commissions at preferred rates.';
$button = get_sub_field('button');
$benefits = get_sub_field('benefits');
?>

<section class="section" id="institutional-subscriptions">
    <div class="container">
        <div class="two-col">
            <div class="reveal">
                <div class="section-header">
                    <?php if ($eyebrow) : ?>
                        <div class="section-header__eyebrow">
                            <span class="eyebrow"><?php echo esc_html($eyebrow); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($title) : ?>
                        <h2 class="section-header__title"><?php echo esc_html($title); ?></h2>
                    <?php endif; ?>
                </div>
                <?php if ($description) : ?>
                    <p class="lead" style="margin-bottom: var(--space-4);">
                        <?php echo esc_html($description); ?>
                    </p>
                <?php endif; ?>
                <?php if ($details) : ?>
                    <p style="color: var(--color-gray-600); line-height: var(--leading-relaxed); margin-bottom: var(--space-8);">
                        <?php echo esc_html($details); ?>
                    </p>
                <?php endif; ?>
                <?php 
                // Use default button if none provided
                if (empty($button) || empty($button['url'])) {
                    $button = array('title' => 'Enquire About Licensing', 'url' => '#', 'target' => '');
                }
                if ($button && !empty($button['url'])) : 
                    $button_url = is_array($button) ? ($button['url'] ?? '#') : '#';
                    $button_title = is_array($button) ? ($button['title'] ?? 'Enquire About Licensing') : $button;
                    $button_target = is_array($button) ? ($button['target'] ?? '') : '';
                ?>
                    <a href="<?php echo esc_url($button_url); ?>" class="btn btn--primary btn--lg" <?php echo $button_target ? 'target="_blank" rel="noopener"' : ''; ?>>
                        <?php echo esc_html($button_title); ?>
                        <svg class="btn__arrow" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
            
            <?php 
            // Use default benefits if none provided
            if (empty($benefits)) {
                $benefits = array(
                    array('text' => 'Unlimited access to all publications for your team'),
                    array('text' => 'Early access to new research before public release'),
                    array('text' => 'Quarterly briefings with GLOCEPS researchers'),
                    array('text' => 'Priority seating at GLOCEPS events and dialogues'),
                    array('text' => 'Custom research commissions at preferred rates'),
                );
            }
            if ($benefits && !empty($benefits)) : ?>
            <div class="reveal reveal--delay-2" style="background: var(--color-gray-50); padding: var(--space-10); border-radius: var(--radius-2xl);">
                <h4 style="margin-bottom: var(--space-6);"><?php esc_html_e('Institutional Benefits', 'gloceps'); ?></h4>
                <ul style="display: flex; flex-direction: column; gap: var(--space-4);">
                    <?php foreach ($benefits as $benefit) : 
                        $benefit_text = is_array($benefit) ? ($benefit['text'] ?? '') : $benefit;
                        if (empty($benefit_text)) continue;
                    ?>
                    <li style="display: flex; align-items: flex-start; gap: var(--space-3);">
                        <svg width="20" height="20" fill="none" stroke="var(--color-primary)" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0; margin-top: 2px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span><?php echo esc_html($benefit_text); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
