## Create files and folders

Blade Rendering File = /resources/views/blocks

Preview image = /resources/images/preview

Add These lines to top of the rendered files.

        {{--
            Title: Leadspace Block
            Description: Customer testimonial
            Category: firstmode
            Icon: admin-comments
            Keywords: leadspace block
            Mode: auto
            Align: center
            PostTypes: page post
            SupportsMode: false
            SupportsMultiple: true
            SupportsAlign: left right
            SupportsAlignContent: center
            EnqueueStyle:
            EnqueueScript:
            EnqueueAssets: assetsEnqueue
            EnqueueAssetsCSS: styles/modules/leadspace-block.css,styles/modules/leadspace-pages.css
            EnqueueAssetsJS: scripts/leadspace-block.js,scripts/slider.js
            --}}
