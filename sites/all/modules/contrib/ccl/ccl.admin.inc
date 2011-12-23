<?php
/**
 * @file
 * Provides administrative functions for ccl.
 */

/**
 * CCL adminitsration form.
 */
function ccl_admin() {
  $result = db_query("SELECT * FROM {ccl}");
  if ($result->rowCount()) {

    // Get cached version of ccl entries, of blocks in the system and content type names
    $cache = ccl_cache_get('ccl_nodes');
    $blocks = ccl_get_blocks();
    $cts = node_type_get_names();

    // Prepare table header
    $header = array(
      t('Title'),
      t('URL'),
      t('Type'),
      t('Target'),
      t('Operation')
    );

    $rows = array();
    foreach ($result as $record) {
      // Target: Global
      if ($record->global) {
        $target = t('Global');
      }
      else {
        // Target: Single Node
        if ($record->type == 'node' && empty($record->ct)) {
          $target = t('Node ID') . ': ' . $record->nid;
        }
        // Target: Content Type
        elseif ($record->type == 'node' && !empty($record->ct)) {
          $target = t('Content Type') . ': ' . $cts[$record->ct];
        }
        // Target: Block
        else{
          $target = t('Block') . ': ' . $blocks[$record->block];
        }
      }

      // Prepare table row
      $rows[] = array(
        $record->title,
        $record->link,
        $record->type,
        $target,
        l(t('Edit'), 'admin/config/user-interface/ccl/' . $record->clid . '/edit') . ' | ' . l(t('Delete'), 'admin/config/user-interface/ccl/' . $record->clid . '/delete')
      );
    }
    return theme('table', array('header' => $header, 'rows' => $rows));
  }

  // No results text
  else {
    return array(
      'empty_text' => array(
        '#type' => 'markup',
        '#markup' => '<p>' . t('No custom contextual links have been added yet. <a href="@add-page">Add a link here</a>.', array('@add-page' => url('admin/config/user-interface/ccl/add'))) . '</p>',
      )
    );
  }
}

/**
 * Helper function to retrieve all the information for all blocks in the system.
 */
function ccl_get_blocks() {
  $block_info = cache_get('ccl_block_info');

  // If no data is found in the cache invoke all block hooks and create the cache entry
  if (empty($block_info)) {
    $block_info = array();
    foreach (module_implements('block_info') as $module) {
      $module_blocks = module_invoke($module, 'block_info');
      if (!empty($module_blocks)) {
        foreach ($module_blocks as $delta => $block) {
          $block = (object) $block;
          $block->module = $module;
          $block->delta = $delta;
          $block->bid = "{$block->module}|{$block->delta}";
          $block_info[$block->bid] = $block->info;
        }
      }
    }
    cache_set('ccl_block_info', $block_info);
  }

  if (isset($block_info->cid)) {
    return $block_info->data;
  }
  else {
    return $block_info;
  }
}

/**
 * Link add and edit form.
 *
 */
