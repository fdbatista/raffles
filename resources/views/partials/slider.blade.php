<!-- BEGIN SLIDER -->
    <div class="page-slider margin-bottom-35">
        
        <div id="layerslider" style="width: 100%; height: 494px; margin: 0 auto;">
            <?php
                $slides = App\Models\MainSlider::orderBy('id')->get();
                
                foreach ($slides as $slide)
                {
                    $randomNumber = random_int(1, 110);
                    $animationTransition = $randomNumber++ . ',' . $randomNumber++ . ',' . $randomNumber++ . ',' . $randomNumber++;
                ?>
                    <div class="ls-layer ls-layer1" style="slidedirection: right; transition2d: <?= $animationTransition ?>;">
                        <img src="<?= asset($slide->image_path) ?>" class="ls-bg" alt="Slide background">
                        <div class="ls-s-1 title" style=" top: 96px; left: 35%; slidedirection : fade; slideoutdirection : fade; durationin : 750; durationout : 750; easingin : easeOutQuint; rotatein : 90; rotateout : -90; scalein : .5; scaleout : .5; showuntil : 4000; white-space: nowrap;">
                        <?= $slide->content ?>
                        </div>
                    </div>
                <?php
                }
            ?>
        </div>
        
    </div>
