Drupal.behaviors.plinkthemesettings = {
  // Constructor
  attach: function(context, settings) {
  (function ($) {
    
      // Variables
      var pl = Drupal.behaviors.plinkthemesettings; // shortcut var
      pl.master_preview =   $("#edit-page-layout-options .content-region");
      pl.grid_enabled =     $("#edit-enable-grid-system");
      pl.grid_width =       $("#edit-grid-master-width");
      pl.grid_columns =     $("#edit-master-grid-columns");
      pl.grid_unit =        $("#edit-grid-fixed-or-fluid");
      
    
      // Change the layout options radios into clickable things 
      Drupal.behaviors.plinkthemesettings.plink_settings_fancy_radios();
    
      // Handle Changes to the theme settings and update the preview 
      Drupal.behaviors.plinkthemesettings.plink_settings_update_master_preview_events();
    
      // Create the sidebar grid select fields
      Drupal.behaviors.plinkthemesettings.plink_settings_grid_select_handlers();
    
      // Add events to the changes of the sidbebar widths so they update the thumbnail preview
      Drupal.behaviors.plinkthemesettings.plink_settings_region_width_preview_events();
    
      // Preview updates for the full width region toggles
      Drupal.behaviors.plinkthemesettings.full_width_region_events();

      // Update the preview
      
      // main
      Drupal.behaviors.plinkthemesettings.plink_settings_region_width_preview_event_handlers($("#edit-main-secondary-width"), $("#edit-main-tertiary-width"), $("#edit-main-layout"), true);
      // Media Query 1
      Drupal.behaviors.plinkthemesettings.plink_settings_region_width_preview_event_handlers($("#edit-mq1-secondary-width"), $("#edit-mq1-tertiary-width"), $("#edit-content-layouts-mq1"), false);
      // Media Query 2
      Drupal.behaviors.plinkthemesettings.plink_settings_region_width_preview_event_handlers($("#edit-mq2-secondary-width"), $("#edit-mq2-tertiary-width"), $("#edit-content-layouts-mq2"), false);
      // Media Query 3
      Drupal.behaviors.plinkthemesettings.plink_settings_region_width_preview_event_handlers($("#edit-mq3-secondary-width"), $("#edit-mq3-tertiary-width"), $("#edit-content-layouts-mq3"), false);
      
  })(jQuery);
  }
  
  
  /* 
    Change the layout radio options into fancy icons
  */
  ,plink_settings_fancy_radios: function() {
    (function ($) {

      // Main Layout Selection Radio Options
      var radios = $(".fancy-radios .form-item");
      radios.find('input').hide();
      radios.find(":checked").parents('.form-item').addClass('active');
      
      // Click handling
      radios.click(function(e){
        e.preventDefault();
        $(this).parents('.fancy-radios:first').find('.form-item').removeClass('active');
        $(this).addClass('active');
        $(this).find('input').attr('checked','checked');
      });
            
    })(jQuery);
  }
  
  
  /* 
    Handle Changes to the theme settings and update the preview
  */
  ,plink_settings_update_master_preview_events: function(){
  (function ($) {
  
    // Master layout theme options
    var layout_options = $("#edit-main-layout .form-item"); 
    
    layout_options.click(function(e){
      e.preventDefault();
      Drupal.behaviors.plinkthemesettings.plink_settings_update_master_preview($(this));
    });
  
    // initial setup
    Drupal.behaviors.plinkthemesettings.plink_settings_update_master_preview($("#edit-main-layout .active"));
    
    
  })(jQuery);
  }
  
  
  /* 
    The update preview function for the mater layout preview
  */
  ,plink_settings_update_master_preview: function(active_selection) {
  (function ($) {  
    var pl = Drupal.behaviors.plinkthemesettings; // shortcut var
    var target = active_selection.find('.layout-block');
    
    pl.master_preview.html(target.html());
    pl.master_preview.attr("class","content-region");
    pl.master_preview.addClass(target.attr('class'));
  
  })(jQuery);  
  }
  
  /*
    Handle initialization and the toggling of different grid selects
    This function updates text fields with the appropriate grid options
  */
  ,plink_settings_grid_select_handlers: function() {
  (function ($) {  
    
    var pl = Drupal.behaviors.plinkthemesettings; // shortcut var
    
    // store grid selections for later
    $("body").data('grid_select_columns', pl.grid_columns.find('option') );
    
    /*
      Change handlers
    */
    
    // Grid width selector
    pl.grid_width.change(function(){
      
     var opts = {
        gwidth : pl.grid_width.val()
        ,grids : pl.grid_columns.val()
      }
      
      // MAIN
      opts.field = $("#edit-main-secondary-width");
      Drupal.behaviors.plinkthemesettings.plink_settings_generate_grid_select(opts);
      opts.field = $("#edit-main-tertiary-width");
      Drupal.behaviors.plinkthemesettings.plink_settings_generate_grid_select(opts);      
      
      $('body').data('current_grids', pl.grid_columns.find(":selected").val());
      pl.grid_columns.html(''); // truncate options
      pl.grid_columns.append($('body').data('grid_select_columns'));      
      
    });
    
    
    // Media Query Grid Widths
    
    // Media Query 1
   $('#edit-mq1-grid-width').change(function(){
      
     var opts = {
        gwidth : $('#edit-mq1-grid-width').val()
        ,grids : pl.grid_columns.val()
      }
      
      opts.field = $("#edit-mq1-secondary-width");
      Drupal.behaviors.plinkthemesettings.plink_settings_generate_grid_select(opts);
      opts.field = $("#edit-mq1-tertiary-width");
      Drupal.behaviors.plinkthemesettings.plink_settings_generate_grid_select(opts);
      
    });
    
    // Media Query 2
     $('#edit-mq2-grid-width').change(function(){

       var opts = {
          gwidth : $('#edit-mq2-grid-width').val()
          ,grids : pl.grid_columns.val()
        }
          
      opts.field = $("#edit-mq2-secondary-width");
      Drupal.behaviors.plinkthemesettings.plink_settings_generate_grid_select(opts);
      opts.field = $("#edit-mq2-tertiary-width");
      Drupal.behaviors.plinkthemesettings.plink_settings_generate_grid_select(opts);
    });
    
    // Media Query 3
    $('#edit-mq3-grid-width').change(function(){

       var opts = {
          gwidth : $('#edit-mq3-grid-width').val()
          ,grids : pl.grid_columns.val()
        }
        
      opts.field = $("#edit-mq3-secondary-width");
      Drupal.behaviors.plinkthemesettings.plink_settings_generate_grid_select(opts);
      opts.field = $("#edit-mq3-tertiary-width");
      Drupal.behaviors.plinkthemesettings.plink_settings_generate_grid_select(opts);
    });
    
    
    
    // Grid columns selector
    pl.grid_columns.change(function(){
      Drupal.behaviors.plinkthemesettings.grid_width.change();
      $('#edit-mq1-grid-width').change();
      $('#edit-mq2-grid-width').change();
      $('#edit-mq3-grid-width').change();
    });
    
    // Grid units selector
    pl.grid_unit.change(function(){
      Drupal.behaviors.plinkthemesettings.grid_width.change();
    });
    
    // Grid enabled/disabled
    pl.grid_enabled.change(function(){
            
      if($(this).attr('checked')) {
        Drupal.behaviors.plinkthemesettings.grid_width.change();
      } else {
        Drupal.behaviors.plinkthemesettings.plink_settings_destroy_grid_selects();
      }
      
    });
  
  
    // Initialization
    if(pl.grid_enabled.val()) {
      Drupal.behaviors.plinkthemesettings.grid_width.change();
      $('#edit-mq1-grid-width').change();
      $('#edit-mq2-grid-width').change();
      $('#edit-mq3-grid-width').change();
    }
    
  
  })(jQuery);  
  }
  
  /*
    Replace a text field with a select element and generate grid options
  */
  ,plink_settings_generate_grid_select: function(settings) {
  (function ($) {  
    
    var pl = Drupal.behaviors.plinkthemesettings; // shortcut var
    var defaults = {field:null, gwidth:960, grids:12, default_value: 4};
    var options = $.extend(defaults, settings);
    if(pl.grid_unit.val() == "%") { options.gwidth = 96; }
    var unit_val = options.gwidth / options.grids;
  
    if(options.field == null) { throw "Invalid field passed to generate grid select"; }
        
    // Check if the field has a current value and process it
    var cval = options.field.val();
        
    if(cval) { 
      if(cval > Math.ceil(options.grids)) {
        options.default_value = cval / unit_val; 
      } else {
        options.default_value = cval; 
      } 
    }    
                    
    // parent container
    var container = options.field.parents('.form-item:first');
    
    // hide suffix if there is one
    container.find(".field-suffix").hide();
    
    // current value of the field
    var current_value = (options.field.val() > 0) ? options.field.val() : options.default_value;
    
    // Create select element
    var select = $("<select />");
    
    // add the current fields properties
    select.addClass(options.field.attr("class"));
    select.addClass('grid-options-select');
    select.attr('id',options.field.attr('id'));
    select.attr('name', options.field.attr("name"));
    
    // remove the current field
    options.field.remove();
    
    // Create options
    var i = 0;
    while(i < options.grids) {
      var opt = $("<option />");
      opt.attr('value', i);
      if(i == options.default_value) {
        opt.attr("selected",'selected');
      } 
      opt.text(i + " grids [" + Math.ceil(unit_val * i) + pl.grid_unit.val() + "]");
      
      opt.appendTo(select);                  
      i++;
    }
    
    // Add the select option into the dom
    container.append(select);
    
  })(jQuery);  
  }
  
  /*
    Handle the disabling of grid systems
  */
  ,plink_settings_destroy_grid_selects: function() {
  (function ($) {
  
    var pl = Drupal.behaviors.plinkthemesettings; // shortcut var
    var unit_val = Math.ceil( pl.grid_width.val() / pl.grid_columns.val() );
    var selects = $(".grid-options-select");
    
    $.each(selects,function(k,v){
          
      var elem = selects.eq(k);
      var container = elem.parents('.form-item:first');
      elem.removeClass('grid-options-select');
      
      var txt = $('<input />').attr('type', 'textfield');
      txt.attr("id", elem.attr('id'));
      txt.attr("name", elem.attr("name"));
      txt.addClass(elem.attr("class"));
      
      
      txt.attr('value', elem.val() * unit_val );
      
      elem.remove();
      
      container.append(txt);
      
    });
  
  })(jQuery);  
  }
  
  /*
    Add events to the secondary and tertiary region selects to adjust for preview
  */
  
  ,plink_settings_region_width_preview_events: function() {
  (function ($) {
  
    // MAIN
    $("#edit-main-secondary-width, #edit-main-tertiary-width").live("change", function(){
      var thumbs = $("#edit-main-layout");
      Drupal.behaviors.plinkthemesettings.plink_settings_region_width_preview_event_handlers($("#edit-main-secondary-width"), $("#edit-main-tertiary-width"), thumbs, true);
    });
    
    // Media query 1
    $("#edit-mq1-secondary-width, #edit-mq1-tertiary-width").live("change", function(){
      var thumbs = $("#edit-responsive-layouts-mq1");
      Drupal.behaviors.plinkthemesettings.plink_settings_region_width_preview_event_handlers($("#edit-mq1-secondary-width"), $("#edit-mq1-tertiary-width"), thumbs, false);
    });
    
    // Media query 2
    $("#edit-mq2-secondary-width, #edit-mq2-tertiary-width").live("change", function(){
      var thumbs = $("#edit-responsive-layouts-mq2");
      Drupal.behaviors.plinkthemesettings.plink_settings_region_width_preview_event_handlers($("#edit-mq2-secondary-width"), $("#edit-mq2-tertiary-width"), thumbs, false);
    });
    
    // Media query 3
    $("#edit-mq3-secondary-width, #edit-mq3-tertiary-width").live("change", function(){
      var thumbs = $("#edit-responsive-layouts-mq3");
      Drupal.behaviors.plinkthemesettings.plink_settings_region_width_preview_event_handlers($("#edit-mq3-secondary-width"), $("#edit-mq3-tertiary-width"), thumbs, false);
    });
    
  
  })(jQuery);  
  }
  
  /*
    Secondary and Teritary region width select handlers
    Adjusts the thumbnails preview (and the master layout preview for master content layouts) 
  */
  ,plink_settings_region_width_preview_event_handlers: function(secondary, tertiary, selector, master) {
  (function ($) {
        
    var pl = Drupal.behaviors.plinkthemesettings; // shortcut var

    // tertiary secondary value
    var sec = secondary.val();
    var ter = tertiary.val();
    var unit_val = pl.grid_width.val() / pl.grid_columns.val();
    
    // if grid system enabled we need to convert grids to pixels
    if(pl.grid_enabled.val()) {
      sec *= unit_val;
      ter *= unit_val;
    }
        
    // convert widths into percentages
    sec = Math.ceil((sec / pl.grid_width.val()) * 100);
    ter = Math.ceil((ter / pl.grid_width.val()) * 100);
    b = 100; // base
        
    selector.find(".option1 .prim").css({width: (b - sec - ter) + "%"});
    selector.find(".option1 .sec").css({width: sec + "%"});
    selector.find(".option1 .ter").css({width: ter + '%'});

    selector.find(".option2 .prim").css({width: (b - sec - ter) + "%"});
    selector.find(".option2 .sec").css({width: sec + "%"});
    selector.find(".option2 .ter").css({width: ter + '%'});

    selector.find(".option3 .prim").css({width: (b - sec - ter) + "%"});
    selector.find(".option3 .sec").css({width: sec + "%"});
    selector.find(".option3 .ter").css({width: ter + '%'});

    selector.find(".option4 .prim").css({width: (b - sec - ter) + "%"});
    selector.find(".option4 .sec").css({width: sec + "%"});
    selector.find(".option4 .ter").css({width: ter + '%'});

    selector.find(".option5 .prim").css({width: (b - sec - ter) + "%"});
    selector.find(".option5 .sec").css({width: sec + "%"});
    selector.find(".option5 .ter").css({width: ter + '%'});

    selector.find(".option6 .prim").css({width: (b - sec - ter) + "%"});
    selector.find(".option6 .sec").css({width: sec + "%"});
    selector.find(".option6 .ter").css({width: ter + '%'});

    selector.find(".option7 .prim").css({width: b - sec + "%"});
    selector.find(".option7 .sec").css({width: sec + "%" });
    selector.find(".option7 .ter").css({width: '100%'});

    selector.find(".option8 .prim").css({width: b - sec + "%"});
    selector.find(".option8 .sec").css({width: sec + "%"});
    selector.find(".option8 .ter").css({width: '100%'});

    selector.find(".option9 .prim").css({width: "100%"});
    selector.find(".option9 .sec").css({width: sec + "%"});
    selector.find(".option9 .ter").css({width: ter + '%'});

    selector.find(".option10 .prim").css({width: "100%"});
    selector.find(".option10 .sec").css({width: sec + "%"});
    selector.find(".option10 .ter").css({width: ter + '%'});

    selector.find(".option11 .prim").css({width: (b - sec) + '%'});
    selector.find(".option11 .sec").css({width: sec + '%'});
    selector.find(".option11 .ter").css({width: (b - sec) + '%'});

    selector.find(".option12 .prim").css({width: (b - sec) + '%'});
    selector.find(".option12 .sec").css({width: sec + "%"});
    selector.find(".option12 .ter").css({width: (b - sec) + '%'});

    // selector.find(".option13 .prim").css({width: (b - sec) + '%'});
    // selector.find(".option13 .sec").css({width: sec + "%"});
    // selector.find(".option13 .ter").css({width: (b - sec) + '%'});

    // selector.find(".option14 .prim").css({width: (b - sec) + '%'});
    // selector.find(".option14 .sec").css({width: sec + "%"});
    // selector.find(".option14 .ter").css({width: (b - sec) + '%'});
    
    
    // If this is the master layout then update the master preview
    if(master) {
      pl.plink_settings_update_master_preview(selector.find('.active'));
    }
    
  })(jQuery);      
  }
  
  /* 
    Adds the events for the handles that check the checked or unchecked full width regions
  */
  ,full_width_region_events: function() {
    (function ($) {
    
      // fill in the blanks
      var full_width_inputs = $('.form-item-full-width-regions input');

      $.each(full_width_inputs,function(i,v){
        Drupal.behaviors.plinkthemesettings.update_full_width_region(full_width_inputs.eq(i));
      });

      // On change
      $('.form-item-full-width-regions input').change(function() {    
        Drupal.behaviors.plinkthemesettings.update_full_width_region($(this));
      });
      
    })(jQuery);      
  }
  
  /* 
    Handles the checked or unchecked full width regions
  */
  ,update_full_width_region : function(regionObj) {
    (function ($) {      
      if(regionObj.attr('checked') == true){
        $(".page-layout-preview ." + regionObj.val()).css('width','100%');
      }else {
        $(".page-layout-preview ." + regionObj.val()).css('width','60%');
      }
    })(jQuery);      
  }
  
  
};