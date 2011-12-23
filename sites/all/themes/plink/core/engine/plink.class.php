<?php
/**
 *  PLINK CORE ENGINE
 *  Author: Shea McKinney - sherakama
 *  This class controls and stores all the layout data for your pages
 *  Layout Processing is done in this class and the singleton can be 
 *  accessed in your themes by calling $plink = Plink::singleton();
 **/

class Plink
{

		// VARIABLES
		// ----------------------------------------------------------------------------------------
    private static $instance; // ME!
		
		// Master Layout width
		private $page_width = 960;
		
		// Master Layout Grid option
		private $grid_types = array('px' => 'fixed', '%' => 'fluid');
		
		// ignore these regions in region processing
		private $special_regions = array(
			'content_first', 'content', 'content_last', 
			'secondary', 'tertiary', 
			'help', 'highlighted', 'page_title_prefix', 'page_title_suffix'
		);
		
		// storage for the region classes;
    private $region_classes = array('primary' => array(), 'secondary' => array(), 'tertiary' => array()); 
		
		// Prefix and suffix blocks
		private $title_blocks = array(); 
		
		// storage for the current themes settings
		public $theme_settings = array(); 
		private $theme_data = array();

		// Whether or not this is a grid based layout or not. default to on
		private $is_grid = TRUE; 
		
		// Whether or not we have run the setup
		private $processed = FALSE;
		
		// The page loads vars from preprocess
		private $vars = array();

		// Storage information for blocks
		private $blocks = array();
		private $blocks_increment = 0;
		private $blocks_region_count = array();

    // Media Queries - Storage for media queries
    private $media_queries = array();
    


    // CONSTRUCTOR
		// ----------------------------------------------------------------------------------------
    private function __construct() 
    {
        // yay lets go!
				global $theme; // current theme
				$this->theme_data = $theme_data = list_themes();
				$default_settings = ($theme && isset($theme_data[$theme]->info['settings'])) ? $theme_data[$theme]->info['settings'] : array();
				$saved_settings = variable_get('theme_' . $theme . '_settings', array());
			
				// The current settings for this theme
				$this->theme_settings = array_merge($default_settings,$saved_settings);
				
				// Alter the theme settings if the plinko companion module is around.
				// We change the settings here before anything is processed and this
				// way we can change the page layout for any number of conditions.
				
				// TODO: write a context reaction for this 
				// If someone could write a context reaction that changes page settings I would heart you.
				
				if(module_exists('plinko')) {
					// Do something like an array_merge with a plinko method on the theme settings
				} 
				  
				  
				  // Set the is grid to true by default until the custom widths are available
					$this->is_grid = TRUE;
		      
		      // Set the master page width
					$this->page_width = $this->theme_settings['grid_master_width'];
			  
			    // Set the available media queries to off by default
			    $this->media_queries['mq1'] = 0;
			    $this->media_queries['mq2'] = 0;
			    $this->media_queries['mq3'] = 0;
			    
    }



    // THE SINGLETON METHOD
		// ----------------------------------------------------------------------------------------
    public static function singleton() 
    {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
    }
    
    // Prevent users from cloning the instance
    // ----------------------------------------------------------------------------------------
		public function __clone()
    {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }
				
		
		/**
		 * NEEDS DOCUMENTATION
		 **/
		
		public function init_processes(&$vars) {
			global $theme;
			$this->processed = TRUE;
			$this->set_vars($vars);
			
      if (isset($vars['page']['page_title_prefix']) && isset($vars['page']['page_title_suffix'])) {
        $this->set_title_blocks($vars['page']['page_title_prefix'],$vars['page']['page_title_suffix']);
      }
			
			$this->tally_region_block_counts();
				
			// do not process sidebars on overlay page.			
			if(module_exists('overlay') && isset($_REQUEST['render']) && $_REQUEST['render'] == 'overlay') { return; }
			
			// GRID SPECIFIC PROCESSES
			if($this->is_grid()) {
				$this->init_grid_layouts();
			} 
			else 
			{
			// MANUAL INPUT SPECIFIC PROCESSES
			//	$this->init_manual_input_layouts();
			}
			
			// Media Query Layouts
			$this->init_media_query_based_layouts();
			
		}
		
		
		
		// PUBLIC METHODS
		// ----------------------------------------------------------------------------------------
		
		/**
		 * Returns an object with all theme settings 
		 * 
		 * @return the theme setting values
		 */
		public function theme_settings() {	  
		  return (object) $this->theme_settings;
		}
		
		
		/**
		 * NEEDS DOCUMENTATION
		 **/
		
		public function is_processed() {
			return $this->processed;
		}

		/**
		 * NEEDS DOCUMENTATION
		 **/
    public function is_grid() {
			return $this->is_grid;
		}		
		
		/**
		 * NEEDS DOCUMENTATION
		 **/
		
		private function set_vars($vars) {
			$this->vars = $vars;
		}

		/**
		 * NEEDS DOCUMENTATION
		 **/
    public function set_title_blocks($prefix = null,$suffix = null)
    {
        if(!is_null($prefix)){ $this->title_blocks['prefix'] = $prefix; }
				if(!is_null($suffix)){ $this->title_blocks['suffix'] = $suffix; }
    }

