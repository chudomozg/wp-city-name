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
}

//add js and css
add_action( 'admin_enqueue_scripts', 'wp_cn_assets_add' );
function wp_cn_assets_add(){
    wp_enqueue_script('scriptoffser.js', plugins_url('wp-city-name/js/scriptoffset.js'));
   // wp_localize_script( 'mt_rooms.js', 'site_url', get_site_url());
   wp_enqueue_style( 'scriptoffset', plugins_url('wp-city-name/css/scriptoffset.css'));
}

// settings page
function wp_cn_admin() {
?>
<div class="wrap">
<h2><?php echo get_admin_page_title() ?></h2>

<form method="post" action="options.php">
    <?php settings_fields( 'wp_cn_option_group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Тип записи к которому будет создана таксономия Городов и Регионов</th>
        <td><input type="text" name="wp_cn_opt_post_type" value="<?php echo get_option('wp_cn_opt_post_type'); ?>" /></td>
        </tr>
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
    <div class="wp_cn_file_uploads">
        <div class="form">
        <input id="url" name="url">
            <input id="offset" name="offset" type="hidden">

            <div class="progress" style="display: none;">
                <div class="bar" style="width: 0%;"></div>
            </div>

            <a href="#" id="runScript"  class="btn" data-action="run">Старт</a>
            <a href="#" id="refreshScript" class="btn" style="display: none;">Заново</a>
        </div>
    </div>

</form>
</div>
<?php }



add_action( 'wp_ajax_wp_cn_ajax_response', 'wp_cn_ajax_response' );
add_action( 'wp_ajax_nopriv_wp_cn_ajax_response', 'wp_cn_ajax_response' );

function wp_cn_ajax_response(){
    // Можно передавать в скрипт разный action и в соответствии с ним выполнять разные действия.
    $action = $_POST['act'];
    //ChromePhp::log("Дошли до php");//дебаг
    if (empty($action)) {
        ChromePhp::log("empty action");//дебаг
        return;
    }

    //$region = 'http://t1.imake.site/wp-content/uploads/2019/07/region.csv';
    $region = plugin_dir_url( __FILE__ ).'city/region.csv';
    //$city = 'http://t1.imake.site/wp-content/uploads/2019/07/city.csv';
    $city = plugin_dir_url( __FILE__ ).'city/city.csv';
    ChromePhp::log("region: ".$region);//дебаг
    ChromePhp::log("city: ".$city);//дебаг
    $url = $_POST['url']; if (empty($url)){
        ChromePhp::log("empty url");//дебаг
        return;
    } 
    //$url = $region;
    // Получаем от клиента номер итерации
    $offset = $_POST['offset'];
    // $count = 50;
    $file = file( $url);
    $count = count($file);
    $step = 10;

    ChromePhp::log("file count: ".$count);//дебаг
    ChromePhp::log("url: ".$url);//дебаг
    ChromePhp::log("line start: ".$offset);//дебаг
    $data=wp_cn_parse_csv_file ($url, ';', $offset, $step);
    ChromePhp::log($data);//дебаг

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

