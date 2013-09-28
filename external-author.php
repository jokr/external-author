<?php
/*
Plugin Name: External Author
Plugin URI: http://www.aleaiactaest.ch
Description: Allows publishing of posts with an author possible who does not have a wordpress account.
Version: 0.1
Author: Joel Krebs
Author URI: http://www.aleaiactaest.ch
License: GPL2
*/

include_once dirname( __FILE__ ) . '/class-external-author.php';

if ( class_exists( 'External_Author' ) && is_admin() ) {
	new External_Author();
}