		/** 
		 * NEEDS DOCUMENTATION
		 **/
		
		public function get_vars() {
			return $this->vars;
		}
		
		
		/**
		 * NEEDS DOCUMENTATION
		 **/
		
		public function get_title_blocks() {
			return $this->title_blocks;
		}
		
		
		/**
		 * NEEDS DOCUMENTATION
		 **/
		public function get_body_classes() {
			$vars = $this->get_vars();
			$ret = array();
			
	    // Check for grid system
			if($this->is_grid) {
				$ret[] = 'grid-enabled';
				$ret[] = 'grid-system-type-' . $this->grid_types[$this->theme_settings['grid_fixed_or_fluid']];
				$ret[] = 'grid-system-' . $this->theme_settings['master_grid_columns'];	
			} else {
				$ret[] = 'grid-disabled';
			}
			
			// Check for media queries
			$ret[] = ($this->theme_settings['enable_responsive_mq1']) ? 'mq1' : '';
			$ret[] = ($this->theme_settings['enable_responsive_mq2']) ? 'mq2' : '';
			$ret[] = ($this->theme_settings['enable_responsive_mq3']) ? 'mq3' : '';			
						
			return $ret;
		}
		
		
		/**
		 * NEEDS DOCUMENTATION
		 **/
		public function get_main_region_classes() {		  
			return 'container-' . $this->theme_settings['master_grid_columns'];
		}
		
		
		/**
		 * NEEDS DOCUMENTATION
		 **/
		public function get_content_region_classes($id = 'primary') {			
			return implode($this->region_classes[$id],' ');
		}
		
		
		/**
		 * NEEDS DOCUMENTATION
		 **/
		public function get_region_classes($region = null) {
			
			// Handle missing param
			if(is_null($region)) { trigger_error('No region name provided', E_USER_ERROR); }
			
			// handle ignored regions
			if(in_array($region,$this->special_regions)){ return ''; }
			
			// shortcut var
			$ts = $this->theme_settings;

			// check for full width region
			if(!isset($ts['full_width_regions'][$region]) || is_numeric($ts['full_width_regions'][$region])) {
				// not a full width region
				return 'container-' . $ts['master_grid_columns'];
			}		
			
			
			// full width region or one that doesnt exist
			return '';
			
		}
		
		
		/**
		 * NEEDS DOCUMENTATION
		 **/
		public function get_region_inner_classes($region = null) {
			
			// Handle missing param
			if(is_null($region)) { trigger_error('No region name provided', E_USER_ERROR); }
			
			// handle ignored regions
			if(in_array($region,$this->special_regions)){ return ''; }
			
			// shortcut var
			$ts = $this->theme_settings;

			// check for full width region
			if(!isset($ts['full_width_regions'][$region]) || is_numeric($ts['full_width_regions'][$region])) {
				// not a full width region
				return 'grid-' . $ts['master_grid_columns'];
			}			
			
			// full width region or one that doesnt exist
			return 'container-' . $ts['master_grid_columns'];
			
		}
		
		
		/**
		 * NEEDS DOCUMENTATION
		 **/
		
		public function get_block_classes($info) {
			$vars = $this->get_vars();
			$block = $info['block'];
			$ret = array();
			
			$this->blocks[$block->region][] = array('bid' => $block->bid, 'title' => $block->title);
			$region_count = count($this->blocks[$block->region]);
			$region_total = $this->get_region_block_counts($block->region);
			
			if($region_count == 1) { $ret[] = 'first'; };
			if($region_count == $region_total) { $ret[] = 'last'; }
			
			$ret[] = $info['block_zebra'];
			$ret[] = drupal_clean_css_identifier(strtolower($block->title));					
															
			$this->blocks_increment++;
			return $ret;
		}

		/**
		 * NEEDS DOCUMENTATION
		 **/
		private function get_region_block_counts($region = null) {
			
			if(is_null($region)) { return $this->blocks_region_count; }
			return $this->blocks_region_count[$region];
			
		}
		
    
		// PROCESS METHODS
		// ----------------------------------------------------------------------------------------
		
		/**
		 * NEEDS DOCUMENTATION
		 * Process Block Counts
		 **/
		
