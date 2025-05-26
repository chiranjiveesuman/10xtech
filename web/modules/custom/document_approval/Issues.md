# Document Approval Module - Issues and Solutions

## 🚨 **IDENTIFIED PROBLEMS:**

1. **Missing document_submission table** - Entity table not created
2. **Orphaned admin_entries module reference** - Causing update failures
3. **Entity definition not properly installed** - Database schema issues

## 🔧 **SOLUTIONS PROVIDED:**

### **Quick Fix (Recommended):**
Run the batch file: `fix_module.bat`

### **Manual Fix:**
Follow the steps in `FIX_SCRIPT.md`

### **Advanced Fix:**
Run the PHP script: `php web/modules/custom/document_approval/fix_database.php`

## 📋 **ORIGINAL ERROR LOG:**

PS C:\xampp\htdocs\install-dir> .\vendor\bin\drush pmu document_approval
In ExceptionHandler.php line 56:

  SQLSTATE[42S02]: Base table or view not found: 1146 Table 'new.document_submission' doesn't exist: SELECT "base_table"."id" AS "id", "base_
  table"."id" AS "base_table_id"
  FROM
  "document_submission" "base_table"
  LIMIT 1 OFFSET 0; Array
  (
  )


In StatementWrapperIterator.php line 113:

  SQLSTATE[42S02]: Base table or view not found: 1146 Table 'new.document_submission' doesn't exist


PS C:\xampp\htdocs\install-dir> .\vendor\bin\drush cr
 [success] Cache rebuild complete.
PS C:\xampp\htdocs\install-dir> .\vendor\bin\drush en document_approval
 [notice] Already enabled: document_approval
