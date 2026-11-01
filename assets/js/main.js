(function () {
  "use strict";

  /**
   * Apply .scrolled class to the body as the page is scrolled down
   */
  function toggleScrolled() {
    const selectBody = document.querySelector('body');
    const selectHeader = document.querySelector('#header');
    if (!selectHeader || (!selectHeader.classList.contains('scroll-up-sticky') && !selectHeader.classList.contains('sticky-top') && !selectHeader.classList.contains('fixed-top'))) return;
    window.scrollY > 100 ? selectBody.classList.add('scrolled') : selectBody.classList.remove('scrolled');
  }

  document.addEventListener('scroll', toggleScrolled);
  window.addEventListener('load', toggleScrolled);

  /**
   * Mobile nav toggle
   */
  const mobileNavToggleBtn = document.querySelector('.mobile-nav-toggle');

  function mobileNavToogle() {
    document.querySelector('body').classList.toggle('mobile-nav-active');
    mobileNavToggleBtn.classList.toggle('bi-list');
    mobileNavToggleBtn.classList.toggle('bi-x');
  }

  if (mobileNavToggleBtn) {
    mobileNavToggleBtn.addEventListener('click', mobileNavToogle);
  }

  /**
   * Hide mobile nav on same-page/hash links
   */
  document.querySelectorAll('#navmenu a').forEach(navmenu => {
    navmenu.addEventListener('click', () => {
      if (document.querySelector('.mobile-nav-active')) {
        mobileNavToogle();
      }
    });
  });

  /**
   * Toggle mobile nav dropdowns
   */
  document.querySelectorAll('.navmenu .toggle-dropdown').forEach(navmenu => {
    navmenu.addEventListener('click', function (e) {
      e.preventDefault();
      this.parentNode.classList.toggle('active');
      this.parentNode.nextElementSibling.classList.toggle('dropdown-active');
      e.stopImmediatePropagation();
    });
  });

  /**
   * Scroll top button
   */
  const scrollTop = document.querySelector('.scroll-top'); // Use const if not reassigned

  function toggleScrollTop() {
    if (scrollTop) {
      window.scrollY > 100 ? scrollTop.classList.add('active') : scrollTop.classList.remove('active');
    }
  }

  if (scrollTop) { // Check if scrollTop exists before adding listener
    scrollTop.addEventListener('click', (e) => {
      e.preventDefault();
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
    window.addEventListener('load', toggleScrollTop);
    document.addEventListener('scroll', toggleScrollTop);
  }


  /**
   * Animation on scroll function and init
   */
  function aosInit() {
    // Check if AOS is defined before initializing
    if (typeof AOS !== 'undefined') {
      AOS.init({
        duration: 600,
        easing: 'ease-in-out',
        once: true,
        mirror: false
      });
    } else {
      console.warn('AOS library not found. Skipping animation initialization.');
    }
  }
  window.addEventListener('load', aosInit);

  /**
   * Initiate glightbox
   */
  function initGlightbox() {
    // Check if GLightbox is defined
    if (typeof GLightbox !== 'undefined') {
      const glightbox = GLightbox({
        selector: '.glightbox'
      });
    } else {
      console.warn('GLightbox library not found. Skipping lightbox initialization.');
    }
  }
  window.addEventListener('load', initGlightbox); // Init after load if it depends on images

  /**
   * Init swiper sliders
   */
  function initSwiperSliders() { // Renamed for clarity
    // Check if Swiper is defined
    if (typeof Swiper === 'undefined') {
      console.warn('Swiper library not found. Skipping slider initialization.');
      return;
    }

    document.querySelectorAll(".init-swiper").forEach(function (swiperElement) {
      let config = {}; // Initialize config
      const configElement = swiperElement.querySelector(".swiper-config");
      if (configElement) {
        try {
          config = JSON.parse(configElement.innerHTML.trim());
        } catch (e) {
          console.error("Error parsing Swiper config:", e, configElement.innerHTML.trim());
          return; // Skip this slider if config is invalid
        }
      } else {
        console.warn("Swiper config element not found for:", swiperElement);
        return; // Skip if no config found
      }


      // Swiper initialization logic seems complex (swiper-tab, custom pagination)
      // Keep the original logic but ensure Swiper exists
      if (swiperElement.classList.contains("swiper-tab")) {
        // Assuming initSwiperWithCustomPagination is defined elsewhere or should be included
        // initSwiperWithCustomPagination(swiperElement, config);
        console.warn("initSwiperWithCustomPagination function needs to be defined or included for swiper-tab elements.");
        // Fallback or specific handling needed here if the function isn't available
        new Swiper(swiperElement, config); // Example fallback: standard init
      } else {
        new Swiper(swiperElement, config);
      }
    });
  }

  window.addEventListener("load", initSwiperSliders);

  /**
   * Initiate Pure Counter
   */
  function initPureCounter() {
    // Check if PureCounter is defined
    if (typeof PureCounter !== 'undefined') {
      new PureCounter();
    } else {
      console.warn('PureCounter library not found. Skipping counter initialization.');
    }
  }
  // PureCounter might be better initialized after DOMContentLoaded if it doesn't depend on images/styles fully loaded
  document.addEventListener('DOMContentLoaded', initPureCounter);

  /**
   * Frequently Asked Questions Toggle
   */
  function initFaqToggle() {
    document.querySelectorAll('.faq-item h3, .faq-item .faq-toggle').forEach((faqItem) => {
      faqItem.addEventListener('click', () => {
        // Consider toggling only the clicked item's parent, not globally
        faqItem.closest('.faq-item').classList.toggle('faq-active');
      });
    });
  }
  // Can run after DOMContentLoaded as it only depends on DOM structure
  document.addEventListener('DOMContentLoaded', initFaqToggle);

  /**
   * Correct scrolling position upon page load for URLs containing hash links.
   */
  function handleHashLinksOnLoad() {
    if (window.location.hash) {
      const element = document.querySelector(window.location.hash); // Use const
      if (element) {
        setTimeout(() => {
          const header = document.querySelector('#header'); // Consider header height
          const headerHeight = header ? header.offsetHeight : 0;
          const elementOffset = element.offsetTop;
          let scrollMarginTop = parseInt(getComputedStyle(element).scrollMarginTop) || 0; // Handle potential NaN

          // Adjust scroll position considering header height and scroll margin
          window.scrollTo({
            top: elementOffset - headerHeight - scrollMarginTop,
            behavior: 'smooth' // Behavior might be ignored by setTimeout, consider 'auto' or removing behavior
          });
        }, 100); // 100ms might be arbitrary, adjust if needed
      }
    }
  }
  window.addEventListener('load', handleHashLinksOnLoad);

  /**
   * Navmenu Scrollspy
   */
  function initNavmenuScrollspy() { // Wrap in a function
    const navmenulinks = document.querySelectorAll('.navmenu a'); // Use const

    function navmenuScrollspyAction() { // The actual function to run on scroll/load
      const position = window.scrollY + 200; // 200px offset might need adjustment
      let anyLinkActivated = false; // Flag to track if any link is activated

      navmenulinks.forEach(navmenulink => {
        if (!navmenulink.hash) return;
        const section = document.querySelector(navmenulink.hash);
        if (!section) return;

        if (position >= section.offsetTop && position <= (section.offsetTop + section.offsetHeight)) {
          if (!navmenulink.classList.contains('active')) { // Only add if not already active
            // Deactivate all links first (more robust)
            document.querySelectorAll('.navmenu a.active').forEach(link => link.classList.remove('active'));
            navmenulink.classList.add('active');
            anyLinkActivated = true; // Mark that a link was activated
          }
        } else {
          if (navmenulink.classList.contains('active')) { // Only remove if active
            navmenulink.classList.remove('active');
          }
        }
      });

      // Optional: Handle case where no section is active (e.g., top/bottom of page)
      // if (!anyLinkActivated) {
      //    // Maybe activate the first link or clear all activations
      //    document.querySelectorAll('.navmenu a.active').forEach(link => link.classList.remove('active'));
      // }
    }

    window.addEventListener('load', navmenuScrollspyAction);
    document.addEventListener('scroll', navmenuScrollspyAction);
  }
  initNavmenuScrollspy(); // Call the setup function

  /**
   * Sign-in/Sign-up Mode Toggle Functionality
   * --- START OF ADDED/MODIFIED CODE ---
   */
  function initAuthFormToggle() {
    const signInBtn = document.querySelector("#sign-in-btn");
    const signUpBtn = document.querySelector("#sign-up-btn");
    // Be specific if possible. If multiple '.container' exist, this might select the wrong one.
    // Consider using a more specific ID like '#auth-container' if you control the HTML.
    const authContainer = document.querySelector(".container");

    // Check if all required elements exist on the page
    if (signInBtn && signUpBtn && authContainer) {

      signUpBtn.addEventListener("click", (e) => {
        e.preventDefault(); // Prevent default button behavior (e.g., form submission)
        authContainer.classList.add("sign-up-mode");
      });

      signInBtn.addEventListener("click", (e) => {
        e.preventDefault(); // Prevent default button behavior
        authContainer.classList.remove("sign-up-mode");
      });

      // Optional: If you want the mode to be set based on URL hash, e.g., /login#signup
      /*
      if (window.location.hash === '#signup' && !authContainer.classList.contains('sign-up-mode')) {
          authContainer.classList.add("sign-up-mode");
      } else if (window.location.hash === '#signin' && authContainer.classList.contains('sign-up-mode')) {
          authContainer.classList.remove("sign-up-mode");
      }
      */


    } else {
      // Optional: Log a warning if elements are expected but not found on a page
      // This helps during development if the script runs on pages without the auth form
      // console.warn("Sign-in/Sign-up UI elements not found. Skipping toggle initialization.");
    }
  }

  // Initialize the auth form toggle after the DOM is ready
  // 'DOMContentLoaded' is generally preferred over 'load' for DOM manipulations
  document.addEventListener('DOMContentLoaded', initAuthFormToggle);
  // --- END OF ADDED/MODIFIED CODE ---


})(); // End of the main IIFE