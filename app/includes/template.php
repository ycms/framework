<?php
/**
 * Template loading functions.
 *
 * @package WordPress
 * @subpackage Template
 */

/**
 * Retrieve path to a template
 *
 * Used to quickly retrieve the path of a template without including the file
 * extension. It will also check the parent theme, if the file exists, with
 * the use of {@link locate_template()}. Allows for more generic template location
 * without the use of the other get_*_template() functions.
 *
 * @since 1.5.0
 *
 * @param string $type Filename without extension.
 * @param array  $templates An optional list of template candidates
 * @return string Full path to template file.
 */
function get_query_template($type, $templates = array())
{
    $type = preg_replace('|[^a-z0-9-]+|', '', $type);

    if (empty($templates)) {
        $templates = array("{$type}.php");
    }

    $template = locate_template($templates);
    /**
     * Filter the path of the queried template by type.
     *
     * The dynamic portion of the hook name, `$type`, refers to the filename
     * -- minus the extension -- of the file to load. This hook also applies
     * to various types of files loaded as part of the Template Hierarchy.
     *
     * @since 1.5.0
     *
     * @param string $template Path to the template. See {@see locate_template()}.
     */
    return apply_filters("{$type}_template", $template);
}

/**
 * Retrieve path of index template in current or parent template.
 *
 * The template path is filterable via the 'index_template' hook.
 *
 * @since 3.0.0
 *
 * @see get_query_template()
 *
 * @return string Full path to index template file.
 */
function get_index_template()
{
    return get_query_template('index');
}

/**
 * Retrieve path of 404 template in current or parent template.
 *
 * The template path is filterable via the '404_template' hook.
 *
 * @since 1.5.0
 *
 * @see get_query_template()
 *
 * @return string Full path to 404 template file.
 */
function get_404_template()
{
    return get_query_template('404');
}

/**
 * Retrieve path of archive template in current or parent template.
 *
 * The template path is filterable via the 'archive_template' hook.
 *
 * @since 1.5.0
 *
 * @see get_query_template()
 *
 * @return string Full path to archive template file.
 */
function get_archive_template()
{
    $post_types = array_filter((array)get_query_var('post_type'));

    $templates = array();

    if (count($post_types) == 1) {
        $post_type = reset($post_types);
        $templates[] = "archive-{$post_type}.php";
    }
    $templates[] = 'archive.php';

    return get_query_template('archive', $templates);
}

/**
 * Retrieve path of post type archive template in current or parent template.
 *
 * The template path is filterable via the 'archive_template' hook.
 *
 * @since 3.7.0
 *
 * @see get_archive_template()
 *
 * @return string Full path to archive template file.
 */
function get_post_type_archive_template()
{
    $post_type = get_query_var('post_type');
    if (is_array($post_type)) {
        $post_type = reset($post_type);
    }

    $obj = get_post_type_object($post_type);
    if (!$obj->has_archive) {
        return '';
    }

    return get_archive_template();
}

/**
 * Retrieve path of author template in current or parent template.
 *
 * The template path is filterable via the 'author_template' hook.
 *
 * @since 1.5.0
 *
 * @see get_query_template()
 *
 * @return string Full path to author template file.
 */
function get_author_template()
{
    $author = get_queried_object();

    $templates = array();

    if ($author instanceof WP_User) {
        $templates[] = "author-{$author->user_nicename}.php";
        $templates[] = "author-{$author->ID}.php";
    }
    $templates[] = 'author.php';

    return get_query_template('author', $templates);
}

/**
 * Retrieve path of category template in current or parent template.
 *
 * Works by first retrieving the current slug, for example 'category-default.php', and then
 * trying category ID, for example 'category-1.php', and will finally fall back to category.php
 * template, if those files don't exist.
 *
 * The template path is filterable via the 'category_template' hook.
 *
 * @since 1.5.0
 *
 * @see get_query_template()
 *
 * @return string Full path to category template file.
 */
function get_category_template()
{
    $category = get_queried_object();

    $templates = array();

    if (!empty($category->slug)) {
        $templates[] = "category-{$category->slug}.php";
        $templates[] = "category-{$category->term_id}.php";
    }
    $templates[] = 'category.php';

    return get_query_template('category', $templates);
}

/**
 * Retrieve path of tag template in current or parent template.
 *
 * Works by first retrieving the current tag name, for example 'tag-wordpress.php', and then
 * trying tag ID, for example 'tag-1.php', and will finally fall back to tag.php
 * template, if those files don't exist.
 *
 * The template path is filterable via the 'tag_template' hook.
 *
 * @since 2.3.0
 *
 * @see get_query_template()
 *
 * @return string Full path to tag template file.
 */
function get_tag_template()
{
    $tag = get_queried_object();

    $templates = array();

    if (!empty($tag->slug)) {
        $templates[] = "tag-{$tag->slug}.php";
        $templates[] = "tag-{$tag->term_id}.php";
    }
    $templates[] = 'tag.php';

    return get_query_template('tag', $templates);
}

