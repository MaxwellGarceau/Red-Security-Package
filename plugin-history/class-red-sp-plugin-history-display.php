<?php
class Plugin_History_Display extends Plugin_History {

  /**
   * HTML
   */
  public static function output_admin_menu() {
    $current_plugins = parent::get_plugins(); // Hardcoded for now
    $last_saved_plugins = parent::get_last_plugin_save();
    $last_plugin_save_date = parent::get_last_plugin_save_date() ? parent::get_last_plugin_save_date() : 'Never';

    $output .= '<div class="ph-wrap">';

      $output .= '<h1>Plugin History</h1>';

      $output .= '<p class="ph-notice">*Click column title to copy text</p>';

      $output .= '<div class="ph-all-tables-container">';

        $output .= '<div class="ph-table-container">';

          $output .= self::get_plugin_table( $current_plugins, $last_saved_plugins );

          $output .= '<p>Last plugin save was: <span class="ph-notice">' . $last_plugin_save_date . '</span></p>';

          $output .= '<div class="ph-button-container">';

          $output .= self::get_save_plugins_button();

          $output .= self::get_delete_plugins_button();

          $output .= '</div>';

          $output .= self::display_plugin_changes( $current_plugins, $last_saved_plugins );

        $output .= '</div>';

      $output .= '</div>'; // .ph-all-tables-container

    $output .= '</div>'; // .ph-wrap

    echo $output;
  }

  public static function get_plugin_table( $current_plugins, $compare_plugins = array() ) {
    $output .= '<table id="plugin-history" class="ph-plugin-table">';
    $output .= '<thead>
        <tr>
            <th class="ph-plugin-name-header" title="Current Plugins">
                Plugin Name
            </th>
            <th title="Click to select column">
                Plugin Version
            </th>
            <th title="Click to select column">
                Last Updated
            </th>';

      if ( ! empty( $compare_plugins ) ) {
        $output .= '<th class="ph-table-spacer"></th>'; // Space between last table and this one

        $output .= '<th class="ph-plugin-name-header" title="Last Saved Plugins">
            Plugin Name
        </th>
        <th title="Click to select column">
            Plugin Version
        </th>
        <th title="Click to select column">
            Last Updated
        </th>';
      }

    $output .= '</tr>
    </thead>';

    $combined_plugins = parent::combine_plugin_groups( $current_plugins, $compare_plugins );
    foreach ( $combined_plugins as $folder_file => $plugin ) {

      /**
       * Compare current and old plugin
       */
      $current_plugin = $plugin['current_plugin'];
      $compare_plugin = $plugin['compare_plugin'];
      $has_current_plugin = isset( $current_plugin ) && !empty( $current_plugin );
      $has_compare_plugin = isset( $compare_plugin ) && !empty( $compare_plugin );

      $plugin_row_classes = array();

      /* Plugin was deleted */
      if ( ! $has_current_plugin && $has_compare_plugin ) {
        $plugin_row_classes[] = 'ph-deleted';
        $plugin_row_classes[] = 'ph-tooltip';
      }

      /* Plugin was added */
      if ( $has_current_plugin && ! $has_compare_plugin ) {
        $plugin_row_classes[] = 'ph-added';
        $plugin_row_classes[] = 'ph-tooltip';
      }


      if ( $has_current_plugin && $has_compare_plugin ) {

        /* Plugin was upgraded */
        if ( version_compare( $current_plugin['Version'], $compare_plugin['Version'], '>' ) ) {
          $plugin_row_classes[] = 'ph-upgraded';
          $plugin_row_classes[] = 'ph-tooltip';

        /* Plugin was downgraded */
        } else if ( version_compare( $current_plugin['Version'], $compare_plugin['Version'], '<' ) ) {
          $plugin_row_classes[] = 'ph-downgraded';
          $plugin_row_classes[] = 'ph-tooltip';
        }

      }

      $output .= '<tr class="' . implode( ' ', $plugin_row_classes ) . '">';
        /**
         * Current plugins
         */
        if ( $has_current_plugin ) {
          $output .= '<td>' . $current_plugin['Name'] . '</td>';
          $output .= '<td>' . $current_plugin['Version'] . '</td>';
          $output .= '<td>' . date( 'm/d/Y', $current_plugin['last_updated'] ) . '</td>';
        } else {
          /* Placeholders */
          $output .= '<td></td>';
          $output .= '<td></td>';
          $output .= '<td></td>';
        }

        if ( ! empty( $compare_plugins ) ) {
          $output .= '<td class="ph-table-spacer"></td>'; // Space between last table and this one
        }

        /**
         * Last Saved plugins
         */
        if ( $has_compare_plugin ) {
          $output .= '<td>' . $compare_plugin['Name'] . '</td>';
          $output .= '<td>' . $compare_plugin['Version'] . '</td>';
          $output .= '<td>' . date( 'm/d/Y', $compare_plugin['last_updated'] ) . '</td>';
        } else if ( ! empty( $compare_plugins ) ) { // Make sure we have compare plugins
          /* Placeholders */
          $output .= '<td></td>';
          $output .= '<td></td>';
          $output .= '<td></td>';
        }

      $output .= '</tr>';
    }

    $output .= '</table>';
    return $output;
  }

  public static function display_plugin_changes( $current_plugins, $compare_plugins ) {
    $plugin_changes = parent::get_plugin_changes_string( $current_plugins, $compare_plugins );

    $html .= '<h2>Plugin Changelog for Security Package Email</h2>';
    $html .= '<div class="ph-changelog">';

    /* No changes */
    if ( empty( $plugin_changes ) ) {
      $html .= '<p class="ph-notice">There are no changes between the last plugin save and the present time</p>';
    } else {

    /* If not empty print changelog */
      $html .= '<ul>';

      foreach ( $plugin_changes as $plugin_change ) {
        $html .= '<li class="ph-plugin-change">';
        $html .= '<span>' . $plugin_change . '</span>';
        $html .= '</li>';
      }

      $html .= '</ul>';
    }

    $html .= '</div>'; // .ph-changelog
    return $html;
  }

  public static function get_save_plugins_button() {
    return '<button id="ph-save-plugin-data" class="button button-primary">Save Plugin Data</button>';
  }

  public static function get_delete_plugins_button() {
    return '<button style="pointer-events: none; opacity: .5;" id="ph-erase-plugin-history" class="button button-primary">Delete Plugin Data (Testing Purposes Only)</button>';
  }

}