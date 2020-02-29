<?php
/**
 * Change predefined variables in template file and optionally remove HTML comments.
 *
 * Filename can be an actual file name or a link with a file name.
 *
 * Change following placeholders -if exist-
 *
 * [#variable]
 * %%VARIABLE%%
 * {$variable}
 * {{variable}}
 *
 * in template file based on the given $variables_array and optionally remove HTML comments even munltiline ones.
 * It will also remove not existing placeholders.
 *
 * The function accept multidimensional arrays as well.
 *
 * $value_to_change = array(
 *                        subarray => array(
 *                            subsubarray => array(
 *                                key => value,
 *                            )
 *                        )
 *                    );
 *
 * For multidimensional arrays use [#array.subarray(.subsubarray ...).key] inside the template file.
 *
 * In case of %%VARIABLE%% in $variables_array the variable name is case insensitive,
 * (because %%VARIABLENAME%% is uppercase)
 * in case of [#variable] the variable name in $variables_array case sensitive!
 *
 * eg: in HTML file [#testvariable] or %%TESTVARIABLE%% will changed to
 *     $variables_array['testvariable'] value or in multidimentinal array
 *     [#subarray.testvariable] or %%SUBARRAY.TESTVARIABLE%% to
 *     to $variables_array['subarray']['testvariable']
 *
 * If the variable does not exist, then remove unused [#...] and %%...%% tags.
 *
 * Created by Joe Szalai
 */
if ( ! class_exists( 'Exopite_Template' ) ) {
    class Exopite_Template {

        public static $variables_array;
        public static $filename;
        public static $display_errors = false;
        public static $remove_HTML_comments = false;

        public static function URL_exists( $url ) {
            // Source: http://stackoverflow.com/questions/981954/how-can-one-check-to-see-if-a-remote-file-exists-using-php/982045#982045
            if ( $ch = curl_init( $url ) ) {
                curl_setopt( $ch, CURLOPT_NOBODY, true );
                curl_exec( $ch );
                $retcode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
                // $retcode >= 404 -> not found, $retcode = 200, found.
                curl_close( $ch );

                if ( $retcode === 200 ) {
                    return true;
                } else {
                    return false;
                }
            }
        }

        /**
         * Flatten a multi-dimensional array and constructing concatenated keys for nested elements.
         * @param  array $array
         * @return array [array.subarray.subarray] => 'value'
         * @link source: http://stackoverflow.com/questions/9546181/flatten-multidimensional-array-concatenating-keys/9546215#9546215
         */
        public static function flat_concatenate_array( $array, $prefix = '' ) {
            //
            $result = array();
            foreach( $array as $key => $value ) {
                if( is_array( $value ) ) {
                    $result = $result + self::flat_concatenate_array( $value, $prefix . $key . '.' );
                }
                else {
                    $result[$prefix . $key] = $value;
                }
            }
            return $result;
        }

        /*
         * Loop through variable names array and change them to variable value
         */
        public static function replace_variables_in_template( $template, $variables_array ) {
            foreach ( $variables_array as $name => $value ) {
                $template = str_replace(
                    array( '[#' . $name . ']', '%\%' . strtoupper( $name ) . '%%', '{$' . $name . '}', '{{' . $name . '}}' ),
                    array( $value, $value, $value, $value ),
                    $template
                );
            }

            // Rid of other empty tags or in $variables_array not existing HTML variables
            $template = preg_replace(
                array( '/\[#([^]]*)\]/', '/\%\%([^\%\%][A-Z0-9]+)\%\%/', '/{$([^]]*)}/', '/{{([^]]*)}}/' ),
                array( "", "", "", "" ),
                $template
            );

            return $template;
        }

        public static function remove_HTML_comments( $template ) {
            $template = preg_replace(
                '/<!--(.*?)-->/s',
                '',
                $template
            );

            return $template;
        }

        public static function get_template() {

            // Check is file or url exist
            if ( ! ( file_exists( self::$filename ) || self::URL_exists( self::$filename ) ) ) {
                /*
                 * This function assume you are using under Wordpress.
                 * Otherwise error message style will be not displayed correctly!
                 */
                $retVal = '';
                if ( self::$display_errors ) {
                    $retVal = '<div class="alert alert-danger">
                               <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                               <strong>Error!</strong> File does not exist: ' . self::$filename .
                               '</div>';
                }

                return $retVal;
            }

            // Read file content
            $template = file_get_contents( self::$filename );

            // Check input array, if empty return file content.
            if ( ! is_array( self::$variables_array ) || empty( self::$variables_array ) ) {
                return $template;
            }

            // Flat and concatenat array.
            $_variables_array = self::flat_concatenate_array( self::$variables_array );

            // Replace variables in tmeplate.
            $template = self::replace_variables_in_template( $template, $_variables_array );

            //remove HTML comments even munltiline ones.
            if ( self::$remove_HTML_comments ) {
                $template = self::remove_HTML_comments( $template );
            }

            return $template;
        }

    }
}

?>
