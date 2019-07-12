<?php
/**
 * Plugin Name: MotoHome
 * Description: Плагин бронирования мотодомов: добавление, бронирование, управление.
 * Plugin URI:  https://github.com/chudomozg/wp-city-name
 * Author URI:  https://github.com/chudomozg
 * Author:      chudomozg (Иван Тимошенко)
 * Version:     1.0
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * Network:     false.
 */
/*  Copyright 2019  Ivan Timoshenko  (email: chudomozg@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//ДЕБАГ - НЕ ЗАБУДЬ ЗАКОМЕНТИТЬ///////////////////////////////////////////////////////////////////////////////
if( WP_DEBUG && WP_DEBUG_DISPLAY && (defined('DOING_AJAX') && DOING_AJAX) ){
    @ ini_set( 'display_errors', 1 );
}

// require_once( dirName(__FILE__). '/ChromePhp.php' );//расширение для вывода php переменных в консоль Chrome
include( dirName(__FILE__). '/include/ChromePhp.php' );//расширение для вывода php переменных в консоль Chrome
//ДЕБАГ - НЕ ЗАБУДЬ ЗАКОМЕНТИТЬ///////////////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////////////////////////////////////

function wp_cn_install_notice() {
	?>
	<div class="notice notice-success is-dismissible">
		<p>Настройки обновлены!</p>
	</div>
	<?php
}

 function wp_cn_install(){	
	add_action( 'admin_notices', 'wp_cn_install_notice' );	
 }
//хук активации (установки) плагина
register_activation_hook( __FILE__, 'wp_cn_install' );

//add menu item
add_action( 'admin_menu', 'wp_cn_add_menu_item' );

function wp_cn_add_menu_item (){
	add_menu_page(
		'Настройки Городов и Регионов', // Название страниц (Title)
		'Города и Регионы', // Текст ссылки в меню
		'manage_options', // Требование к возможности видеть ссылку 
		'wp-city-name/include/mfp-first-acp-page.php' // 'slug' - файл отобразится по нажатию на ссылку
	);
}




//добавление Городов и Регионов из CSV файлов
// add_action( 'init', 'mth_add_city' );
// function mth_add_city (){
// 	if (taxonomy_exists('mth_city')){
// 		//загружаем список Регионов
// 		//$region = mth_parse_csv_file( dirName(__FILE__).'/city/region.csv', ';');
// 		$region = mth_parse_csv_file( 'http://t1.imake.site/wp-content/uploads/2019/07/region.csv', ';');
// 		//загружаем список Городов
// 		$city = mth_parse_csv_file( 'http://t1.imake.site/wp-content/uploads/2019/07/city.csv', ';');
// 		ChromePhp::log('hello world');
// 		//ChromePhp::log($region);
// 		ChromePhp::log(dirName(__FILE__).'/city/region.csv');

// 		for ($i=1; $i<count($region); $i++){
// 			if ($region[$i][1]=="3159"){//Если номер страны = Россия
// 				$new_region = wp_insert_term( $region[$i][3], 'mth_city', array(
// 					'description' => '',
// 					'parent'      => 0,
// 					'slug'        => '',
// 				) );
// 			}
// 			if (gettype($new_region)=='array'){
// 				//Если вернул id Нового термина таксономии
// 				for ($c=1; $c<count($city); $c++){
// 					if ($city[$c][2]==$region[$i][0]){//Если region_id как у вновь созданного региона
// 						wp_insert_term( $city[$c][3], 'mth_city', array(
// 							'description' => '',
// 							'parent'      => $new_region['term_id'],
// 							'slug'        => '',
// 						) );
// 					}
// 				}
// 			}
// 		}
		
// 	}else{
// 		echo "Таксономия Города отсутствует!";
// 	}
// }

