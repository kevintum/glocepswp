/**
 * ACF Flexible Content Enhancements
 * Fixes drag-and-drop, collapse, and adds up/down controls
 */
(function($) {
    'use strict';

    /**
     * Initialize ACF Flexible Content enhancements
     */
    function initACFEnhancements() {
        // Wait for ACF to be ready
        if (typeof acf === 'undefined') {
            return;
        }

        // Fix ACF's collapse error by patching the collapse handler
        // The error happens because ACF tries to call .replace() on undefined title
        $(document).on('acf/setup_fields', function(e, $el) {
            $el.find('.acf-fc-layout').each(function() {
                var $layout = $(this);
                var $title = $layout.find('.acf-fc-layout-handle .acf-fc-layout-title, .acf-fc-layout-handle strong');
                
                // Ensure title exists and has text
                if ($title.length === 0 || !$title.text().trim()) {
                    var layoutName = $layout.data('layout') || $layout.attr('data-layout') || 'Layout';
                    var $handle = $layout.find('.acf-fc-layout-handle');
                    if ($handle.length) {
                        if ($title.length === 0) {
                            $handle.prepend('<strong class="acf-fc-layout-title">' + layoutName + '</strong>');
                        } else {
                            $title.text(layoutName);
                        }
                    }
                }
            });
        });
        
        // Patch ACF's collapse handler to prevent the error
        // Run BEFORE ACF's handler (use capture phase)
        $(document).on('click', '.acf-icon.-collapse, a.acf-icon.-collapse', function(e) {
            var $layout = $(this).closest('.acf-fc-layout');
            if ($layout.length) {
                var $handle = $layout.find('.acf-fc-layout-handle');
                var $title = $handle.find('.acf-fc-layout-title, strong');
                
                if ($title.length === 0 || !$title.text().trim()) {
                    // Ensure title exists before ACF tries to use it
                    var layoutName = $layout.data('layout') || $layout.attr('data-layout') || $layout.find('input[data-name="acf_fc_layout"]').val() || 'Layout';
                    if ($handle.length) {
                        if ($title.length === 0) {
                            $handle.prepend('<strong class="acf-fc-layout-title">' + layoutName + '</strong>');
                        } else {
                            $title.text(layoutName);
                        }
                    }
                }
            }
            // Don't prevent default - let ACF handle it, but we've fixed the title
        });

        // Add up/down arrow controls to each layout
        function addMoveControls() {
            // Look for layouts in multiple possible locations
            var $layouts = $('.acf-fc-layout, [data-layout]');

            if ($layouts.length === 0) {
                return;
            }

            $layouts.each(function() {
                var $layout = $(this);
                
                // Only add controls if they don't exist
                if ($layout.find('.acf-fc-move-controls').length === 0) {
                    var $controls = $('<div class="acf-fc-move-controls"></div>');
                    var $upBtn = $('<button type="button" class="acf-fc-move-up" title="Move Up" aria-label="Move block up"><span class="dashicons dashicons-arrow-up-alt"></span></button>');
                    var $downBtn = $('<button type="button" class="acf-fc-move-down" title="Move Down" aria-label="Move block down"><span class="dashicons dashicons-arrow-down-alt"></span></button>');
                    
                    $controls.append($upBtn).append($downBtn);
                    
                    // Try multiple locations to insert controls
                    var $controlsWrap = $layout.find('.acf-fc-layout-controls, .acf-fc-layout-action-wrap');
                    var $header = $layout.find('.acf-fc-layout-handle');
                    var $actionWrap = $layout.find('.acf-fc-layout-action');
                    
                    // Ensure controls are inside the layout element
                    if ($controlsWrap.length) {
                        $controlsWrap.prepend($controls);
                    } else if ($actionWrap.length) {
                        $actionWrap.prepend($controls);
                    } else if ($header.length) {
                        // Insert at the beginning of header to ensure it's inside layout
                        $header.prepend($controls);
                    } else {
                        // Last resort: prepend to layout itself
                        $layout.prepend($controls);
                    }
                }
            });
        }

        // Move block up - simplified approach
        $(document).on('click', '.acf-fc-move-up', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            var $button = $(this);
            
            if ($button.prop('disabled')) {
                return false;
            }
            
            // Try multiple ways to find the layout
            var $layout = $button.closest('.acf-fc-layout');
            if ($layout.length === 0) {
                // Try going up to find any element with data-layout
                $layout = $button.closest('[data-layout]');
            }
            if ($layout.length === 0) {
                // Try finding by looking for the layout handle
                $layout = $button.closest('.acf-fc-layout-handle').closest('.acf-fc-layout');
            }
            
            if ($layout.length === 0) {
                return false;
            }
            
            var $container = $layout.parent();
            // Find previous sibling - use .layout selector (not .acf-fc-layout)
            var $prev = $layout.prev('.layout');
            
            if ($prev.length) {
                // Simply move the DOM element
                $layout.insertBefore($prev);
                
                // Trigger ACF to recognize the change
                var $field = $layout.closest('.acf-field-flexible-content');
                if ($field.length && typeof acf !== 'undefined') {
                    // Use ACF's change detection
                    setTimeout(function() {
                        if (typeof acf !== 'undefined') {
                            acf.do_action('refresh', $field);
                            // Also trigger on the input
                            $field.find('input[type="hidden"]').trigger('change');
                        }
                    }, 50);
                }
                
                updateButtonStates();
            }
            
            return false;
        });

        // Move block down - simplified approach
        $(document).on('click', '.acf-fc-move-down', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            var $button = $(this);
            
            if ($button.prop('disabled')) {
                return false;
            }
            
            // Try multiple ways to find the layout
            var $layout = $button.closest('.acf-fc-layout');
            if ($layout.length === 0) {
                // Try going up to find any element with data-layout
                $layout = $button.closest('[data-layout]');
            }
            if ($layout.length === 0) {
                // Try finding by looking for the layout handle
                $layout = $button.closest('.acf-fc-layout-handle').closest('.acf-fc-layout');
            }
            
            if ($layout.length === 0) {
                return false;
            }
            
            var $container = $layout.parent();
            // Find next sibling - use .layout selector (not .acf-fc-layout)
            var $next = $layout.next('.layout');
            
            if ($next.length) {
                // Simply move the DOM element
                $layout.insertAfter($next);
                
                // Trigger ACF to recognize the change
                var $field = $layout.closest('.acf-field-flexible-content');
                if ($field.length && typeof acf !== 'undefined') {
                    // Use ACF's change detection
                    setTimeout(function() {
                        if (typeof acf !== 'undefined') {
                            acf.do_action('refresh', $field);
                            // Also trigger on the input
                            $field.find('input[type="hidden"]').trigger('change');
                        }
                    }, 50);
                }
                
                updateButtonStates();
            }
            
            return false;
        });

        // Ensure sortable is initialized properly
        function initSortable() {
            // Find the container that holds all layouts
            // In block editor, layouts might be directly in .acf-flexible-content or in a .value container
            var $layouts = $('.acf-fc-layout');
            if ($layouts.length === 0) {
                return;
            }
            
            // Find the common parent that contains all layouts
            var $firstLayout = $layouts.first();
            var $container = $firstLayout.parent();
            
            // Walk up to find the actual sortable container
            while ($container.length && $container.find('.acf-fc-layout').length === $layouts.length) {
                var $parent = $container.parent();
                if ($parent.find('.acf-fc-layout').length === $layouts.length) {
                    $container = $parent;
                } else {
                    break;
                }
            }

            if ($container.length === 0) {
                return;
            }
            
            // Check if already sortable
            if ($container.hasClass('ui-sortable')) {
                // Re-enable if disabled
                if (!$container.sortable('option', 'disabled')) {
                    return;
                }
            }
            
            var $layouts = $container.find('.acf-fc-layout');
            if ($layouts.length === 0) {
                return;
            }

            // Destroy any existing sortable first
            if ($container.data('ui-sortable')) {
                $container.sortable('destroy');
            }

            $container.sortable({
                handle: '.acf-fc-layout-handle',
                items: '.acf-fc-layout',
                axis: 'y',
                tolerance: 'pointer',
                cursor: 'move',
                opacity: 0.6,
                placeholder: 'acf-fc-layout-placeholder',
                forcePlaceholderSize: true,
                start: function(e, ui) {
                    ui.placeholder.height(ui.item.height());
                },
                update: function(e, ui) {
                    // Trigger ACF refresh after sort
                    var $field = $container.closest('.acf-field-flexible-content');
                    if ($field.length && typeof acf !== 'undefined') {
                        acf.do_action('refresh', $field);
                    }
                    updateButtonStates();
                },
                stop: function(e, ui) {
                    // Sortable drag stopped
                }
            });
        }

        // Update button states (enable/disable based on position)
        function updateButtonStates() {
            $('.acf-fc-layout').each(function() {
                var $layout = $(this);
                var $upBtn = $layout.find('.acf-fc-move-up');
                var $downBtn = $layout.find('.acf-fc-move-down');
                
                // Disable up if first, disable down if last
                $upBtn.prop('disabled', $layout.prev('.acf-fc-layout').length === 0);
                $downBtn.prop('disabled', $layout.next('.acf-fc-layout').length === 0);
            });
        }

        // Add controls when layouts are added - wait for layouts to actually exist
        $(document).on('acf/setup_fields', function(e, $el) {
            // Wait a bit for ACF to fully render
            setTimeout(function() {
                addMoveControls();
                initSortable();
                updateButtonStates();
            }, 50);
        });

        // Initial setup - wait longer for ACF to load
        $(document).ready(function() {
            setTimeout(function() {
                addMoveControls();
                initSortable();
                updateButtonStates();
            }, 500);
        });

        // Re-initialize when new layouts are added
        $(document).on('click', '.acf-button[data-name="add-layout"], .acf-fc-layout-handle', function() {
            setTimeout(function() {
                addMoveControls();
                initSortable();
                updateButtonStates();
            }, 300);
        });
    }

    // Initialize when DOM is ready
    $(document).ready(function() {
        // Function to try initialization
        function tryInit() {
            if (typeof acf !== 'undefined') {
                initACFEnhancements();
                return true;
            }
            return false;
        }

        // Try immediately
        if (!tryInit()) {
            // Try after delays
            var attempts = [100, 300, 500, 1000, 2000];
            attempts.forEach(function(delay) {
                setTimeout(function() {
                    tryInit();
                }, delay);
            });
        }
    });

    // Also listen for ACF ready event
    $(document).on('acf/ready acf/setup_fields', function() {
        initACFEnhancements();
    });

    // For block editor, also listen to WordPress events
    if (typeof wp !== 'undefined' && wp.domReady) {
        wp.domReady(function() {
            setTimeout(function() {
                if (typeof acf !== 'undefined') {
                    initACFEnhancements();
                }
            }, 500);
        });
    }

})(jQuery);