function ccl_add_form($form, &$form_state, $clid = 0) {
  // Check if we are in edit mode and load the link values
  if ($clid) {
    $link = db_query('SELECT * FROM {ccl} WHERE clid = :clid', array(':clid' => $clid))->fetchObject();
    $form_state['clid'] = $clid;
    if ($link->nid) {
      $node_options = 'node';
      $title = db_query('SELECT title FROM {node} WHERE nid = :nid', array(':nid' => $link->nid))->fetchField();
      $nid_text = $title . ' [nid:' . $link->nid . ']';
    }
    elseif ($link->ct) {
      $node_options = 'ct';
    }
    elseif ($link->global) {
      $node_options = 'global';
    }
  }

  $form = array();
  // Pull in library, js and css for the form
  $form['#attached']['js'][] = drupal_get_path('module', 'ccl') . '/ccl.js';
  $form['#attached']['css'][] = drupal_get_path('module', 'ccl') . '/ccl.css';
  $form['#attached']['library'][] = array('system', 'ui.button');

  $form['link_group'] = array(
    '#type' => 'fieldset',
    '#title' => t('Link'),
    '#collapsible' => TRUE,
  );

  $form['link_group']['title'] = array(
    '#type' => 'textfield',
    '#title' => t('Title'),
    '#description' => t('The title of this link as it will be displayed in the contextual widget.'),
    '#size' => 40,
    '#default_value' => $clid ? $link->title : '',
    '#maxlength' => 255,
    '#required' => TRUE,
  );

  $form['link_group']['link'] = array(
    '#type' => 'textfield',
    '#title' => t('URL'),
    '#default_value' => $clid ? $link->link : '',
    '#description' => t('The URL of this link. For nodes you can use \'%nid\' as a replacement token for the node id. Example: node/%nid/devel'),
    '#size' => 40,
    '#maxlength' => 255,
    '#required' => TRUE
  );

  $form['options_group'] = array(
    '#type' => 'fieldset',
    '#title' => t('Options'),
    '#collapsible' => TRUE,
  );

  $form['options_group']['link_type'] = array(
    '#type' => 'radios',
    '#title' => t('Link Type'),
    '#description' => t('Select if this link should be displayed for a node or for a block.'),
    '#options' => array(
      'node' => t('Node'),
      'block' => t('Block'),
    ),
    '#default_value' => $clid ? $link->type : 'node',
  );

  $form['options_group']['node_options'] = array(
    '#type' => 'radios',
    '#title' => t('Show link for'),
    '#description' => t('Select if this link should be displayed for all nodes, all nodes of a content type or a specific node.'),
    '#options' => array(
      'node' => t('Single node'),
      'ct' => t('Content type'),
      'global' => t('All nodes')
    ),
    '#default_value' => isset($node_options) ? $node_options : 'node',
    '#states' => array(
      'visible' => array(
        ':input[name="link_type"]' => array('value' => 'node'),
      ),
    ),

  );

  // Load the content type names
  $types = node_type_get_names();
  $form['options_group']['ct'] = array(
    '#type' => 'select',
    '#title' => t('Content Type'),
    '#description' => t('The content type this link will be displayed for.'),
    '#options' => $types,
    '#default_value' => $clid ? $link->ct : -1,
    '#states' => array(
      'visible' => array(
        ':input[name="node_options"]' => array('value' => 'ct'),
        ':input[name="link_type"]' => array('value' => 'node'),
      ),
    ),
  );

  $form['options_group']['nid'] = array(
    '#type' => 'textfield',
    '#title' => t('Node ID'),
    '#description' => t('Enter the title of the node or the id of the node this link should be added to.'),
    '#size' => 40,
    '#maxlength' => 128,
    '#default_value' => isset($nid_text) ? $nid_text : '',
    '#autocomplete_path' => 'admin/config/user-interface/ccl/autocomplete',
    '#states' => array(
      'visible' => array(
        ':input[name="node_options"]' => array('value' => 'node'),
        ':input[name="link_type"]' => array('value' => 'node'),
      ),
    ),
  );

  $form['options_group']['global_block'] = array(
    '#type' => 'checkbox',
    '#title' => t('Add this link to all blocks'),
    '#default_value' => $clid ? $link->global : -1,
    '#states' => array(
      'visible' => array(
        ':input[name="link_type"]' => array('value' => 'block'),
      ),
    ),
  );

  $options = ccl_get_blocks();
  $form['options_group']['block'] = array(
    '#type' => 'select',
    '#title' => t('Block'),
    '#description' => t('Seletc the block this link should be added to.'),
    '#options' => $options,
    '#default_value' => $clid ? $link->block : -1,
    '#states' => array(
      'visible' => array(
        ':input[name="global_block"]' => array('checked' => FALSE),
        ':input[name="link_type"]' => array('value' => 'block'),
      ),
    ),
  );

  $form['save_link'] = array(
    '#type' => 'submit',
    '#value' => t('Save Link'),
  );

  return $form;
}

