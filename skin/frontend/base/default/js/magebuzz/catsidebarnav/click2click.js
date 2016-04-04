jQuery(document).ready(function() {
    jQuery('#category-sidebar-nav li a.show-cat').click(function() {
      jQuery(this).children('ul').slideUp();

      if (!jQuery(this).hasClass('active')) {
        jQuery(this).next().slideToggle();
        jQuery(this).addClass('active');
      }
      else if (jQuery(this).hasClass('active')) {
        jQuery(this).next().slideToggle();
        jQuery(this).removeClass('active');
      }
  });
});