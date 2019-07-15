<?php
/**
 * Plugin Name: WP City Name
 * Description: Plugin add names of Region and Cities like taxonomy terms
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
//////////////////////////////////////////////////////////////////////////////////////////////

// require_once( dirName(__FILE__). '/ChromePhp.php' );//расширение для вывода php переменных в консоль Chrome
//include( dirName(__FILE__). '/ChromePhp.php' );//расширение для вывода php переменных в консоль Chrome
//ДЕБАГ - НЕ ЗАБУДЬ ЗАКОМЕНТИТЬ///////////////////////////////////////////////////////////////////////////////

function wp_cn_install_notice() {
	?>
	<div class="notice notice-success is-dismissible">
		<p>Для завершения установки плагина перейдите в настройки "Города и Регионы"</p>
	</div>
	<?php
}

 function wp_cn_install(){	
    add_action( 'admin_notices', 'wp_cn_install_notice' );	
 }
//хук активации (установки) плагина
register_activation_hook( __FILE__, 'wp_cn_install' );


//add menu item
add_action('admin_menu', 'wp_cn_add_menu_item');

function wp_cn_add_menu_item() {

	//create new top-level menu
	add_menu_page('Настройки плагина Города и Регионы', 'Города и Регионы', 'administrator', 'wp_cn_settings', 'wp_cn_admin');

	//call register settings function
	add_action( 'admin_init', 'wp_cn_register_settings' );
}


function wp_cn_register_settings() {
	//register our settings
    register_setting( 'wp_cn_option_group', 'wp_cn_opt_post_type' );
    register_setting( 'wp_cn_option_group', 'wp_cn_opt_names_added' );
    
}

//add js and css
add_action( 'admin_enqueue_scripts', 'wp_cn_assets_add' );
function wp_cn_assets_add($hook){
    $screen = get_current_screen();
	//if ( $hook == 'wp_cn_settings' ) {
    if ( strpos($screen->base, 'wp_cn_settings') === false){
        return;
	}
    wp_enqueue_script('scriptoffser.js', plugins_url('wp-city-name/js/scriptoffset.js'));
    wp_localize_script( 'scriptoffser.js', 'site_url', get_site_url());
    wp_enqueue_style( 'scriptoffset', plugins_url('wp-city-name/css/scriptoffset.css'));
}



/////////////////////////////////////////////////////////////////////////////////////////////////////////
// Регистрация таксономии Города и Регионы:
add_action( 'init', 'wp_cn_city_taxonomy', 0 );
function wp_cn_city_taxonomy () {
	$args = array(
		// Название таксономии
		'label' => _x( 'Города и Регионы', 'taxonomy general name' ), 
		
		// Значения таксономии в админ-панели:
		'labels' => array(
		// Общее название таксономии (множественное число). 
		// По умолчанию: 'МеткИ' или 'РубрикИ'
		'name' => _x( 'Города и Регионы', 'taxonomy general name' ), 
		// Название таксономии (единственное число). 
		// По умолчанию: 'МеткА' или 'РубрикА'
		'singular_name' => _x( 'Город или Регион', 'taxonomy singular name' ), 
		// Название таксономии в пункте меню. 
		'menu_name' => __( 'Города и Регионы' ), 
		// По умолчанию: 'Все метки' или 'Все рубрики'
		'all_items' => __( 'Все Города и Регионы' ),
		// Текст изменения таксономии на странице редактирования. 
		// По умолчанию: 'Изменить метку' или 'Изменить рубрику'
		'edit_item' => __( 'Изменить Город (Регион)' ), 
		// Текст в админ-панели на странице редактирования данной таксономии.
		// По умолчанию: 'Просмотреть метку' или 'Просмотреть рубрику'
		'view_item' => __( 'Просмотреть Город (Регион)' ), 
		// Текст обновления таксономии во вкладке свойства. 
		// По умолчанию: 'Обновить метку' или 'Обновить рубрику'
		'update_item' => __( 'Обновить Город (Регион)' ), 
		// Текст добавления новой таксономии при ее создании. 
		// По умолчанию: 'Добавить новую метку' или 'Добавить новую рубрику'
		'add_new_item' => __( 'Добавить Город (Регион)' ), 
		// Название таксономии при ее создании и редактировании. 
		// По умолчанию: 'Название'
		'new_item_name' => __( 'Название' ), 
		// Текст родительской таксономии при создании и редактировании. 
		// Для древовидных таксономий. 
		// По умолчанию: Родительская.
		'parent_item' => __( 'Родительская' ),
		// То же, что и parent_item, но с добавлением двоеточия. 
		// По умолчанию: 'Родительская:'
		'parent_item_colon' => __( 'Родительская:' ), 
		// Текст в кнопке поиска на странице всех таксономий. 
		// По умолчанию: 'Поиск меток' или 'Поиск рубрик'
		'search_items' => __( 'Поиск Города (Региона)' ),
		
		// ЧЕТЫРЕ НИЖНИХ параметра НЕ используется для древовидных таксономий:
		// Надпись популярных таксономий (на странице всех таксономий). 
		// По умолчанию: Популярные метки или null.
		'popular_items' => null, 
		// Надпись разделения таксономий запятыми в метабоксе. 
		// По умолчанию: Метки разделяются запятыми или null.
		'separate_items_with_commas' => null, 
		// Надпись добавления или удаления таксономий в метабоксе когда JavaScript отключен. 
		// По умолчанию: Добавить или null.
		'add_or_remove_items' => null, 
		// Текст выбора из часто используемых таксономий в метабоксе. 
		// По умолчанию: Выбрать из часто используемых или null.
		'choose_from_most_used' => null, 
		
		// Текст в случае, если запрашиваемая таксономия не найдена. 
		// По умолчанию: 'Меток не найдено. или 'Рубрик не найдено.
		'not_found' => __( 'Город (Регион) не найден.' ), 
		),
		// Если true, то таксономия становится доступной для использования.
		'public' => true, 
		// Доступность таксономии для управления в админ-панели, но не показывает ее в меню. 
		// По умолчанию: 'public'.
		'show_ui' => true, 
		// Показывать таксономию в админ-меню. 
		// Значение аргумента 'show_ui' должно быть true. 
		// По умолчанию: значение аргумента 'show_ui'.
		'show_in_menu' => true, 
		// Добавляет или исключает таксономию в навигации сайта "Внешний вид -> Меню"
		// По умолчанию: 'public'.
		'show_in_nav_menus' => true, 
		// Позволяет виджет 'Облако меток' использовать в таксономии. 
		// По умолчанию: 'show_ui'.
		'show_tagcloud' => false, 
		// Показ таксономии в меню быстрого доступа. 
		// По умолчанию: 'show_ui'.
		'show_in_quick_edit' => true, 
		// Обеспечивает показ метабокса с таксономией в записи. По умолчанию: null.
		'meta_box_cb' => null, 
		// Позволяет автоматическое создание столбцов таксономии в таблице ассоциативных типов постов. 
		// По умолчанию: false.
		'show_admin_column' => false,
		// Подключает описание таксономии в таблице со всеми таксономиями. По умолчанию: ''
		'description' => '', 
		// Делает таксономию древовидной как рубрики или недревовидной как метки. По умолчанию: false.
		'hierarchical' => true, 
		// Название функции, вызываемая после обновления ассоциативных типов объектов записи (поста)
		// Действует во многом как хук. 
		// По умолчанию: ''.
		'update_count_callback' => '', 
		// Значение запроса. По умолчанию: true.
		'query_var' => true, 
		'show_in_rest' => true,
		// Перезапись URL. По умолчанию: true.
		'rewrite' => array(
		// Текст в ЧПУ. По умолчанию: название таксономии.
		'slug' => 'city', 
		// Позволяет ссылку добавить к базовому URL.
		'with_front' => true,
		// Использовать (true) или не использовать (false) древовидную структуру ссылок. 
		// По умолчанию: false.
		'hierarchical' => true, 
		// Перезаписывает конечное значение таксономии. По умолчанию: EP_NONE.
		'ep_mask' => EP_NONE, 
		),
		
		/*
		// Массив полномочий зарегестрированных пользователей:
		'capabilities' => array(
		'manage_terms' => 'manage_resource',
		'edit_terms'   => 'manage_categories',
		'delete_terms' => 'manage_categories',
		'assign_terms' => 'edit_posts',
		),
		*/
		
		// Должна ли таксономия запоминать порядок, в котором посты были созданы. 
		// По умолчанию: null.
		'sort' => null, 
		// Является ли таксономия собственной или встроенной. 
		// Рекомендация: не использовать этот аргумент при регистрации собственной таксономии. 
		// По умолчанию: false.
		'_builtin' => false, 
		);
	// Названия типов записей к которым будет привязана таксономия
	register_taxonomy( 'wp_cn_city', array(get_option('wp_cn_opt_post_type','wp_cn_post_type')), $args );
}