/**
 * Validafion handler for ccl_add_form().
 *
 * @see ccl_add_form()
 */
function ccl_add_form_validate($form, &$form_state) {
  $values = $form_state['values'];

  // Check that the node id token is not used for blocks
  if ($values['link_type'] == 'block' && strpos($values['link'], '%nid') !== FALSE) {
    form_set_error('link', t('The node token \'%nid\' is only allowed in connection with the link type node.'));
  }

  // Check that that we can get the node id and trnasform it for saving right away.
  if ($values['link_type'] == 'node' && $values['node_options'] == 'node') {
    if (is_numeric($values['nid'])) {
      form_set_value($form['options_group']['nid'], $values['nid'], $form_state);
    }
    elseif (preg_match('/\[nid:[0-9]*\]/', $values['nid'], $match)) {
      form_set_value($form['options_group']['nid'], substr(rtrim($match[0], ']'), 5), $form_state);
    }
    else{
      form_set_error('nid', t('Enter a node id or use the autocomplete widget to select an existing node.'));
    }
  }
}

/**
 * Submit handler for ccl_add_form().
 *
 * @see ccl_add_form()
 */
function ccl_add_form_submit($form, &$form_state) {
  $values = $form_state['values'];

  // Check if we have a global setting
  if ($values['global_block'] && $values['link_type'] == 'block' || $values['link_type'] == 'node' && $values['node_options'] == 'global') {
    $global = 1;
  }
  else{
    $global = 0;
  }

  $record = array(
    'type' => $values['link_type'],
    'global' => $global,
    'ct' => ($values['link_type'] == 'node' && $values['node_options'] == 'ct' ? $values['ct'] : ''),
    'nid' => !empty($values['nid']) ? $values['nid'] : 0,
    'block' => ($values['link_type'] == 'block' && !$values['global_block'] ? $values['block'] : ''),
    'title' => $values['title'],
    'link' => $values['link'],
  );

  if (isset($form_state['clid'])) {
    $record['clid'] = $form_state['clid'];
    $res = drupal_write_record('ccl', $record, 'clid');
  }
  else{
    $res = drupal_write_record('ccl', $record);
  }

  if ($res) {
    drupal_set_message(t('Contextual link saved.'));
    _ccl_update_cache();
  }
  else{
    drupal_set_message(t('There was an error writing this record to the database. Please try again.'), 'error');
  }
  drupal_goto('admin/config/user-interface/ccl');
}

/**
 * Link delete confirmation page.
 */
function ccl_delete_confirm($form, &$form_state, $id) {
  $form['clid'] = array(
    '#type' => 'value',
    '#value' => $id,
  );
  return confirm_form($form,
    t('Are you sure you want to delete the link?'),
    'admin/config/user-interface/ccl',
    t('This action cannot be undone.'),
    t('Delete'), t('Cancel')
  );
}

/**
 * Submit handler for link deletion page.
 */
function ccl_delete_confirm_submit($form, &$form_state) {
  if ($form_state['values']['confirm']) {
    db_delete('ccl')
      ->condition('clid', $form_state['values']['clid'])
      ->execute();
    drupal_set_message(t('Link removed.'));
    _ccl_update_cache();
    drupal_goto('admin/config/user-interface/ccl');
  }
}

/**
 * Node ID autocomplete callback function.
 */
function ccl_node_autocomplete($string = '') {
  $matches = array();
  if ($string) {
    $result = db_select('node', 'n')
      ->fields('n', array('title', 'nid'))
      ->condition('title', db_like($string) . '%', 'LIKE')
      ->range(0, 10)
      ->execute();
    foreach ($result as $node) {
      $matches[check_plain($node->title) . ' [nid:' . $node->nid . ']'] = check_plain($node->title);
    }
  }
  drupal_json_output($matches);
}
