<?php
// $Id$
/**
 * @file
 * Provide views data and handler information for workflow.module.
 */

/**
 * @defgroup views_workflow_module workflow.module handlers
 *
 * Includes the 'workflow_node' and 'workflow_state' tables, but generates a
 * pseudo-table for each separate workflow that's been set up.
 * @{
 */

/**
 * Implements hook_views_data().
 */
function workflow_views_views_data() {
  $data['workflow_states']['table']['group']  = t('Workflow');
  // Explain how this table joins to others.
  $data['workflow_states']['table']['join'] = array(
    'node' => array(
      'field' => 'sid',
      'left_table' => 'workflow_node',
      'left_field' => 'sid',
    ),
  );
  $data['workflow_states']['weight'] = array(
    'title' => t('State weight'),
    'help' => t('The weight of the current workflow state that the node is in.'),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
  );
  $data['workflow_states']['state'] = array(
    'title' => t('Current state name'),
    'help' => t('The readable name of the workflow state that the node is in. (Less efficient, use only when click-sorting by state name.)'),
    'field' => array(
      'handler' => 'views_handler_field',
      'click sortable' => TRUE,
    ),
  );
  $data['workflow_node']['table']['group']  = t('Workflow');
  // Explain how this table joins to others.
  $data['workflow_node']['table']['join'] = array(
    'node' => array(
      'field' => 'nid',
      'left_table' => 'node',
      'left_field' => 'nid',
    ),
  );
  $data['workflow_node']['sid'] = array(
    'title' => t('Current state'),
    'help' => t('The current workflow state that the node is in.'),
    'field' => array(
      'handler' => 'workflow_views_handler_field_sid',
      'click sortable' => TRUE,
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_numeric',
      'click sortable' => TRUE,
      'numeric' => TRUE,
      'name table' => 'workflow_states',
      'name field' => 'state',
    ),
    'filter' => array(
      'handler' => 'workflow_views_handler_filter_sid',
      'numeric' => TRUE,
    ),
  );
  $data['workflow_scheduled_transition']['table']['group']  = t('Workflow');
  // Explain how this table joins to others.
  $data['workflow_scheduled_transition']['table']['join'] = array(
    'node' => array(
      'field' => 'nid',
      'left_table' => 'node',
      'left_field' => 'nid',
    ),
  );
  $data['workflow_scheduled_transition']['sid'] = array(
    'title' => t('Scheduled state'),
    'help' => t('The current workflow state that the node is in.'),
    'field' => array(
      'handler' => 'workflow_views_handler_field_sid',
      'click sortable' => TRUE,
    ),
    'filter' => array(
      'handler' => 'workflow_views_handler_filter_sid',
      'numeric' => TRUE,
    ),
  );
  $data['workflow_node']['stamp'] = array(
    'title' => t('Current time'),
    'help' => t('The time at which the node moved to the current state.'),
    'field' => array(
      'handler' => 'views_handler_field_date',
      'click sortable' => TRUE,
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_date',
      'numeric' => TRUE,
    ),
    'sort' => array(
      'handler' => 'views_handler_sort_date',
    ),
  );
  $data['workflow_scheduled_transition']['scheduled'] = array(
    'title' => t('Scheduled time'),
    'help' => t('The time at which the node will change workflow states.'),
    'field' => array(
      'handler' => 'views_handler_field_date',
      'click sortable' => TRUE,
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_date',
      'numeric' => TRUE,
    ),
    'sort' => array(
      'handler' => 'views_handler_sort_date',
    ),
  );
  $data['workflow_scheduled_transition']['comment'] = array(
    'title' => t('Scheduled comment'),
    'help' => t('A comment describing why the node was scheduled for state transition.'),
    'field' => array(
      'handler' => 'views_handler_field_xss',
      'click sortable' => TRUE,
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_string',
      'numeric' => TRUE,
    ),
  );
  $data['workflow_node_history']['table']['group']  = t('Workflow');
  // Explain how this table joins to others.
  $data['workflow_node_history']['table']['join'] = array(
    'node' => array(
      'field' => 'nid',
      'left_table' => 'node',
      'left_field' => 'nid',
    ),
  );
  $data['workflow_node_history']['sid'] = array(
    'title' => t('Previous state'),
    'help' => t('A workflow state that the node was in previously.'),
    'field' => array(
      'handler' => 'workflow_views_handler_field_sid',
      'click sortable' => TRUE,
    ),
    'filter' => array(
      'handler' => 'workflow_views_handler_filter_sid',
      'numeric' => TRUE,
    ),
  );
  $data['workflow_node_history']['stamp'] = array(
    'title' => t('Previous time'),
    'help' => t('The time at which the node moved from one state to another.'),
    'field' => array(
      'handler' => 'views_handler_field_date',
      'click sortable' => TRUE,
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_date',
      'numeric' => TRUE,
    ),
    'sort' => array(
      'handler' => 'views_handler_sort_date',
    ),
  );
  $data['workflow_node_history']['comment'] = array(
    'title' => t('Previous comment'),
    'help' => t('A comment describing why the node was moved from one state to another in the past.'),
    'field' => array(
      'handler' => 'views_handler_field_xss',
      'click sortable' => TRUE,
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_string',
      'numeric' => TRUE,
    ),
  );
  return $data;
}

/**
 * @}
 */