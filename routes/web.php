<?php


Route::get('/login','HomeController@login');

Route::get('/home', 'HomeController@index')->name('home');
