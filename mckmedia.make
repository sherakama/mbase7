; Core version
; ------------
core = 6.x


; API version
; ------------
api = 2


; Core project
; ------------

projects[] =  drupal


; CONTRIB MODULES
; ---------------
projects[admin_menu][subdir] = "contrib"
projects[admin_theme][subdir] = "contrib"
projects[adminrole][subdir] = "contrib"

projects[advanced_help][subdir] = "contrib"
projects[autocomplete_widgets][subdir] = "contrib"
projects[backup_migrate][subdir] = "contrib"
projects[better_formats][subdir] = "contrib"
projects[browserclass][subdir] = "contrib"
projects[cck][subdir] = "contrib"
projects[checkbox_validate][subdir] = "contrib"
projects[content_taxonomy][subdir] = "contrib"

projects[ctools][subdir] = "contrib"
projects[context][subdir] = "contrib"
projects[contextual][subdir] = "contrib"
projects[date][subdir] = "contrib"

projects[devel][subdir] = "contrib"
projects[devel_info][subdir] = "contrib"

projects[dialog][subdir] = "contrib"
projects[dialog][version] = "1.0-alpha1"

projects[disqus][subdir] = "contrib"
projects[email][subdir] = "contrib"
projects[features][subdir] = "contrib"
projects[filefield][subdir] = "contrib"
projects[filefield_sources][subdir] = "contrib"
projects[format_number][subdir] = "contrib"
projects[globalredirect][subdir] = "contrib"
projects[google_analytics][subdir] = "contrib"

projects[imageapi][subdir] = "contrib"
projects[imagecache][subdir] = "contrib"
projects[imagecache_actions][subdir] = "contrib"
projects[imagecache_effects][subdir] = "contrib"
projects[imagecache_profiles][subdir] = "contrib"
projects[imagefield][subdir] = "contrib"
projects[imagecache_actions][subdir] = "contrib"
projects[imagecache_effects][subdir] = "contrib"
projects[imagecache_profiles][subdir] = "contrib"

projects[imce][subdir] = "contrib"
projects[imce_mkdir][subdir] = "contrib"
projects[imce_rename][subdir] = "contrib"
projects[imce_wysiwyg][subdir] = "contrib"

projects[jquery_ui][subdir] = "contrib"
projects[jquery_update][subdir] = "contrib"
projects[jquery_update][version] = "2.x-dev"

projects[link][subdir] = "contrib"

projects[menu_attributes][subdir] = "contrib"
projects[menu_block][subdir] = "contrib"
projects[menutrails][subdir] = "contrib"

projects[nodewords][subdir] = "contrib"
projects[page_title][subdir] = "contrib"
projects[pathauto][subdir] = "contrib"
projects[pathrules][subdir] = "contrib"

projects[phone][subdir] = "contrib"
projects[salt][subdir] = "contrib"

projects[skinr][subdir] = "contrib"
projects[skinr][version] = "2.x-dev"

projects[token][subdir] = "contrib"
projects[token_extra][subdir] = "contrib"
projects[taxonomy_access][subdir] = "contrib"
projects[vertical_tabs][subdir] = "contrib"
projects[vertical_tabs][version] = "1.x-dev"

projects[views][subdir] = "contrib"
projects[views_attach][subdir] = "contrib"
projects[views_attachment_block][subdir] = "contrib"
projects[views_bonus][subdir] = "contrib"
projects[views_customfield][subdir] = "contrib"
projects[views_clone_display][subdir] = "contrib"
projects[draggableviews][subdir] = "contrib"
projects[views_bonus][subdir] = "contrib"


projects[webform][subdir] = "contrib"
projects[webformblock][subdir] = "contrib"

projects[wysiwyg][subdir] = "contrib"
projects[wysiwyg_filter][subdir] = "contrib"

projects[xmlsitemap][subdir] = "contrib"



; UTILITY
; ----------





; THEMES
; ---------

; Admin Themes
projects[tao][location] = http://code.developmentseed.org/fserver
projects[rubik][location] = http://code.developmentseed.org/fserver



; LIBRARIES
; ----------

; TinyMCE
libraries[tinymce][download][type] = "get"
libraries[tinymce][download][url] = "http://downloads.sourceforge.net/project/tinymce/TinyMCE/3.2.7/tinymce_3_2_7.zip"
libraries[tinymce][directory_name] = "tinymce"

libraries[jquery_ui][download][type] = "get"
libraries[jquery_ui][download][url] = "http://jquery-ui.googlecode.com/files/jquery-ui-1.7.3.zip"
libraries[jquery_ui][directory_name] = "jquery.ui"
libraries[jquery_ui][destination] = "modules/contrib/jquery_ui"






