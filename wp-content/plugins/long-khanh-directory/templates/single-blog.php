<?php
if (!defined('ABSPATH')) exit;
the_post();
$article = get_post();
$protected = post_password_required($article);
$body = $protected ? array('html' => get_the_password_form($article), 'meta' => '', 'legacy' => false, 'full_document' => false) : lk_blog_article_body($article);
$content = $body['full_document'] ? array('html' => $body['html'], 'items' => array()) : lk_blog_table_of_contents($body['html']);
$related = $protected ? array() : lk_blog_related_posts($article->ID);
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class('elementor-template-canvas lk-blog-single'); ?>>
<?php wp_body_open(); do_action('elementor/page_templates/canvas/before_content'); ?>
<?php if ($body['full_document']): echo $content['html']; else: ?>
<section class="lk-section lk-page lk-gradient lk-blog-article-shell">
 <article class="lk-panel lk-article lk-blog-native-article">
  <h1><?php echo esc_html(get_the_title($article)); ?></h1>
  <p class="lk-article-meta"><?php echo esc_html($body['meta'] ?: lk_blog_legacy_byline($article)); ?></p>
  <?php if (!$protected && !$body['legacy'] && has_post_thumbnail($article)): ?>
   <figure class="lk-blog-cover"><?php echo get_the_post_thumbnail($article, 'large', array('loading' => 'eager')); ?></figure>
  <?php endif; ?>
  <div class="lk-blog-native-content"><?php echo $content['html']; ?></div>
  <?php wp_link_pages(array('before' => '<nav class="lk-blog-content-pages" aria-label="Các phần của bài viết">', 'after' => '</nav>')); ?>
 </article>
 <aside class="lk-article-sidebar lk-blog-native-sidebar" aria-label="Thông tin bài viết">
  <?php if ($content['items']): ?>
  <nav class="lk-panel lk-toc" aria-label="Nội dung bài viết">
   <h2>Nội dung bài viết</h2><ul>
   <?php foreach ($content['items'] as $item): ?>
    <li class="<?php echo $item['level'] === 'h3' ? 'lk-blog-toc-subheading' : ''; ?>"><a href="#<?php echo esc_attr($item['id']); ?>"><?php echo esc_html($item['text']); ?></a></li>
   <?php endforeach; ?>
   </ul>
  </nav>
  <?php endif; ?>
  <?php if ($related): ?>
  <section class="lk-panel lk-blog-related">
   <h2>Bài viết liên quan</h2>
   <?php foreach ($related as $other): ?>
   <a class="lk-blog-related-link" href="<?php echo esc_url(get_permalink($other)); ?>">
    <?php if (has_post_thumbnail($other)): echo get_the_post_thumbnail($other, 'thumbnail', array('loading' => 'lazy')); else: ?>
     <img src="<?php echo esc_url(lk_asset('no-image')); ?>" width="100" height="70" alt="" loading="lazy">
    <?php endif; ?>
    <span><?php echo esc_html(get_the_title($other)); ?></span>
   </a>
   <?php endforeach; ?>
  </section>
  <?php endif; ?>
 </aside>
</section>
<?php endif; ?>
<?php do_action('elementor/page_templates/canvas/after_content'); wp_footer(); ?>
</body></html>
