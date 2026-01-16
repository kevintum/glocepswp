<?php
/**
 * Block: Goals Strip
 * 
 * Horizontal strip/list of goals
 * 
 * @package GLOCEPS
 */

$eyebrow = get_sub_field('eyebrow') ?: 'Strategic Direction';
$title = get_sub_field('title') ?: 'Our Goals';
$goals = get_sub_field('goals');
$anchor_id = get_sub_field('anchor_id') ?: 'goals';
?>

<?php if ($goals && !empty($goals)) : ?>
<section class="section section--compact goals-strip" id="<?php echo esc_attr($anchor_id); ?>">
    <div class="container">
        <div class="goals-strip__inner reveal">
            <div class="goals-strip__header">
                <?php if ($eyebrow) : ?>
                    <span class="eyebrow"><?php echo esc_html($eyebrow); ?></span>
                <?php endif; ?>
                <?php if ($title) : ?>
                    <h3 class="goals-strip__title"><?php echo esc_html($title); ?></h3>
                <?php endif; ?>
            </div>
            <div class="goals-strip__list">
                <?php 
                $counter = 1;
                foreach ($goals as $goal) : 
                    $goal_text = $goal['text'] ?? '';
                    if (!$goal_text) continue;
                ?>
                    <div class="goals-strip__item">
                        <span class="goals-strip__num"><?php echo esc_html($counter); ?></span>
                        <span class="goals-strip__text"><?php echo esc_html($goal_text); ?></span>
                    </div>
                <?php 
                    $counter++;
                endforeach; 
                ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>


