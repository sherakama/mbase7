<?php

/**
 * Implements hook_skinr_skin_PLUGIN_info().
 */
 

 
function MYTHEME_skinr_skin_wireframes_info() {

  $skins = array(); 
 
     $skins['plink_skins_imageplaceholder'] = array(
    'title' => t('Image Placeholder'),
    'type' => 'checkboxes',
    'description' => t('Add a placeholder image background for wireframing. This option hides all the contents, except the title.'),
    'group' => 'wireframe',
    'theme hooks' => array('block'),
    'default status' => TRUE,
	'attached' => array(
      'css' => array('css/wireframes.css'),
    ),
    'options' => array(
      'plink-placeholder-image' => array(
        'title' => 'Image Placeholder',
        'class' => array('plink-placeholder'),
      ),   
        
    ),
  );


   
    $skins['plink_skins_wireframeheight'] = array(
    'title' => t('Block Height'),
    'type' => 'select',
    'description' => t('Add a set height to the block.'),
    'group' => 'wireframe',
    'theme hooks' => array('block'),
    'default status' => TRUE,
	'attached' => array(
      'css' => array('css/wireframes.css'),
    ),
    'options' => array(
      'plink-height-50' => array(
        'title' => '50px',
        'class' => array('plink-height-50'),
      ),   
      'plink-height-100' => array(
        'title' => '100px',
        'class' => array('plink-height-100'),
      ),   
            'plink-height-150' => array(
        'title' => '150px',
        'class' => array('plink-height-150'),
      ),   
            'plink-height-200' => array(
        'title' => '200px',
        'class' => array('plink-height-200'),
      ),   
            'plink-height-250' => array(
        'title' => '250px',
        'class' => array('plink-height-250'),
      ),   
            'plink-height-300' => array(
        'title' => '300px',
        'class' => array('plink-height-300'),
      ),   
            'plink-height-350' => array(
        'title' => '350px',
        'class' => array('plink-height-350'),
      ),   
            'plink-height-400' => array(
        'title' => '400px',
        'class' => array('plink-height-400'),
      ),   
        
    ),
  );


  
  $skins['plink_skins_block_color'] = array(
    'title' => t('Wireframe Block color'),
    'type' => 'select',
    'description' => t('Change the color of the block for wireframing'),
    'group' => 'wireframe',
    'theme hooks' => array('block'),
    'default status' => TRUE,
	'attached' => array(
      'css' => array('css/wireframes.css'),
    ),
     'options' => array(
      'plink-block-eee' => array(
        'title' => 'Grey #eee',
        'class' => array('plink-block-eee'),
      ),
      'plink-block-ddd' => array(
        'title' => 'Grey #ddd',
        'class' => array('plink-block-ddd'),
      ),
      'plink-block-ccc' => array(
        'title' => 'Grey #ccc',
        'class' => array('plink-block-ccc'),
      ),
      'plink-block-bbb' => array(
        'title' => 'Grey #bbb',
        'class' => array('plink-block-bbb'),
      ),
      'plink-block-aaa' => array(
        'title' => 'Grey #aaa',
        'class' => array('plink-block-aaa'),
      ),
      'plink-block-black' => array(
        'title' => 'Black',
        'class' => array('plink-block-black'),
      ),   
    ),
  );
  
    $skins['plink_skins_block_border'] = array(
    'title' => t('Wireframe Block border'),
    'type' => 'select',
    'description' => t('Add a border to the block for wireframing'),
    'group' => 'wireframe',
    'theme hooks' => array('block'),
    'default status' => TRUE,
	'attached' => array(
      'css' => array('css/wireframes.css'),
    ),
    'options' => array(
      'plink-block-border-grey' => array(
        'title' => 'Square Grey 1px',
        'class' => array('plink-block-border-grey'),
      ),
      'plink-block-border-black' => array(
        'title' => ' Square Black 1px',
        'class' => array('plink-block-border-black'),
      ),   
      'plink-block-border-grey-rnd' => array(
        'title' => 'Round Grey 1px',
        'class' => array('plink-block-border-grey-rnd'),
      ),
      'plink-block-border-black-rnd' => array(
        'title' => 'Round Black 1px',
        'class' => array('plink-block-border-black-rnd'),
      ),   

    ),
  );
  
  

  return $skins;
}