/**
 * Retrieve path of taxonomy template in current or parent template.
 *
 * Retrieves the taxonomy and term, if term is available. The template is
 * prepended with 'taxonomy-' and followed by both the taxonomy string and
 * the taxonomy string followed by a dash and then followed by the term.
 *
 * The taxonomy and term template is checked and used first, if it exists.
 * Second, just the taxonomy template is checked, and then finally, taxonomy.php
 * template is used. If none of the files exist, then it will fall back on to
 * index.php.
 *
 * The template path is filterable via the 'taxonomy_template' hook.
 *
 * @since 2.5.0
 *
 * @see get_query_template()
 *
 * @return string Full path to taxonomy template file.
 */
function get_taxonomy_template()
{
    $term = get_queried_object();

    $templates = array();

    if (!empty($term->slug)) {
        $taxonomy = $term->taxonomy;
        $templates[] = "taxonomy-$taxonomy-{$term->slug}.php";
        $templates[] = "taxonomy-$taxonomy.php";
    }
    $templates[] = 'taxonomy.php';

    return get_query_template('taxonomy', $templates);
}

/**
 * Retrieve path of date template in current or parent template.
 *
 * The template path is filterable via the 'date_template' hook.
 *
 * @since 1.5.0
 *
 * @see get_query_template()
 *
 * @return string Full path to date template file.
 */
function get_date_template()
{
    return get_query_template('date');
}

/**
 * Retrieve path of home template in current or parent template.
 *
 * This is the template used for the page containing the blog posts.
 * Attempts to locate 'home.php' first before falling back to 'index.php'.
 *
 * The template path is filterable via the 'home_template' hook.
 *
 * @since 1.5.0
 *
 * @see get_query_template()
 *
 * @return string Full path to home template file.
 */
function get_home_template()
{
    $templates = array('home.php', 'index.php');

    return get_query_template('home', $templates);
}

/**
 * Retrieve path of front-page template in current or parent template.
 *
 * Looks for 'front-page.php'. The template path is filterable via the
 * 'front_page_template' hook.
 *
 * @since 3.0.0
 *
 * @see get_query_template()
 *
 * @return string Full path to front page template file.
 */
function get_front_page_template()
{
    $templates = array('front-page.php');

    return get_query_template('front_page', $templates);
}

/**
 * Retrieve path of page template in current or parent template.
 *
 * Will first look for the specifically assigned page template.
 * Then will search for 'page-{slug}.php', followed by 'page-{id}.php',
 * and finally 'page.php'.
 *
 * The template path is filterable via the 'page_template' hook.
 *
 * @since 1.5.0
 *
 * @see get_query_template()
 *
 * @return string Full path to page template file.
 */
function get_page_template()
{
    $id = get_queried_object_id();
    $template = get_page_template_slug();
    $pagename = get_query_var('pagename');

    if (!$pagename && $id) {
        // If a static page is set as the front page, $pagename will not be set. Retrieve it from the queried object
        $post = get_queried_object();
        if ($post) {
            $pagename = $post->post_name;
        }
    }

    $templates = array();
    if ($template && 0 === validate_file($template)) {
        $templates[] = $template;
    }
    if ($pagename) {
        $templates[] = "page-$pagename.php";
    }
    if ($id) {
        $templates[] = "page-$id.php";
    }
    $templates[] = 'page.php';

    return get_query_template('page', $templates);
}

/**
 * Retrieve path of paged template in current or parent template.
 *
 * The template path is filterable via the 'paged_template' hook.
 *
 * @since 1.5.0
 *
 * @see get_query_template()
 *
 * @return string Full path to paged template file.
 */
function get_paged_template()
{
    return get_query_template('paged');
}

/**
 * Retrieve path of search template in current or parent template.
 *
 * The template path is filterable via the 'search_template' hook.
 *
 * @since 1.5.0
 *
 * @see get_query_template()
 *
 * @return string Full path to search template file.
 */
function get_search_template()
{
    return get_query_template('search');
}

/**
 * Retrieve path of single template in current or parent template.
 *
 * The template path is filterable via the 'single_template' hook.
 *
 * @since 1.5.0
 *
 * @see get_query_template()
 *
 * @return string Full path to single template file.
 */
function get_single_template()
{
    $object = get_queried_object();

    $templates = array();

    if (!empty($object->post_type)) {
        $templates[] = "single-{$object->post_type}.php";
    }
    $templates[] = "single.php";

    return get_query_template('single', $templates);
}

/**
 * Retrieve path of attachment template in current or parent template.
 *
 * The attachment path first checks if the first part of the mime type exists.
 * The second check is for the second part of the mime type. The last check is
 * for both types separated by an underscore. If neither are found then the file
 * 'attachment.php' is checked and returned.
 *
 * Some examples for the 'text/plain' mime type are 'text.php', 'plain.php', and
 * finally 'text_plain.php'.
 *
 * The template path is filterable via the 'attachment_template' hook.
 *
 * @since 2.0.0
 *
 * @see get_query_template()
 *
 * @return string Full path to attachment template file.
 */
