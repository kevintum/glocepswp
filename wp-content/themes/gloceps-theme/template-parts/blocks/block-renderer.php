<?php
/**
 * Flexible Content Block Renderer
 * 
 * This file loops through the flexible content blocks and renders each one.
 * When included directly, it automatically renders the blocks.
 * 
 * @package GLOCEPS
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render flexible content blocks
 * 
 * @param int|null $post_id Optional. Post ID to render blocks for. Defaults to current post.
 */
function gloceps_render_blocks($post_id = null) {
    if (!function_exists('have_rows')) {
        return;
    }

    // Get total count of blocks first
    $total_blocks = 0;
    if (have_rows('content_blocks', $post_id)) {
        $total_blocks = count(get_field('content_blocks', $post_id) ?: array());
    }
    echo '<!-- Total blocks in ACF: ' . $total_blocks . ' -->';
    
    if (have_rows('content_blocks', $post_id)) :
        $block_count = 0;
        while (have_rows('content_blocks', $post_id)) : the_row();
            $block_count++;
            $layout = get_row_layout();
            
            // Skip if layout is empty
            if (empty($layout)) {
                echo '<!-- Block #' . $block_count . ': Empty layout, skipping -->';
                continue;
            }
            
            $block_file = get_template_directory() . '/template-parts/blocks/block-' . str_replace('_', '-', $layout) . '.php';
            
            // Debug comment for each block
            echo '<!-- Block #' . $block_count . ': ' . esc_html($layout) . ' -->';
            
            if (file_exists($block_file)) {
                // Use output buffering and isolate block execution
                ob_start();
                $block_error = false;
                $error_message = '';
                $last_error_before = error_get_last();
                
                // Temporarily disable error display to prevent fatal errors from stopping execution
                $old_error_reporting = error_reporting(E_ALL);
                $old_display_errors = ini_get('display_errors');
                ini_set('display_errors', '0');
                
                // Use a closure to isolate the include
                $include_block = function() use ($block_file, &$block_error, &$error_message) {
                    try {
                        include $block_file;
                    } catch (Throwable $e) {
                        $block_error = true;
                        $error_message = $e->getMessage() . ' (Line: ' . $e->getLine() . ')';
                        error_log('GLOCEPS Block Exception (' . basename($block_file) . '): ' . $error_message);
                        return false;
                    }
                    return true;
                };
                
                $include_success = $include_block();
                
                // Restore error settings
                error_reporting($old_error_reporting);
                if ($old_display_errors !== false) {
                    ini_set('display_errors', $old_display_errors);
                }
                
                // Check for new errors
                $last_error_after = error_get_last();
                if ($last_error_after && $last_error_after !== $last_error_before) {
                    if ($last_error_after['type'] === E_ERROR || $last_error_after['type'] === E_PARSE) {
                        $block_error = true;
                        $error_message = $last_error_after['message'] . ' in ' . basename($last_error_after['file']) . ' on line ' . $last_error_after['line'];
                        error_log('GLOCEPS Block Fatal Error (' . $layout . '): ' . $error_message);
                    }
                }
                
                $block_output = ob_get_clean();
                
                // If block failed to include and produced no output, show error
                if (!$include_success || ($block_error && empty(trim($block_output)))) {
                    if (current_user_can('edit_posts')) {
                        echo '<!-- Block Error: ' . esc_html($layout) . ' - ' . esc_html($error_message) . ' -->';
                        echo '<div class="block-error" style="padding: 2rem; margin: 1rem 0; background: #fee; border: 2px solid #fcc; text-align: center;">';
                        echo '<p><strong>Block Error: ' . esc_html(ucwords(str_replace('_', ' ', $layout))) . '</strong></p>';
                        echo '<p style="color: #c00; font-size: 0.875rem;">' . esc_html($error_message) . '</p>';
                        echo '<p style="font-size: 0.75rem; color: #666;">Execution continued to next block. Check debug.log for details.</p>';
                        echo '</div>';
                    }
                } else {
                    // Output the block content (even if there was an error, show what was generated)
                    echo $block_output;
                }
            } else {
                // Debug output for developers (visible in HTML source)
                echo '<!-- Block: ' . esc_html($layout) . ' (template not found at: block-' . str_replace('_', '-', $layout) . '.php) -->';
                
                // Visual placeholder for admin users
                if (current_user_can('edit_posts')) {
                    echo '<div class="block-placeholder" style="padding: 2rem; margin: 1rem 0; background: #f8f9fa; border: 2px dashed #dee2e6; text-align: center;">';
                    echo '<p><strong>Block: ' . esc_html(ucwords(str_replace('_', ' ', $layout))) . '</strong></p>';
                    echo '<p style="color: #6c757d;">Template file not found. Create <code>block-' . esc_html(str_replace('_', '-', $layout)) . '.php</code></p>';
                    echo '</div>';
                }
            }
        endwhile;
        echo '<!-- Total blocks rendered: ' . $block_count . ' -->';
    endif;
}

// Auto-render when this file is included directly (for backwards compatibility)
if (basename($_SERVER['SCRIPT_FILENAME']) !== 'block-renderer.php') {
    gloceps_render_blocks();
}
