<!-- BEGIN CONTENT -->
<h2>Latest items</h2>
<div class="bxslider-wrapper">
    <ul class="bxslider" data-slides-phone="1" data-slides-tablet="2" data-slides-desktop="3" data-slide-margin="15">
        <?php
                $incRaffles = App\Models\VNextRaffle::orderBy('created_at', 'desc')->take(9)->get();
                foreach ($incRaffles as $raffle)
                {?>
                    <li>
                        <div class="product-item">
                            <div class="pi-img-wrapper">
                                <img src="<?= asset($raffle->image_path) ?>" class="img-responsive" alt="<?= $raffle->product_name ?>" style="max-height: 130px; width: auto; margin: 0 auto;">
                                <div>
                                    <a href="<?= asset($raffle->image_path) ?>" class="btn btn-default fancybox-button">Zoom</a>
                                    <a href="<?= url("product-details/$raffle->product_id") ?>" class="btn btn-default fancybox-fast-view">Details</a>
                                </div>
                            </div>
                            <div class="sticker sticker-new"></div>
                            <h3><a href="<?= url("product-details/$raffle->product_id") ?>"><?= $raffle->product_name ?></a> | <a href="<?= url("product-list/$raffle->product_category_id") ?>"><?= $raffle->product_category ?></a></h3>
                            <div style="font-style: italic">
                                <?= substr($raffle->product_desc, 0, 74) . '...' ?>
                            </div>
                            <div class="pi-price">$<?= $raffle->ticket_price ?></div>
                        </div>
                    </li>
                <?php
                }
            ?>
    </ul>
</div>
<!-- END CONTENT -->