<?php
// frontend url
/*$app->get('/hosting', function ($request, $response, $args) {
    $model = new \ExtensionsModel\PostModel();

    return $this->view->render($response, 'blog.phtml', [
        'name' => $args['name'],
        'mpost' => $model
    ]);
});
*/

foreach (glob(__DIR__.'/*_controller.php') as $controller) {
	$cname = basename($controller, '.php');
	if (!empty($cname)) {
		require_once $controller;
	}
}

foreach (glob(__DIR__.'/../components/*.php') as $component) {
    $cname = basename($component, '.php');
    if (!empty($cname)) {
        require_once $component;
    }
}

$app->group('/hosting', function () use ($user) {
    $this->group('/companies', function() use ($user) {
        new Extensions\Controllers\CompaniesController($this, $user);
    });
    $this->group('/reviewers', function() use ($user) {
        new Extensions\Controllers\ReviewersController($this, $user);
    });
});

?>