		private function tally_region_block_counts() {
			global $theme;
			$vars = $this->get_vars();
			$ignore = array("#sorted",'#theme_wrappers','#region');
			
			foreach($this->theme_data[$theme]->info['regions'] as $k => $v){
				if(isset($vars['page'][$k])) {
					foreach($vars['page'][$k] as $key => $value) {
						if(!in_array($key,$ignore)){
							if(!isset($this->blocks_region_count[$k])) { $this->blocks_region_count[$k] = 0; }
							$this->blocks_region_count[$k]++;
						}
					}
				}
			}	
		}
		
		
		/**
		 * NEEDS DOCUMENTATION
		 **/
		private function init_manual_input_layouts() {
			$vars = $this->get_vars();
			$ts = $this->theme_settings; // shortcut var			
			
			$this->process_manual_input_css($vars);
		}
		
		
		/**
		 * NEEDS DOCUMENTATION
		 **/
		private function init_grid_layouts() {
			$vars = $this->get_vars();
			$ts = $this->theme_settings; // shortcut var
			
			// Add in the appropriate stylesheets
      
      switch ($ts['grid_fixed_or_fluid']) {
        
        // Fluid is fluid no matter what the grid base
        case '%':
  			  drupal_add_css(drupal_get_path('theme', 'plink') . "/css/fluid.css");
        break;
        
        default: // pickles
          drupal_add_css(drupal_get_path('theme', 'plink') . "/css/" . $ts['grid_master_width'] . ".css");
      }
      

			// setup the classes
			$this->process_content_region_classes();
		}
		
		
		/**
		 * NEEDS DOCUMENTATION
		 * @param $class_prefix - string - goes before the push/pull/grid classes
		 **/
		public function process_content_region_classes($class_prefix = '', $media_query = null) {
						
			// the page variable			
			$vars = $this->get_vars();
			$page = $vars['page'];
					
			$ts = $this->theme_settings;    // shortcut var	
			$available_grids;               // The total amount of grids available to this layout
			$grid_unit;                     // The width of a grid in px
			$page_width;                    // Current Page width
			$layout;                        // Current Layout to process
			$secondary_grids;               // # of grids width for the secondary column
	    $tertiary_grids;                // same as above
	
			if(!is_null($media_query)) {
			  $page_width = $media_query['page_width'];
			  $layout = $media_query['main_layout'];
			  $secondary_grids = $media_query['secondary_width'];
  			$tertiary_grids = $media_query['tertiary_width'];
			} else {
			  $page_width = $this->page_width;
			  $layout = $this->theme_settings['main_layout'];
			  $secondary_grids = $ts['main_secondary_width'];
  			$tertiary_grids = $ts['main_tertiary_width'];
			}
					
			
			// TODO: expand functionality to allow for changing the # of grids on mq level
	    $grid_unit = $page_width / $this->theme_settings['master_grid_columns'];
			$available_grids = $this->theme_settings['master_grid_columns'];
			
						
			// Is there anything in the region and is the region set to 0 grids
			$is_secondary = (isset($page['secondary']) && count($page['secondary'])) ? TRUE : FALSE;
			$is_tertiary = (isset($page['tertiary']) && count($page['tertiary'])) ? TRUE : FALSE;


			switch($layout) {
				
				// Layout 1
				// -----------------------------------------------------------------------------------
				case "1" ;
					if($is_secondary) { 
						$this->region_classes['secondary'][] = $class_prefix . 'grid-' . $secondary_grids;
						$available_grids -= $secondary_grids; 
						$this->region_classes['primary'][] = $class_prefix . 'push-' . $secondary_grids;
					}
					if($is_tertiary) { 
						$this->region_classes['tertiary'][] = $class_prefix . 'grid-' . $tertiary_grids;
						$available_grids -= $tertiary_grids; 
					}
									
					// add in the rest of the available grids to the primary region
					$this->region_classes['primary'][] = $class_prefix . 'grid-' . $available_grids;
					$this->region_classes['secondary'][] = $class_prefix . 'pull-' . $available_grids;
					
					
				break;
				
				// Layout 2
				// -----------------------------------------------------------------------------------
				case "2" ;
					if($is_secondary) { 
						$this->region_classes['secondary'][] = $class_prefix . 'grid-' . $secondary_grids;
						$available_grids -= $secondary_grids; 
					}
					if($is_tertiary) { 
						$this->region_classes['tertiary'][] = $class_prefix . 'grid-' . $tertiary_grids;
						$available_grids -= $tertiary_grids; 
						$this->region_classes['primary'][] = $class_prefix . 'push-' . $tertiary_grids;
					}

					if($is_secondary && $is_tertiary) {
						$this->region_classes['secondary'][] = $class_prefix . 'push-' . $tertiary_grids;
						$this->region_classes['tertiary'][] = $class_prefix . 'pull-' . ($available_grids + $secondary_grids);
					} else if(!$is_secondary) {
						$this->region_classes['tertiary'][] = $class_prefix . 'pull-' . ($available_grids);
						
					}
						
					// add in the rest of the available grids to the primary region
					$this->region_classes['primary'][] = $class_prefix . 'grid-' . $available_grids;
					
					
				break;
				
				// Layout 3
				// -----------------------------------------------------------------------------------
				case "3" ;
					if($is_secondary) { 
						$this->region_classes['secondary'][] = $class_prefix . 'grid-' . $secondary_grids;
						$available_grids -= $secondary_grids; 
					}
					if($is_tertiary) { 
						$this->region_classes['tertiary'][] = $class_prefix . 'grid-' . $tertiary_grids;
						$available_grids -= $tertiary_grids; 
					}					
					
					// add in the rest of the available grids to the primary region
					$this->region_classes['primary'][] = $class_prefix . 'grid-' . $available_grids;
				break;
				
				// Layout 4
				// -----------------------------------------------------------------------------------
				case "4" ;
					if($is_secondary) { 
						$this->region_classes['secondary'][] = $class_prefix . 'grid-' . $secondary_grids;
						$available_grids -= $secondary_grids; 
					}
					if($is_tertiary) { 
						$this->region_classes['tertiary'][] = $class_prefix . 'grid-' . $tertiary_grids;
						$available_grids -= $tertiary_grids; 
					}
					// add in the rest of the available grids to the primary region
					$this->region_classes['primary'][] = $class_prefix . 'grid-' . $available_grids;

					if($is_secondary && $is_tertiary){
						$this->region_classes['secondary'][] = $class_prefix . 'push-' . $tertiary_grids;						
						$this->region_classes['tertiary'][] = $class_prefix . 'pull-' . $secondary_grids;
					}
					
				break;
				
				// Layout 5
				// -----------------------------------------------------------------------------------
				case "5" ;
					if($is_secondary) { 
						$this->region_classes['secondary'][] = $class_prefix . 'grid-' . $secondary_grids;
						$available_grids -= $secondary_grids; 
					}
					if($is_tertiary) { 
						$this->region_classes['tertiary'][] = $class_prefix . 'grid-' . $tertiary_grids;
						$available_grids -= $tertiary_grids; 
					}
					// add in the rest of the available grids to the primary region
					$this->region_classes['primary'][] = $class_prefix . 'grid-' . $available_grids;
					
					if($is_secondary && $is_tertiary){
						$this->region_classes['primary'][] = $class_prefix . 'push-' . ($secondary_grids + $tertiary_grids);						
					} else if($is_secondary) {
						$this->region_classes['primary'][] = $class_prefix . 'push-' . ($secondary_grids);
					} else if($is_tertiary) {
						$this->region_classes['primary'][] = $class_prefix . 'push-' . ($tertiary_grids);
					}
					
					$this->region_classes['secondary'][] = $class_prefix . 'pull-' . $available_grids;
					$this->region_classes['tertiary'][] = $class_prefix . 'pull-' . $available_grids;
				break;
				
				// Layout 6
				// -----------------------------------------------------------------------------------
				case "6" ;
					if($is_secondary) { 
						$this->region_classes['secondary'][] = $class_prefix . 'grid-' . $secondary_grids;
						$available_grids -= $secondary_grids; 
					}
					if($is_tertiary) { 
						$this->region_classes['tertiary'][] = $class_prefix . 'grid-' . $tertiary_grids;
						$available_grids -= $tertiary_grids; 
					}
					// add in the rest of the available grids to the primary region
					$this->region_classes['primary'][] = $class_prefix . 'grid-' . $available_grids;

					if($is_secondary && $is_tertiary){
						$this->region_classes['primary'][] = $class_prefix . 'push-' . ($secondary_grids + $tertiary_grids);						
					} else if($is_secondary) {
						$this->region_classes['primary'][] = $class_prefix . 'push-' . ($secondary_grids);
					} else if($is_tertiary) {
						$this->region_classes['primary'][] = $class_prefix . 'push-' . ($tertiary_grids);
					}
					
					if($is_tertiary) {
						$this->region_classes['secondary'][] = $class_prefix . 'pull-' . ($available_grids - $tertiary_grids);						
					} else {
						$this->region_classes['secondary'][] = $class_prefix . 'pull-' . ($available_grids);												
					}
					
					if($is_secondary) {
						$this->region_classes['tertiary'][] = $class_prefix . 'pull-' . ($available_grids + $secondary_grids);
					} else {
						$this->region_classes['tertiary'][] = $class_prefix . 'pull-' . ($available_grids);						
					}

				break;
				
				// Layout 7
				// -----------------------------------------------------------------------------------
				case "7" ;
					if($is_tertiary) { 
						$this->region_classes['tertiary'][] = $class_prefix . 'grid-' . $available_grids;
						$this->region_classes['tertiary'][] = $class_prefix . 'pclear';
					}	
				
					if($is_secondary) { 
						$this->region_classes['secondary'][] = $class_prefix . 'grid-' . $secondary_grids;
						$available_grids -= $secondary_grids; 
					}
				
					// add in the rest of the available grids to the primary region
					$this->region_classes['primary'][] = $class_prefix . 'grid-' . $available_grids;
				break;
				
				// Layout 8
				// -----------------------------------------------------------------------------------
				case "8" ;
					if($is_tertiary) { 
						$this->region_classes['tertiary'][] = $class_prefix . 'grid-' . $available_grids;
						$this->region_classes['tertiary'][] = $class_prefix . 'pclear';
					}	
			
					if($is_secondary) { 
						$this->region_classes['secondary'][] = $class_prefix . 'grid-' . $secondary_grids;
						$available_grids -= $secondary_grids; 
						$this->region_classes['secondary'][] = $class_prefix . 'pull-' . $available_grids;
						$this->region_classes['primary'][] = $class_prefix . 'push-' . $secondary_grids;
					}
			
					// add in the rest of the available grids to the primary region
					$this->region_classes['primary'][] = $class_prefix . 'grid-' . $available_grids;
				break;
				
				// Layout 9
				// -----------------------------------------------------------------------------------
				case "9" ;
					if($is_secondary) { 
						$this->region_classes['secondary'][] = $class_prefix . 'grid-' . $secondary_grids;
					}
					if($is_tertiary) { 
						$this->region_classes['tertiary'][] = $class_prefix . 'grid-' . $tertiary_grids;
					}
					
					$this->region_classes['primary'][] = $class_prefix . 'pclear';
					$this->region_classes['primary'][] = $class_prefix . 'grid-' . $available_grids;
					
				break;
				
				// Layout 10
				// -----------------------------------------------------------------------------------
				case "10" ;
					if($is_secondary) { 
						$this->region_classes['secondary'][] = $class_prefix . 'grid-' . $secondary_grids;
						
						if($is_tertiary){ $this->region_classes['secondary'][] = $class_prefix . 'push-' . $tertiary_grids; }
					}
					if($is_tertiary) { 
						$this->region_classes['tertiary'][] = $class_prefix . 'grid-' . $tertiary_grids;
						if($is_secondary) { $this->region_classes['tertiary'][] = $class_prefix . 'pull-' . $secondary_grids; }
					}
				
					$this->region_classes['primary'][] = $class_prefix . 'pclear';
					$this->region_classes['primary'][] = $class_prefix . 'grid-' . $available_grids;
				break;
				
				// Layout 11
				// -----------------------------------------------------------------------------------
				case "11" ;
					if($is_secondary) { 
						$this->region_classes['secondary'][] = $class_prefix . 'grid-' . $secondary_grids;
						$available_grids -= $secondary_grids; 
					}					
					$this->region_classes['primary'][] = $class_prefix . 'grid-' . $available_grids;
					$this->region_classes['tertiary'][] = $class_prefix . 'grid-' . $available_grids;
					$this->region_classes['tertiary'][] = $class_prefix . 'pclear';
				break;
				
				// Layout 12
				// -----------------------------------------------------------------------------------
				case "12" ;
					if($is_secondary) { 
						$this->region_classes['secondary'][] = $class_prefix . 'grid-' . $secondary_grids;
						$available_grids -= $secondary_grids; 
						$this->region_classes['secondary'][] = $class_prefix . 'pull-' . $available_grids;
						
						$this->region_classes['primary'][] = $class_prefix . 'push-' . $secondary_grids;
						$this->region_classes['tertiary'][] = $class_prefix . 'push-' . $secondary_grids;						
					}					
					
					$this->region_classes['primary'][] = $class_prefix . 'grid-' . $available_grids;
					$this->region_classes['tertiary'][] = $class_prefix . 'grid-' . $available_grids;
				break;

				// Layout 13
				// -----------------------------------------------------------------------------------				
				case "13" ;
				
				  // Everything full width
					$this->region_classes['secondary'][] = $class_prefix . 'grid-' . $available_grids;
					$this->region_classes['primary'][] = $class_prefix . 'grid-' . $available_grids;
					$this->region_classes['tertiary'][] = $class_prefix . 'grid-' . $available_grids;						
					
				break;
				
			}

		}
		
		
		
