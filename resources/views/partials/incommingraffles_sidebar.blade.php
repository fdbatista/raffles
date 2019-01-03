<!-- BEGIN INCOMMING RAFFLES SIDEBAR -->
<div class="sidebar-products clearfix">
    <h2>Incomming raffles</h2>
    
    <?php
        $raffles = App\Models\VNextRaffle::get();
        for($i = 0; $i < count($raffles) && $i < 3; $i++)
        {
            $url = url("/product-details/" . $raffles[$i]->product_id);
            $product_name = $raffles[$i]->product_name;
            ?>
            <div class="item">
                <a href="<?= $url ?>"><img src="<?= asset($raffles[$i]->image_path) ?>" alt="<?= $product_name ?>" title="<?= $product_name ?>"></a>
                <h3><a href="<?= $url ?>"><?= $product_name ?></a></h3>
                <div class="price">$<?= $raffles[$i]->ticket_price ?></div>
            </div>
        <?php }
    ?>
    
    
</div>
<!-- END INCOMMING RAFFLES SIDEBAR -->