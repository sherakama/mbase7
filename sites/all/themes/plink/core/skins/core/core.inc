<?php

/**
 * Implements hook_skinr_skin_PLUGIN_info().
 */
function plink_skinr_skin_core_info() {



  $skins = array();  
  $theme = variable_get('theme_default','NOTHING');
  $theme_data = list_themes();
  $default_settings = ($theme && isset($theme_data[$theme]->info['settings'])) ? $theme_data[$theme]->info['settings'] : array();
  $saved_settings = variable_get('theme_' . $theme . '_settings', array());
  $plink_unit = $saved_settings['master_grid_columns'];
  $plink_width = $saved_settings['grid_master_width'];

 
 /**
 * LAYOUT STYLES
 */

   
    $skins['plink_skins_grid_width'] = array(
    'title' => $plink_width . t(' px ') . $plink_unit . t(' column grid'),
    'description' => t("Change the width of this block. Fluid grid % widths are relative to the parent region's width. Default widths: sidebar blocks default to the sidebar width; in other regions width is divided equally among all blocks."),
    'type' => 'select',
    'group' => 'layout',
    'theme hooks' => array('block'),
    'default status' => TRUE,
    'options' => _plink_skins_grid_options($plink_unit, $plink_width), 
      
  );     
  

  $skins['plink_skins_grid_pull'] = array(
    'title' => t('Pull the Position of the Block'),
    'description' => t("Pull the block over in the grid."),
    'type' => 'select',
    'group' => 'layout',
    'theme hooks' => array('block'),
    'default status' => TRUE,
    'options' => _plink_skins_pull_options($plink_unit, $plink_width),   
  );  
    
  
  $skins['plink_skins_grid_push'] = array(
    'title' => t('Push the Position of the Block'),
    'description' => t("Push the block over in the grid."),
    'type' => 'select',
    'group' => 'layout',
    'theme hooks' => array('block'),
    'default status' => TRUE,
    'options' => _plink_skins_push_options($plink_unit, $plink_width),   
  );   
      
      
  $skins['plink_skins_grid_prefix'] = array(
    'title' => t('Prefix the Block'),
    'description' => t("Prefix the Block with Padding."),
    'type' => 'select',
    'group' => 'layout',
    'theme hooks' => array('block'),
    'default status' => TRUE,
    'options' => _plink_skins_prefix_options($plink_unit, $plink_width),   
  ); 
    
    
  $skins['plink_skins_grid_suffix'] = array(
    'title' => t('Suffix the Block'),
    'description' => t("Suffix the Block with Padding."),
    'type' => 'select',
    'group' => 'layout',
    'theme hooks' => array('block'),
    'default status' => TRUE,
    'options' => _plink_skins_suffix_options($plink_unit, $plink_width),   
  );
       
  $skins['plink_skins_block_positioning'] = array(
    'title' => t('Block position'),
    'type' => 'select',
    'description' => t('Change the position of this block (default is to float to the left)'),
    'group' => 'layout',
    'theme hooks' => array('block'),
    'default status' => TRUE,
    'options' => array(
      'plink-right' => array(
        'title' => 'Float block to the right',
        'class' => array('plink-right'),
      ),
      'plink-center' => array(
        'title' => 'Position block in the center',
        'class' => array('plink-center'),
      ),   
    ),
  );
  
    $skins['plink_skins_marginfloat_styles'] = array(
    'title' => t('Clearing & Margins'),
    'description' => t('These are the clear and margin options for blocks'),
    'theme hooks' => array('block'),
    'type' => 'checkboxes',
    'group' => 'layout',
    'default status' => TRUE,
    'options' => array(
      'clear-both' => array(
        'title' => t('Clear both, drops to the next line.'),
        'class' => array('clear-both'),
      ),
      'clear-left' => array(
        'title' => t('Clear left'),
        'class' => array('clear-left'),
      ),
      'clear-right' => array(
        'title' => 'Clear Right',
        'class' => array('clear-right'),
      ),
      'grid-row' => array(
        'title' => 'Add 10px margins to the top and bottom of the block',
        'class' => array('grid-row'),
      ),
       'alpha' => array(
        'title' => 'Remove left margin from the block',
        'class' => array('alpha'),
      ),
      'omega' => array(
        'title' => 'Remove right margin from the block',
        'class' => array('omega'),
      ),
      'plink-border' => array(
        'title' => 'Border: add 1px border and 10px padding',
        'class' => array('plink-border'),
      ),
      'plink-padding' => array(
        'title' => 'Padding: add 30px extra padding inside block',
        'class' => array('plink-padding'),
      ),
    ),
  );  
  
  
  /**
 * TYPOGRAPHY STYLES
 */

  
  $skins['plink_skins_content_alignment'] = array(
    'title' => t('Content alignment'),
    'description' => t('Default is left aligned content'),
    'type' => 'select',
    'group' => 'typography',
    'default status' => TRUE,
    'options' => array(
      'plink-right' => array(
        'title' => 'Center align content within its container',
        'class' => array('plink-center-content'),
      ),
      'plink-center' => array(
        'title' => 'Right align content within its container',
        'class' => array('plink-right-content'),
      ),   
    ),
  );  
  
   $skins['plink_multicol'] = array(
    'title' => t('List columns'),
    'type' => 'select',
    'description' => t('Put items in lists (menus, list views, etc.) in multiple columns'),    
    'theme hooks' => array('block', 'panels_display', 'panels_pane', 'panels_panel', 'views_view'),    
    'group' => 'typography',
    'default status' => TRUE,
    'options' => array(
      'plink-2-col-list' => array(
        'title' => '2-column list/menu (50%/50%)',
        'class' => array('plink-2-col-list', 'clearfix'),
      ),
      'plink-3-col-list' => array(
        'title' => '3-column list/menu (33%/33%/33%)',
        'class' => array('plink-3-col-list', 'clearfix'),      
      ),
    ),
  );   
  
  $skins['plink_menu'] = array(
    'title' => t('Menu Layout'),
    'type' => 'radios',
    'description' => t('Different layouts and alignment options for your menus'),    
    'theme hooks' => array('block', 'panels_display', 'panels_pane', 'panels_panel'),    
    'group' => 'typography',
    'default status' => TRUE,
    'options' => array(
      'plink-inline-menu' => array(
        'title' => 'Single line menu with separators',
        'class' => array('plink-inline-menu'),
      ),
      'plink-multicol-menu' => array(
        'title' => t('Multi-column menu with bold headers (set menu items to Expanded)'),
        'class' => array('plink-multicol-menu', 'clearfix'),
      ),
    ),
  );  
  
  $skins['plink_list_styles'] = array(
    'title' => t('General text styles'),
    'type' => 'checkboxes',
    'theme hooks' => array('block', 'panels_display', 'panels_pane', 'panels_panel', 'views_view'),    
    'group' => 'typography',
    'default status' => TRUE,
    'options' => array(
      'plink-list-bottom-border' => array(
        'title' => t('Bottom border on list items (no bullets)'),
        'class' => array('plink-list-bottom-border'),
      ),
      'plink-multicol-menu' => array(
        'title' => t('Extra vertical spacing  on list items (nobullets)'),
        'class' => array('plink-list-vertical-spacing'),
      ),
      'plink-callout' => array(
        'title' => t('Text: large, bold callout style'),
        'class' => array('plink-callout'),
      ),
      'plink-bold-links' => array(
        'title' => t('Links: bold all links'),
        'class' => array('plink-bold-links'),
      ),
    ),
  );  

  
      
  return $skins;
}

