<?php
/**
 * Custom Navigation Walker Classes
 *
 * @package GLOCEPS
 */

/**
 * Custom walker for primary navigation with dropdown support
 */
class GLOCEPS_Primary_Nav_Walker extends Walker_Nav_Menu {
    
    /**
     * Start of an element output.
     */
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'nav__item';
        
        // Check if item has children
        if ( in_array( 'menu-item-has-children', $classes ) ) {
            $classes[] = 'nav__item--has-dropdown';
        }
        
        if ( in_array( 'current-menu-item', $classes ) || in_array( 'current-menu-ancestor', $classes ) ) {
            $classes[] = 'nav__item--active';
        }
        
        $class_names = join( ' ', array_filter( $classes ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
        
        $output .= '<li' . $class_names . '>';
        
        $atts = array();
        $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts['target'] = ! empty( $item->target ) ? $item->target : '';
        $atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
        $atts['href']   = ! empty( $item->url ) ? $item->url : '';
        $atts['class']  = 'nav__link';
        
        // Add rel="noopener noreferrer" for security when target="_blank"
        if ( $atts['target'] === '_blank' && empty( $atts['rel'] ) ) {
            $atts['rel'] = 'noopener noreferrer';
        }
        
        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
        
        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }
        
        $title = apply_filters( 'the_title', $item->title, $item->ID );
        
        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . $title . $args->link_after;
        
        // Add dropdown arrow for parent items
        if ( in_array( 'menu-item-has-children', (array) $item->classes ) && $depth === 0 ) {
            $item_output .= '<svg class="nav__arrow" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>';
        }
        
        $item_output .= '</a>';
        $item_output .= $args->after;
        
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
    
    /**
     * Start of sublevel output.
     */
    public function start_lvl( &$output, $depth = 0, $args = null ) {
        $output .= '<div class="nav__dropdown"><ul class="nav__dropdown-list">';
    }
    
    /**
     * End of sublevel output.
     */
    public function end_lvl( &$output, $depth = 0, $args = null ) {
        $output .= '</ul></div>';
    }
}

/**
 * Custom walker for mobile navigation
 */
class GLOCEPS_Mobile_Nav_Walker extends Walker_Nav_Menu {
    
    /**
     * Start of an element output.
     */
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'mobile-menu__item';
        
        if ( in_array( 'menu-item-has-children', $classes ) ) {
            $classes[] = 'mobile-menu__item--has-children';
        }
        
        if ( in_array( 'current-menu-item', $classes ) ) {
            $classes[] = 'mobile-menu__item--active';
        }
        
        $class_names = join( ' ', array_filter( $classes ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
        
        $output .= '<li' . $class_names . '>';
        
        $atts = array();
        $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts['target'] = ! empty( $item->target ) ? $item->target : '';
        $atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
        $atts['href']   = ! empty( $item->url ) ? $item->url : '';
        $atts['class']  = 'mobile-menu__link';
        
        // Add rel="noopener noreferrer" for security when target="_blank"
        if ( $atts['target'] === '_blank' && empty( $atts['rel'] ) ) {
            $atts['rel'] = 'noopener noreferrer';
        }
        
        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
        
        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }
        
        $title = apply_filters( 'the_title', $item->title, $item->ID );
        
        $item_output = $args->before;
        
        if ( in_array( 'menu-item-has-children', (array) $item->classes ) && $depth === 0 ) {
            $item_output .= '<div class="mobile-menu__parent">';
            $item_output .= '<a' . $attributes . ' class="mobile-menu__link">' . $args->link_before . $title . $args->link_after . '</a>';
            $item_output .= '<button class="mobile-menu__toggle" aria-label="' . esc_attr__( 'Toggle submenu', 'gloceps' ) . '" aria-expanded="false">';
            $item_output .= '<svg class="mobile-menu__chevron" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>';
            $item_output .= '</button>';
            $item_output .= '</div>';
        } else {
            $item_output .= '<a' . $attributes . '>' . $args->link_before . $title . $args->link_after . '</a>';
        }
        
        $item_output .= $args->after;
        
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
    
    /**
     * Start of sublevel output.
     */
    public function start_lvl( &$output, $depth = 0, $args = null ) {
        $output .= '<ul class="mobile-menu__submenu">';
    }
    
    /**
     * End of sublevel output.
     */
    public function end_lvl( &$output, $depth = 0, $args = null ) {
        $output .= '</ul>';
    }
}

