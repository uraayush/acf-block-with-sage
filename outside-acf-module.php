<?php
/**
 * Plugin Name: Outside Acf modules
 * Plugin URI: https://outside.tech
 * Description: Outside ACF Block/Moudle development - Render Template must create block under /views/blocks folder and preview image should be in /images/preview folder
 * Version: 1.0
 * Author: Outside
 * Author URI: https://outside.tech
 */
namespace App;
class OutsideAcfModule{
    private $assetsData;

    public function __construct()
    {

        if (! function_exists('acf_register_block_type')) {
            return;
        }
        if (! function_exists('add_filter')) {
            return;
        }
        if (! function_exists('add_action')) {
            return;
        }

        add_filter('outside-sage-acf-gutenberg-blocks-templates', function () {
            return ['views/blocks'];
        });


        /**
         * style update in header
         */
        add_action('wp_enqueue_scripts', function () {
            $directories = apply_filters('outside-sage-acf-gutenberg-blocks-templates', []);
            
            $id = get_the_ID();
            foreach ($directories as $directory) {
                $dir = isSage10() ? \Roots\resource_path($directory) : \locate_template($directory);
        
                // Sanity check whether the directory we're iterating over exists first
                if (!file_exists($dir)) {
                    return;
                }
                $template_directory = new \DirectoryIterator($dir);
                foreach ($template_directory as $template) {
                    
                    // print_r($template);
                    if (!$template->isDot() && !$template->isDir()) {
                        
                        // Strip the file extension to get the slug
                        $slug = removeBladeExtension($template->getFilename());
                        // If there is no slug (most likely because the filename does
                        // not end with ".blade.php", move on to the next file.
                        if (!$slug) {
                            continue;
                        }
    
                        // Get header info from the found template file(s)
                        $file = "${dir}/${slug}.blade.php";
                        $file_path = file_exists($file) ? $file : '';
                        $cssFilePath = "styles/modules/${slug}.css";

                        if ( checkFileExists( $cssFilePath ) ) {
                            if ( has_block('acf/'.$slug , $id) ) {
                                wp_enqueue_style($cssFilePath , checkAssetPath($cssFilePath), false, null);
                            }
                        }


                    }
                }

            }

        },1000);

        /**
         * Create blocks based on templates found in Sage's "views/blocks" directory
        */
        add_action('acf/init', function () {

            // Global $sage_error so we can throw errors in the typical sage manner
            global $sage_error;
            global $enqueData;
            // Get an array of directories containing blocks
            $directories = apply_filters('outside-sage-acf-gutenberg-blocks-templates', []);

            // Check whether ACF exists before continuing
            foreach ($directories as $directory) {
                $dir = isSage10() ? \Roots\resource_path($directory) : \locate_template($directory);
    
                // Sanity check whether the directory we're iterating over exists first
                if (!file_exists($dir)) {
                    return;
                }
    
                // Iterate over the directories provided and look for templates
                $template_directory = new \DirectoryIterator($dir);
    
                foreach ($template_directory as $template) {
                    if (!$template->isDot() && !$template->isDir()) {
    
                        // Strip the file extension to get the slug
                        $slug = removeBladeExtension($template->getFilename());
                        // If there is no slug (most likely because the filename does
                        // not end with ".blade.php", move on to the next file.
                        if (!$slug) {
                            continue;
                        }
    
                        // Get header info from the found template file(s)
                        $file = "${dir}/${slug}.blade.php";
                        $file_path = file_exists($file) ? $file : '';
                        $file_headers = get_file_data($file_path, [
                            'title' => 'Title',
                            'description' => 'Description',
                            'category' => 'Category',
                            'icon' => 'Icon',
                            'keywords' => 'Keywords',
                            'mode' => 'Mode',
                            'align' => 'Align',
                            'post_types' => 'PostTypes',
                            'supports_align' => 'SupportsAlign',
                            'supports_anchor' => 'SupportsAnchor',
                            'supports_mode' => 'SupportsMode',
                            'supports_jsx' => 'SupportsInnerBlocks',
                            'supports_align_text' => 'SupportsAlignText',
                            'supports_align_content' => 'SupportsAlignContent',
                            'supports_multiple' => 'SupportsMultiple',
                            'enqueue_style'     => 'EnqueueStyle',
                            'enqueue_script'    => 'EnqueueScript',
                            'enqueue_assets'    => 'EnqueueAssets',
                            'enqueue_assetsCSS'    => 'EnqueueAssetsCSS',
                            'enqueue_assetsJS'    => 'EnqueueAssetsJS',
                        ]);
                        // $enqueData = array(
                        //     'styles' => $file_headers['enqueue_assetsCSS'],
                        //     'scripts' => $file_headers['enqueue_assetsJS']
                        // );
                        // $this->setAssets($enqueData);


                        
                        if (empty($file_headers['title'])) {
                            $sage_error(__('This block needs a title: ' . $dir . '/' . $template->getFilename(), 'sage'), __('Block title missing', 'sage'));
                        }
    
                        if (empty($file_headers['category'])) {
                            $sage_error(__('This block needs a category: ' . $dir . '/' . $template->getFilename(), 'sage'), __('Block category missing', 'sage'));
                        }
    
                        // Checks if dist contains this asset, then enqueues the dist version.
                        if (!empty($file_headers['enqueue_style'])) {
                            $this->checkAssetPath($file_headers['enqueue_style']);
                        }
    
                        if (!empty($file_headers['enqueue_script'])) {
                            $this->checkAssetPath($file_headers['enqueue_script']);
                        }
                        
                        // Set up block data for registration
                        $data = [
                            'name' => $slug,
                            'title' => $file_headers['title'],
                            'description' => $file_headers['description'],
                            'category' => $file_headers['category'],
                            'icon' => $file_headers['icon'],
                            'keywords' => explode(' ', $file_headers['keywords']),
                            'mode' => $file_headers['mode'],
                            'align' => $file_headers['align'],
                            'additionalAssets' => array(
                                'styles' => $file_headers['enqueue_assetsCSS'],
                                'scripts' => $file_headers['enqueue_assetsJS']
                            ),
                            'render_callback'  => __NAMESPACE__.'\\sage_blocks_callback',
                            'enqueue_style'   => $file_headers['enqueue_style'],
                            'enqueue_script'  => $file_headers['enqueue_script'],
                            'enqueue_assets'  => __NAMESPACE__.'\\assetsEnqueue',
                            // 'enqueue_assets'  => function($data){
                            //     $asData = $this->getAssets();
                            //     print_r($data);
                            // }
                        ];
                        
                        $data['example']['attributes'] = array(
                            'mode' => 'preview',
                            'data' => []
                        );

                        // If the PostTypes header is set in the template, restrict this block to those types
                        if (!empty($file_headers['post_types'])) {
                            $data['post_types'] = explode(' ', $file_headers['post_types']);
                        }
    
                        // If the SupportsAlign header is set in the template, restrict this block to those aligns
                        if (!empty($file_headers['supports_align'])) {
                            $data['supports']['align'] = in_array($file_headers['supports_align'], array('true', 'false'), true) ? filter_var($file_headers['supports_align'], FILTER_VALIDATE_BOOLEAN) : explode(' ', $file_headers['supports_align']);
                        }
    
                        // If the SupportsMode header is set in the template, restrict this block mode feature
                        if (!empty($file_headers['supports_anchor'])) {
                            $data['supports']['anchor'] = $file_headers['supports_anchor'] === 'true' ? true : false;
                        }
    
                        // If the SupportsMode header is set in the template, restrict this block mode feature
                        if (!empty($file_headers['supports_mode'])) {
                            $data['supports']['mode'] = $file_headers['supports_mode'] === 'true' ? true : false;
                        }
    
                        // If the SupportsInnerBlocks header is set in the template, restrict this block mode feature
                        if (!empty($file_headers['supports_jsx'])) {
                        $data['supports']['jsx'] = $file_headers['supports_jsx'] === 'true' ? true : false;
                        }
    
                        // If the SupportsAlignText header is set in the template, restrict this block mode feature
                        if (!empty($file_headers['supports_align_text'])) {
                        $data['supports']['align_text'] = $file_headers['supports_align_text'] === 'true' ? true : false;
                        }
    
                        // If the SupportsAlignContent header is set in the template, restrict this block mode feature
                        if (!empty($file_headers['supports_align_text'])) {
                        $data['supports']['align_content'] = $file_headers['supports_align_content'] === 'true' ? true : false;
                        }
    
                        // If the SupportsMultiple header is set in the template, restrict this block multiple feature
                        if (!empty($file_headers['supports_multiple'])) {
                            $data['supports']['multiple'] = $file_headers['supports_multiple'] === 'true' ? true : false;
                        }
    
                        // Register the block with ACF
                        \acf_register_block_type( apply_filters( "sage/blocks/$slug/register-data", $data ) );
                    }
                }
            }

        });

        /**
         * Callback to register blocks
         */
        function sage_blocks_callback($block, $content = '', $is_preview = false, $post_id = 0)
        {
            // Set up the slug to be useful
            $slug  = str_replace('acf/', '', $block['name']);
            $block = array_merge(['className' => ''], $block);

            // Set up the block data
            $block['post_id'] = $post_id;
            $block['is_preview'] = $is_preview;
            $block['content'] = $content;
            $block['slug'] = $slug;
            $block['anchor'] = isset($block['anchor']) ? $block['anchor'] : '';
            // Send classes as array to filter for easy manipulation.
            $block['classes'] = [
                $slug,
                $block['className'],
                $block['is_preview'] ? 'is-preview' : null,
                'align'.$block['align']
            ];

            // Filter the block data.
            $block = apply_filters("sage/blocks/$slug/data", $block);

            // Join up the classes.
            $block['classes'] = implode(' ', array_filter($block['classes']));

            // Get the template directories.
            $directories = apply_filters('outside-sage-acf-gutenberg-blocks-templates', []);

            foreach ($directories as $directory) {
                $view = ltrim($directory, 'views/') . '/' . $slug;

                if (isSage10()) {

                    if (\Roots\view()->exists($view)) {
                        // Use Sage's view() function to echo the block and populate it with data
                        echo \Roots\view($view, ['block' => $block]);
                    }

                } else {
                    try {
                        // Use Sage 9's template() function to echo the block and populate it with data
                        echo \App\template($view, ['block' => $block]);
                    } catch (\Exception $e) {
                        //
                    }
                }
            }
        }

        /**
         * Function to strip the `.blade.php` from a blade filename
         */
        function removeBladeExtension($filename)
        {
            // Filename must end with ".blade.php". Parenthetical captures the slug.
            $blade_pattern = '/(.*)\.blade\.php$/';
            $matches = [];
            // If the filename matches the pattern, return the slug.
            if (preg_match($blade_pattern, $filename, $matches)) {
                return $matches[1];
            }
            // Return FALSE if the filename doesn't match the pattern.
            return FALSE;
        }

        /**
         * Checks asset path for specified asset.
         *
         * @param string &$path
         *
         * @return void
         */
        function checkAssetPath(&$path)
        {
            if (preg_match("/^(styles|scripts)/", $path)) {
                return $path = isSage10() ? \Roots\asset($path)->uri() : \App\asset_path($path);
            }
        }

        function checkFileExists($path) {
            if (preg_match("/^(styles|scripts)/", $path)) {
                $path = isSage10() ? \Roots\asset($path)->uri() : \App\asset_path($path);
                $cssFilePath = $_SERVER['DOCUMENT_ROOT'] . parse_url($path, PHP_URL_PATH);
                return file_exists($cssFilePath);

            }
        }

        /**
         * Check if Sage 10 is used.
         *
         * @return bool
         */
        function isSage10()
        {
            return class_exists('Roots\Acorn\Application');
        }

        /**
         * Asset Enqueue function 
         * @data = 
         */
        function assetsEnqueue($data) {
            $enqueData = $data['additionalAssets'];
            /**
             * Style enqueue
             */
            if (!empty($enqueData['styles'])) {
                $enqueAssetsFiles = explode(",", $enqueData['styles']);
                foreach ($enqueAssetsFiles as $file) {
                    if (checkFileExists($file)) {
                        wp_enqueue_style($file, checkAssetPath($file), true, null);
                    }
                }
            }
            
            /**
             * Script Enqueue
             */
            if (!empty($enqueData['scripts'])) {
                $enqueAssetsFiles = explode(",", $enqueData['scripts']);
                
                foreach ($enqueAssetsFiles as $file) {
                    if (checkFileExists($file)) {
                        wp_enqueue_script($file, checkAssetPath($file), array('jquery'), '', true);
                    }

                }
            }

        }
    }
    // public function setAssets($assetsFiles) {
    //     $this->assetsData = $assetsFiles;
    // }
    // public function getAssets() {
    //     return $this->assetsData;
    // }


}

new OutsideAcfModule();