{{--
    Title: Leadspace Default
    Description: Customer testimonial
    Category: firstmode
    Icon: admin-comments
    Keywords: leadspace default
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
    EnqueueAssetsCSS: styles/modules/leadspace-default.css,styles/modules/leadspace-pages.css
    EnqueueAssetsJS: scripts/leadspace-default.js,scripts/slider.js
    --}}
<?php
    // Align: left
// SupportsAlign: left right
if (!empty($_POST['query']['preview'])) :

    /* Render screenshot for example */
    $imgUrl = \Roots\asset('images/preview/leadspace-default.png')->uri();
    echo '<img loading="lazy" src="' . $imgUrl . '">';
else:
    $content = get_field('leadspace');
    $bgPattern = $content['section_background_patterns'];
    $layoutType = $content['layout_type'];
    $images = $content['images'];
    $contentBlock = $content['content_block'];

    $imagesChunk = array_chunk($images, 2);

?>
    <section class="newone leadspace-default leadspace-default--{{ $layoutType }} section-gutter-sm <?php echo $bgPattern;?>">
        <div class="container">
            <?php
            foreach ($imagesChunk as $imgKey => $imageDatas):
                if($imgKey === 0):
                    ?>
                    <div class="leadspace-default__top">
                        <div class="row align-items-end">
                            <?php
                            foreach ($imageDatas as $key => $imageData):
                                $imageData = $imageData['image'];
                                if($key === 0 && isset($imageData['url'])):?>
                                    <div class="col-md-5">
                                        <div class="leadspace-default__img leadspace-default__img--first position-relative pt-4 pt-md-7 pe-4 pe-md-7">
                                            <div class="pattern-one position-absolute position-t-0 position-r-0 bg-eye--sky"></div>
                                            @if (isset($imageData) && !empty($imageData))
                                                <figure class="figure ratio ratio-486x364">
                                                    <img loading="lazy" src="{{ $imageData['url'] }}" class="object-fit object-position-center" alt="<?php echo $imageData['alt'];?>">
                                                </figure>
                                            @endif
                                        </div>
                                    </div>
                                <?php else:
                                    if(isset($imageData['url'])):
                                    ?>
                                    <div class="col-md-7">
                                        <div class="leadspace-default__img leadspace-default__img--second position-relative pb-4 pb-md-7 pe-4 pe-md-7">
                                            <div class="pattern-two position-absolute position-b-0 position-r-0 bg-topo--earth"></div>
                                             <?php if (isset($imageData) && !empty($imageData)): ?>
                                                 <figure class="figure ratio ratio-706x397">
                                                    <img loading="lazy" src="<?php echo $imageData['url'];?>" class="object-fit object-position-center" alt="<?php echo $imageData['alt'];?>">
                                                 </figure>
                                             <?php endif; ?>
                                        </div>
                                    </div>
                                <?php
                                    endif;
                                endif;
                            endforeach;?>
                        </div>
                    </div>
                <?php else:?>
                    <div class="leadspace-default__bottom pt-6 pt-sm-10 pt-xl-20">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="leadspace-default__content py-10 px-7 p-sm-10 p-lg-20 <?php echo $contentBlock['background'];?>">
                                    <h2><?php echo $contentBlock['title'];?></h2>
                                    <div class="post-content mb-0 pt-4 body-extra-large">
                                        <p>
                                            <?php echo $contentBlock['content'];?>
                                        </p>
                                    </div>
                                    <?php
                                    if(isset($content['cta'])):?>
                                    <div class="leadspace-default__btn pt-6 pt-lg-10">
                                        <a href="<?php echo $content['cta']['url'];?>" target="<?php echo $content['cta']['target'];?>" title="<?php echo $content['cta']['title'];?>" class="btn btn-outline-white">
                                        <?php echo $content['cta']['title'];?> <i class="icon-arrow-right ps-2"></i>
                                        </a>
                                    </div>
                                    <?php endif;?>
                                </div>
                            </div>
                            <?php
                            foreach ($imageDatas as $key => $imageData):
                                $imageData = $imageData['image'];
                                if($key === 0 && isset($imageData['url'])):?>
                                    <div class="col-md-4">
                                        <div class="leadspace-default__img leadspace-default__img--first position-relative">
                                            <div class="pattern-three position-absolute position-b-0 bg-squircle bg-squircle--moon"></div>
                                         <?php if (isset($imageData) && !empty($imageData)): ?>
                                             <figure class="figure ratio ratio-1x1">
                                                <img loading="lazy" src="<?php echo $imageData['url'];?>" class="object-fit object-position-center" alt="<?php echo $imageData['alt'];?>">
                                             </figure>
                                         <?php endif; ?>
                                        </div>
                                    </div>
                                <?php else:?>
                                <?php endif;?>

                            <?php endforeach;?>
                        </div>
                    </div>
                <?php endif;?>
            <?php endforeach;?>

        </div>
    </section>
<?php
endif;