PS C:\xampp\htdocs\install-dir> .\vendor\bin\drush updb
 [warning] Schema information for module document_approval was missing from the database. You should manually review the module updates and your database to check if any updates have been skipped up to, and including, document_approval_update_8001().
 [error]   (Currently using Missing or invalid module The following module is marked as installed in the core.extension
configuration, but it is missing:
 * admin_entries

Review the  suggestions for resolving this incompatibility [1] to repair your
installation, and then re-run update.php.

[1] https://www.drupal.org/docs/updating-drupal/troubleshooting-database-updates
)

 Requirements check reports errors. Do you wish to continue? (yes/no) [yes]:
 > yes

 --------------- ----------------------------------------------------- --------------- --------------------------------------------------------
  Module          Update ID                                             Type            Description
 --------------- ----------------------------------------------------- --------------- --------------------------------------------------------
  system          10100                                                 hook_update_n   10100 - Remove the year 2038 date limitation.
  system          10101                                                 hook_update_n   10101 - Change the {batch} table [bid] field to
                                                                                        serial.
  system          10201                                                 hook_update_n   10201 - Clear left over entries in the revision data
                                                                                        table.
  block_content   10100                                                 hook_update_n   10100 - Update entity definition to handle revision
                                                                                        routes.
  block_content   10200                                                 hook_update_n   10200 - Remove the unique values constraint from block
                                                                                        content info fields.
  block_content   10300                                                 hook_update_n   10300 - Apply index to reusable column.
  comment         10100                                                 hook_update_n   10100 - Remove the year 2038 date limitation.
  dblog           10100                                                 hook_update_n   10100 - Remove the year 2038 date limitation.
  dblog           10101                                                 hook_update_n   10101 - Converts the 'wid' of the 'watchdog' table to
                                                                                        a big integer.
  help            10200                                                 hook_update_n   10200 - Install search index table for help topics.
  history         10100                                                 hook_update_n   10100 - Remove the year 2038 date limitation.
  taxonomy        10100                                                 hook_update_n   10100 - Update entity definition to handle revision
                                                                                        routes.
  big_pipe        html5_placeholders                                    post-update     Clear the render cache.
  block_content   block_library_view_permission                         post-update     Update block_content 'block library' view permission.
  block_content   move_custom_block_library                             post-update     Moves the custom block library to Content.
  block_content   revision_type                                         post-update     Update configuration for revision type.
  block_content   sort_permissions                                      post-update     Update permissions for users with "administer blocks"
                                                                                        permission.
  ckeditor5       code_block                                            post-update     Updates Text Editors using CKEditor 5 Code Block.
  ckeditor5       list_multiblock                                       post-update     Updates Text Editors using CKEditor 5.
  ckeditor5       list_start_reversed                                   post-update     Updates Text Editors using CKEditor 5 to native List
                                                                                        "start" functionality.
  contact         set_empty_default_form_to_null                        post-update     Converts empty `default_form` in settings to NULL.
  editor          image_lazy_load                                       post-update     Enable filter_image_lazy_load if editor_file_reference
                                                                                        is enabled.
  editor          sanitize_image_upload_settings                        post-update     Clean up image upload settings.
  file            add_default_filename_sanitization_configuration       post-update     Add default filename sanitization configuration.
  file            add_permissions_to_roles                              post-update     Grant all non-anonymous roles the 'delete own files'
                                                                                        permission.
  filter          consolidate_filter_config                             post-update     Change filter_settings to type mapping.
  filter          sort_filters                                          post-update     Sorts filter format filter configuration.
  help            add_permissions_to_roles                              post-update     Grant all admin roles the 'access help pages'
                                                                                        permission.
  help            help_topics_search                                    post-update     Install or update config for help topics if the search
                                                                                        module installed.
  help            help_topics_uninstall                                 post-update     Uninstall the help_topics module if installed.
  node            set_node_type_description_and_help_to_null            post-update     Converts empty `description` and `help` in content
                                                                                        types to NULL.
  path_alias      drop_path_alias_status_index                          post-update     Remove the path_alias__status index.
  system          add_description_to_entity_form_mode                   post-update     Update description for form modes.
  system          add_description_to_entity_view_mode                   post-update     Update description for view modes.
  system          add_langcode_to_all_translatable_config               post-update     Adds a langcode to all simple config which needs it.
  system          amend_config_sync_readme_url                          post-update     Fix path in README.txt in CONFIG_SYNC_DIRECTORY.
  system          convert_empty_country_and_timezone_settings_to_null   post-update     Updates system.date config to NULL for empty country
                                                                                        and timezone defaults.
  system          enable_password_compatibility                         post-update     Enable the password compatibility module.
  system          linkset_settings                                      post-update     Add new menu linkset endpoint setting.
  system          mail_notification_setting                             post-update     Adds default value for the mail_notification config
                                                                                        parameter.
  system          mailer_dsn_settings                                   post-update     Add new default mail transport dsn.
  system          mailer_structured_dsn_settings                        post-update     Add new default mail transport dsn.
  system          move_development_settings_to_keyvalue                 post-update     Move development settings from state to raw key-value
                                                                                        storage.
  system          remove_asset_entries                                  post-update     Remove redundant asset state and config.
  system          remove_asset_query_string                             post-update     Remove redundant asset query string state.
  system          set_blank_log_url_to_null                             post-update     Updates system.theme.global:logo.url config if it's
                                                                                        still at the default.
  system          set_cron_logging_setting_to_boolean                   post-update     Fix system.cron:logging values to boolean.
  system          timestamp_formatter                                   post-update     Update timestamp formatter settings for entity view
                                                                                        displays.
  taxonomy        set_new_revision                                      post-update     Re-save Taxonomy configurations with new_revision
                                                                                        config.
  taxonomy        set_vocabulary_description_to_null                    post-update     Converts empty `description` in vocabularies to NULL.
  text            allowed_formats                                       post-update     Add allowed_formats setting to existing text fields.
  update          set_blank_fetch_url_to_null                           post-update     Updates update.settings:fetch.url config if it's still
                                                                                        at the default.
  user            sort_permissions                                      post-update     No-op update.
  user            sort_permissions_again                                post-update     Ensure permissions stored in role configuration are
                                                                                        sorted using the schema.
  views           add_missing_labels                                    post-update     Add labels to views which don't have one.
  views           boolean_custom_titles                                 post-update     Update Views config schema to make boolean custom
                                                                                        titles translatable.
  views           fix_revision_id_part                                  post-update     Fix '-revision_id' replacement token syntax.
  views           oembed_eager_load                                     post-update     Add eager load option to all oembed type field
                                                                                        configurations.
  views           pager_heading                                         post-update     Adds a default pager heading.
  views           remove_default_argument_skip_url                      post-update     Remove default_argument_skip_url setting.
  views           remove_skip_cache_setting                             post-update     Remove the skip_cache settings.
  views           rendered_entity_field_cache_metadata                  post-update     Removes entity display cache metadata from views with
                                                                                        rendered entity fields.
  views           responsive_image_lazy_load                            post-update     Add lazy load options to all responsive image type
                                                                                        field configurations.
  views           taxonomy_filter_user_context                          post-update     Removes User context from views with taxonomy filters.
  views           timestamp_formatter                                   post-update     Update timestamp formatter settings for views.
  views           views_data_argument_plugin_id                         post-update     Post update configured views for entity reference
                                                                                        argument plugin IDs.
 --------------- ----------------------------------------------------- --------------- --------------------------------------------------------

 Do you wish to run the specified pending updates? (yes/no) [yes]:
 > yes

 [notice] Module admin_entries has an entry in the system.schema key/value storage, but is missing from your site. <a href="https://www.drupal.org/node/3137656">More information about this error</a>.
