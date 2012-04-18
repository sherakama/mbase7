<?php

/**
 * @file
 * Absolute Messages module admin functions.
 */

/**
 * Implements hook_admin_settings().
 */
function absolute_messages_admin_settings_form() {
  $form = array();

  $form['general'] = array(
    '#type'         => 'fieldset',
    '#title'        => t('General settings'),
  );
  $form['general']['absolute_messages_dismiss_all_count'] = array(
    '#type'           => 'textfield',
    '#title'          => t('Show "Dismiss all messages" when number of messages is higher than'),
    '#default_value'  => variable_get('absolute_messages_dismiss_all_count', 2),
  );
  $form['general']['absolute_messages_display_lines'] = array(
    '#type'           => 'textfield',
    '#title'          => t('Maximum number of message lines to display'),
    '#description'    => t('If set, only first n lines of each message will be displayed, expanding to full message after clicking on it. Useful for length messages. Leave empty for no limit.'),
    '#default_value'  => variable_get('absolute_messages_display_lines', ''),
  );
  $form['general']['absolute_messages_fixed'] = array(
    '#type'           => 'checkbox',
    '#title'          => t("Fix message bar to the top of browser's viewport"),
    '#description'    => t("Enabling this option will always keep messages at the top of the visible area within the browser's window, over the page content (without pushing it down). They will remain at that position regardless of scrolling."),
    '#default_value'  => variable_get('absolute_messages_fixed', FALSE),
  );
  $form['general']['absolute_messages_group'] = array(
    '#type'           => 'checkbox',
    '#title'          => t('Group duplicate messages'),
    '#description'    => t("Enabling this option will group identical messages, displaying only one instead and adding a number of repetitions at the end."),
    '#default_value'  => variable_get('absolute_messages_group', FALSE),
  );

  $form['tabs'] = array(
    '#type' => 'vertical_tabs',
    '#attached' => array(
      'js' => array(drupal_get_path('module', 'absolute_messages') . '/absolute_messages.admin.js'),
    ),
  );

  // Automatic dismissal settings.
  $form['tabs']['autodismiss'] = array(
    '#type'         => 'fieldset',
    '#title'        => t('Automatic dismissal'),
    '#description'  => t('Automatically dismiss messages after a specific time.'),
  );
  foreach (_absolute_messages_message_types() as $message_type) {
    $form['tabs']['autodismiss']['absolute_messages_dismiss_' . $message_type] = array(
      '#type'           => 'checkbox',
      '#title'          => t('Automatically dismiss %type messages', array('%type' => $message_type)),
      '#default_value'  => variable_get('absolute_messages_dismiss_' . $message_type, FALSE),
    );
    $form['tabs']['autodismiss']['absolute_messages_dismiss_time_' . $message_type] = array(
      '#type'           => 'textfield',
      '#title'          => t('Automatically dismiss %type messages after', array('%type' => $message_type)),
      '#default_value'  => variable_get('absolute_messages_dismiss_time_' . $message_type, ''),
      '#size'           => 3,
      '#field_suffix'   => t('seconds'),
      '#states'         => array(
        'visible'         => array(
          ':input[name="absolute_messages_dismiss_' . $message_type . '"]' => array('checked' => TRUE),
        ),
      ),
      '#attributes' => array('style' => 'width: auto'),
    );
  }

  // Page specific visibility settings.
  $php_access = user_access('use PHP for tracking visibility');
  $visibility = variable_get('absolute_messages_visibility_pages', 0);
  $pages = variable_get('absolute_messages_pages', NULL);

  // If PHP mode is selected and current user does not have permission
  // "use PHP for tracking visibility", let's just pass current value
  // and do not display the tab at all.
  if ($visibility == 2 && !$php_access) {
    $form['tabs']['pages'] = array();
    $form['tabs']['pages']['visibility'] = array('#type' => 'value', '#value' => 2);
    $form['tabs']['pages']['pages'] = array('#type' => 'value', '#value' => $pages);
  }
  else {
    $form['tabs']['pages'] = array(
      '#type'         => 'fieldset',
      '#title'        => t('Pages'),
      '#collapsible'  => TRUE,
      '#collapsed'    => TRUE,
    );
    $options = array(
      t('Every page except the listed pages'),
      t('The listed pages only'),
    );
    $description = t("Specify pages by using their paths. Enter one path per line. The '*' character is a wildcard. Example paths are %blog for the blog page and %blog-wildcard for every personal blog. %front is the front page.", array('%blog' => 'blog', '%blog-wildcard' => 'blog/*', '%front' => '<front>'));

    if (module_exists('php') && $php_access) {
      $options[] = t('Pages on which this PHP code returns <code>TRUE</code> (experts only)');
      $title = t('Pages or PHP code');
      $description .= ' ' . t('If the PHP option is chosen, enter PHP code between %php. Note that executing incorrect PHP code can break your Drupal site.', array('%php' => '<?php ?>'));
    }
    else {
      $title = t('Pages');
    }
    $form['tabs']['pages']['absolute_messages_visibility_pages'] = array(
      '#type' => 'radios',
      '#title' => t('Enable Absolute Messages on specific pages'),
      '#options' => $options,
      '#default_value' => $visibility,
    );
    $form['tabs']['pages']['absolute_messages_pages'] = array(
      '#type' => 'textarea',
      '#title' => $title,
      '#title_display' => 'invisible',
      '#default_value' => $pages,
      '#description' => $description,
      '#rows' => 10,
    );
  }

  // Role specific visibility settings.
  $form['tabs']['roles'] = array(
    '#type' => 'fieldset',
    '#title' => t('Roles'),
  );

  $form['tabs']['roles']['absolute_messages_visibility_roles'] = array(
    '#type' => 'radios',
    '#title' => t('Enable Absolute Messages for specific roles'),
    '#options' => array(
      t('Enable for the selected roles only'),
      t('Enable for every role except the selected ones'),
    ),
    '#default_value' => variable_get('absolute_messages_visibility_roles', 0),
  );

  $role_options = array_map('check_plain', user_roles());
  $form['tabs']['roles']['absolute_messages_roles'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Roles'),
    '#default_value' => variable_get('absolute_messages_roles', array()),
    '#options' => $role_options,
    '#description' => t('Enable Absolute Messages only for the selected role(s). If none of the roles are selected, Absolute Messages will be enabled for all users.'),
  );
  
  // Advanced settings.
  $form['tabs']['advanced'] = array(
    '#type'         => 'fieldset',
    '#title'        => t('Advanced settings'),
    '#collapsible'  => TRUE,
    '#collapsed'    => TRUE,
  );
  $form['tabs']['advanced']['absolute_messages_no_js_check'] = array(
    '#type'           => 'checkbox',
    '#title'          => t('Do not check "has_js" cookie'),
    '#description'    => t('In PressFlow 6 "has_js" cookie was dropped, therefore there is no option to detect if JS is enabled without setting it back (which is not always desired). This option could skip "has_js" cookie check, and make the module work with default Pressflow 6 installations. The downside to this solution though is that the module will also try to work even for users which have JS really disabled, thus making them unable to see ANY system messages.'),
    '#default_value'  => variable_get('absolute_messages_no_js_check', FALSE),
  );

  return system_settings_form($form);
}
