/**
 * GLOCEPS - Main JavaScript
 * Premium interactions and animations
 */

(function() {
  'use strict';

  // ============================================
  // Header Scroll Effect
  // ============================================
  const header = document.getElementById('header');
  
  if (header) {
    let lastScroll = 0;
    
    const handleScroll = () => {
      const currentScroll = window.scrollY;
      
      if (currentScroll > 50) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
      
      lastScroll = currentScroll;
    };
    
    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();
  }

  // ============================================
  // Search Button Functionality
  // ============================================
  const searchButton = document.querySelector('.header__search');
  if (searchButton) {
    searchButton.addEventListener('click', function(e) {
      e.preventDefault();
      // Redirect to search page
      const searchUrl = window.location.origin + '/?s=';
      window.location.href = searchUrl;
    });
  }

  // ============================================
  // Search Page Functionality
  // ============================================
  // Auto-submit filter form when checkboxes change
  const filterCheckboxes = document.querySelectorAll('.search-filter-checkbox');
  filterCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', function() {
      // Get the form
      const form = document.getElementById('searchFiltersForm');
      if (form) {
        // Get search query from main search input
        const searchInput = document.querySelector('.search-page__input');
        if (searchInput && searchInput.value) {
          const hiddenInput = form.querySelector('input[name="s"]');
          if (!hiddenInput) {
            const sInput = document.createElement('input');
            sInput.type = 'hidden';
            sInput.name = 's';
            sInput.value = searchInput.value;
            form.appendChild(sInput);
          } else {
            hiddenInput.value = searchInput.value;
          }
        }
        form.submit();
      }
    });
  });

  // Toggle filter dropdown
  const filterToggle = document.querySelector('.search-page__filter-toggle');
  if (filterToggle) {
    filterToggle.addEventListener('click', function(e) {
      e.preventDefault();
      const isExpanded = this.getAttribute('aria-expanded') === 'true';
      this.setAttribute('aria-expanded', !isExpanded ? 'true' : 'false');
      const options = this.nextElementSibling;
      if (options) {
        options.classList.toggle('search-page__filter-options--open');
      }
    });
  }

  // ============================================
  // Intersection Observer for Reveal Animations
  // ============================================
  const revealElements = document.querySelectorAll('.reveal');
  
  if (revealElements.length > 0) {
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -80px 0px'
    };

    const revealObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          revealObserver.unobserve(entry.target);
        }
      });
    }, observerOptions);

    revealElements.forEach(el => {
      revealObserver.observe(el);
    });
  }

  // ============================================
  // Smooth Scroll for Anchor Links
  // ============================================
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      const targetId = this.getAttribute('href');
      
      if (targetId === '#') return;
      
      const targetElement = document.querySelector(targetId);
      
      if (targetElement) {
        e.preventDefault();
        
        const headerHeight = header ? header.offsetHeight : 0;
        const targetPosition = targetElement.getBoundingClientRect().top + window.scrollY - headerHeight - 20;
        
        window.scrollTo({
          top: targetPosition,
          behavior: 'smooth'
        });
      }
    });
  });

  // ============================================
  // Filter Tag Interactions (Store Page)
  // ============================================
  const filterTags = document.querySelectorAll('.filter-tag');
  
  filterTags.forEach(tag => {
    tag.addEventListener('click', function() {
      // Remove active class from all tags
      filterTags.forEach(t => t.classList.remove('filter-tag--active'));
      // Add to clicked tag
      this.classList.add('filter-tag--active');
      
      // Animate products
      const productCards = document.querySelectorAll('.product-card');
      productCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
          card.style.transition = 'all 0.4s cubic-bezier(0.16, 1, 0.3, 1)';
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }, index * 50);
      });
    });
  });

  // ============================================
  // Add to Cart Animation
  // ============================================
  document.querySelectorAll('.product-card .btn--accent, .pub-featured .btn--primary').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      
      const originalHTML = this.innerHTML;
      this.innerHTML = `
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        Added!
      `;
      this.style.background = 'var(--color-success)';
      this.disabled = true;
      
      setTimeout(() => {
        this.innerHTML = originalHTML;
        this.style.background = '';
        this.disabled = false;
      }, 2000);
    });
  });

  // ============================================
  // Mobile Navigation
  // ============================================
  const navToggle = document.getElementById('navToggle');
  const mobileMenu = document.querySelector('.mobile-menu');
  const navOverlay = document.querySelector('.nav-overlay');
  const mobileMenuClose = document.querySelector('.mobile-menu__close');
  
  const closeMobileMenu = () => {
    document.body.classList.remove('menu-open');
    if (mobileMenu) mobileMenu.classList.remove('active');
    if (navOverlay) navOverlay.classList.remove('active');
  };
  
  const openMobileMenu = () => {
    document.body.classList.add('menu-open');
    if (mobileMenu) mobileMenu.classList.add('active');
    if (navOverlay) navOverlay.classList.add('active');
  };
  
  if (navToggle) {
    navToggle.addEventListener('click', () => {
      if (mobileMenu && mobileMenu.classList.contains('active')) {
        closeMobileMenu();
      } else {
        openMobileMenu();
      }
    });
  }
  
  if (navOverlay) {
    navOverlay.addEventListener('click', closeMobileMenu);
  }
  
  if (mobileMenuClose) {
    mobileMenuClose.addEventListener('click', closeMobileMenu);
  }
  
  // Close mobile menu when clicking a link or CTA button
  document.querySelectorAll('.mobile-menu__link, .mobile-menu__sublink, .btn--mobile-menu').forEach(link => {
    link.addEventListener('click', closeMobileMenu);
  });
  
  // Mobile menu submenu toggles
  document.querySelectorAll('.mobile-menu__toggle').forEach(toggle => {
    toggle.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      
      const isExpanded = this.getAttribute('aria-expanded') === 'true';
      // Find the parent li, then find the submenu within it
      const parentItem = this.closest('.mobile-menu__item');
      const submenu = parentItem ? parentItem.querySelector('.mobile-menu__submenu') : null;
      
      // Close all other submenus
      document.querySelectorAll('.mobile-menu__toggle').forEach(otherToggle => {
        if (otherToggle !== this) {
          otherToggle.setAttribute('aria-expanded', 'false');
          const otherParentItem = otherToggle.closest('.mobile-menu__item');
          const otherSubmenu = otherParentItem ? otherParentItem.querySelector('.mobile-menu__submenu') : null;
          if (otherSubmenu) otherSubmenu.classList.remove('active');
        }
      });
      
      // Toggle this submenu
      this.setAttribute('aria-expanded', !isExpanded ? 'true' : 'false');
      if (submenu) {
        submenu.classList.toggle('active', !isExpanded);
      }
    });
  });

  // ============================================
  // Parallax Effect for Hero
  // ============================================
  const heroSection = document.querySelector('.hero');
  const heroBg = document.querySelector('.hero__bg-image');
  
  if (heroSection && heroBg) {
    window.addEventListener('scroll', () => {
      const scrolled = window.scrollY;
      const rate = scrolled * 0.3;
      
      if (scrolled < window.innerHeight) {
        heroBg.style.transform = `translateY(${rate}px) scale(1.1)`;
      }
    }, { passive: true });
  }

  // ============================================
  // Cursor Effect on Cards (Desktop only)
  // ============================================
  if (window.matchMedia('(pointer: fine)').matches) {
    const cards = document.querySelectorAll('.pillar-card, .product-card');
    
    cards.forEach(card => {
      card.addEventListener('mousemove', (e) => {
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;
        
        // Further reduced tilt for subtler, less "noisy" effect (was /29, now /50)
        const rotateX = (y - centerY) / 50;
        const rotateY = (centerX - x) / 50;
        
        // Reduced scale and increased perspective for gentler effect
        card.style.transform = `perspective(1200px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.005, 1.005, 1.005)`;
      });
      
      card.addEventListener('mouseleave', () => {
        card.style.transform = '';
      });
    });
  }

  // ============================================
  // Keyboard Navigation Support
  // ============================================
  document.addEventListener('keydown', (e) => {
    // ESC key closes mobile menu
    if (e.key === 'Escape') {
      closeMobileMenu();
    }
  });

  // ============================================
  // Focus Management
  // ============================================
  document.body.addEventListener('mousedown', () => {
    document.body.classList.add('using-mouse');
  });

  document.body.addEventListener('keydown', (e) => {
    if (e.key === 'Tab') {
      document.body.classList.remove('using-mouse');
    }
  });

  // ============================================
  // Counter Animation for Stats
  // ============================================
  const animateCounters = () => {
    const counters = document.querySelectorAll('.stat-item__value');
    
    counters.forEach(counter => {
      const text = counter.textContent;
      const match = text.match(/(\d+)/);
      
      if (match && !counter.classList.contains('counted')) {
        const target = parseInt(match[0]);
        const suffix = text.replace(match[0], '');
        let current = 0;
        const increment = target / 50;
        const duration = 1500;
        const stepTime = duration / 50;
        
        counter.classList.add('counted');
        
        const timer = setInterval(() => {
          current += increment;
          if (current >= target) {
            counter.innerHTML = target + suffix;
            clearInterval(timer);
          } else {
            counter.innerHTML = Math.floor(current) + suffix;
          }
        }, stepTime);
      }
    });
  };

  // Trigger counter animation when stats section is visible
  const statsSection = document.querySelector('.stats-section');
  if (statsSection) {
    const statsObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          animateCounters();
          statsObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.3 });
    
    statsObserver.observe(statsSection);
  }

  // ============================================
  // Image Loading Enhancement
  // ============================================
  document.querySelectorAll('img').forEach(img => {
    img.addEventListener('load', function() {
      this.classList.add('loaded');
    });
    
    if (img.complete) {
      img.classList.add('loaded');
    }
  });

  // ============================================
  // Print Styles Warning
  // ============================================
  window.addEventListener('beforeprint', () => {
    document.querySelectorAll('.reveal').forEach(el => {
      el.classList.add('visible');
    });
  });

  // ============================================
  // Cart Slide-in Panel
  // ============================================
  let cartSlide = document.getElementById('cart-slide');
  let cartToggle = document.getElementById('cart-toggle');
  const mobileCartToggle = document.getElementById('mobile-cart-toggle');
  
  // Function to initialize cart slide
  function initCartSlide() {
    cartSlide = document.getElementById('cart-slide');
    cartToggle = document.getElementById('cart-toggle');
    
    if (!cartSlide) return;
    
    const cartSlideClose = cartSlide.querySelector('.cart-slide__close');
    const cartSlideOverlay = cartSlide.querySelector('.cart-slide__overlay');
    
    // Open cart slide
    const openCartSlide = () => {
      if (!cartSlide) return;
      cartSlide.setAttribute('aria-hidden', 'false');
      cartSlide.classList.add('is-open');
      document.body.style.overflow = 'hidden';
      if (cartToggle) cartToggle.setAttribute('aria-expanded', 'true');
      if (mobileCartToggle) mobileCartToggle.setAttribute('aria-expanded', 'true');
      // Close mobile menu if open
      document.body.classList.remove('menu-open');
      const mobileMenu = document.querySelector('.mobile-menu');
      if (mobileMenu) mobileMenu.classList.remove('active');
      const navOverlay = document.querySelector('.nav-overlay');
      if (navOverlay) navOverlay.classList.remove('active');
    };
    
    // Close cart slide
    const closeCartSlide = () => {
      if (!cartSlide) return;
      cartSlide.setAttribute('aria-hidden', 'true');
      cartSlide.classList.remove('is-open');
      document.body.style.overflow = '';
      if (cartToggle) cartToggle.setAttribute('aria-expanded', 'false');
      if (mobileCartToggle) mobileCartToggle.setAttribute('aria-expanded', 'false');
    };
    
    // Toggle cart slide (desktop)
    if (cartToggle) {
      // Remove old listeners
      const newToggle = cartToggle.cloneNode(true);
      cartToggle.parentNode.replaceChild(newToggle, cartToggle);
      cartToggle = newToggle;
      
      cartToggle.addEventListener('click', (e) => {
        e.preventDefault();
        if (cartSlide.classList.contains('is-open')) {
          closeCartSlide();
        } else {
          openCartSlide();
        }
      });
    }
    
    // Toggle cart slide (mobile)
    if (mobileCartToggle) {
      mobileCartToggle.addEventListener('click', (e) => {
        e.preventDefault();
        if (cartSlide.classList.contains('is-open')) {
          closeCartSlide();
        } else {
          openCartSlide();
        }
      });
    }
    
    // Close on overlay click
    if (cartSlideOverlay) {
      const newOverlay = cartSlideOverlay.cloneNode(true);
      cartSlideOverlay.parentNode.replaceChild(newOverlay, cartSlideOverlay);
      newOverlay.addEventListener('click', closeCartSlide);
    }
    
    // Close on close button
    if (cartSlideClose) {
      const newClose = cartSlideClose.cloneNode(true);
      cartSlideClose.parentNode.replaceChild(newClose, cartSlideClose);
      newClose.addEventListener('click', closeCartSlide);
    }
    
    // Close on Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && cartSlide && cartSlide.classList.contains('is-open')) {
        closeCartSlide();
      }
    });
    
    return { openCartSlide, closeCartSlide };
  }
  
  // Initialize cart slide
  let cartSlideFunctions = initCartSlide();
  let openCartSlide = cartSlideFunctions?.openCartSlide;
  let closeCartSlide = cartSlideFunctions?.closeCartSlide;
  
  // Remove item from cart (AJAX) - use event delegation on document
  document.addEventListener('click', function(e) {
    const removeButton = e.target.closest('.cart-slide__item-remove');
    if (!removeButton) return;
    
    e.preventDefault();
    const cartItemKey = removeButton.getAttribute('data-cart-item-key');
    if (!cartItemKey) return;
    
    // Show loading state
    removeButton.disabled = true;
    removeButton.innerHTML = '<svg class="spinner" width="20" height="20" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" opacity="0.25"/><path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round"/></svg>';
    
    // AJAX remove using WooCommerce endpoint
    const wcAjaxUrl = typeof wc_add_to_cart_params !== 'undefined' 
      ? wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'remove_from_cart')
      : '/?wc-ajax=remove_from_cart';
    
    fetch(wcAjaxUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({
        cart_item_key: cartItemKey
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data && data.fragments) {
        // Update cart fragments
        if (typeof jQuery !== 'undefined') {
          jQuery.each(data.fragments, function(key, value) {
            if (key === '#cart-slide') {
              jQuery(key).replaceWith(value);
              // Re-initialize cart slide
              setTimeout(() => {
                cartSlideFunctions = initCartSlide();
                openCartSlide = cartSlideFunctions?.openCartSlide;
                closeCartSlide = cartSlideFunctions?.closeCartSlide;
              }, 100);
            } else {
              jQuery(key).replaceWith(value);
            }
          });
          
          // Trigger removed_from_cart event
          jQuery(document.body).trigger('removed_from_cart', [data.fragments, data.cart_hash, jQuery(removeButton)]);
        }
        
        // If cart is empty, close slide
        const cartCount = jQuery('.cart-slide__item').length || 0;
        if (cartCount === 0 && closeCartSlide) {
          closeCartSlide();
          if (window.location.pathname.includes('/cart')) {
            window.location.reload();
          }
        }
      } else {
        // Fallback: reload page
        window.location.reload();
      }
    })
    .catch(error => {
      console.error('Error removing item:', error);
      removeButton.disabled = false;
      removeButton.innerHTML = '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
    });
  });

  // ============================================
  // Featured Publication: Direct AJAX handler
  // ============================================
  // Handle clicks in capture phase to intercept before WooCommerce or form submission
  if (typeof jQuery !== 'undefined') {
    // Add capture-phase listener to catch events before they're stopped
    document.addEventListener('click', function(e) {
      const target = e.target;
      const button = target && target.closest ? target.closest('.pub-featured__add-to-cart .add_to_cart_button.ajax_add_to_cart') : null;
      
      if (button) {
        // Prevent default and stop propagation immediately
        e.preventDefault();
        e.stopImmediatePropagation();
        
        const productId = button.getAttribute('data-product_id');
        
        if (!productId) {
          return;
        }
        
        const $button = jQuery(button);
        
        // Add loading state
        $button.addClass('loading').prop('disabled', true);
        
        // Get AJAX URL
        let ajaxUrl = '/?wc-ajax=add_to_cart';
        if (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.wc_ajax_url) {
          ajaxUrl = wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart');
        }
        
        // Make AJAX call
        jQuery.ajax({
          type: 'POST',
          url: ajaxUrl,
          data: {
            product_id: productId,
            quantity: 1
          },
          success: function(response) {
            if (response.error && response.product_url) {
              window.location = response.product_url;
              return;
            }
            
            // Update fragments
            if (response.fragments) {
              jQuery.each(response.fragments, function(key, value) {
                jQuery(key).replaceWith(value);
              });
            }
            
            // Trigger added_to_cart event (this will show notification and update cart)
            jQuery(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
            
            // Update button state
            $button.removeClass('loading').addClass('added').prop('disabled', false);
            setTimeout(function() {
              $button.removeClass('added');
            }, 2000);
          },
          error: function(xhr, status, error) {
            $button.removeClass('loading').prop('disabled', false);
          }
        });
      }
    }, true); // Use capture phase
    
    // Prevent form submission as backup
    jQuery(document).on('submit', '.pub-featured__add-to-cart', function(e) {
      e.preventDefault();
      e.stopImmediatePropagation();
      return false;
    });
  }

  // ============================================
  // Add to Cart Notification
  // ============================================
  const addToCartNotification = document.getElementById('add-to-cart-notification');
  
  if (addToCartNotification) {
    const notificationContent = addToCartNotification.querySelector('.add-to-cart-notification__content');
    const notificationClose = addToCartNotification.querySelector('.add-to-cart-notification__close');
    const notificationActions = addToCartNotification.querySelectorAll('.add-to-cart-notification__action--close');
    const productNameSpan = addToCartNotification.querySelector('.add-to-cart-notification__product-name');
    
    // Show notification
    const showNotification = (productName = '') => {
      if (productNameSpan && productName) {
        productNameSpan.textContent = productName;
      }
      addToCartNotification.setAttribute('aria-hidden', 'false');
      addToCartNotification.classList.add('is-visible');
      
      // Auto-hide after 5 seconds
      setTimeout(() => {
        hideNotification();
      }, 5000);
    };
    
    // Hide notification
    const hideNotification = () => {
      addToCartNotification.setAttribute('aria-hidden', 'true');
      addToCartNotification.classList.remove('is-visible');
    };
    
    // Close button
    if (notificationClose) {
      notificationClose.addEventListener('click', hideNotification);
    }
    
    // Close action buttons
    notificationActions.forEach(button => {
      button.addEventListener('click', hideNotification);
    });
    
    // Listen for WooCommerce add to cart events (jQuery)
    if (typeof jQuery !== 'undefined') {
      jQuery(document.body).on('added_to_cart', function(event, fragments, cart_hash, $button) {
        // Update cart fragments
        if (fragments) {
          jQuery.each(fragments, function(key, value) {
            if (key === '#cart-slide') {
              jQuery(key).replaceWith(value);
              // Re-initialize cart slide after fragment update
              setTimeout(() => {
                cartSlideFunctions = initCartSlide();
                openCartSlide = cartSlideFunctions?.openCartSlide;
                closeCartSlide = cartSlideFunctions?.closeCartSlide;
              }, 100);
            } else if (key === '.header__cart-count' || key === '.mobile-menu__cart-count') {
              // Update cart count badges
              const target = jQuery(key);
              if (value && value.trim() !== '') {
                if (target.length) {
                  target.replaceWith(value);
                } else {
                  // If element doesn't exist, find parent and append
                  if (key === '.header__cart-count') {
                    jQuery('#cart-toggle').append(value);
                  } else if (key === '.mobile-menu__cart-count') {
                    jQuery('#mobile-cart-toggle').append(value);
                  }
                }
              } else {
                // Remove count if cart is empty
                target.remove();
              }
            } else if (key === '#cart-toggle') {
              // Replace entire cart toggle button
              jQuery(key).replaceWith(value);
              // Re-initialize cart slide listeners
              setTimeout(() => {
                cartSlideFunctions = initCartSlide();
                openCartSlide = cartSlideFunctions?.openCartSlide;
                closeCartSlide = cartSlideFunctions?.closeCartSlide;
              }, 100);
            } else if (key !== 'gloceps_product_name') {
              jQuery(key).replaceWith(value);
            }
          });
        }
        
        // Try to get product name from button context or fragments
        let productName = '';
        
        if (fragments && fragments.gloceps_product_name) {
          productName = fragments.gloceps_product_name;
        } else if ($button && $button.length) {
          // Try to find product name near the button
          productName = $button.closest('.publication-card, .product, article, .publication-header, .publication-sidebar, .pub-featured').find('.publication-card__title, .product-title, h1, h2, h3, .publication-header__title, .pub-featured__title').first().text().trim();
        }
        
        if (productName) {
          showNotification(productName);
        }
        
        // Open cart slide after a short delay to ensure it's initialized
        setTimeout(() => {
          const currentCartSlide = document.getElementById('cart-slide');
          if (currentCartSlide && !currentCartSlide.classList.contains('is-open')) {
            cartSlideFunctions = initCartSlide();
            if (cartSlideFunctions && cartSlideFunctions.openCartSlide) {
              cartSlideFunctions.openCartSlide();
            }
          }
        }, 150);
      });
    }
    
    // Also listen for custom event (fallback)
    document.body.addEventListener('added_to_cart', function(e) {
      const productName = e.detail?.product_name || '';
      if (productName) {
        showNotification(productName);
      }
    });
  }

  // ============================================
  // WooCommerce Notice Dismiss
  // ============================================
  document.addEventListener('click', function(e) {
    const dismissBtn = e.target.closest('.woocommerce-notice-dismiss');
    if (dismissBtn) {
      e.preventDefault();
      const notice = dismissBtn.closest('.woocommerce-message, .woocommerce-error, .woocommerce-info');
      if (notice) {
        notice.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        notice.style.opacity = '0';
        notice.style.transform = 'translateY(-10px)';
        setTimeout(() => {
          notice.remove();
        }, 300);
      }
    }
  });

  // ============================================
  // Checkout Coupon Form
  // ============================================
  // Coupon functionality is controlled via Theme Settings > General Settings
  // WooCommerce handles all coupon functionality when enabled

  // ============================================
  // Checkout Payment Method Selection
  // ============================================
  
  // Use WooCommerce's native payment method handling, but add our card styling
  function updatePaymentMethodCardStyling() {
    // Update card selected state based on checked radio
    document.querySelectorAll('.payment-method-card').forEach(card => {
      card.classList.remove('payment-method-card--selected');
    });
    
    const checkedRadio = document.querySelector('#payment input[name="payment_method"]:checked');
    if (checkedRadio) {
      const li = checkedRadio.closest('.wc_payment_method');
      const card = li?.querySelector('.payment-method-card');
      if (card) {
        card.classList.add('payment-method-card--selected');
      }
    }
  }
  
  // Listen to WooCommerce's payment method change events
  jQuery(document.body).on('payment_method_selected', updatePaymentMethodCardStyling);
  jQuery(document.body).on('updated_checkout', updatePaymentMethodCardStyling);
  
  // Also listen to direct radio changes
  document.querySelectorAll('#payment input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', updatePaymentMethodCardStyling);
  });
  
  // Initialize on page load
  if (typeof jQuery !== 'undefined') {
    jQuery(document).ready(function() {
      updatePaymentMethodCardStyling();
    });
  } else {
    updatePaymentMethodCardStyling();
  }

  // ============================================
  // Checkout Submit Button - Update with Total
  // ============================================
  function updateCheckoutSubmitButton() {
    const submitButton = document.querySelector('#place_order');
    const submitText = document.querySelector('#place_order .checkout-submit__text');
    
    if (!submitButton || !submitText) {
      return;
    }
    
    // Get total from order review section
    let total = '';
    
    // Try multiple selectors to find the total
    const totalSelectors = [
      '.checkout-summary__total-kes',
      '.order-total .woocommerce-Price-amount',
      '.order-total .amount',
      '.woocommerce-Price-amount.amount'
    ];
    
    for (let selector of totalSelectors) {
      const orderTotalElement = document.querySelector(selector);
      if (orderTotalElement) {
        const totalText = orderTotalElement.textContent || orderTotalElement.innerText;
        // Remove currency symbols and extract numbers
        const matches = totalText.match(/[\d,]+\.?\d*/);
        if (matches) {
          total = matches[0].replace(/,/g, '');
          // Format with thousand separators
          total = parseFloat(total).toLocaleString('en-US', { maximumFractionDigits: 0 });
          break;
        }
      }
    }
    
    // Fallback: try jQuery if available
    if (!total && typeof jQuery !== 'undefined') {
      const $totalEl = jQuery('.checkout-summary__total-kes, .order-total .amount').first();
      if ($totalEl.length) {
        const totalText = $totalEl.text();
        const matches = totalText.match(/[\d,]+\.?\d*/);
        if (matches) {
          total = matches[0].replace(/,/g, '');
          total = parseFloat(total).toLocaleString('en-US', { maximumFractionDigits: 0 });
        }
      }
    }
    
    if (total) {
      submitText.textContent = 'Pay KES ' + total;
    }
  }
  
  // Override WooCommerce's button text update to preserve our custom format
  if (typeof jQuery !== 'undefined') {
    // Watch for DOM changes and restore our text immediately
    if (typeof MutationObserver !== 'undefined') {
      const observer = new MutationObserver(function(mutations) {
        const submitButton = document.querySelector('#place_order');
        const submitText = submitButton ? submitButton.querySelector('.checkout-submit__text') : null;
        
        if (submitText) {
          // If text doesn't contain "KES", WooCommerce overwrote it - restore immediately
          if (!submitText.textContent.includes('KES')) {
            updateCheckoutSubmitButton();
          }
        } else if (submitButton) {
          // Span was removed, recreate it immediately
          const span = document.createElement('span');
          span.className = 'checkout-submit__text';
          submitButton.appendChild(span);
          updateCheckoutSubmitButton();
        }
      });
      
      // Start observing once button exists
      function startObserving() {
        const submitButton = document.querySelector('#place_order');
        if (submitButton) {
          observer.observe(submitButton, {
            childList: true,
            subtree: true,
            characterData: true
          });
        } else {
          // Retry if button doesn't exist yet
          setTimeout(startObserving, 100);
        }
      }
      startObserving();
    }
    
    // Also hook into WooCommerce events
    jQuery(document.body).on('payment_method_selected updated_checkout', function() {
      setTimeout(updateCheckoutSubmitButton, 50);
    });
    
    // Initial load
    jQuery(document).ready(function() {
      setTimeout(updateCheckoutSubmitButton, 100);
      // Also set up a periodic check as fallback
      setInterval(function() {
        const submitText = document.querySelector('#place_order .checkout-submit__text');
        if (submitText && !submitText.textContent.includes('KES')) {
          updateCheckoutSubmitButton();
        }
      }, 500);
    });
  }

  // ============================================
  // Cart Page Item Removal
  // ============================================
  const cartRemoveButtons = document.querySelectorAll('.cart-item__remove[data-cart-item-key]');
  
  cartRemoveButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      const cartItemKey = this.getAttribute('data-cart-item-key');
      if (!cartItemKey) return;
      
      // Show loading state
      this.disabled = true;
      this.innerHTML = '<svg class="spinner" width="20" height="20" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" opacity="0.25"/><path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round"/></svg>';
      
      // AJAX remove from cart page using WooCommerce endpoint
      const wcAjaxUrl = typeof wc_add_to_cart_params !== 'undefined' 
        ? wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'remove_from_cart')
        : '/?wc-ajax=remove_from_cart';
      
      fetch(wcAjaxUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
          cart_item_key: cartItemKey
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data && data.fragments) {
          // Update cart fragments
          if (typeof jQuery !== 'undefined') {
            jQuery.each(data.fragments, function(key, value) {
              jQuery(key).replaceWith(value);
            });
            
            // Trigger removed_from_cart event
            jQuery(document.body).trigger('removed_from_cart', [data.fragments, data.cart_hash, jQuery(this)]);
          }
          
          // Reload page to update cart totals and layout
          window.location.reload();
        } else {
          // Fallback: reload page
          window.location.reload();
        }
      })
      .catch(error => {
        console.error('Error removing item:', error);
        this.disabled = false;
      });
    });
  });

  // ============================================
  // Download Receipt as PDF
  // ============================================
  const downloadReceiptBtn = document.getElementById('download-receipt-pdf');
  
  if (downloadReceiptBtn) {
    downloadReceiptBtn.addEventListener('click', function() {
      // Use browser's print functionality which allows saving as PDF
      window.print();
    });
  }

  // ============================================
  // Resend Publications Form
  // ============================================
  const resendForm = document.getElementById('resend-publications-form');
  const resendMessages = document.getElementById('resend-form-messages');
  const resendSubmitBtn = document.getElementById('resend-submit-btn');
  
  if (resendForm) {
    resendForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      
      // Clear previous messages
      resendMessages.innerHTML = '';
      resendMessages.className = 'form-messages';
      
      // Disable submit button
      resendSubmitBtn.disabled = true;
      const originalButtonHTML = resendSubmitBtn.innerHTML;
      resendSubmitBtn.innerHTML = '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 8px;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>Sending...';
      
      // Get form data
      const formData = new FormData(resendForm);
      
      // Add AJAX URL
      formData.append('action', 'gloceps_resend_publications');
      
      // Determine AJAX URL - use localized script or fallback
      const ajaxUrl = (typeof glocepsAjax !== 'undefined' && glocepsAjax.ajaxurl) 
        ? glocepsAjax.ajaxurl 
        : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php');
      
      
      // Make AJAX request
      fetch(ajaxUrl, {
        method: 'POST',
        body: formData,
      })
      .then(response => {
        return response.json();
      })
      .then(data => {
        if (data.success) {
          resendMessages.className = 'form-messages form-messages--success';
          resendMessages.innerHTML = '<p>' + data.data.message + '</p>';
          resendForm.reset();
        } else {
          resendMessages.className = 'form-messages form-messages--error';
          resendMessages.innerHTML = '<p>' + (data.data?.message || 'An error occurred. Please try again.') + '</p>';
        }
      })
      .catch(error => {
        resendMessages.className = 'form-messages form-messages--error';
        resendMessages.innerHTML = '<p>An error occurred. Please try again.</p>';
      })
      .finally(() => {
        resendSubmitBtn.disabled = false;
        resendSubmitBtn.innerHTML = originalButtonHTML || '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 8px;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>Resend Publications';
      });
    });
  }
  
  // FAQ Accordion
  const faqItems = document.querySelectorAll('.resend-publications__faq-item');
  faqItems.forEach(item => {
    const question = item.querySelector('.resend-publications__faq-question');
    const answer = item.querySelector('.resend-publications__faq-answer');
    
    if (question && answer) {
      question.addEventListener('click', function() {
        const isExpanded = item.getAttribute('aria-expanded') === 'true';
        
        // Close all other FAQs
        faqItems.forEach(i => {
          if (i !== item) {
            i.setAttribute('aria-expanded', 'false');
            const otherAnswer = i.querySelector('.resend-publications__faq-answer');
            if (otherAnswer) {
              otherAnswer.style.display = 'none';
            }
          }
        });
        
        // Toggle current FAQ
        item.setAttribute('aria-expanded', !isExpanded);
        answer.style.display = isExpanded ? 'none' : 'block';
      });
    }
  });

  // ============================================
  // Team Bio Modal
  // ============================================
  const bioModal = document.getElementById('bioModal');
  
  if (bioModal) {
    const bioLinks = document.querySelectorAll('.team-card__bio-link');
    const closeBtn = bioModal.querySelector('.bio-modal__close');
    const overlay = bioModal.querySelector('.bio-modal__overlay');

    function openBioModal(card) {
      const name = card.dataset.name;
      const title = card.dataset.title || name;
      const role = card.dataset.role;
      const jobTitle = card.dataset.jobTitle || '';
      const bioHtml = card.dataset.bioHtml || card.dataset.bio;
      const img = card.querySelector('.team-card__image');

      const bioNameEl = document.getElementById('bioName');
      const bioRoleEl = document.getElementById('bioRole');
      const bioJobTitleEl = document.getElementById('bioJobTitle');
      const bioBioEl = document.getElementById('bioBio');
      const bioImageEl = document.getElementById('bioImage');

      if (bioNameEl) bioNameEl.textContent = title;
      if (bioRoleEl) bioRoleEl.textContent = role || '';
      
      if (bioJobTitleEl) {
        if (jobTitle) {
          bioJobTitleEl.textContent = jobTitle;
          bioJobTitleEl.style.display = 'block';
        } else {
          bioJobTitleEl.style.display = 'none';
        }
      }

      if (bioBioEl) {
        // Use HTML bio if available, otherwise format plain text
        if (bioHtml && bioHtml.includes('<')) {
          bioBioEl.innerHTML = bioHtml;
        } else if (bioHtml) {
          // Split bio by double line breaks and create paragraphs
          const bioParagraphs = bioHtml.split('\n\n').filter(p => p.trim());
          bioBioEl.innerHTML = bioParagraphs.map(p => '<p>' + p.trim() + '</p>').join('');
        } else {
          bioBioEl.innerHTML = '<p>No biography available.</p>';
        }
      }

      if (bioImageEl) {
        if (img && img.tagName === 'IMG') {
          bioImageEl.innerHTML = '<img src="' + img.src + '" alt="' + name + '" />';
        } else {
          bioImageEl.innerHTML = '';
        }
      }

      bioModal.classList.add('bio-modal--open');
      document.body.style.overflow = 'hidden';
    }

    function closeBioModal() {
      bioModal.classList.remove('bio-modal--open');
      document.body.style.overflow = '';
    }

    bioLinks.forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        const card = this.closest('.team-card');
        if (card) {
          openBioModal(card);
        }
      });
    });

    if (closeBtn) {
      closeBtn.addEventListener('click', closeBioModal);
    }
    
    if (overlay) {
      overlay.addEventListener('click', closeBioModal);
    }

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && bioModal.classList.contains('bio-modal--open')) {
        closeBioModal();
      }
    });
  }

  // ============================================
  // Navigation Overflow Handler (More Menu)
  // ============================================
  function handleNavOverflow() {
    // Only run on desktop (above 1024px)
    if (window.innerWidth <= 1024) {
      const navMore = document.getElementById('navMore');
      const navMoreList = document.getElementById('navMoreList');
      const navList = document.querySelector('.nav__list');
      
      if (navMore && navMoreList && navList) {
        // Reset: clear More menu and show all items in main nav on mobile
        navMoreList.innerHTML = '';
        // Show all items in main nav
        Array.from(navList.querySelectorAll('.nav__item--hidden')).forEach(item => {
          item.classList.remove('nav__item--hidden');
        });
        navMore.style.display = 'none';
      }
      return;
    }

    const navList = document.querySelector('.nav__list');
    const navMore = document.getElementById('navMore');
    const navMoreList = document.getElementById('navMoreList');
    const navBand = document.querySelector('.nav__band');
    const headerActions = document.querySelector('.header__actions');

    if (!navList || !navMore || !navMoreList || !navBand) {
      return;
    }

    // Reset: clear More menu and show all items in main nav
    navMoreList.innerHTML = '';
    // Show all items in main nav (remove hidden class)
    Array.from(navList.querySelectorAll('.nav__item--hidden')).forEach(item => {
      item.classList.remove('nav__item--hidden');
    });

    // Hide more menu initially
    navMore.style.display = 'none';

    // Get available width for nav
    // Measure from the start of nav list to the start of header actions
    const navListRect = navList.getBoundingClientRect();
    const headerActionsRect = headerActions ? headerActions.getBoundingClientRect() : { left: navBand.getBoundingClientRect().right };
    const availableWidth = headerActionsRect.left - navListRect.left - 20; // 20px padding

    // Get all nav items (excluding hidden ones for calculations)
    const allNavItems = Array.from(navList.querySelectorAll('.nav__item'));
    const navItems = allNavItems.filter(item => !item.classList.contains('nav__item--hidden'));
    
    if (navItems.length === 0) {
      return;
    }

    // Better approach: Check if items would overflow by calculating total width
    // First, restore to nowrap to get accurate single-line measurements
    const originalFlexWrap = navList.style.flexWrap || '';
    navList.style.flexWrap = 'nowrap';
    
    // Force a reflow
    void navList.offsetHeight;
    
    // Calculate total width of all items
    let totalItemsWidth = 0;
    const itemWidths = [];
    navItems.forEach((item, index) => {
      const width = item.getBoundingClientRect().width;
      itemWidths.push(width);
      totalItemsWidth += width;
    });
    
    // Now check if items would wrap by temporarily allowing wrap
    navList.style.flexWrap = 'wrap';
    void navList.offsetHeight;
    
    // Measure if items are wrapping by checking if any item is on a second row
    let isWrapping = false;
    if (navItems.length > 0) {
      const firstItemTop = navItems[0].getBoundingClientRect().top;
      for (let i = 1; i < navItems.length; i++) {
        const itemTop = navItems[i].getBoundingClientRect().top;
        if (itemTop > firstItemTop + 5) { // 5px tolerance for rounding
          isWrapping = true;
          break;
        }
      }
    }

    // Restore original flex-wrap
    navList.style.flexWrap = originalFlexWrap || 'nowrap';

    // Only show More menu if items are actually wrapping OR would overflow
    // Use width check as primary, wrapping check as secondary
    const wouldOverflow = totalItemsWidth > availableWidth;
    if (!isWrapping && !wouldOverflow) {
      return; // Everything fits, no need for More menu
    }

    // Calculate which items fit
    // Strategy: Find how many items can fit, then move the rest
    const moreButtonWidth = 85; // Approximate width of More button
    const itemsToMove = [];
    
    // First, calculate how many items can fit WITHOUT the More button
    let totalWidth = 0;
    let itemsThatFit = 0;
    
    for (let i = 0; i < navItems.length; i++) {
      const item = navItems[i];
      const itemRect = item.getBoundingClientRect();
      const itemWidth = itemRect.width;
      
      if (totalWidth + itemWidth <= availableWidth) {
        totalWidth += itemWidth;
        itemsThatFit++;
      } else {
        break;
      }
    }
    
    // Now check if we need More button - if items fit perfectly, we don't need it
    // If we need More, recalculate accounting for More button width
    if (itemsThatFit < navItems.length) {
      // We need More button, so recalculate with More button width
      totalWidth = 0;
      itemsThatFit = 0;
      const availableWithMore = availableWidth - moreButtonWidth;
      
      for (let i = 0; i < navItems.length; i++) {
        const item = navItems[i];
        const itemRect = item.getBoundingClientRect();
        const itemWidth = itemRect.width;
        
        if (totalWidth + itemWidth <= availableWithMore) {
          totalWidth += itemWidth;
          itemsThatFit++;
        } else {
          break;
        }
      }
      
      // Mark items that don't fit (from itemsThatFit onwards)
      for (let i = itemsThatFit; i < navItems.length; i++) {
        itemsToMove.push(navItems[i]);
      }
    }

    // Handle overflow items
    if (itemsToMove.length > 0) {
      // Close all open dropdowns in More menu before clearing
      const existingItems = navMoreList.querySelectorAll('.nav__item--has-dropdown');
      existingItems.forEach(existingItem => {
        existingItem.classList.remove('nav__item--open');
        const existingTimeouts = moreMenuTimeouts.get(existingItem);
        if (existingTimeouts) {
          if (existingTimeouts.showTimeout) clearTimeout(existingTimeouts.showTimeout);
          if (existingTimeouts.hideTimeout) clearTimeout(existingTimeouts.hideTimeout);
        }
      });
      
      // Clear More menu first
      navMoreList.innerHTML = '';
      
      itemsToMove.forEach(item => {
        // Hide in main nav (but keep in DOM - this preserves dropdown functionality)
        item.classList.add('nav__item--hidden');
        
        // Clone for More menu (deep clone to preserve all structure including dropdowns)
        const clone = item.cloneNode(true);
        // Remove hidden class from clone
        clone.classList.remove('nav__item--hidden');
        // Add identifier to track which original item this came from
        clone.dataset.isClone = 'true';
        navMoreList.appendChild(clone);
      });

      // Show More menu
      navMore.style.display = 'block';
    }
  }

  // Run on load and resize
  if (window.innerWidth > 1024) {
    // Wait for fonts and images to load
    window.addEventListener('load', () => {
      setTimeout(handleNavOverflow, 100);
    });
    
    // Also try immediately if DOM is ready
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
      setTimeout(handleNavOverflow, 100);
    }
  }

  let resizeTimeout;
  window.addEventListener('resize', () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
      handleNavOverflow();
    }, 150);
  });

  // Handle More menu toggle
  const navMoreToggle = document.querySelector('.nav__more-toggle');
  if (navMoreToggle) {
    navMoreToggle.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      
      const isExpanded = this.getAttribute('aria-expanded') === 'true';
      const dropdown = this.nextElementSibling;
      
      // Close all other dropdowns
      document.querySelectorAll('.nav__item--has-dropdown .nav__link').forEach(link => {
        const item = link.closest('.nav__item');
        if (item && item !== this.closest('.nav__item')) {
          item.classList.remove('nav__item--open');
          const itemDropdown = item.querySelector('.nav__dropdown');
          if (itemDropdown) {
            itemDropdown.classList.remove('nav__dropdown--open');
          }
        }
      });

      // Toggle this dropdown
      this.setAttribute('aria-expanded', !isExpanded ? 'true' : 'false');
      if (dropdown) {
        dropdown.classList.toggle('nav__dropdown--open');
        this.closest('.nav__more').classList.toggle('nav__more--open');
      }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
      if (navMoreToggle && !navMoreToggle.contains(e.target) && navMoreToggle.nextElementSibling && !navMoreToggle.nextElementSibling.contains(e.target)) {
        navMoreToggle.setAttribute('aria-expanded', 'false');
        const dropdown = navMoreToggle.nextElementSibling;
        if (dropdown) {
          dropdown.classList.remove('nav__dropdown--open');
        }
        const navMore = navMoreToggle.closest('.nav__more');
        if (navMore) {
          navMore.classList.remove('nav__more--open');
        }
      }
    });
  }

  // Ensure dropdowns aren't clipped by forcing overflow: visible
  function ensureDropdownVisibility() {
    const navList = document.querySelector('.nav__list');
    if (navList) {
      // Force overflow: visible to ensure dropdowns aren't clipped
      navList.style.overflow = 'visible';
      
      // Also check parent containers
      const navBand = navList.closest('.nav__band');
      if (navBand) {
        navBand.style.overflow = 'visible';
      }
      const nav = navList.closest('.nav');
      if (nav) {
        nav.style.overflow = 'visible';
      }
    }
  }
  
  // ============================================
  // More Menu Dropdown Handler (with event delegation)
  // ============================================
  // Store timeouts globally so they persist across re-initializations
  const moreMenuTimeouts = new WeakMap();
  let moreMenuInitialized = false;
  
  function initMoreMenuDropdowns() {
    const moreDropdown = document.getElementById('navMoreList');
    if (!moreDropdown) {
      return;
    }
    
    // Only attach event listeners once (event delegation works for dynamically added items)
    if (moreMenuInitialized) {
      return;
    }
    
    moreMenuInitialized = true;
    
    // Use event delegation - query items dynamically each time
    moreDropdown.addEventListener('mouseenter', function(e) {
      const item = e.target.closest('.nav__item--has-dropdown');
      if (!item || !moreDropdown.contains(item)) return;
      
      const dropdown = item.querySelector('.nav__dropdown');
      if (!dropdown) return;
      
      // Get or create timeout object for this item
      let timeouts = moreMenuTimeouts.get(item);
      if (!timeouts) {
        timeouts = { showTimeout: null, hideTimeout: null };
        moreMenuTimeouts.set(item, timeouts);
      }
      
      // Clear any pending hide timeout
      if (timeouts.hideTimeout) {
        clearTimeout(timeouts.hideTimeout);
        timeouts.hideTimeout = null;
      }
      
      // Close all other dropdowns in More menu (query dynamically)
      const allMoreItems = moreDropdown.querySelectorAll('.nav__item--has-dropdown');
      allMoreItems.forEach(otherItem => {
        if (otherItem !== item) {
          const otherTimeouts = moreMenuTimeouts.get(otherItem);
          if (otherTimeouts) {
            if (otherTimeouts.showTimeout) {
              clearTimeout(otherTimeouts.showTimeout);
              otherTimeouts.showTimeout = null;
            }
            if (otherTimeouts.hideTimeout) {
              clearTimeout(otherTimeouts.hideTimeout);
              otherTimeouts.hideTimeout = null;
            }
          }
          otherItem.classList.remove('nav__item--open');
        }
      });
      
      // Show this dropdown after small delay
      timeouts.showTimeout = setTimeout(() => {
        item.classList.add('nav__item--open');
        timeouts.showTimeout = null;
      }, 50);
    }, true);
    
    // Handle mouseleave on items
    moreDropdown.addEventListener('mouseleave', function(e) {
      const item = e.target.closest('.nav__item--has-dropdown');
      if (!item || !moreDropdown.contains(item)) return;
      
      const dropdown = item.querySelector('.nav__dropdown');
      if (!dropdown) return;
      
      // Check if mouse is moving to dropdown
      const relatedTarget = e.relatedTarget;
      if (relatedTarget && (dropdown.contains(relatedTarget) || relatedTarget === dropdown)) {
        return; // Mouse is moving to dropdown, don't hide
      }
      
      let timeouts = moreMenuTimeouts.get(item);
      if (!timeouts) {
        timeouts = { showTimeout: null, hideTimeout: null };
        moreMenuTimeouts.set(item, timeouts);
      }
      
      // Clear any pending show timeout
      if (timeouts.showTimeout) {
        clearTimeout(timeouts.showTimeout);
        timeouts.showTimeout = null;
      }
      
      // Set timeout to hide dropdown
      timeouts.hideTimeout = setTimeout(() => {
        item.classList.remove('nav__item--open');
        timeouts.hideTimeout = null;
      }, 200);
    }, true);
    
    // Handle hover on dropdowns themselves (event delegation)
    moreDropdown.addEventListener('mouseenter', function(e) {
      const dropdown = e.target.closest('.nav__dropdown');
      if (!dropdown || !moreDropdown.contains(dropdown)) return;
      
      const item = dropdown.closest('.nav__item--has-dropdown');
      if (!item) return;
      
      let timeouts = moreMenuTimeouts.get(item);
      if (!timeouts) {
        timeouts = { showTimeout: null, hideTimeout: null };
        moreMenuTimeouts.set(item, timeouts);
      }
      
      if (timeouts.hideTimeout) {
        clearTimeout(timeouts.hideTimeout);
        timeouts.hideTimeout = null;
      }
      if (timeouts.showTimeout) {
        clearTimeout(timeouts.showTimeout);
        timeouts.showTimeout = null;
      }
      item.classList.add('nav__item--open');
    }, true);
    
    moreDropdown.addEventListener('mouseleave', function(e) {
      const dropdown = e.target.closest('.nav__dropdown');
      if (!dropdown || !moreDropdown.contains(dropdown)) return;
      
      const item = dropdown.closest('.nav__item--has-dropdown');
      if (!item) return;
      
      let timeouts = moreMenuTimeouts.get(item);
      if (!timeouts) {
        timeouts = { showTimeout: null, hideTimeout: null };
        moreMenuTimeouts.set(item, timeouts);
      }
      
      timeouts.hideTimeout = setTimeout(() => {
        item.classList.remove('nav__item--open');
        timeouts.hideTimeout = null;
      }, 200);
    }, true);
  }
  
  document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for handleNavOverflow to run
    setTimeout(ensureDropdownVisibility, 500);
    // Also check after resize
    window.addEventListener('resize', function() {
      setTimeout(ensureDropdownVisibility, 200);
    });
    
    // Initialize More menu dropdowns after a delay to ensure items are cloned
    setTimeout(() => {
      initMoreMenuDropdowns();
    }, 600);
    
    // Re-initialize after handleNavOverflow runs (on resize or initial load)
    // Hook into handleNavOverflow by wrapping it
    const originalHandleNavOverflow = window.handleNavOverflow || handleNavOverflow;
    window.handleNavOverflow = function() {
      originalHandleNavOverflow();
      // Re-initialize dropdown handlers after items are cloned
      setTimeout(() => {
        initMoreMenuDropdowns();
      }, 100);
    };
  });

  // ============================================
  // Partners Carousel
  // ============================================
  const initPartnersCarousel = () => {
    const partnersCarousels = document.querySelectorAll('[data-partners-carousel]');
    
    partnersCarousels.forEach((carousel) => {
      const wrapper = carousel.closest('.partners-carousel-wrapper');
      const track = carousel.querySelector('.partners-carousel__track');
      const slides = carousel.querySelectorAll('.partners-carousel__slide');
      const prevBtn = wrapper ? wrapper.querySelector('.partners-carousel__btn--prev') : null;
      const nextBtn = wrapper ? wrapper.querySelector('.partners-carousel__btn--next') : null;
      
      if (!track || !slides.length) return;
      
      let currentIndex = 0;
      let slidesToShow = 6;
      let isAnimating = false;
      
      // Calculate slides to show based on viewport width
      const updateSlidesToShow = () => {
        const width = window.innerWidth;
        if (width <= 480) {
          slidesToShow = 2;
        } else if (width <= 768) {
          slidesToShow = 3;
        } else if (width <= 1024) {
          slidesToShow = 4;
        } else {
          slidesToShow = 6;
        }
      };
      
      updateSlidesToShow();
      window.addEventListener('resize', () => {
        updateSlidesToShow();
        updateCarousel();
      });
      
      const updateCarousel = (force = false) => {
        if (!slides[0]) return;
        
        // Only skip if animating and not forced (for resize events)
        if (isAnimating && !force) return;
        
        const slideWidth = slides[0].offsetWidth;
        const gap = parseInt(getComputedStyle(track).gap) || 24;
        const translateX = -(currentIndex * (slideWidth + gap));
        
        track.style.transform = `translateX(${translateX}px)`;
        
        // Update button states
        if (prevBtn) {
          prevBtn.disabled = currentIndex === 0;
        }
        if (nextBtn) {
          const maxIndex = Math.max(0, slides.length - slidesToShow);
          nextBtn.disabled = currentIndex >= maxIndex;
        }
      };
      
      if (prevBtn) {
        prevBtn.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();
          
          if (currentIndex > 0 && !isAnimating) {
            currentIndex--;
            updateCarousel(true);
            isAnimating = true;
            setTimeout(() => {
              isAnimating = false;
            }, 500);
          }
        });
      }
      
      if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();
          
          const maxIndex = Math.max(0, slides.length - slidesToShow);
          if (currentIndex < maxIndex && !isAnimating) {
            currentIndex++;
            updateCarousel(true);
            isAnimating = true;
            setTimeout(() => {
              isAnimating = false;
            }, 500);
          }
        });
      }
      
      // Initialize after a short delay to ensure DOM is ready
      setTimeout(() => {
        updateCarousel();
      }, 100);
    });
  };
  
  // Initialize on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPartnersCarousel);
  } else {
    initPartnersCarousel();
  }
  
  // Also initialize after reveal animations (if using reveal library)
  if (typeof window !== 'undefined' && window.addEventListener) {
    window.addEventListener('load', () => {
      setTimeout(initPartnersCarousel, 500);
    });
  }

  // ============================================
  // Articles Carousel (reuses partners carousel logic)
  // ============================================
  const initArticlesCarousel = () => {
    const articlesCarousels = document.querySelectorAll('[data-articles-carousel]');
    
    articlesCarousels.forEach((carousel) => {
      const wrapper = carousel.closest('.articles-carousel-wrapper');
      const track = carousel.querySelector('.articles-carousel__track');
      const slides = carousel.querySelectorAll('.articles-carousel__slide');
      const prevBtn = wrapper ? wrapper.querySelector('.articles-carousel__btn--prev') : null;
      const nextBtn = wrapper ? wrapper.querySelector('.articles-carousel__btn--next') : null;
      
      if (!track || !slides.length) return;
      
      let currentIndex = 0;
      let slidesToShow = 3;
      let isAnimating = false;
      
      // Calculate slides to show based on viewport width
      const updateSlidesToShow = () => {
        const width = window.innerWidth;
        if (width <= 480) {
          slidesToShow = 1;
        } else if (width <= 768) {
          slidesToShow = 2;
        } else {
          slidesToShow = 3;
        }
      };
      
      updateSlidesToShow();
      window.addEventListener('resize', () => {
        updateSlidesToShow();
        updateCarousel();
      });
      
      const updateCarousel = (force = false) => {
        if (!slides[0]) return;
        
        // Only skip if animating and not forced (for resize events)
        if (isAnimating && !force) return;
        
        const slideWidth = slides[0].offsetWidth;
        const gap = parseInt(getComputedStyle(track).gap) || 24;
        const translateX = -(currentIndex * (slideWidth + gap));
        
        track.style.transform = `translateX(${translateX}px)`;
        
        // Update button states
        if (prevBtn) {
          prevBtn.disabled = currentIndex === 0;
        }
        if (nextBtn) {
          const maxIndex = Math.max(0, slides.length - slidesToShow);
          nextBtn.disabled = currentIndex >= maxIndex;
        }
      };
      
      if (prevBtn) {
        prevBtn.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();
          
          if (currentIndex > 0 && !isAnimating) {
            currentIndex--;
            updateCarousel(true);
            isAnimating = true;
            setTimeout(() => {
              isAnimating = false;
            }, 500);
          }
        });
      }
      
      if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();
          
          const maxIndex = Math.max(0, slides.length - slidesToShow);
          if (currentIndex < maxIndex && !isAnimating) {
            currentIndex++;
            updateCarousel(true);
            isAnimating = true;
            setTimeout(() => {
              isAnimating = false;
            }, 500);
          }
        });
      }
      
      // Initialize after a short delay to ensure DOM is ready
      setTimeout(() => {
        updateCarousel();
      }, 100);
    });
  };
  
  // Initialize on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initArticlesCarousel);
  } else {
    initArticlesCarousel();
  }
  
  // Also initialize after reveal animations
  if (typeof window !== 'undefined' && window.addEventListener) {
    window.addEventListener('load', () => {
      setTimeout(initArticlesCarousel, 500);
    });
  }

  // ============================================
  // Event Gallery Thumbnail Navigation
  // ============================================
  const initEventGallery = () => {
    const galleries = document.querySelectorAll('[data-event-gallery]');
    
    galleries.forEach((gallery) => {
      const thumbnailsWrapper = gallery.closest('.event-gallery').querySelector('.event-gallery__thumbnails-wrapper');
      if (!thumbnailsWrapper) return;
      
      const thumbnails = thumbnailsWrapper.querySelector('[data-event-gallery-thumbnails]');
      const thumbTrack = thumbnails ? thumbnails.querySelector('.event-gallery__thumbnails-track') : null;
      const thumbBtns = thumbnailsWrapper.querySelectorAll('.event-gallery__thumb');
      const prevBtn = thumbnailsWrapper.querySelector('.event-gallery__thumb-btn--prev');
      const nextBtn = thumbnailsWrapper.querySelector('.event-gallery__thumb-btn--next');
      
      if (!thumbTrack || !thumbBtns.length) return;
      
      let currentIndex = 0;
      let thumbsToShow = 8;
      let isAnimating = false;
      
      // Calculate thumbs to show based on viewport width
      const updateThumbsToShow = () => {
        const width = window.innerWidth;
        if (width <= 480) {
          thumbsToShow = 4;
        } else if (width <= 768) {
          thumbsToShow = 6;
        } else {
          thumbsToShow = 8;
        }
      };
      
      updateThumbsToShow();
      window.addEventListener('resize', () => {
        updateThumbsToShow();
        updateThumbnails();
      });
      
      const updateThumbnails = (force = false) => {
        if (isAnimating && !force) return;
        
        const thumbWidth = thumbBtns[0].offsetWidth;
        const gap = parseInt(getComputedStyle(thumbTrack).gap) || 8;
        const translateX = -(currentIndex * (thumbWidth + gap));
        
        thumbTrack.style.transform = `translateX(${translateX}px)`;
        
        // Update button states
        if (prevBtn) {
          prevBtn.disabled = currentIndex === 0;
        }
        if (nextBtn) {
          const maxIndex = Math.max(0, thumbBtns.length - thumbsToShow);
          nextBtn.disabled = currentIndex >= maxIndex;
        }
      };
      
      // Handle thumbnail clicks - open lightbox at that image
      thumbBtns.forEach((btn, index) => {
        btn.addEventListener('click', () => {
          // Remove active class from all thumbs
          thumbBtns.forEach(t => t.classList.remove('active'));
          // Add active class to clicked thumb
          btn.classList.add('active');
          
          // Open lightbox at this image
          const galleryLink = gallery.querySelector(`[data-event-gallery-image][data-image-index="${index}"]`);
          if (galleryLink) {
            galleryLink.click();
          } else {
            // Fallback: scroll to corresponding image in grid
            const galleryItem = gallery.querySelector(`[data-gallery-index="${index}"]`);
            if (galleryItem) {
              galleryItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
          }
        });
      });
      
      // Set first thumb as active
      if (thumbBtns[0]) {
        thumbBtns[0].classList.add('active');
      }
      
      if (prevBtn) {
        prevBtn.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();
          
          if (currentIndex > 0 && !isAnimating) {
            currentIndex--;
            updateThumbnails(true);
            isAnimating = true;
            setTimeout(() => {
              isAnimating = false;
            }, 300);
          }
        });
      }
      
      if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();
          
          const maxIndex = Math.max(0, thumbBtns.length - thumbsToShow);
          if (currentIndex < maxIndex && !isAnimating) {
            currentIndex++;
            updateThumbnails(true);
            isAnimating = true;
            setTimeout(() => {
              isAnimating = false;
            }, 300);
          }
        });
      }
      
      // Initialize
      setTimeout(() => {
        updateThumbnails();
      }, 100);
    });
  };
  
  // Initialize on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initEventGallery);
  } else {
    initEventGallery();
  }
  
  // Also initialize after reveal animations
  if (typeof window !== 'undefined' && window.addEventListener) {
    window.addEventListener('load', () => {
      setTimeout(initEventGallery, 500);
    });
  }

  // ============================================
  // Event Gallery Lightbox
  // ============================================
  const initEventGalleryLightbox = () => {
    const lightbox = document.getElementById('lightbox');
    if (!lightbox) return;
    
    const lightboxImage = lightbox.querySelector('.lightbox__image');
    const lightboxCaption = lightbox.querySelector('.lightbox__caption');
    const lightboxCounter = lightbox.querySelector('.lightbox__counter');
    const closeBtn = lightbox.querySelector('.lightbox__close');
    const prevBtn = lightbox.querySelector('.lightbox__nav--prev');
    const nextBtn = lightbox.querySelector('.lightbox__nav--next');
    
    if (!lightboxImage) return;
    
    let currentGallery = null;
    let currentIndex = 0;
    let images = [];
    
    function openLightbox(galleryElement, index) {
      const galleryItems = galleryElement.querySelectorAll('[data-event-gallery-image]');
      if (!galleryItems.length) return;
      
      images = Array.from(galleryItems).map(item => ({
        url: item.getAttribute('data-image-url') || item.href,
        caption: item.getAttribute('data-image-caption') || '',
        alt: item.getAttribute('data-image-alt') || ''
      }));
      
      currentGallery = galleryElement;
      currentIndex = parseInt(index) || 0;
      
      updateLightbox();
      lightbox.classList.add('lightbox--open');
      document.body.style.overflow = 'hidden';
    }
    
    function closeLightbox() {
      lightbox.classList.remove('lightbox--open');
      document.body.style.overflow = '';
      currentGallery = null;
      images = [];
    }
    
    function updateLightbox() {
      if (images.length === 0 || currentIndex < 0 || currentIndex >= images.length) return;
      
      const image = images[currentIndex];
      lightboxImage.src = image.url;
      lightboxImage.alt = image.alt;
      
      if (lightboxCaption) {
        lightboxCaption.textContent = image.caption || '';
      }
      
      if (lightboxCounter) {
        lightboxCounter.textContent = (currentIndex + 1) + ' / ' + images.length;
      }
      
      // Update navigation button states
      if (prevBtn) {
        prevBtn.disabled = currentIndex === 0;
      }
      if (nextBtn) {
        nextBtn.disabled = currentIndex === images.length - 1;
      }
      
      // Update active thumbnail
      if (currentGallery) {
        const allThumbs = currentGallery.closest('.event-gallery').querySelectorAll('.event-gallery__thumb');
        allThumbs.forEach((thumb, index) => {
          if (index === currentIndex) {
            thumb.classList.add('active');
          } else {
            thumb.classList.remove('active');
          }
        });
      }
    }
    
    function showPrev() {
      if (currentIndex > 0) {
        currentIndex--;
        updateLightbox();
      }
    }
    
    function showNext() {
      if (currentIndex < images.length - 1) {
        currentIndex++;
        updateLightbox();
      }
    }
    
    // Handle gallery image clicks
    document.addEventListener('click', (e) => {
      const galleryLink = e.target.closest('[data-event-gallery-image]');
      if (galleryLink) {
        e.preventDefault();
        const galleryElement = galleryLink.closest('[data-event-gallery]');
        const index = galleryLink.getAttribute('data-image-index');
        if (galleryElement) {
          openLightbox(galleryElement, index);
        }
      }
    });
    
    // Close lightbox
    if (closeBtn) {
      closeBtn.addEventListener('click', closeLightbox);
    }
    
    // Close on background click
    lightbox.addEventListener('click', (e) => {
      if (e.target === lightbox) {
        closeLightbox();
      }
    });
    
    // Navigation
    if (prevBtn) {
      prevBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        showPrev();
      });
    }
    
    if (nextBtn) {
      nextBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        showNext();
      });
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
      if (!lightbox.classList.contains('lightbox--open')) return;
      
      if (e.key === 'Escape') {
        closeLightbox();
      } else if (e.key === 'ArrowLeft') {
        showPrev();
      } else if (e.key === 'ArrowRight') {
        showNext();
      }
    });
  };
  
  // Initialize on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initEventGalleryLightbox);
  } else {
    initEventGalleryLightbox();
  }
  
  // Also initialize after reveal animations
  if (typeof window !== 'undefined' && window.addEventListener) {
    window.addEventListener('load', () => {
      setTimeout(initEventGalleryLightbox, 500);
    });
  }

  // ============================================
  // Navigation Dropdown Hover Delay
  // Improves UX by preventing dropdown from closing when moving mouse to it
  // ============================================
  function initNavDropdownHover() {
    const navItems = document.querySelectorAll('.nav__item--has-dropdown:not(.nav__more .nav__item)');
    
    navItems.forEach(item => {
      let hideTimeout = null;
      const dropdown = item.querySelector('.nav__dropdown');
      
      if (!dropdown) return;
      
      // Show dropdown on hover
      item.addEventListener('mouseenter', () => {
        // Clear any pending hide timeout
        if (hideTimeout) {
          clearTimeout(hideTimeout);
          hideTimeout = null;
        }
        // Show dropdown immediately
        item.classList.add('nav__item--dropdown-open');
      });
      
      // Hide dropdown with delay on mouse leave
      item.addEventListener('mouseleave', (e) => {
        // Check if mouse is moving to dropdown
        const relatedTarget = e.relatedTarget;
        if (relatedTarget && (dropdown.contains(relatedTarget) || relatedTarget === dropdown)) {
          return; // Mouse is moving to dropdown, don't hide
        }
        
        // Set timeout to hide dropdown (200ms delay)
        hideTimeout = setTimeout(() => {
          item.classList.remove('nav__item--dropdown-open');
          hideTimeout = null;
        }, 200);
      });
      
      // Keep dropdown open when hovering over it
      dropdown.addEventListener('mouseenter', () => {
        if (hideTimeout) {
          clearTimeout(hideTimeout);
          hideTimeout = null;
        }
        item.classList.add('nav__item--dropdown-open');
      });
      
      // Hide dropdown when leaving dropdown
      dropdown.addEventListener('mouseleave', () => {
        hideTimeout = setTimeout(() => {
          item.classList.remove('nav__item--dropdown-open');
          hideTimeout = null;
        }, 200);
      });
    });
  }
  
  // Initialize dropdown hover delay
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNavDropdownHover);
  } else {
    initNavDropdownHover();
  }

})();
