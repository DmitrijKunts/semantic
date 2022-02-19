<?php // routes/breadcrumbs.php

use App\Models\Cat;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Главная', route('home'));
});

Breadcrumbs::for('cat', function (BreadcrumbTrail $trail, Cat $cat) {
    $trail->parent('home');
    // dd($cat->parent);
    while ($cat = $cat->parent) {
        $trail->push($cat->name, url($cat->slug));
    }
});
