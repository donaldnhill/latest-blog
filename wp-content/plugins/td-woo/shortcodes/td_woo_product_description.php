<?php

/**
 * Class td_woo_product_description - shortcode for woocommerce single product page description
 */
class td_woo_product_description extends td_block {

    private static $read_more_enabled = false;

    public function get_custom_css() {
        // $unique_block_class
        $unique_block_class = $this->block_uid;

        $compiled_css = '';

        $raw_css =
            "<style>

                /* @style_general_td_woo_product_description */
                .td_woo_product_description blockquote {
                    padding: 0;
                    position: relative;
                    border-left: none;
                    margin: 40px 5% 38px;
                    font-family: var(--td_default_google_font_2, 'Roboto', sans-serif);
                    font-size: 32px;
                    line-height: 40px;
                    font-weight: 400;
                    text-transform: uppercase;
                    font-style: italic;
                    text-align: center;
                    color: var(--td_theme_color, #4db2ec);
                    word-wrap: break-word;
                }
                .td_woo_product_description blockquote.td_quote_left {
                    float: left;
                    width: 50%;
                    margin: 18px 18px 18px 0;
                    text-align: left;
                }
                .td_woo_product_description blockquote.td_quote_right {
                    float: right;
                    width: 50%;
                    margin: 21px 0 21px 21px;
                }
                .td_woo_product_description blockquote.td_quote_box {
                    margin: 0;
                    background-color: #FCFCFC;
                    border-left: 2px solid var(--td_theme_color, #4db2ec);
                    padding: 15px 23px 16px 23px;
                    position: relative;
                    top: 6px;
                    font-family: var(--td_default_google_font_1, 'Open Sans', 'Open Sans Regular', sans-serif);
                    color: #777;
                    font-size: 13px;
                    line-height: 21px;
                    text-transform: none;
                    clear: both;
                }
                .td_woo_product_description blockquote.td_box_center {
                    margin: 0 0 29px 0;
                }
                .td_woo_product_description blockquote.td_box_left,
                .td_woo_product_description blockquote.td_box_right {
                    text-align: left;
                }
                .td_woo_product_description blockquote.td_box_left {
                    width: 40%;
                    float: left;
                    margin: 0 34px 20px 0;
                }
                .td_woo_product_description blockquote.td_box_right {
                    width: 40%;
                    float: right;
                    margin: 0 34px 20px 0;
                }
                .td_woo_product_description blockquote.td_pull_quote {
                    padding: 18px 25px;
                    margin: 0;
                    clear: both;
                    font-family: var(--td_default_google_font_1, 'Open Sans', 'Open Sans Regular', sans-serif);
                    font-size: 14px;
                    line-height: 26px;
                    font-weight: 600;
                    text-transform: none;
                    text-align: center;
                }
                .td_woo_product_description blockquote.td_pull_quote:before,
                .td_woo_product_description blockquote.td_pull_quote:after {
                    content: '';
                    position: absolute;
                    display: block;
                    width: 15px;
                    height: 15px;
                    box-sizing: border-box;
                    -webkit-box-sizing: border-box;
                }
                .td_woo_product_description blockquote.td_pull_quote:before {
                    left: 0;
                    background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA8AAAALBAMAAABSacpvAAAALVBMVEUAAAC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLRK0HxpAAAADnRSTlMAd+67mWZR3SKqMxGIzB8/1rAAAABlSURBVAjXFcexDQEBAAXQd+KCRm4CDZURFGICMYFadTHBxQQmEDHCzWAI9XGJ8s/ANS95FBvccKwYr5kuUQ/5omm5dpQ9Fu+H2efEPX07Sg62f+bJ2T6pJkmnTi5FslM2L56r9geMACBhjTsodgAAAABJRU5ErkJggg==) no-repeat;
                }
                .td_woo_product_description blockquote.td_pull_quote:after {
                    right: 0;
                    background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA8AAAALBAMAAABSacpvAAAALVBMVEUAAAC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLRK0HxpAAAADnRSTlMA3ZnuqndmIhG7VYhEMzOiL2oAAABkSURBVAjXY+D1E2PgULZuYGB89+4A07t3AQzn3r1T4Hv3ToCh7t27CUDRBwxAYQe2d+8MGBiuAuWr5BwYGBjeFTAwzEtgYOB6xMDA8RAowGnOwMD6CsjIA4oWKwBFXYGcLQ0MAFHHH+tW1OhlAAAAAElFTkSuQmCC) no-repeat;
                }
                .td_woo_product_description blockquote.td_pull_center {
                    margin: 17px 0;
                    padding: 15px 50px;
                }
                .td_woo_product_description blockquote.td_pull_left {
                    width: 40%;
                    margin-right: 34px;
                    float: left;
                }
                .td_woo_product_description blockquote.td_pull_right {
                    width: 30%;
                    margin-left: 24px;
                    float: right;
                }
                .td_woo_product_description p:empty {
                    display: none;
                }
                
                /* @style_general_td_wpd_read_more */
                .td_woo_product_description.tdw-pd-content-collapsed .tdw-block-inner {
                    position: relative;
                    overflow: hidden; 
                }
                .td_woo_product_description.tdw-pd-content-collapsed .tdw-block-inner:after {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-image: linear-gradient(to bottom, rgba(0,0,0,0) 20%, rgb(255,255,255) 100%);
                    pointer-events: none;
                }
                .td_woo_product_description .tdw-pd-read-more-wrap {
                    position: absolute;
                    bottom: 0;
                    left: 0;
                    display: none;
                    width: 100%;
                    z-index: 100;
                }
                .td_woo_product_description.tdw-pd-content-collapsed .tdw-pd-read-more-wrap {
                    display: flex;
                }
                .td_woo_product_description .tdw-pd-read-more {
                    display: flex;
                    align-items: center;
                    padding: 7px 16px 8px;
                    background-color: var(--td_theme_color, #4db2ec);
                    font-family: var(--td_default_google_font_2, 'Roboto', sans-serif);
                    font-size: 13px;
                    line-height: 1.2;
                    color: #fff;
                    cursor: pointer;
                }
                .td_woo_product_description .tdw-pd-read-more:hover {
                    background-color: #222;
                }
                .td_woo_product_description .tdw-pd-icon svg {
                    display: block;
                    width: 1em;
                    height: auto;
                    fill: #fff;
                }
                
                
                /* @content_height */
                .td_woo_product_description.tdw-pd-content-collapsed .tdw-block-inner {
                    max-height: @content_height;
                }
                
                /* @btn_align_horiz */
                .td_woo_product_description .tdw-pd-read-more-wrap {
                    justify-content: @btn_align_horiz;
                }
                
                /* @btn_padd */
                .td_woo_product_description .tdw-pd-read-more {
                    padding: @btn_padd;
                }
                /* @all_btn_border */
                .td_woo_product_description .tdw-pd-read-more {
                    border-width: @all_btn_border;
                    border-style: @all_btn_border_style;
                    border-color: @all_btn_border_color;
                }
                /* @btn_radius */
                .td_woo_product_description .tdw-pd-read-more {
                    border-radius: @btn_radius;
                }
                
                /* @btn_icon_size */
                .td_woo_product_description .tdw-pd-icon {
                    font-size: @btn_icon_size;
                }
                /* @btn_icon_space_right */
                .td_woo_product_description .tdw-pd-icon {
                    margin-left: 0;
                    margin-right: @btn_icon_space_right;
                }
                /* @btn_icon_space_left */
                .td_woo_product_description .tdw-pd-icon {
                    margin-left: @btn_icon_space_left;
                    margin-right: 0;
                }
                
                /* @btn_bg */
                .td_woo_product_description .tdw-pd-read-more {
                    background-color: @btn_bg;
                }
                /* @btn_bg_h */
                .td_woo_product_description .tdw-pd-read-more:hover {
                    background-color: @btn_bg_h;
                }
                /* @btn_border_color_h */
                .td_woo_product_description .tdw-pd-read-more:hover {
                    border-color: @btn_border_color_h;
                }
                /* @btn_color */
                .td_woo_product_description .tdw-pd-read-more {
                    color: @btn_color;
                }
                .td_woo_product_description .tdw-pd-read-more svg {
                    fill: @btn_color;
                }
                /* @btn_color_h */
                .td_woo_product_description .tdw-pd-read-more:hover {
                    color: @btn_color_h;
                }
                .td_woo_product_description .tdw-pd-read-more:hover svg {
                    fill: @btn_color;
                }
                /* @btn_icon_color */
                .td_woo_product_description .tdw-pd-read-more .tdw-pd-icon {
                    color: @btn_icon_color;
                }
                .td_woo_product_description .tdw-pd-read-more .tdw-pd-icon svg {
                    fill: @btn_icon_color;
                }
                /* @btn_icon_color_h */
                .td_woo_product_description .tdw-pd-read-more:hover .tdw-pd-icon {
                    color: @btn_icon_color_h;
                }
                .td_woo_product_description .tdw-pd-read-more:hover .tdw-pd-icon svg {
                    fill: @btn_icon_color_h;
                }
                
                /* @f_btn */
                .td_woo_product_description .tdw-pd-read-more {
                    @f_btn
                }
                

                	                
                /* @align_center */
				.td-theme-wrap .$unique_block_class {
					text-align: center;
				}
				/* @align_right */
				.td-theme-wrap .$unique_block_class {
					text-align: right;
				}
				/* @align_left */
				.td-theme-wrap .$unique_block_class {
					text-align: left;
				}
				
				/* @descr_color */
				.$unique_block_class {
					color: @descr_color;
				}
				/* @h_color */
				.$unique_block_class h1,
				.$unique_block_class h2,
				.$unique_block_class h3,
				.$unique_block_class h4,
				.$unique_block_class h5,
				.$unique_block_class h6 {
			        color: @h_color;
		        }
				/* @bq_color */
				body .$unique_block_class .tdw-block-inner blockquote {
			        color: @bq_color;
		        }
				/* @a_color */
				.$unique_block_class a {
			        color: @a_color;
		        }
				/* @a_hover_color */
				.$unique_block_class a:hover {
			        color: @a_hover_color;
		        }
				
				
				
				/* @f_descr */
				.$unique_block_class {
					@f_descr
				}
				/* @f_h1 */
				.$unique_block_class h1 {
			        @f_h1
		        }
				/* @f_h2 */
				.$unique_block_class h2 {
			        @f_h2
		        }
				/* @f_h3 */
				.$unique_block_class h3 {
			        @f_h3
		        }
				/* @f_h4 */
				.$unique_block_class h4 {
			        @f_h4
		        }
				/* @f_h5 */
				.$unique_block_class h5 {
			        @f_h5
		        }
				/* @f_h6 */
				.$unique_block_class h6 {
			        @f_h6
		        }
				/* @f_list */
				.$unique_block_class li {
			        @f_list
		        }
				/* @f_list_arrow */
				.$unique_block_class li:before {
				    margin-top: 1px;
			        line-height: @f_list_arrow !important;
		        }
				/* @f_bq */
				.$unique_block_class blockquote {
			        @f_bq
		        }
                
            </style>";

        $td_css_res_compiler = new td_css_res_compiler( $raw_css );
        $td_css_res_compiler->load_settings( __CLASS__ . '::cssMedia', $this->get_all_atts() );

        $compiled_css .= $td_css_res_compiler->compile_css();
        return $compiled_css;
    }

    static function cssMedia( $res_ctx ) {

        $res_ctx->load_settings_raw('style_general_td_woo_product_description', 1);


        /* --
        -- Read more.
        -- */
        if( self::$read_more_enabled ) {

            /* -- Layout -- */
            // General style
            $res_ctx->load_settings_raw('style_general_td_wpd_read_more', self::$read_more_enabled);

            // Initial content height
            $content_height = $res_ctx->get_shortcode_att('content_height');
            $content_height = $content_height !== '' ? $content_height : '500px';
            $content_height .= is_numeric( $content_height ) ? 'px' : '';
            $res_ctx->load_settings_raw('content_height', $content_height);

            // Button horizontal align
            $btn_align_horiz = $res_ctx->get_shortcode_att('btn_align_horiz');
            switch ( $btn_align_horiz ) {
                case 'content-horiz-center':
                    $res_ctx->load_settings_raw('btn_align_horiz', 'center');
                    break;
                case 'content-horiz-right':
                    $res_ctx->load_settings_raw('btn_align_horiz', 'flex-end');
                    break;
                case 'content-horiz-left':
                default:
                    $res_ctx->load_settings_raw('btn_align_horiz', 'flex-start');
                    break;
            }

            // Button padding
            $btn_padd = $res_ctx->get_shortcode_att('btn_padd');
            $btn_padd .= is_numeric( $btn_padd ) ? 'px' : '';
            $res_ctx->load_settings_raw('btn_padd', $btn_padd);

            // Button border size
            $btn_border = $res_ctx->get_shortcode_att('all_btn_border');
            $btn_border .= is_numeric( $btn_border ) ? 'px' : '';
            $res_ctx->load_settings_raw('all_btn_border', $btn_border);

            // Button border style
            $btn_border_style = $res_ctx->get_shortcode_att('all_btn_border_style');
            $btn_border_style = $btn_border_style !== '' ? $btn_border_style : 'solid';
            $res_ctx->load_settings_raw('all_btn_border_style', $btn_border_style);

            // Button border radius
            $btn_radius = $res_ctx->get_shortcode_att('btn_radius');
            $btn_radius .= is_numeric( $btn_radius ) ? 'px' : '';
            $res_ctx->load_settings_raw('btn_radius', $btn_radius);

            // Button icon size
            $btn_icon_size = $res_ctx->get_shortcode_att('btn_icon_size');
            $btn_icon_size .= is_numeric( $btn_icon_size ) ? 'px' : '';
            $res_ctx->load_settings_raw('btn_icon_size', $btn_icon_size);

            // Button icon space
            $btn_pos = $res_ctx->get_shortcode_att('btn_icon_position') != '' ? $res_ctx->get_shortcode_att('btn_icon_position') : 'after';
            $btn_icon_space = $res_ctx->get_shortcode_att('btn_icon_space');
            $btn_icon_space = $btn_icon_space != '' ? $btn_icon_space : '8px';
            $btn_icon_space .= is_numeric( $btn_icon_space ) ? 'px' : '';
            if( $btn_pos == 'before' ) {
                $res_ctx->load_settings_raw('btn_icon_space_right', $btn_icon_space);
            } else {
                $res_ctx->load_settings_raw('btn_icon_space_left', $btn_icon_space);
            }


            /* -- Colors -- */
            // Background
            $res_ctx->load_settings_raw('btn_bg', $res_ctx->get_shortcode_att('btn_bg'));
            $res_ctx->load_settings_raw('btn_bg_h', $res_ctx->get_shortcode_att('btn_bg_h'));

            // Border color
            $border_color = $res_ctx->get_shortcode_att('all_btn_border_color');
            $border_color = $border_color != '' ? $border_color : '#000';
            $res_ctx->load_settings_raw('all_btn_border_color', $border_color);
            $res_ctx->load_settings_raw('btn_border_color_h', $res_ctx->get_shortcode_att('btn_border_color_h'));

            // Text color
            $res_ctx->load_settings_raw('btn_color', $res_ctx->get_shortcode_att('btn_color'));
            $res_ctx->load_settings_raw('btn_color_h', $res_ctx->get_shortcode_att('btn_color_h'));

            // Icon color
            $res_ctx->load_settings_raw('btn_icon_color', $res_ctx->get_shortcode_att('btn_icon_color'));
            $res_ctx->load_settings_raw('btn_icon_color_h', $res_ctx->get_shortcode_att('btn_icon_color_h'));


            /* -- Fonts -- */
            // Text
            $res_ctx->load_font_settings( 'f_btn' );

        }


        // content align
        $content_align = $res_ctx->get_shortcode_att('content_align_horizontal');
        if ( $content_align == 'content-horiz-center' ) {
            $res_ctx->load_settings_raw( 'align_center', 1 );
        } else if ( $content_align == 'content-horiz-right' ) {
            $res_ctx->load_settings_raw( 'align_right', 1 );
        } else if ( $content_align == 'content-horiz-left' ) {
            $res_ctx->load_settings_raw( 'align_left', 1 );
        }



        /*-- COLORS -- */
        $res_ctx->load_settings_raw( 'descr_color', $res_ctx->get_shortcode_att('descr_color') );
        $res_ctx->load_settings_raw( 'h_color', $res_ctx->get_shortcode_att('h_color') );
        $res_ctx->load_settings_raw( 'bq_color', $res_ctx->get_shortcode_att('bq_color') );
        $res_ctx->load_settings_raw( 'a_color', $res_ctx->get_shortcode_att('a_color') );
        $res_ctx->load_settings_raw( 'a_hover_color', $res_ctx->get_shortcode_att('a_hover_color') );



        /*-- FONTS -- */
        $res_ctx->load_font_settings( 'f_descr' );
        $res_ctx->load_font_settings( 'f_h1' );
        $res_ctx->load_font_settings( 'f_h2' );
        $res_ctx->load_font_settings( 'f_h3' );
        $res_ctx->load_font_settings( 'f_h4' );
        $res_ctx->load_font_settings( 'f_h5' );
        $res_ctx->load_font_settings( 'f_h6' );
        $res_ctx->load_font_settings( 'f_list' );
        $f_list_size = $res_ctx->get_shortcode_att('f_list_font_size');
        $f_list_lh = $res_ctx->get_shortcode_att('f_list_font_line_height');
        if( $f_list_size != '' && $f_list_lh == '' ) {
            if( is_numeric( $f_list_size ) ) {
                $res_ctx->load_settings_raw( 'f_list_arrow', $f_list_size . 'px' );
            } else {
                $res_ctx->load_settings_raw( 'f_list_arrow', $f_list_size );
            }
        }
        if( $f_list_size == '' && $f_list_lh != '' ) {
            if( is_numeric( $f_list_lh ) ) {
                $res_ctx->load_settings_raw( 'f_list_arrow', 15 * $f_list_lh . 'px' );
            } else {
                $res_ctx->load_settings_raw( 'f_list_arrow', $f_list_lh );
            }
        }
        if( $f_list_size != '' && $f_list_lh != '' ) {
            if( is_numeric( $f_list_lh ) ) {
                $res_ctx->load_settings_raw( 'f_list_arrow', $f_list_size * $f_list_lh . 'px' );
            } else {
                $res_ctx->load_settings_raw( 'f_list_arrow', $f_list_lh );
            }
        }
        $res_ctx->load_font_settings( 'f_bq' );

    }
    
    function __construct() {
        parent::disable_loop_block_features();
    }

    function render($atts, $content = null) {

        global $td_woo_state_single_product_page;

	    $product_description_data = $td_woo_state_single_product_page->product_description->__invoke($atts);
        
        parent::render($atts);


        /* -- In composer flag. -- */
        $in_composer = tdc_state::is_live_editor_ajax() || tdc_state::is_live_editor_iframe();


        /* -- Additional classes array. -- */
        $additional_classes = array();


        /* -- Block atts. -- */
        // Read more functionality.
        self::$read_more_enabled = $this->get_att('read_more') !== '';

        if( self::$read_more_enabled ) {
            $additional_classes[] = 'tdw-pd-content-collapsed';
        }

        // Initial content height.
        $content_height = $this->get_att('content_height');
        $content_height = $content_height !== '' ? $content_height : 500;

        // Button text
        $btn_text = $this->get_att('btn_text');
        if( $btn_text == '' ) {
            $btn_text = 'Read more';
        }

        // Button icon
        $icon = $this->get_icon_att( 'btn_tdicon' );
        $icon_html = '';
        if ( $icon != '' ) {
            if( base64_encode( base64_decode( $icon ) ) == $icon ) {
                $icon_data = '';
                if( td_util::tdc_is_live_editor_iframe() || td_util::tdc_is_live_editor_ajax() ) {
                    $icon_data = 'data-td-svg-icon="' . $this->get_att('tdicon') . '"';
                }

                $icon_html = '<span class="tdw-pd-icon" ' . $icon_data . '>' . base64_decode( $icon ) . '</span>';
            } else {
                $icon_html = '<i class="tdw-pd-icon ' . $icon . '"></i>';
            }
        }

        // Button icon position
        $icon_position = $this->get_att( 'btn_icon_position' ) != '' ? $this->get_att( 'btn_icon_position' ) : 'after';


        $buffy = '';

        $buffy .= '<div class="' . $this->get_block_classes($additional_classes)  . '" ' . $this->get_block_html_atts() . ( self::$read_more_enabled ? ' data-content-height="' . $content_height . '"' : '' ) . '>';

	        //get the block css
	        $buffy .= $this->get_block_css();

	        //get the js for this block
	        $buffy .= $this->get_block_js();

            $buffy .= '<div class="tdw-block-inner td-fix-index">';
                $buffy .= '<div class="tdw-pd-content-wrap">';
                    $buffy .= do_shortcode($product_description_data['description']);
                $buffy .= '</div>';

                if( self::$read_more_enabled ) {
                    $buffy .= '<div class="tdw-pd-read-more-wrap">';
                        $buffy .= '<div class="tdw-pd-read-more">';
                            if( $icon_position == 'before' ) {
                                $buffy .= $icon_html;
                            }

                            $buffy .= $btn_text;

                            if( $icon_position == 'after' ) {
                                $buffy .= $icon_html;
                            }
                        $buffy .= '</div>';
                    $buffy .= '</div>';

                    if( !$in_composer ) {
                        td_resources_load::render_script( TD_WOO_SCRIPTS_URL . '/tdwProductDescription.js' . TD_WOO_SCRIPTS_VER, 'tdwProductDescription-js', '', 'footer' );
                    }
                }
            $buffy .= '</div>';

        $buffy .= '</div>';

        return $buffy;
    }

}

