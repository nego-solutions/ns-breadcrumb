<?php

if (!defined('FW')) die('Forbidden');


class FW_Extension_ns_breadcrumb extends FW_Extension{


	public function _init(){

		add_action('ns_show_breadcrumb', array($this, 'output_html'));

	}




	/**
	 * Defaul options. They can be changed as you need by using add_filter
	 * in your development eviromment
	 * @param  string $opt
	 * @return string | void
	 */
	private function set_opt($opt=''){

		$el_prefix = fw_get_db_ext_settings_option('ns-breadcrumb', 'prefix-label') != '' ? '<li><a href="'.home_url().'">'.fw_get_db_ext_settings_option('ns-breadcrumb', 'prefix-label').'</a></li>' : '';

		$set_opt = apply_filters('bdb_set_opt', array(
			'title_length' => fw_get_db_ext_settings_option('ns-breadcrumb', 'title-length', 40),
			'category'  => fw_get_db_ext_settings_option('ns-breadcrumb', 'category-label', 'Category:').' ',
			'tags'      => fw_get_db_ext_settings_option('ns-breadcrumb', 'tags-label', 'Posts Tagged:').' ',
			'search'    => fw_get_db_ext_settings_option('ns-breadcrumb', 'search-label', 'Search Results for:').' ',
			'author'    => fw_get_db_ext_settings_option('ns-breadcrumb', 'author-label', 'Archived Article(s) by Author:').' ',
			'error'     => fw_get_db_ext_settings_option('ns-breadcrumb', 'error-label', 'Error 404 - Not Found').' ',
			'el_prefix' => $el_prefix,
		));

		if(!empty($opt)){
			return $set_opt[$opt];
		}

		return;
	}




	/**
	 * This checks on which page we're and display the correct breancrumb navigation
	 * @return string | void
	 */
	public function output_html(){

		global $post, $cat, $wp_query;

		$output = '';
		$arc_year = get_the_time('Y');
		$arc_month = get_the_time('F');
		$arc_day = get_the_time('d');
    	$arc_day_full = get_the_time('l');
    	$url_year = get_year_link($arc_year);
    	$url_month = get_month_link($arc_year,$arc_month);

    	$templates = array_keys(fw_get_db_ext_settings_option('ns-breadcrumb', 'hide-bdb-templates', array()));
		$types = array_keys(fw_get_db_ext_settings_option('ns-breadcrumb', 'hide-bdb-types', array()));


		if(in_array(get_post_type(), $types)){

			return;
		}


		if (!is_front_page()) {

			//breadcrumb for single post
			if (is_single() && !in_array('single', $templates)) {
				$output = $this->is_single_post();
			}
			//breadcrumb for category and sub-category archive
			elseif(is_category() && !in_array('category', $templates)){
				$output = '<li>'.$this->set_opt('category') . get_category_parents($cat, true, ' &raquo; ').'</li>';
			}
			//breadcrumb for taxomonies
			elseif(is_tax() && !in_array('category', $templates)){

				$term =	$wp_query->queried_object;
				$output = '<li>'.$this->set_opt('category') . $term->name.'</li>';
			}
			//breadcrumb for tag archive
			elseif ( is_tag() && !in_array('tag', $templates)) {

	            $output = '<li>'.$this->set_opt('tags') . single_tag_title('', false).'</li>';
	        }
	        //breadcrumb for calendar (day, month, year) archive
	        elseif ( is_day() && !in_array('archive', $templates)) {

	            $output = '<li><a href="' . $url_year . '">' . $arc_year . '</a></li>';
	            $output .= '<li><a href="' . $url_month . '">' . $arc_month . '</a></li><li>'. $arc_day . ' (' . $arc_day_full . ')</li>';
	        }
	        elseif ( is_month() && !in_array('archive', $templates)) {

	            $output = '<li><a href="' . $url_year . '">' . $arc_year . '</a></li><li>'. $arc_month.'</li>';
	        }
	        elseif ( is_year() && !in_array('archive', $templates)) {

	            $output = '<li>'.$arc_year.'</li>';
	        }
	        //breadcrumb for search result page
	        elseif ( is_search() && !in_array('search', $templates)) {

	            $output = '<li>'.$this->set_opt('search') . get_search_query().'</li>';
	        }
	        //breadcrumb for top-level pages (top-level menu)
	        elseif ( is_page() && !$post->post_parent && !in_array('page', $templates)) {

	            $output = '<li>'.get_the_title().'</li>';
	        }
	        //breadcrumb for top-level pages (top-level menu)
	        elseif ( is_page() && $post->post_parent && !in_array('page', $templates)) {

	        	$output = $this->is_page_n_has_parent();
	        }
	        //breadcrumb for author archive
	        elseif ( is_author() && !in_array('author', $templates)) {

	            global $author;

	            $user_info = get_userdata($author);
	            $output = '<li>'.$this->set_opt('author') . $user_info->display_name.'</li>';
	        }
	        //Display breadcrumb for 404 Error
	        elseif ( is_404() && !in_array('404', $templates)) {

	            $output = '<li>'.$this->set_opt('error').'</li>';
	        }
	        else {
	            //All other cases no Breadcrumb trail.
	        }
		}


		if(!empty($output)){

			$return = '<ul class="breadcrumb">';
				$return .= $this->set_opt('el_prefix');
				$return .= $output;
			$return .='</ul>';

			echo $return;
		}

	}