		/**
		 * NEEDS DOCUMENTATION
		 **/
		// public function process_manual_input_css($vars) {
		//      
		//      // setup the page width
		//      $css = array();
		//      $page = $vars['page'];
		//      $page_width = $this->page_width;
		//            
		//            
		//      $css[] = ".region { width: " . $page_width . "px; margin: 0px auto; position: relative; }";
		//      $css[] = "#container { width: " . $page_width . "px; margin: 0px auto; position: relative; }";
		//      $css[] = "#primary, #secondary, #tertiary { position: relative; margin: 0px; float:left; }";
		//      $css[] = '#primary .region { width: auto; }';
		// 
		//      $css = implode($css, "\n");
		//      drupal_add_css($css, array('type'=>'inline','every_page'=>TRUE,'preprocess'=>TRUE));
		//      
		//      
		//      
		//      // Now do the fancy content region stuff
		//      $css = array();
		//      
		//      $available_width = $page_width;
		//      // get and convert the widths for the sidebars into grids
		//      $secondary_width = $this->theme_settings['main_secondary_width'];
		//      $tertiary_width = $this->theme_settings['main_tertiary_width'];
		//            
		//      // Is there anything in the region and is the region set to 0 grids
		//      $is_secondary = ($page['secondary'] && $secondary_width) ? TRUE : FALSE;
		//      $is_tertiary = ($page['tertiary'] && $tertiary_width) ? TRUE : FALSE;
		//      
		//      switch($this->theme_settings['main_layout']) {
		//        
		//        // Layout 1
		//        // -----------------------------------------------------------------------------------
		//        case "1":
		//          if($is_secondary) { 
		//            $css[] = '#secondary { width: ' . $secondary_width . 'px; } ';
		//            $css[] = '#primary { left: ' . $secondary_width . "px; }";
		//            $available_width -= $secondary_width; 
		//          }
		//          if($is_tertiary) { 
		//            $css[] = '#tertiary { width: ' .  $tertiary_width . 'px; } ';
		//            $available_width -= $tertiary_width; 
		//          }
		//          // add in the rest of the available grids to the primary region
		//          $css[] = '#primary { width: ' . $available_width . "px; } ";
		//          $css[] = '#secondary { left: -' . $available_width . "px; } ";
		//        break;
		//        
		//        // Layout 2
		//        // -----------------------------------------------------------------------------------
		//        case "2":
		//          if($is_secondary) { 
		//            $css[] = '#secondary { width: ' . $secondary_width . 'px; } ';
		//            $available_width -= $secondary_width; 
		//          }
		//          if($is_tertiary) { 
		//            $css[] = '#tertiary { width: ' .  $tertiary_width . 'px; } ';
		//            $css[] = "#secondary { right: -" . $tertiary_width . "px; }\r\n";
		//            $css[] = "#primary { left: " . $tertiary_width . "px; }\r\n";
		//            
		//            $available_width -= $tertiary_width; 
		//          }
		//          if($is_secondary) {
		//            $css[] = "#tertiary { left: -" . ($available_width + $secondary_width) . "px; }\r\n";           
		//          } else {
		//            $css[] = "#tertiary { left: -" . ($available_width) . "px; }\r\n";            
		//          }
		//          // add in the rest of the available grids to the primary region
		//          $css[] = '#primary { width: ' . $available_width . "px; } ";
		//        break;
		//        
		//        // Layout 3
		//        // -----------------------------------------------------------------------------------
		//        case "3":
		//          if($is_secondary) { 
		//            $css[] = '#secondary { width: ' . $secondary_width . 'px; } ';
		//            $available_width -= $secondary_width; 
		//          }
		//          if($is_tertiary) { 
		//            $css[] = '#tertiary { width: ' .  $tertiary_width . 'px; } ';
		//            $available_width -= $tertiary_width; 
		//          }
		//          // add in the rest of the available grids to the primary region
		//          $css[] = '#primary { width: ' . $available_width . "px; } ";
		//        break;
		//        
		//        // Layout 4
		//        // -----------------------------------------------------------------------------------
		//        case "4":
		//            if($is_secondary) { 
		//              $css[] = '#secondary { width: ' . $secondary_width . 'px; } ';
		//              $available_width -= $secondary_width; 
		//              $css[] = '#tertiary { left:  -' . $secondary_width . 'px;}';
		//            }
		//            if($is_tertiary) { 
		//              $css[] = '#tertiary { width: ' .  $tertiary_width . 'px; } ';
		//              $available_width -= $tertiary_width; 
		//              $css[] = '#secondary { left:  ' . $tertiary_width . 'px;}';
		//            }
		//            // add in the rest of the available grids to the primary region
		//            $css[] = '#primary { width: ' . $available_width . "px; } ";
		//        break;
		//        
		//        // Layout 5
		//        // ----------------------------------------------------------------------------------
		//        case "5":
		//            $offset = 0;
		//            if($is_secondary) { 
		//              $css[] = '#secondary { width: ' . $secondary_width . 'px; } ';
		//              $available_width -= $secondary_width; 
		//              $offset += $secondary_width;
		//            }
		//            if($is_tertiary) { 
		//              $css[] = '#tertiary { width: ' .  $tertiary_width . 'px; } ';
		//              $available_width -= $tertiary_width; 
		//              $offset += $tertiary_width;
		//            }
		//            // add in the rest of the available grids to the primary region
		//            $css[] = '#primary { width: ' . $available_width . "px; left: " . $offset . "px; } ";
		//            $css[] = '#secondary { left: -' . $available_width . 'px; }';
		//            $css[] = '#tertiary { left: -' . $available_width . 'px; }';            
		//        break;
		//        
		//        // Layout 6
		//        // ----------------------------------------------------------------------------------
		//        case "6":
		//            $offset = 0;
		//            $secondary_offset = 0;
		//            $tertiary_offset = 0;
		//            
		//            if($is_secondary) { 
		//              $css[] = '#secondary { width: ' . $secondary_width . 'px; } ';
		//              $tertiary_offset += $secondary_width;
		//              $available_width -= $secondary_width; 
		//              $offset += $secondary_width;
		//            }
		//            if($is_tertiary) { 
		//              $css[] = '#tertiary { width: ' .  $tertiary_width . 'px; } ';
		//              $secondary_offset += $tertiary_width;
		//              $available_width -= $tertiary_width; 
		//              $offset += $tertiary_width;
		//            }
		//            // add in the rest of the available grids to the primary region
		//            $css[] = '#primary { width: ' . $available_width . "px; left: " . $offset . "px; } ";         
		//            $css[] = '#secondary { left: -' . ( $available_width - $secondary_offset) . 'px; }';
		//            $css[] = '#tertiary { left: -' . ( $tertiary_offset + $available_width) . 'px; }';            
		//        break;
		//        
		//        // Layout 7
		//        // ----------------------------------------------------------------------------------
		//        case "7":
		//          if($is_secondary) { 
		//            $css[] = '#secondary { width: ' . $secondary_width . 'px; } ';
		//            $available_width -= $secondary_width; 
		//          }
		//          if($is_tertiary) { 
		//            $css[] = '#tertiary { clear:both; } ';
		//          }
		//          // add in the rest of the available grids to the primary region
		//          $css[] = '#primary { width: ' . $available_width . "px; } ";          
		//        break;
		// 
		//        // Layout 8
		//        // ----------------------------------------------------------------------------------
		//        case "8":
		//            if($is_secondary) { 
		//              $css[] = '#secondary { width: ' . $secondary_width . 'px; } ';
		//              $available_width -= $secondary_width; 
		//              $css[] = '#secondary { left: -' . $available_width . 'px; }';
		//              $css[] = '#primary { left: ' . $secondary_width .'px; }';
		//            }
		//            if($is_tertiary) { 
		//              $css[] = '#tertiary { clear:both; } ';
		//            }
		//            // add in the rest of the available grids to the primary region
		//            $css[] = '#primary { width: ' . $available_width . "px; } ";          
		//        break;
		//        
		//        // Layout 9
		//        // ----------------------------------------------------------------------------------
		//        case "9":
		//            if($is_secondary) { 
		//              $css[] = '#secondary { width: ' . $secondary_width . 'px; } ';
		//            }
		//            if($is_tertiary) { 
		//              $css[] = '#tertiary { width: ' . $tertiary_width . 'px; } ';
		//            }
		//            $css[] = "#primary { clear:both; } ";         
		//        break;
		//        
		//        // Layout 10
		//        // ----------------------------------------------------------------------------------
		//        case "10":
		//            if($is_secondary) { 
		//              $css[] = '#secondary { width: ' . $secondary_width . 'px; } ';
		//              $css[] = '#tertiary { left: -' . $secondary_width.'px; }';
		//            }
		//            if($is_tertiary) { 
		//              $css[] = '#tertiary { width: ' . $tertiary_width . 'px; } ';
		//              $css[] = '#secondary { left: ' . $tertiary_width.'px; }';
		//            }
		//            $css[] = "#primary { clear:both; } ";         
		//        break;  
		//      
		//        // Layout 11
		//        // ----------------------------------------------------------------------------------
		//        case "11":
		//            if($is_secondary) { 
		//              $css[] = '#secondary { width: ' . $secondary_width . 'px; } ';
		//              $available_width -= $secondary_width; 
		//            }
		//            if($is_tertiary) { 
		//              $css[] = '#tertiary { width: ' . $available_width . 'px; } ';
		//            }
		//            $css[] = '#primary { width: ' . $available_width . "px; } ";          
		//        break;
		//        
		//        // Layout 12
		//        // ----------------------------------------------------------------------------------
		//        case "12":
		//            if($is_secondary) { 
		//              $css[] = '#secondary { width: ' . $secondary_width . 'px; } ';
		//              $available_width -= $secondary_width; 
		//              $css[] = '#secondary{ left: -' . $available_width . 'px; }';
		//              $css[] = "#primary, #tertiary { left: " . $secondary_width . "px; }";
		//            }
		//            if($is_tertiary) { 
		//              $css[] = '#tertiary { width: ' . $available_width . 'px; } ';
		//            }
		//            $css[] = '#primary { width: ' . $available_width . "px; } ";          
		//        break;  
		//        
		//      }
		//      
		//      $css = implode($css, "\n");
		//      drupal_add_css($css, array('type'=>'inline','every_page'=>TRUE,'preprocess'=>TRUE));
		//    }


// ////////////////////////////////////////////////////////////////////////////////////////////////
// MEDIA QUERY BASED FUNCTIONS
// // ////////////////////////////////////////////////////////////////////////////////////////////////

