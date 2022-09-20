<?php
/**
 * The template for displaying all pages created by leadgen.
 *
 */
if(!defined('ABSPATH'))
	exit; // Exit if accessed directly.

?>
<?php get_header(); ?>

<div id="content" <?php post_class( 'site-main leadgenpro' ); ?> role="main">
    <?php the_content(); ?>
    
</div>
<script>
//Make contained width to full width.
$(function(){
   $('body').find('div, main, section').each(function(){
      if($(this).css('max-width') !== 'none' && $(this).children().find('.page-container').length){
         $(this).css({'max-width' : '100%', 'padding' : 0});
         return false;
      }
   })
})
</script>
<?php get_footer(); ?>
