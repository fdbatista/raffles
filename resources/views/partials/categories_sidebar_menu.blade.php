<ul class="list-group margin-bottom-25 sidebar-menu" id="sidebar-menu-categs">
    <?php
        $categs = App\Models\Category::all()->sortBy('name');
        foreach ($categs as $categ) {
            echo '<li class="list-group-item clearfix"><a href="' . url("/product-list/$categ->id") . '"><i class="fa fa-angle-right"></i>' . $categ->name . '</a></li>';
        }
        if ($categs)
        {
            echo '<li class="list-group-item clearfix"><a href="' . url("/product-list") . '"><i class="fa fa-angle-right"></i>ALL</a></li>';
        }
        else
        {
            echo '<li class="list-group-item clearfix"><a>No categories yet</a></li>';
        }
    ?>
</ul>
