@extends('layouts.layout_guest')

@section('pagelevelstyles')
@parent
@endsection

@section('innercontent')

<div class="row" style="margin-bottom: 15px;">
    <div class="col-lg-12">
        <a class="breadcrumb" href="<?= url('/countries') ?>">Countries</a>
    </div>
</div>

@include('flash')
<div class="row">
    <div class="form-group">
        <div class="col-sm-4">
            <a href="{{ url('/countries/new') }}" class="btn btn-info"> New Country</a>
        </div>
    </div>
</div>
<br />
<form name="frmSearch" action="<?= url('/countries') ?>">
    <div class="row">
        <div class="form-group error">
            <div class="col-sm-3">
                <input type="text" class="form-control" id="criteria" name="criteria" placeholder="Search terms" value="<?= $criteria ?>">
            </div>
            <div class="col-sm-3">
                <button type="submit" class="btn btn-success" id="btnSearch" name="btnSearch"><i class="fa fa-btn fa-search"></i> Search</button>
            </div>
        </div>
    </div>
    <br />
    <div class="row">
        <div class="form-group">
            <div class="col-sm-5">
                Results: <span class="badge badge-<?= $count > 0 ? 'primary' : 'danger' ?>"><?= $count ?></span>
            </div>
        </div>
    </div>
    
    <div class="row" style="margin-top: 10px;">
        <div class="col-lg-12">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50px;"></th>
                        <th style="width: 300px;"><a href="">Name</a></th>
                        <th style="width: 50px;"><a href="">Code</a></th>
                        <th><a href="">Actions</a></th>
                    </tr>
                </thead>

                <tbody id="grid-content">
                    <?php
                        foreach($items as $item)
                        {?>
                            <tr>
                                <td><a><i style="font-size: 20px;" class="fa fa-globe"></i></a></td>
                                <td><img width="24" height="24" src="<?= asset($item->flag_path) ?>" />&nbsp;&nbsp;<a><?= $item->name ?></a></td>
                                <td><?= $item->phone_code ?></td>
                                <td>
                                    <a href="<?= url("/countries/states/$item->id") ?>" class="btn btn-info"><i class="fa fa-map-signs"></i> States</a>
                                    <a href="<?= url("/countries/edit/$item->id") ?>" class="btn btn-success"><i class="fa fa-edit"></i> Edit</a>
                                    <a href="<?= url("/countries/delete/$item->id") ?>" class="btn btn-danger btn-delete"><i class="fa fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                        <?php
                        }
                    ?>
                </tbody>
            </table>
            <div class="col-lg-10">
                <ul class="pagination">
                    <?php
                        if (count($pages) > 0)
                        {
                            $prevPage = $pages[0] - 1 > 0 ? $pages[0] - 1 : 1;
                            $nextPage = $pages[count($pages) - 1] == $pagesCount ? $pagesCount : $pages[count($pages) - 1] + 1;
                        ?>
                            <li><a href="<?= url('/countries?page=' . $prevPage . '&criteria=' . $criteria) ?>">&laquo;</a></li>
                        <?php
                            foreach($pages as $page)
                            {?>
                                <li><a href="<?= url('/countries?page=' . $page . '&criteria=' . $criteria) ?>"><?= $page ?></a></li>
                            <?php
                            }?>
                            <li><a href="<?= url('/countries?page=' . $nextPage . '&criteria=' . $criteria) ?>">&raquo;</a></li>
                        <?php
                        }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</form>

@endsection

@section('javascripts')
<script src="/assets/plugins/pop-confirm/jquery.popconfirm.js"></script>

<script type="text/javascript">
    var q1 = null;
    $(document).ready(function() {
        q1 = $(".btn-delete").popConfirm({
            title: "<b>This action cannot be undone</b>!",
            content: "<code>Do you really want to proceed?</code>",
            placement: "bottom",
            yesBtn: "<i class='fa fa-trash'></i> Yes",
            noBtn: "<i class='fa fa-times'></i> No"
        });
    });
</script>

@parent
@endsection
