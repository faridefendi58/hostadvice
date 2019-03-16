<?php
// frontend url
$app->get('/hosting-company/[{name}]', function ($request, $response, $args) {
    $model = new \ExtensionsModel\HostingCompanyModel();
    $data = $model->getItem(['title' => $args['name']]);

    return $this->view->render($response, 'hosting_company.phtml', [
        'data' => $data
    ]);
});

$app->get('/hosting-services/[{slug}]', function ($request, $response, $args) {
    $model = new \ExtensionsModel\HostingProductCategoryModel();
    $data = $model->getItem(['slug' => $args['slug']]);

    $companies = [];
    if (!empty($data['id'])) {
        $cmodel = new \ExtensionsModel\HostingCompanyModel();
        $companies = $cmodel->getItemsByProductCategory($data['id']);
    }
    //var_dump($companies); exit;

    return $this->view->render($response, 'hosting_services.phtml', [
        'data' => $data,
        'companies' => $companies
    ]);
});

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
    $this->group('/expert-reviewers', function() use ($user) {
        new Extensions\Controllers\ExpertReviewersController($this, $user);
    });
});

?>