/**
 * Generates Skinr options based on plink grid parameters.
 * Assists in hook_skinr_skin_PLUGIN_info() implementation.
 * @return array
 */
function _plink_skins_grid_options($units, $width) {
  $options = array();
  $unit_width = floor($width / $units);
  for ($i = 1; $i <= $units; $i++) {
    $plural = ($i == 1) ? '' : 's';
    $this_width = $i * $unit_width;
    $this_percentage = round($i * (100 / $units), 2);
    $options["grid-{$i}"] = array(
      'title' => "$i unit{$plural} wide ({$this_width}px/{$this_percentage}%)",
      'class' => "grid-{$i}"
    );
  }
  return $options;
}

/**
 * PUSH Grid
 */

function _plink_skins_push_options($units, $width) {
  $options = array();
  $unit_width = floor($width / $units);
  for ($i = 1; $i <= $units; $i++) {
    $plural = ($i == 1) ? '' : 's';
    $this_width = $i * $unit_width;
    $this_percentage = round($i * (100 / $units), 2);
    $options["push-{$i}"] = array(
      'title' => "$i unit{$plural} wide ({$this_width}px/{$this_percentage}%)",
      'class' => "push-{$i}"
    );
        
  }
  return $options;
}

/**
 * PULL Grid
 */

function _plink_skins_pull_options($units, $width) {
  $options = array();
  $unit_width = floor($width / $units);
  for ($i = 1; $i <= $units; $i++) {
    $plural = ($i == 1) ? '' : 's';
    $this_width = $i * $unit_width;
    $this_percentage = round($i * (100 / $units), 2);
    $options["pull-{$i}"] = array(
      'title' => "$i unit{$plural} wide ({$this_width}px/{$this_percentage}%)",
      'class' => "pull-{$i}"
    );
        
  }
  return $options;
}

/**
 * SUFFIX Grid
 */

function _plink_skins_suffix_options($units, $width) {
  $options = array();
  $unit_width = floor($width / $units);
  for ($i = 1; $i <= $units; $i++) {
    $plural = ($i == 1) ? '' : 's';
    $this_width = $i * $unit_width;
    $this_percentage = round($i * (100 / $units), 2);
    $options["suffix-{$i}"] = array(
      'title' => "$i unit{$plural} wide ({$this_width}px/{$this_percentage}%)",
      'class' => "suffix-{$i}"
    );
        
  }
  return $options;
}

/**
 * PREFIX Grid
 */

function _plink_skins_prefix_options($units, $width) {
  $options = array();
  $unit_width = floor($width / $units);
  for ($i = 1; $i <= $units; $i++) {
    $plural = ($i == 1) ? '' : 's';
    $this_width = $i * $unit_width;
    $this_percentage = round($i * (100 / $units), 2);
    $options["prefix-{$i}"] = array(
      'title' => "$i unit{$plural} wide ({$this_width}px/{$this_percentage}%)",
      'class' => "prefix-{$i}"
    );
        
  }
  return $options;
}


function plink_skinr_group_info() {
  $groups['wireframe'] = array(
    'title' => t('Wireframing'),
    'description' => t('Styles for quickly wireframing your site.'),
    'weight' => -10,
  );
  return $groups;
}


