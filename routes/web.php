<?php
/**
 * Web Routes
 */

use App\Core\Router;

$router = new Router();

// Home
$router->get('/', function() {
    header('Location: ' . '/job-portal/public/jobs');
    exit;
});

// Authentication Routes
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->post('/logout', 'AuthController@logout');

// Dashboard
$router->get('/dashboard', 'DashboardController@index');

// Job Routes
$router->get('/jobs', 'JobController@index');
$router->get('/jobs/create', 'JobController@create');
$router->post('/jobs/create', 'JobController@store');
$router->get('/jobs/search', 'JobController@search');
$router->get('/jobs/{id}', 'JobController@show');
$router->get('/jobs/{id}/edit', 'JobController@edit');
$router->post('/jobs/{id}/edit', 'JobController@update');
$router->post('/jobs/{id}/delete', 'JobController@delete');

// Application Routes
$router->post('/applications/apply/{id}', 'ApplicationController@apply');
$router->get('/applications/job/{id}', 'ApplicationController@jobApplications');
$router->post('/applications/{id}/status', 'ApplicationController@updateStatus');

// Saved Jobs Routes
$router->post('/jobs/save/{id}', 'SavedJobController@toggle');
$router->get('/saved-jobs', 'SavedJobController@index');

// API Routes
$router->get('/api/jobs', 'ApiController@jobs');
$router->get('/api/jobs/{id}', 'ApiController@job');

// 404 Handler
$router->notFound(function() {
    http_response_code(404);
    echo "<h1>404 - Page Not Found</h1>";
});

// Dispatch
$router->dispatch();