>  [notice] Update started: system_update_10100
>  [notice] Update completed: system_update_10100
>  [notice] Update started: block_content_update_10100
>  [notice] Added revision routes to Content block entity type.
>  [notice] Update completed: block_content_update_10100
>  [notice] Update started: system_update_10101
>  [notice] Update completed: system_update_10101
>  [notice] Update started: block_content_update_10200
>  [notice] Update completed: block_content_update_10200
>  [notice] Update started: dblog_update_10100
>  [notice] Update completed: dblog_update_10100
>  [notice] Update started: system_update_10201
>  [notice] Update completed: system_update_10201
>  [notice] Update started: block_content_update_10300
>  [notice] Update completed: block_content_update_10300
>  [notice] Update started: comment_update_10100
>  [notice] Update completed: comment_update_10100
>  [notice] Update started: dblog_update_10101
>  [notice] Update completed: dblog_update_10101
>  [notice] Update started: help_update_10200
>  [notice] Update completed: help_update_10200
>  [notice] Update started: history_update_10100
>  [notice] Update completed: history_update_10100
>  [notice] Update started: taxonomy_update_10100
>  [notice] Added revision routes to Taxonomy Term entity type.
>  [notice] Update completed: taxonomy_update_10100
>  [notice] Update started: big_pipe_post_update_html5_placeholders
>  [notice] Update completed: big_pipe_post_update_html5_placeholders
>  [notice] Update started: block_content_post_update_block_library_view_permission
>  [notice] Update completed: block_content_post_update_block_library_view_permission
>  [notice] Update started: block_content_post_update_move_custom_block_library
>  [notice] Update completed: block_content_post_update_move_custom_block_library
>  [notice] Update started: block_content_post_update_revision_type
>  [notice] Update completed: block_content_post_update_revision_type
>  [notice] Update started: block_content_post_update_sort_permissions
>  [notice] Update completed: block_content_post_update_sort_permissions
>  [notice] Update started: ckeditor5_post_update_code_block
>  [notice] Update completed: ckeditor5_post_update_code_block
>  [notice] Update started: ckeditor5_post_update_list_multiblock
>  [notice] Update completed: ckeditor5_post_update_list_multiblock
>  [notice] Update started: ckeditor5_post_update_list_start_reversed
>  [notice] Update completed: ckeditor5_post_update_list_start_reversed
>  [notice] Update started: contact_post_update_set_empty_default_form_to_null
>  [notice] Update completed: contact_post_update_set_empty_default_form_to_null
>  [notice] Update started: editor_post_update_image_lazy_load
>  [notice] Update completed: editor_post_update_image_lazy_load
>  [notice] Update started: editor_post_update_sanitize_image_upload_settings
>  [notice] Update completed: editor_post_update_sanitize_image_upload_settings
>  [notice] Update started: file_post_update_add_default_filename_sanitization_configuration
>  [notice] Update completed: file_post_update_add_default_filename_sanitization_configuration
>  [notice] Update started: file_post_update_add_permissions_to_roles
>  [notice] Update completed: file_post_update_add_permissions_to_roles
>  [notice] Update started: filter_post_update_consolidate_filter_config
>  [notice] Update completed: filter_post_update_consolidate_filter_config
>  [notice] Update started: filter_post_update_sort_filters
>  [notice] Update completed: filter_post_update_sort_filters
>  [notice] Update started: help_post_update_add_permissions_to_roles
>  [notice] Update completed: help_post_update_add_permissions_to_roles
>  [notice] Update started: help_post_update_help_topics_search
>  [notice] Update completed: help_post_update_help_topics_search
>  [notice] Update started: help_post_update_help_topics_uninstall
>  [notice] Update completed: help_post_update_help_topics_uninstall
>  [notice] Update started: node_post_update_set_node_type_description_and_help_to_null
>  [notice] Update completed: node_post_update_set_node_type_description_and_help_to_null
>  [notice] Update started: path_alias_post_update_drop_path_alias_status_index
>  [notice] Update completed: path_alias_post_update_drop_path_alias_status_index
>  [notice] Update started: system_post_update_add_description_to_entity_form_mode
>  [notice] Update completed: system_post_update_add_description_to_entity_form_mode
>  [notice] Update started: system_post_update_add_description_to_entity_view_mode
>  [notice] Update completed: system_post_update_add_description_to_entity_view_mode
>  [notice] Update started: system_post_update_add_langcode_to_all_translatable_config
>  [notice] Processed 50 items of 214.
>  [notice] Processed 100 items of 214.
>  [notice] Processed 150 items of 214.
>  [notice] Processed 200 items of 214.
>  [notice] Finished updating simple config langcodes.
>  [notice] Update completed: system_post_update_add_langcode_to_all_translatable_config
>  [notice] Update started: system_post_update_amend_config_sync_readme_url
>  [notice] Amended configuration synchronization readme file content.
>  [notice] Update completed: system_post_update_amend_config_sync_readme_url
>  [notice] Update started: system_post_update_convert_empty_country_and_timezone_settings_to_null
>  [notice] Update completed: system_post_update_convert_empty_country_and_timezone_settings_to_null
>  [notice] Update started: system_post_update_enable_password_compatibility
>  [error]  The module admin_entries does not exist.
>  [error]  Update failed: system_post_update_enable_password_compatibility

