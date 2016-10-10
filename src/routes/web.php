<?php

#Route::group(['middleware' => 'doNotCacheResponse'], function () {
#    Route::get('robots.txt', 'RobotsController@index');
    Route::get('deploy', 'DeployController@index');
#    Route::get('info', 'FrontendController@info');
#});