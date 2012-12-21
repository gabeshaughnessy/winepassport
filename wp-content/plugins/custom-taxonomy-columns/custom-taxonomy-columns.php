<?php
/*
Plugin Name: Custom Taxonomy Columns
Plugin URI: http://www.mattvanandel.com/
Description: Adds custom taxonomy terms to admin list tables.
Version: 1.0
Author: Matt Van Andel
Author URI: http://www.mattvanandel.com
License: GPL2
*/
/*  Copyright 2011  Matt VanAndel  (email : matt@mattvanandel.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/************************* HOOKS *********************************/
add_action('admin_init','taxocol_admin_init');


/*********************** FUNCTIONS **********************************/
/**
 * Handles hooks for list-table columns, by post type
 */
function taxocol_admin_init(){
    //Only fetch custom post types
    $post_types = get_post_types( /*array('_builtin'=>false)*/ );

    //For each post type, add the following columns
    foreach($post_types as $post_type){
        add_filter('manage_'.$post_type.'_posts_columns','taxocol_taxlist_th');
        add_action('manage_'.$post_type.'_posts_custom_column','taxocol_taxlist_td',10,2); //Priority 10, Takes 2 args (use default priority only so we can specify args)
    }
}

/**
 * Creates a header for the taxonomy column.
 *
 * @param type $columns
 * @return type
 */
function taxocol_taxlist_th($columns){
    global $post_type;

    //Get all the taxonomies for this post type (object_type is partially broken)
    $postax = get_taxonomies( array( '_builtin'=>false,'object_type'=>array( $post_type ) ), 'objects' );

    //Loop through the taxonomies and add them to the thead
    foreach($postax as $tax){

        //Ensure this taxonomy is associated with this post type...
        //This is necessary because the 'object_type' arg filter in get_taxonomies() is flat-out broken)
        if(!in_array($post_type, $tax->object_type)){
           continue;
        }

        //Get the offset for inserting the new column (ie: insert after author... author+1)
        $offset = 1+array_search('author',array_keys($columns));

        //Split the original array and place the new value at the specified position
        $columns = array_slice($columns,0,$offset,true)
             + array($tax->name=>"<div class='$post_type {$tax->name}'>{$tax->labels->name}</div>")
             + array_slice($columns,$offset,NULL,true);
    }

    return $columns;
}


/**
 * Creates row-specific content for the taxonomy column.
 *
 * @param type $col_name
 * @param type $post_id
 */
function taxocol_taxlist_td($col_name, $post_id){
    global $post_type;      //Get from global
    $post_terms = array();  //Initialize
    $tax = $col_name;       //Just for readability

    //We need the array so we can check against it to ensure we have a correct column
    $postax = get_taxonomies( array( '_builtin'=>false,'object_type'=>array( $post_type ) ), 'objects' );

    //Only run if this is a correct taxonomy column (otherwise, this will be run on ALL custom columns)
    if(array_key_exists($tax, $postax)){
        //Get all the terms attached to the current post and taxonomy
        $terms = get_the_terms($post_id, $tax);

        //If the terms array contains items... (dupe of core)
        if ( !empty($terms) ) {
            //Loop through terms
            foreach ( $terms as $term ){
                //Add tax name & link to an array
                $post_terms[] = "<a href='edit.php?post_type={$post_type}&{$tax}={$term->slug}'> " . esc_html(sanitize_term_field('name', $term->name, $term->term_id, $tax, 'edit')) . "</a>";
            }
            //Spit out the array as CSV
            echo implode( ', ', $post_terms );
        } else {
            //Text to show if no terms attached for post & tax
            echo '<em>No terms</em>';
        }
    }
}

?>