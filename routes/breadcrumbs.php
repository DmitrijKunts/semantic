<?php // routes/breadcrumbs.php

use App\Models\Cat;
use App\Models\Good;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Главная', route('home'));
});

Breadcrumbs::for('cat', function (BreadcrumbTrail $trail, Cat $cat, $lastActive = false) {
    $trail->parent('home');

    $_cats = [];
    $_cat = $cat;
    while ($_cat = $_cat->parent) {
        $_cats[] = $_cat;
    }
    $_cats = array_reverse($_cats);
    foreach ($_cats as $_c) {
        $trail->push($_c->name, route('cat', $_c));
    }
    if ($lastActive) {
        $trail->push($cat->name, route('cat', $cat));
    } else {
        $trail->push($cat->name);
    }
});

Breadcrumbs::for('good', function (BreadcrumbTrail $trail, Good $good) {
    $trail->parent('cat', $good->cats()->first(), true);

    $trail->push($good->name);
});