// settings page
function wp_cn_admin() {
?>
<div class="wrap">
<h2><?php echo get_admin_page_title() ?></h2>
<form name="wp_cn_settings_form" id="wp_cn_settings_form" method="post" action="options.php">
    <?php settings_fields( 'wp_cn_option_group' ); ?>
    <p class="wp_cn_names_added" >
        <?php   
        $names_added = get_option('wp_cn_opt_names_added', 'false');
        if ($names_added=='false'){
            echo "<div class='notice notice-warning is-dismissible'><p>Внимание! Для продолжения установки плагина, нажмите кнопку Загрузить и дождитесь окончания загрузки</p></div>";
        }
        ?>
    </p>
    <div class="wp_cn_set_post_type_wrapper">
    <div class="wp_cn_set_post_type_label">Тип записи: </div>
        <div class="wp_cn_set_post_type"><input type="text" name="wp_cn_opt_post_type" value="<?php echo get_option('wp_cn_opt_post_type'); ?>" /></div>
        <input type="hidden" id="wp_cn_opt_names_added" name="wp_cn_opt_names_added" value="<?php echo get_option('wp_cn_opt_names_added', 'false'); ?>">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </div>
    <div class="wp_cn_file_uploads">
        <div class="form">
        <input type="hidden" id="wp_cn_city_url" name='wp_cn_city_url' value="<?php echo plugin_dir_url( __FILE__ ).'city/city.csv' ?>">
        <input type="hidden" id="wp_cn_region_url" name='wp_cn_region_url' value="<?php echo plugin_dir_url( __FILE__ ).'city/region.csv' ?>">
        <input id="offset" name="offset" type="hidden">

        <div class="progress" style="display: none;">
            <div class="bar" style="width: 0%;"></div>
        </div>
        <?php
         if ($names_added=='false'){
        ?>
        <div class="wp_cn_set_start_label">Списки городов и регионов не загружены: </div> <a href="#" id="runScript"  class="button-primary" data-action="run">Загрузка</a>
        <!-- <a href="#" id="refreshScript" class="button-primary" style="display: none;">Заново</a> -->
        <?php } ?>
        </div>
    </div>

</form>
</div>
<?php 
}