function get_attachment_template()
{
    global $posts;

    if (!empty($posts) && isset($posts[0]->post_mime_type)) {
        $type = explode('/', $posts[0]->post_mime_type);

        if (!empty($type)) {
            if ($template = get_query_template($type[0])) {
                return $template;
            } elseif (!empty($type[1])) {
                if ($template = get_query_template($type[1])) {
                    return $template;
                } elseif ($template = get_query_template("$type[0]_$type[1]")) {
                    return $template;
                }
            }
        }
    }

    return get_query_template('attachment');
}

/**
 * Retrieve path of comment popup template in current or parent template.
 *
 * Checks for comment popup template in current template, if it exists or in the
 * parent template.
 *
 * The template path is filterable via the 'comments_popup_template' hook.
 *
 * @since 1.5.0
 *
 * @see get_query_template()
 *
 * @return string Full path to comments popup template file.
 */
function get_comments_popup_template()
{
    $template = get_query_template('comments_popup', array('comments-popup.php'));

    // Backward compat code will be removed in a future release
    if ('' == $template) {
        $template = ABSPATH . WPINC . '/theme-compat/comments-popup.php';
    }

    return $template;
}

/**
 * Retrieve the name of the highest priority template file that exists.
 *
 * Searches in the STYLESHEETPATH before TEMPLATEPATH so that themes which
 * inherit from a parent theme can just overload one file.
 *
 * @since 2.7.0
 *
 * @param string|array $template_names Template file(s) to search for, in order.
 * @param bool         $load If true the template file will be loaded if it is found.
 * @param bool         $require_once Whether to require_once or require. Default true. Has no effect if $load is false.
 * @return string The template filename if one is located.
 */
function locate_template($template_names, $load = false, $require_once = true)
{
    $located = '';

    foreach ((array)$template_names as $template_name) {
        if (!$template_name) {
            continue;
        }

        if (!preg_match("/\.blade\.php$/", $template_name)) {
            $name = preg_replace("/^(.+)\.php$/", "\\1.blade.php", $template_name);

            if (file_exists($path = STYLESHEETPATH . '/Resources/views/' . $name) || (TEMPLATEPATH != STYLESHEETPATH && file_exists($path = TEMPLATEPATH . '/Resources/views/' . $name))) {
                $located = $path;
                break;
            } elseif (file_exists(STYLESHEETPATH . '/' . $name)) {
                $located = STYLESHEETPATH . '/' . $name;
                break;
            } elseif (TEMPLATEPATH != STYLESHEETPATH && file_exists(TEMPLATEPATH . '/' . $name)) {
                $located = TEMPLATEPATH . '/' . $name;
                break;
            }
        }


        if (file_exists($path = STYLESHEETPATH. '/Resources/views/' . $template_name) || (STYLESHEETPATH != TEMPLATEPATH && file_exists($path = TEMPLATEPATH. '/Resources/views/' . $template_name))) {
            $located = $path;
            break;
        } elseif (file_exists(STYLESHEETPATH . '/' . $template_name)) {
            $located = STYLESHEETPATH . '/' . $template_name;
            break;
        } elseif (TEMPLATEPATH != STYLESHEETPATH && file_exists(TEMPLATEPATH . '/' . $template_name)) {
            $located = TEMPLATEPATH . '/' . $template_name;
            break;
        }
    }

    if ($load && '' != $located) {
        load_template($located, $require_once);
    }

    if (preg_match("/\.blade\.php$/", $located)) {
        //kd(load_template($located, null));
    }

    return preg_match("/\.blade\.php$/", $located) ? load_template($located, null) : $located;
}

/**
 * Require the template file with WordPress environment.
 *
 * The globals are set up for the template file to ensure that the WordPress
 * environment is available from within the function. The query variables are
 * also available.
 *
 * @since 1.5.0
 *
 * @param string $_template_file Path to template file.
 * @param bool   $require_once Whether to require_once or require. Default true.
 */
function load_template($_template_file, $require_once = true)
{
    global $posts, $post, $wp_did_header, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;

    if (is_array($wp_query->query_vars)) {
        extract($wp_query->query_vars, EXTR_SKIP);
    }



    if ($require_once === null) {
        return $_template_file;
    }

    extract(View::getShared(), EXTR_REFS);

    if ($isBlade = preg_match("/\.blade\.php$/", $_template_file)) {
        $tplfile = str_replace(['Resources/views/','.blade.php'],'',substr($_template_file,strlen(get_template_directory())+1));
        //echo View::make($tplfile)->with(array_except(get_defined_vars(), array('__data', '__path')));
        echo View::file($_template_file)->with(array_except(get_defined_vars(), array('__data', '__path')) );
        return '';
    }

    if ($require_once) {
        require_once($_template_file);
    } else {
        require($_template_file);
    }
}

