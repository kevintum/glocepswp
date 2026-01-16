/**
 * ACF Flexible Content Enhancements
 * Fixes drag-and-drop, collapse, and adds up/down controls
 */
(function($) {
    'use strict';

    // #region agent log
    function logDebug(message, data) {
        var logData = {
            location: 'admin-acf.js',
            message: message,
            data: data || {},
            timestamp: Date.now(),
            sessionId: 'debug-session',
            runId: 'run1',
            hypothesisId: 'A'
        };
        fetch('http://127.0.0.1:7242/ingest/09ec82c7-ebc5-4630-a97b-1172a388b9cc', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(logData)
        }).catch(function() {});
    }
    // #endregion

    /**
     * Initialize ACF Flexible Content enhancements
     */
    function initACFEnhancements() {
        // #region agent log
        logDebug('initACFEnhancements called', {
            acf_defined: typeof acf !== 'undefined',
            jquery_defined: typeof jQuery !== 'undefined',
            flexible_content_count: $('.acf-flexible-content').length,
            layout_count: $('.acf-fc-layout').length
        });
        // #endregion

        // Wait for ACF to be ready
        if (typeof acf === 'undefined') {
            // #region agent log
            logDebug('ACF not defined, returning early');
            // #endregion
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
                
                // #region agent log
                logDebug('Collapse clicked, checking title', {
                    layout_found: $layout.length > 0,
                    handle_found: $handle.length > 0,
                    title_found: $title.length > 0,
                    title_text: $title.length > 0 ? $title.text() : 'none'
                });
                // #endregion
                
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
            
            // #region agent log
            logDebug('addMoveControls called', { 
                layout_count: $layouts.length,
                flexible_content_count: $('.acf-flexible-content').length,
                value_count: $('.acf-flexible-content .value').length
            });
            // #endregion

            if ($layouts.length === 0) {
                // #region agent log
                logDebug('No layouts found, checking DOM structure', {
                    acf_fields: $('.acf-field').length,
                    acf_flexible: $('.acf-field-flexible-content').length,
                    all_acf_elements: $('[class*="acf"]').length
                });
                // #endregion
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
                    
                    // #region agent log
                    logDebug('Adding move controls to layout', {
                        has_controls_wrap: $controlsWrap.length > 0,
                        has_header: $header.length > 0,
                        has_action_wrap: $actionWrap.length > 0,
                        layout_classes: $layout.attr('class'),
                        layout_html: $layout.html().substring(0, 200)
                    });
                    // #endregion
                    
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
                    
                    // #region agent log
                    logDebug('Controls inserted', {
                        controls_in_layout: $layout.find('.acf-fc-move-controls').length > 0,
                        controls_parent: $controls.parent().attr('class'),
                        layout_contains_controls: $layout[0].contains($controls[0])
                    });
                    // #endregion
                }
            });
            
            // #region agent log
            var controlsAdded = $('.acf-fc-move-controls').length;
            logDebug('addMoveControls completed', { controls_added: controlsAdded });
            // #endregion
        }

        // Move block up - simplified approach
        $(document).on('click', '.acf-fc-move-up', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            var $button = $(this);
            
            // #region agent log
            logDebug('Move up clicked', {
                button_element: $button[0] ? 'found' : 'not found',
                button_parent: $button.parent().attr('class'),
                button_closest_layout: $button.closest('.acf-fc-layout').length,
                button_parents: $button.parents().map(function() { return $(this).attr('class'); }).get().slice(0, 5)
            });
            // #endregion
            
            if ($button.prop('disabled')) {
                // #region agent log
                logDebug('Move up button disabled');
                // #endregion
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
            
            // #region agent log
            logDebug('Layout search result', {
                layout_found: $layout.length > 0,
                layout_class: $layout.attr('class'),
                layout_data: $layout.data('layout') || $layout.attr('data-layout')
            });
            // #endregion
            
            if ($layout.length === 0) {
                // #region agent log
                logDebug('Layout not found for move up after all attempts');
                // #endregion
                return false;
            }
            
            var $container = $layout.parent();
            // Find previous sibling - use .layout selector (not .acf-fc-layout)
            var $prev = $layout.prev('.layout');
            
            // #region agent log
            logDebug('Move up action', {
                layout_found: $layout.length > 0,
                container_found: $container.length > 0,
                prev_found: $prev.length > 0,
                container_class: $container.attr('class'),
                layout_index: $container.find('.layout').index($layout),
                all_layouts_in_container: $container.find('.layout').length
            });
            // #endregion
            
            if ($prev.length) {
                // Simply move the DOM element
                $layout.insertBefore($prev);
                
                // #region agent log
                logDebug('DOM moved, triggering ACF update');
                // #endregion
                
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
                
                // #region agent log
                logDebug('Layout moved up successfully');
                // #endregion
            }
            
            return false;
        });

        // Move block down - simplified approach
        $(document).on('click', '.acf-fc-move-down', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            var $button = $(this);
            
            // #region agent log
            logDebug('Move down clicked', {
                button_element: $button[0] ? 'found' : 'not found',
                button_parent: $button.parent().attr('class'),
                button_closest_layout: $button.closest('.acf-fc-layout').length,
                button_parents: $button.parents().map(function() { return $(this).attr('class'); }).get().slice(0, 5)
            });
            // #endregion
            
            if ($button.prop('disabled')) {
                // #region agent log
                logDebug('Move down button disabled');
                // #endregion
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
            
            // #region agent log
            logDebug('Layout search result', {
                layout_found: $layout.length > 0,
                layout_class: $layout.attr('class'),
                layout_data: $layout.data('layout') || $layout.attr('data-layout')
            });
            // #endregion
            
            if ($layout.length === 0) {
                // #region agent log
                logDebug('Layout not found for move down after all attempts');
                // #endregion
                return false;
            }
            
            var $container = $layout.parent();
            // Find next sibling - use .layout selector (not .acf-fc-layout)
            var $next = $layout.next('.layout');
            
            // #region agent log
            logDebug('Move down action', {
                layout_found: $layout.length > 0,
                container_found: $container.length > 0,
                next_found: $next.length > 0,
                container_class: $container.attr('class'),
                layout_index: $container.find('.layout').index($layout),
                all_layouts_in_container: $container.find('.layout').length
            });
            // #endregion
            
            if ($next.length) {
                // Simply move the DOM element
                $layout.insertAfter($next);
                
                // #region agent log
                logDebug('DOM moved, triggering ACF update');
                // #endregion
                
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
                
                // #region agent log
                logDebug('Layout moved down successfully');
                // #endregion
            }
            
            return false;
        });

        // Ensure sortable is initialized properly
        function initSortable() {
            // Find the container that holds all layouts
            // In block editor, layouts might be directly in .acf-flexible-content or in a .value container
            var $layouts = $('.acf-fc-layout');
            if ($layouts.length === 0) {
                // #region agent log
                logDebug('No layouts found for sortable');
                // #endregion
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
            
            // #region agent log
            logDebug('initSortable called', {
                container_found: $container.length > 0,
                container_class: $container.attr('class'),
                layout_count: $container.find('.acf-fc-layout').length,
                is_sortable: $container.hasClass('ui-sortable'),
                container_html_preview: $container[0] ? $container[0].outerHTML.substring(0, 150) : 'none'
            });
            // #endregion

            if ($container.length === 0) {
                // #region agent log
                logDebug('No sortable container found');
                // #endregion
                return;
            }
            
            // Check if already sortable
            if ($container.hasClass('ui-sortable')) {
                // #region agent log
                logDebug('Container already sortable, checking if working');
                // #endregion
                // Re-enable if disabled
                if (!$container.sortable('option', 'disabled')) {
                    return;
                }
            }
            
            var $layouts = $container.find('.acf-fc-layout');
            if ($layouts.length === 0) {
                // #region agent log
                logDebug('No layouts found in container');
                // #endregion
                return;
            }
            
            // #region agent log
            logDebug('Initializing sortable on container', {
                container_class: $container.attr('class'),
                layout_count: $layouts.length
            });
            // #endregion

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
                    // #region agent log
                    logDebug('Sortable drag started');
                    // #endregion
                },
                update: function(e, ui) {
                    // Trigger ACF refresh after sort
                    var $field = $container.closest('.acf-field-flexible-content');
                    if ($field.length && typeof acf !== 'undefined') {
                        acf.do_action('refresh', $field);
                    }
                    updateButtonStates();
                    // #region agent log
                    logDebug('Sortable order updated');
                    // #endregion
                },
                stop: function(e, ui) {
                    // #region agent log
                    logDebug('Sortable drag stopped');
                    // #endregion
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
            // #region agent log
            var layoutCount = $el.find('.acf-fc-layout').length;
            logDebug('acf/setup_fields fired', {
                layout_count_in_el: layoutCount,
                total_layout_count: $('.acf-fc-layout').length
            });
            // #endregion

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
                // #region agent log
                logDebug('Initial setup timeout', {
                    layout_count: $('.acf-fc-layout').length,
                    flexible_content_count: $('.acf-flexible-content').length
                });
                // #endregion
                addMoveControls();
                initSortable();
                updateButtonStates();
            }, 500);
        });

        // Re-initialize when new layouts are added
        $(document).on('click', '.acf-button[data-name="add-layout"], .acf-fc-layout-handle', function() {
            setTimeout(function() {
                // #region agent log
                logDebug('Layout added/clicked, re-initializing', {
                    layout_count: $('.acf-fc-layout').length
                });
                // #endregion
                addMoveControls();
                initSortable();
                updateButtonStates();
            }, 300);
        });
    }

    // Initialize when DOM is ready
    $(document).ready(function() {
        // #region agent log
        var $body = $('body');
        logDebug('Document ready', {
            acf_defined: typeof acf !== 'undefined',
            jquery_ui_sortable: typeof $.fn.sortable !== 'undefined',
            flexible_content_count: $('.acf-flexible-content').length,
            layout_count: $('.acf-fc-layout').length,
            is_block_editor: typeof wp !== 'undefined' && wp.domReady !== 'undefined',
            body_classes: $body.attr('class'),
            has_metaboxes: $('#poststuff, .edit-post-meta-boxes-area').length > 0,
            all_acf_elements: $('[class*="acf"]').length
        });
        // #endregion

        // Function to try initialization
        function tryInit() {
            if (typeof acf !== 'undefined') {
                // #region agent log
                logDebug('ACF found, initializing enhancements', {
                    layout_count_now: $('.acf-fc-layout').length,
                    flexible_content_count_now: $('.acf-flexible-content').length
                });
                // #endregion
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
                    if (!tryInit()) {
                        // #region agent log
                        logDebug('Retry after delay ' + delay + 'ms', { 
                            acf_defined: typeof acf !== 'undefined',
                            layout_count: $('.acf-fc-layout').length
                        });
                        // #endregion
                    }
                }, delay);
            });
        }
    });

    // Also listen for ACF ready event
    $(document).on('acf/ready acf/setup_fields', function() {
        // #region agent log
        logDebug('ACF event fired', { event_type: arguments[0].type });
        // #endregion
        initACFEnhancements();
    });

    // For block editor, also listen to WordPress events
    if (typeof wp !== 'undefined' && wp.domReady) {
        wp.domReady(function() {
            // #region agent log
            logDebug('WordPress domReady fired');
            // #endregion
            setTimeout(function() {
                if (typeof acf !== 'undefined') {
                    initACFEnhancements();
                }
            }, 500);
        });
    }

})(jQuery);