In ProcessBase.php line 171:

  Unable to decode output into JSON: Syntax error

  {
      "0": {
          "system": {
              "10100": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "update"
              },
              "10101": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "update"
              },
              "10201": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "update"
              },
              "add_description_to_entity_form_mode": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "post_update"
              },
              "add_description_to_entity_view_mode": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "post_update"
              },
              "add_langcode_to_all_translatable_config": {
                  "results": {
                      "query": "Finished updating simple config langcodes.",
                      "success": true
                  },
                  "type": "post_update"
              },
              "amend_config_sync_readme_url": {
                  "results": {
                      "query": "Amended configuration synchronization readme file content.",
                      "success": true
                  },
                  "type": "post_update"
              },
              "convert_empty_country_and_timezone_settings_to_null": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "post_update"
              },
              "enable_password_compatibility": {
                  "#abort": {
                      "success": false,
                      "query": "<em class="placeholder">Drupal\Core\Extension\Exception\UnknownExtensionException</em>: The module admin_entr
  ies does not exist. in <em class="placeholder">Drupal\Core\Extension\ExtensionList-&gt;getPathname()</em> (line <em class="placeholder">519
  </em> of <em class="placeholder">C:\xampp\htdocs\install-dir\web\core\lib\Drupal\Core\Extension\ExtensionList.php</em>)."
                  }
              }
          },
          "block_content": {
              "10100": {
                  "results": {
                      "query": "Added revision routes to Content block entity type.",
                      "success": true
                  },
                  "type": "update"
              },
              "10200": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "update"
              },
              "10300": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "update"
              },
              "block_library_view_permission": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "post_update"
              },
              "move_custom_block_library": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "post_update"
              },
              "revision_type": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "post_update"
              },
              "sort_permissions": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "post_update"
              }
          },
          "dblog": {
              "10100": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "update"
              },
              "10101": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "update"
              }
          },
          "comment": {
              "10100": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "update"
              }
          },
          "help": {
              "10200": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "update"
              },
              "add_permissions_to_roles": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "post_update"
              },
              "help_topics_search": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "post_update"
              },
              "help_topics_uninstall": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "post_update"
              }
          },
          "history": {
              "10100": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "update"
              }
          },
          "taxonomy": {
              "10100": {
                  "results": {
                      "query": "Added revision routes to Taxonomy Term entity type.",
                      "success": true
                  },
                  "type": "update"
              }
          },
          "big_pipe": {
              "html5_placeholders": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "post_update"
              }
          },
          "ckeditor5": {
              "code_block": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "post_update"
              },
              "list_multiblock": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "post_update"
              },
              "list_start_reversed": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "post_update"
              }
          },
          "contact": {
              "set_empty_default_form_to_null": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "post_update"
              }
          },
          "editor": {
              "image_lazy_load": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "post_update"
              },
              "sanitize_image_upload_settings": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "post_update"
              }
          },
          "file": {
              "add_default_filename_sanitization_configuration": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "post_update"
              },
              "add_permissions_to_roles": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "post_update"
              }
          },
          "filter": {
              "consolidate_filter_config": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "post_update"
              },
              "sort_filters": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "post_update"
              }
          },
          "node": {
              "set_node_type_description_and_help_to_null": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "post_update"
              }
          },
          "path_alias": {
              "drop_path_alias_status_index": {
                  "results": {
                      "query": null,
                      "success": true
                  },
                  "type": "post_update"
              }
          },
          "#abort": [
              "system_post_update_enable_password_compatibility"
          ]
      },
      "drush_batch_process_finished": true
  }