  /**
  * NEEDS DOCUMENTATION
  */

    private function init_media_query_based_layouts() {
                        
      $i = 1;
            
      while($i <= count($this->media_queries) ) {
        $tmp_name = "mq" . $i;
                
        // Check for if the media query is enabled
        if($this->theme_settings['enable_responsive_' . $tmp_name]) {
        
          // add the appropriate stylesheet
          $this->plink_add_css(
            $this->theme_settings[ $tmp_name . '_grid_width' ], 
            array(
              'min-width' => $this->theme_settings[ $tmp_name . '_min' ],
              'max-width' => $this->theme_settings[ $tmp_name . '_max' ],
            )
          );
          
          // Build settings array to send to layout processing
          $media_query = array();
          $media_query['page_width'] = $this->theme_settings[ $tmp_name . '_grid_width' ];
    		  $media_query['main_layout'] = $this->theme_settings[ 'responsive_layouts_' . $tmp_name ];
    		  $media_query['secondary_width'] = $this->theme_settings[ $tmp_name . '_secondary_width' ];
    			$media_query['tertiary_width'] = $this->theme_settings[ $tmp_name . '_tertiary_width' ];

          // adds classes to the regions
          $this->process_content_region_classes($tmp_name . '-', $media_query);


          // Build the settings array and send to processing
          $args = array();
          $args['prefix'] = $tmp_name . "-";
          $args['base'] = $this->theme_settings[ $tmp_name . '_grid_width' ];
          $args['min-width'] = $this->theme_settings[ $tmp_name . '_min' ];
          $args['max-width'] = $this->theme_settings[ $tmp_name . '_max' ];
          $args['columns'] = $this->theme_settings[ 'master_grid_columns' ];
          $args['gutter'] = $this->get_css_gutter($args['base'], $args['columns']);
            
          // adds custom css for the media queries
          $this->plink_generate_grid_css($args);
          
        }
        
        $i++;
      }
      

    			      
    }

