<?php
/**
 * Template Name: Custom Single Post with Banner
 * Template Post Type: post
 */

get_header();
global $content_width;
$content_width = 1068;
?>

<div class="td-main-content-wrap td-container-wrap">
    <div class="td-container">
        <div class="td-crumb-container">
            <?php echo tagdiv_page_generator::get_breadcrumbs(array(
                'template' => 'single',
                'title' => get_the_title(),
            )); ?>
        </div>

        <div class="td-pb-row">
            <div class="td-pb-span12 td-main-content">
                <div class="td-ss-main-content">
                    <?php
                    if (have_posts()) {
                        the_post();
                        ?>
                        <article class="<?php echo join(' ', get_post_class());?>">
                            <div class="td-post-header">
                                <ul class="td-category">
                                    <?php
                                    $categories = get_the_category();
                                    if( !empty( $categories ) ) {
                                        foreach($categories as $category) {
                                            $cat_link = get_category_link($category->cat_ID);
                                            $cat_name = $category->name; ?>
                                            <li class="entry-category"><a href="<?php echo esc_url($cat_link) ?>"><?php echo esc_html($cat_name) ?></a></li>
                                        <?php }
                                    } ?>
                                </ul>

                                <header class="td-post-title">
                                    <h3 class="entry-title td-module-title">
                                        <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute() ?>">
                                            <?php the_title() ?>
                                        </a>
                                    </h3>

                                    <div class="td-module-meta-info">
                                        <div class="td-post-author-name">
                                            <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta( 'ID' ))) ?>"><?php the_author() ?></a>
                                            <div class="td-author-line"> - </div>
                                        </div>
                                        <span class="td-post-date">
                                            <time class="entry-date updated td-module-date" datetime="<?php echo esc_html(date(DATE_W3C, get_the_time('U'))) ?>" ><?php the_time(get_option('date_format')) ?></time>
                                        </span>
                                        <div class="td-post-comments">
                                            <a href="<?php comments_link() ?>">
                                                <i class="td-icon-comments"></i>
                                                <?php comments_number('0','1','%') ?>
                                            </a>
                                        </div>
                                    </div>
                                </header>

                                <div class="td-post-content tagdiv-type">
                                    <!-- Featured Image -->
                                    <?php
                                        if( get_the_post_thumbnail_url(null, 'full') != false ) { ?>
                                            <div class="td-post-featured-image">
                                                <?php if(get_the_post_thumbnail_caption() != '' ) { ?>
                                                    <figure>
                                                        <img class="entry-thumb" src="<?php echo esc_url(get_the_post_thumbnail_url(null, 'medium_large')) ?>" alt="<?php the_title() ?>" title="<?php echo esc_attr(strip_tags(the_title())) ?>" />
                                                        <figcaption class="wp-caption-text"><?php echo esc_html(get_the_post_thumbnail_caption()) ?></figcaption>
                                                    </figure>
                                                <?php } else { ?>
                                                    <img class="entry-thumb" src="<?php echo esc_url(get_the_post_thumbnail_url(null, 'medium_large')) ?>" alt="<?php the_title() ?>" title="<?php echo esc_attr(strip_tags(the_title())) ?>" />
                                                <?php } ?>
                                            </div>
                                    <?php } ?>

                                    <?php the_content() ?>
                                    
                                    <!-- Custom Banner Shortcode -->
                                    <div class="td-post-banner-section" style="margin: 40px 0; padding: 20px 0; border-top: 1px solid #eee;">
                                        <h4 style="margin-bottom: 20px; color: #333;">Related Content</h4>
                                        <?php echo do_shortcode('[latest_post_banner show_categories="1" show_date="1" show_excerpt="1"]'); ?>
                                    </div>
                                </div>
                            </div>
                        </article>
                        <?php
                    }
                    comments_template('', true);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>