add_action( 'wp_ajax_wp_cn_ajax_response', 'wp_cn_ajax_response' );
add_action( 'wp_ajax_nopriv_wp_cn_ajax_response', 'wp_cn_ajax_response' );

function wp_cn_ajax_response(){
    // Можно передавать в скрипт разный action и в соответствии с ним выполнять разные действия.  
    $action = $_POST['act'];
    if (empty($action)) {
        return;
    }
    
    $region = $_POST['region_url'];
    if (empty($region)){
        return;
    }
    
    $city = $_POST['city_url'];
    if (empty($city)){
        return;
    }

    // Получаем от клиента номер итерации
    $offset = $_POST['offset'];
    
    $file = file($region);
    $file_city=file( $city);
    $count = count($file);
    $count_city= count($file_city);
    $step = 1;
    $step_city = 50;
    $data_region=wp_cn_parse_csv_file ($region, ';', $offset, $step);
    $data_city=wp_cn_parse_csv_file ($city, ';', 0, $count_city);
    
    if (taxonomy_exists('wp_cn_city')){
        for ($i=0; $i<count($data_region); $i++){
            $new_region = wp_insert_term( $data_region[$i][1], 'wp_cn_city', array(
                'description' => '',
                'parent'      => 0,
                'slug'        => '',
            ) );
            if (gettype($new_region)=='array'){
                //Если вернул id Нового термина таксономии
                for ($c=0; $c<count($data_city); $c++){
                    if ($data_city[$c][1]==$data_region[$i][0]){//Если region_id как у вновь созданного региона
                        wp_insert_term( $data_city[$c][2], 'wp_cn_city', array(
                            'description' => '',
                            'parent'      => $new_region['term_id'],
                            'slug'        => '',
                        ) );
                    }
                }
            }
        }
    }else{
        ChromePhp::log("Нету термина таксы?"); 
    }

    // Проверяем, все ли строки обработаны
    $offset += $step;
    if ($offset >= $count) {
        $sucsess = 1;
    } else {
        $sucsess = round($offset / $count, 2);
    }

    // И возвращаем клиенту данные (номер итерации и сообщение об окончании работы скрипта)
    $output = Array('offset' => $offset, 'sucsess' => $sucsess);
    wp_send_json($output);
}

//Парсим csv в массив оперделенного количества
function wp_cn_parse_csv_file ($file_path, $col_delimiter, $line_start, $count_lines){
	if (($handle = fopen($file_path, "r")) !== FALSE) {
        $data_arr=[];
        $line_num=0;
        $line_end=$line_start + $count_lines;
		while (($data = fgetcsv($handle, 1000, $col_delimiter)) !== FALSE) {
            if (($line_num>=$line_start)&&($line_num<$line_end)){
                $data_arr[]=$data;
            }
            $line_num++;
		}
		fclose($handle);
		return $data_arr;
	}
}