    /**
     * Returns a value for the CSS gutter of a grid
     * The rules of the gutter setting in the css files
     **/
     
     private function get_css_gutter($base, $columns) {
       
       if($this->theme_settings['grid_fixed_or_fluid'] == "%") {
         
         
         // Fluid Layouts
          $g = 1;

          if($base <= 336) {
            $g = 0;
          } 
          else if($base <= 720) {
            $g = 0.5;
          }

          if($columns >= 24) { $g = 0.5; }
          
          
        } 
        else {
          
          
          // FIXED LAYOUTS
          $g = 10;

          if($base <= 336) {
            $g = 0;
          } 
          else if($base <= 720) {
            $g = 5;
          }

          if($columns >= 24) { $g = 5; }
          
          
        }
        
       return $g; 
     }




    /**
    * Attached a style sheet with a media query
    * @param $args
    * Variable $args = array()
    * NEEDS DOCUMENTATION
    **/
    
    public function plink_add_css($stylesheet = null, $args = array()) {
            
      if(!$stylesheet) {  trigger_error('Invalid stylesheet passed to plink_add_css', E_USER_ERROR);  }
      
      $min = $max = '';
      
      $defaults = array(
        'min-width' => 0,
        'max-width' => 0,
        'weight' => 0,
        'media' => 'screen',
        );
        
      $conf = array_merge($defaults, $args);
      
      if($conf['min-width']) {
        $min = " and (min-width : " . $conf['min-width']. "px)";
      }
      
      if($conf['max-width']) {
        $max = " and (max-width : " . $conf['max-width']. "px)";
      }
      
      drupal_add_css(drupal_get_path('theme','plink') . "/css/".$stylesheet.".css", array(
        'group' => CSS_THEME,
        'every_page' => TRUE,
        'weight' => $conf['weight'],
        'media' => $conf['media'] . $min . $max,
        'preprocess' => TRUE,
      ));
      
    }

    
    /**
    * Attached a style sheet with a media query
    * @param $args
    * Variable $args = array()
    * NEEDS DOCUMENTATION
    **/
    