	/**
	 * If we're on a single post
	 * @return string
	 */
	private function is_single_post(){
		global $post, $wpdb;

		$output = '';
		$title = (strlen(get_the_title()) >= $this->set_opt('title_length')) ? trim(substr(get_the_title(), 0, $this->set_opt('title_length'))) . '...' : get_the_title();
		$default_types = array('post', 'page', 'attachment');


	    //check if the current type is not one of the default
	    if(!in_array(get_post_type(), $default_types)){

	    	$get_obj = $wpdb->get_row( $wpdb->prepare("
	    		SELECT t.* FROM {$wpdb->prefix}term_relationships AS t WHERE t.object_id = %s",
	    		$post->ID
	    	));

	    	$get_tax = $wpdb->get_row( $wpdb->prepare("
	    		SELECT t.* FROM {$wpdb->prefix}term_taxonomy AS t WHERE t.term_id = %s LIMIT 1",
	    		$get_obj->term_taxonomy_id
	    	));

    		$taxonomy = $get_tax->taxonomy;

	        $terms = wp_get_object_terms($post->ID, $taxonomy);

	        //echo '<pre>'.print_r($get_tax, true).'</pre>';

	        //if we have more than one term
	        if(count($terms) > 1){
	            $term_list = array();

	            foreach ($terms as $term) {
	                $term_list[] = '<li><a href="'.get_term_link( $term).'">'.$term->name.'</a></li>';
	            }

	            $output = implode('', $term_list);
	            $output .= '<li>'.$title.'</li>';

	        }elseif(count($terms) ==1){

	            $term_name = $terms[0]->name;
	            $term_id = $terms[0]->term_id;

	            $output = '<li><a href="'.get_term_link( $terms[0]).'">'.$term_name.'</a></li><li>'.$title.'</li>';
	        }

	    //then it means we're on the default post type
	    }else{
	        $category = get_the_category();
	        $num_cat = count($category);

	        if ($num_cat <=1){



	            $output = '<li>'.get_category_parents($category[0],  true, '').'</li><li>'. $title.'</li>';
	        }else {

	            $output = strip_tags(get_the_category_list(), '<li><a>');
                $output .= '<li>'.$title.'</li>';
	        }
	    }


	    return $output;

	}



	/**
	 * If we're on a page & it has parents
	 * @return string
	 */
	private function is_page_n_has_parent(){
		global $post;

		$output = '';
		$title = (strlen(get_the_title()) >= $this->set_opt('title_length')) ? trim(substr(get_the_title(), 0, $this->set_opt('title_length'))) . '...' : get_the_title();

        //get_post_ancestors() returns an indexed array containing the list of all the parent categories.
        $post_array = get_post_ancestors($post);

        //Sorts in descending order by key, since the array is from top category to bottom.
        krsort($post_array);

        //Loop through every post id which we pass as an argument to the get_post() function.
        foreach($post_array as $key=>$postid){
            $post_ids = get_post($postid);

            $output = '<li><a href="' . get_permalink($post_ids) . '">' . $post_ids->post_title . '</a></li>';
        }
        $output .= '<li>'.$title.'</li>';


        return $output;
	}

}