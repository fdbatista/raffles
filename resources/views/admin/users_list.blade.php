@extends('layouts.layout_guest')

@section('pagelevelstyles')
    @parent
@endsection

@section('innercontent')

<div class="row" style="margin-bottom: 15px;">
    <div class="col-lg-12">
        <a class="breadcrumb" href="<?= url('/users') ?>">Users</a>
    </div>
</div>

<form name="frmSearch" action="<?= url('/users') ?>">
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
    
    <div class="row" style="margin-top: 10px;">
        <div class="col-lg-12">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 75px;"><a href="#">Username</a></th>
                        <th style="width: 85px;"><a href="#">Full Name</a></th>
                        <th style="width: 100px;"><a href="#">Email</a></th>
                        <th><a>Actions</a></th>
                    </tr>
                </thead>

                <tbody id="grid-content">
                    <?php
                        foreach($users as $user)
                        {?>
                            <tr>
                                <td><a href="<?= url("/users/details/$user->id") ?>"><?= $user->username ?></a></td>
                                <td><?= trim($user->full_name) ?></td>
                                <td><?= $user->email ?></td>
                                <td>
                                    <a href="<?= url("/users/raffles/$user->id") ?>" class="btn btn-primary"><i class="fa fa-gift"></i> Raffles</a>
                                    <a href="<?= url("/users/tickets/$user->id") ?>" class="btn btn-info"><i class="fa fa-calculator"></i> Tickets</a>
                                    <a href="<?= url("/users/edit/$user->id") ?>" class="btn btn-warning <?= Auth::user()->id == $user->id ? 'hidden' : '' ?>"><i class="fa fa-edit"></i> Edit</a>
                                </td>
                            </tr>
                        <?php
                        }
                    ?>
                </tbody>
            </table>
            <div class="col-lg-10">
                <ul class="pagination">
                    <li><a href="#">&laquo;</a></li>
                    <?php
                        foreach($pages as $page)
                        {?>
                            <li><a href="#"><?= $page ?></a></li>
                        <?php
                        }
                    ?>
                    <li><a href="#">&raquo;</a></li>
                </ul>
            </div>
        </div>
    </div>
</form>

@endsection

@section('javascripts')
@parent
@endsection