    private function plink_generate_grid_css($args = array()) {
            
      $defaults = array(
        'base' => 960,
        'gutter' => 10,
        'columns' => 12,
        'prefix' => '',
        'min-width' => 0,
        'max-width' => 0,
        'weight' => 0,
        'media' => 'screen',
        );
        
      $min = $max = $css = ''; 
      $conf = array_merge($defaults, $args);
      $unit = ceil($conf['base'] / $conf['columns']); // grid unit
      $p = $conf['prefix']; // shortcut var
      $size_unit = $this->theme_settings['grid_fixed_or_fluid'];
      
      if($conf['min-width']) {
        $min = " and (min-width : " . $conf['min-width']. "px)";
      }
      
      if($conf['max-width']) {
        $max = " and (max-width : " . $conf['max-width']. "px)";
      }
     
      // Fluid Sites
      if($size_unit == "%") {
        $conf['base'] = 92;
        $unit = ceil($conf['base'] / $conf['columns']); // grid unit
      }
         
     // ----------------------------------------------------------------------------------------
     
     $css .= "." . rtrim($p, "-") . " .container-" . $conf['columns'] . " { width: " . $conf['base'] . $size_unit . "; } \r\n";
     
     // special case     
      $css .= "." . rtrim($p, "-") . " .container-" . $conf['columns'] . " ." . $p . "grid-0 { display:none; }";
     
     $i = 1;
     while($i <= $conf['columns']) {
       
       $d = ceil($unit * $i) - ceil($conf['gutter'] * 2);
       $dd = ceil($unit * $i);
       
       // GRIDS
      // one rule to scale existing grids
      $css .= ".container-" . $conf['columns'] . " .grid-" . $i . ",";
      // and one to target the new
      $css .= "." . rtrim($p, "-") . " .container-" . $conf['columns'] . " ." . $p . "grid-" . $i . " { width: " . $d . $size_unit ."; margin-left: " . $conf['gutter'] . "px; margin-right: " . $conf['gutter'] . "px; } \r\n";
       
      // PUSH
      // one rule to scale existing grids
      $css .= ".container-" . $conf['columns'] . " .push-" . $i . ",";
      // and one to target the new
      $css .= "." . rtrim($p, "-") . " .container-" . $conf['columns'] . " ." . $p . "push-" . $i . " { left: " . $dd . $size_unit ."; position: relative; } \r\n";
      
      // override for layouts
      $css .= ".content-container .push-" . $i . "{ left: 0px; }";

      // PULL
      // one rule to scale existing grids
      $css .= ".container-" . $conf['columns'] . " .pull-" . $i . ",";
      // and one to target the new
      $css .= "." . rtrim($p, "-") . " .container-" . $conf['columns'] . " ." . $p . "pull-" . $i . " { left: -" . $dd . $size_unit ."; position: relative; } \r\n";
      
      // override for layouts
      $css .= ".content-container .pull-" . $i . "{ left: 0; }";
      
      // so far do not need suffix and prefix       
       $i++;
     }
     
     
     // ----------------------------------------------------------------------------------------
            
      drupal_add_css($css, array(
        'group' => CSS_THEME,
        'type' => 'inline',
        'every_page' => TRUE,
        'weight' => $conf['weight'],
        'media' => $conf['media'] . $min . $max,
        'preprocess' => TRUE,
      ));
      
    }

		

}
