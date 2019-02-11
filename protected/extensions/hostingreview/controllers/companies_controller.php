<?php

namespace Extensions\Controllers;

use Components\BaseController as BaseController;

class CompaniesController extends BaseController
{
    public function __construct($app, $user)
    {
        parent::__construct($app, $user);
    }

    public function register($app)
    {
        $app->map(['GET'], '/view', [$this, 'view']);
        $app->map(['GET', 'POST'], '/create', [$this, 'create']);
        $app->map(['GET', 'POST'], '/update/[{id}]', [$this, 'update']);
        $app->map(['POST'], '/delete/[{id}]', [$this, 'delete']);
        $app->map(['POST'], '/create-plan/[{id}]', [$this, 'create_plan']);
        $app->map(['POST'], '/update-plan/[{id}]', [$this, 'update_plan']);
        $app->map(['POST'], '/delete-plan/[{id}]', [$this, 'delete_plan']);
        $app->map(['POST'], '/create-feature/[{id}]', [$this, 'create_feature']);
        $app->map(['POST'], '/update-feature/[{id}]', [$this, 'update_feature']);
        $app->map(['POST'], '/delete-feature/[{id}]', [$this, 'delete_feature']);
        $app->map(['POST'], '/create-review/[{id}]', [$this, 'create_review']);
        $app->map(['POST'], '/update-review/[{id}]', [$this, 'update_review']);
        $app->map(['POST'], '/delete-review/[{id}]', [$this, 'delete_review']);
    }

    public function accessRules()
    {
        return [
            ['allow',
                'actions' => [
                    'view', 'create', 'update', 'delete',
                    'create-plan', 'update-plan', 'delete-plan',
                    'create-feature', 'update-feature', 'delete-feature',
                    'create-review', 'update-review', 'delete-review'
                    ],
                'users'=> ['@'],
            ],
            ['deny',
                'users' => ['*'],
            ],
        ];
    }

    public function view($request, $response, $args)
    {
        $isAllowed = $this->isAllowed($request, $response);
        if ($isAllowed instanceof \Slim\Http\Response)
            return $isAllowed;

        if(!$isAllowed){
            return $this->notAllowedAction();
        }

        $model = new \ExtensionsModel\HostingCompanyModel();
        $companies = $model->getData();

        return $this->_container->module->render($response, 'hostings/view.html', [
            'companies' => $companies
        ]);
    }

    public function create($request, $response, $args)
    {
        $isAllowed = $this->isAllowed($request, $response);
        if ($isAllowed instanceof \Slim\Http\Response)
            return $isAllowed;

        if(!$isAllowed){
            return $this->notAllowedAction();
        }

        if (isset($_POST['HostingCompany'])){
            $model = new \ExtensionsModel\HostingCompanyModel('create');
            $model->title = $_POST['HostingCompany']['title'];
            $model->description = $_POST['HostingCompany']['description'];
            $model->address = $_POST['HostingCompany']['address'];
            $model->phone = $_POST['HostingCompany']['phone'];
            $model->email = $_POST['HostingCompany']['email'];
            $model->website = $_POST['HostingCompany']['website'];
            $model->status = $_POST['HostingCompany']['status'];
            $configs = [];
            if (isset($_POST['HostingCompany']['affiliate_url'])) {
                $configs['affiliate_url'] = $_POST['HostingCompany']['affiliate_url'];
            }
            $model->configs = json_encode($configs);
            $model->created_at = date('Y-m-d H:i:s');
            $model->created_by = $this->_user->id;
            $save = \ExtensionsModel\HostingCompanyModel::model()->save($model);
            if ($save) {
                $message = 'Data berhasil disimpan';
                $success = true;
            } else {
                $message = 'Gagal menyimpan data.';
                $success = false;
            }
        }

        return $this->_container->module->render($response, 'hostings/create.html', [
            'message' => ($message) ? $message : null,
            'success' => $success
        ]);
    }

    public function update($request, $response, $args)
    {
        $isAllowed = $this->isAllowed($request, $response);
        if ($isAllowed instanceof \Slim\Http\Response)
            return $isAllowed;

        if(!$isAllowed){
            return $this->notAllowedAction();
        }

        if (empty($args['id']))
            return false;

        $model = \ExtensionsModel\HostingCompanyModel::model()->findByPk($args['id']);
        $hcmodel = new \ExtensionsModel\HostingCompanyModel();
        $detail = $hcmodel->getDetail($args['id']);
        // hosting plan
        $hpmodel = new \ExtensionsModel\HostingPlanModel();
        $plans = $hpmodel->getData(['hosting_company_id' => $model->id]);
        // feature
        $hfmodel = new \ExtensionsModel\HostingFeatureCompanyModel();
        $features = $hfmodel->getData(['hosting_company_id' => $model->id]);
        // review
        $hrmodel = new \ExtensionsModel\HostingReviewModel();
        $reviews = $hrmodel->getData(['hosting_company_id' => $model->id]);
        // configs
        $configs = [];
        if (!empty($model->configs)) {
            $configs = json_decode($model->configs, true);
        }

        if (isset($_POST['HostingCompany'])){
            $model->title = $_POST['HostingCompany']['title'];
            $model->description = $_POST['HostingCompany']['description'];
            $model->address = $_POST['HostingCompany']['address'];
            $model->phone = $_POST['HostingCompany']['phone'];
            $model->email = $_POST['HostingCompany']['email'];
            $model->website = $_POST['HostingCompany']['website'];
            $model->status = $_POST['HostingCompany']['status'];
            if (isset($_POST['HostingCompany']['affiliate_url'])) {
                $configs['affiliate_url'] = $_POST['HostingCompany']['affiliate_url'];
            }
            $model->configs = json_encode($configs);
            $model->updated_at = date('Y-m-d H:i:s');
            $model->updated_by = $this->_user->id;
            $update = \ExtensionsModel\HostingCompanyModel::model()->update($model);
            if ($update) {
                $detail = $hcmodel->getDetail($model->id);
                $message = 'Data berhasil diubah';
                $success = true;
            } else {
                $message = 'Gagal merubah data.';
                $success = false;
            }
        }

        return $this->_container->module->render($response, 'hostings/update.html', [
            'model' => $model,
            'detail' => $detail,
            'message' => ($message) ? $message : null,
            'success' => $success,
            'plans' => $plans,
            'features' => $features,
            'reviews' => $reviews
        ]);
    }

    public function delete($request, $response, $args)
    {
        $isAllowed = $this->isAllowed($request, $response);
        if ($isAllowed instanceof \Slim\Http\Response)
            return $isAllowed;

        if(!$isAllowed){
            return $this->notAllowedAction();
        }

        if (!isset($args['id'])) {
            return false;
        }

        $model = \ExtensionsModel\HostingCompanyModel::model()->findByPk($args['id']);
        $delete = \ExtensionsModel\HostingCompanyModel::model()->delete($model);
        if ($delete) {
            $delete2 = \ExtensionsModel\HostingPlanModel::model()->deleteAllByAttributes(['hosting_company_id'=>$args['id']]);
            $message = 'Data berhasil dihapus.';
            echo true;
        }
    }

    public function create_plan($request, $response, $args)
    {
        $isAllowed = $this->isAllowed($request, $response);
        if ($isAllowed instanceof \Slim\Http\Response)
            return $isAllowed;

        if (!$isAllowed) {
            return $this->notAllowedAction();
        }

        if (!isset($args['id'])) {
            return false;
        }

        $model = new \ExtensionsModel\HostingPlanModel();
        if (isset($_POST['HostingPlan'])) {
            $model->title = $_POST['HostingPlan']['title'];
            if (isset($_POST['HostingPlan']['term'])) {
                foreach ($_POST['HostingPlan']['term'] as $term => $term_value) {
                    $model->{$term} = (int) $term_value;
                }
            }
            $model->short_description = $_POST['HostingPlan']['short_description'];
            $model->description = $_POST['HostingPlan']['description'];
            $model->hosting_company_id = $args['id'];
            if (!empty($model->price_1y)) {
                $model->base_price = $model->price_1y;
                $model->current_price = $model->price_1y;
            }
            $model->created_at = date("Y-m-d H:i:s");
            $model->created_by = $this->_user->id;
            try {
                $save = \ExtensionsModel\HostingPlanModel::model()->save($model);
            } catch (\Exception $e) {
                var_dump($e->getMessage()); exit;
            }

            if ($save) {
                return $response->withJson(
                    [
                        'status' => 'success',
                        'message' => 'Data berhasil disimpan.',
                    ], 201);
            } else {
                return $response->withJson(['status'=>'failed'], 201);
            }
        } else {
            return $response->withJson(['status'=>'failed', 'messsage'=>'Tidak berhasil menyimpan data.'], 201);
        }
    }

    public function update_plan($request, $response, $args)
    {
        $isAllowed = $this->isAllowed($request, $response);
        if ($isAllowed instanceof \Slim\Http\Response)
            return $isAllowed;

        if (!$isAllowed) {
            return $this->notAllowedAction();
        }

        if (empty($args['id']))
            return false;
        $params = $request->getParams();
        if (isset($params['id']) && isset($params['title'])) {
            $model = \ExtensionsModel\HostingPlanModel::model()->findByPk($params['id']);
            if ($model instanceof \RedBeanPHP\OODBBean) {
                $model->title = $params['title'];
                $model->price_1y = $params['price_1y'];
                $model->updated_at = date("Y-m-d H:i:s");
                $update = \ExtensionsModel\HostingPlanModel::model()->update($model);
                if ($update) {
                    return $response->withJson(
                        [
                            'status' => 'success',
                            'message' => 'Data berhasil diubah.',
                        ], 201);
                }
            }
        }

        return $response->withJson(
            [
                'status' => 'failed',
                'message' => 'Data gagal diperbarui.',
            ], 201);
    }

    public function delete_plan($request, $response, $args)
    {
        $isAllowed = $this->isAllowed($request, $response, $args);
        if ($isAllowed instanceof \Slim\Http\Response)
            return $isAllowed;

        if(!$isAllowed){
            return $this->notAllowedAction();
        }

        if (!isset($args['id'])) {
            return false;
        }

        $model = \ExtensionsModel\HostingPlanModel::model()->findByPk($args['id']);
        $delete = \ExtensionsModel\HostingPlanModel::model()->delete($model);
        if ($delete) {
            return $response->withJson(
                [
                    'status' => 'success',
                    'message' => 'Data berhasil dihapus.',
                ], 201);
        } else {
            return $response->withJson(
                [
                    'status' => 'failed',
                    'message' => 'Data gagal dihapus.',
                ], 201);
        }
    }

    public function update_feature($request, $response, $args)
    {
        $isAllowed = $this->isAllowed($request, $response);
        if ($isAllowed instanceof \Slim\Http\Response)
            return $isAllowed;

        if (!$isAllowed) {
            return $this->notAllowedAction();
        }

        if (empty($args['id']))
            return false;
        $params = $request->getParams();
        if (isset($params['id']) && isset($params['title'])) {
            $model = \ExtensionsModel\HostingFeatureCompanyModel::model()->findByPk($params['id']);
            if ($model instanceof \RedBeanPHP\OODBBean) {
                $model->title = $params['title'];
                $model->value = $params['value'];
                $model->updated_at = date("Y-m-d H:i:s");
                $update = \ExtensionsModel\HostingFeatureCompanyModel::model()->update($model);
                if ($update) {
                    return $response->withJson(
                        [
                            'status' => 'success',
                            'message' => 'Data berhasil diubah.',
                        ], 201);
                }
            }
        }

        return $response->withJson(
            [
                'status' => 'failed',
                'message' => 'Data gagal diperbarui.',
            ], 201);
    }

    public function create_feature($request, $response, $args)
    {
        $isAllowed = $this->isAllowed($request, $response);
        if ($isAllowed instanceof \Slim\Http\Response)
            return $isAllowed;

        if (!$isAllowed) {
            return $this->notAllowedAction();
        }

        if (!isset($args['id'])) {
            return false;
        }

        $model = new \ExtensionsModel\HostingFeatureCompanyModel();
        if (isset($_POST['HostingFeature'])) {
            $model->title = $_POST['HostingFeature']['title'];
            $new_feature = false;
            if (isset($_POST['HostingFeature']['title_new'])) {
                $model->title = $_POST['HostingFeature']['title_new'];
                $new_feature = true;
            }
            if (!empty($_POST['HostingFeature']['hosting_feature_id'])) {
                $model->hosting_feature_id = $_POST['HostingFeature']['hosting_feature_id'];
            }
            $model->description = $_POST['HostingFeature']['description'];
            $model->hosting_company_id = $args['id'];
            $model->value = $_POST['HostingFeature']['value'];
            $model->created_at = date("Y-m-d H:i:s");
            $model->created_by = $this->_user->id;
            try {
                $save = \ExtensionsModel\HostingFeatureCompanyModel::model()->save(@$model);
            } catch (\Exception $e) {
                var_dump($e->getMessage()); exit;
            }

            if ($save) {
                // create new feature if any
                if ($new_feature) {
                    $model2 = new \ExtensionsModel\HostingFeatureModel();
                    $model2->title = $_POST['HostingFeature']['title_new'];
                    $model2->description = $_POST['HostingFeature']['description'];
                    $model2->created_at = date("Y-m-d H:i:s");
                    $model2->created_by = $this->_user->id;
                    try {
                        $save2 = \ExtensionsModel\HostingFeatureModel::model()->save(@$model2);
                        if ($save2) {
                            $model->hosting_feature_id = $model2->id;
                            $update = \ExtensionsModel\HostingFeatureCompanyModel::model()->update($model);
                        }
                    } catch (\Exception $e) {}
                }
                return $response->withJson(
                    [
                        'status' => 'success',
                        'message' => 'Data berhasil disimpan.',
                    ], 201);
            } else {
                return $response->withJson(['status'=>'failed'], 201);
            }
        } else {
            return $response->withJson(['status'=>'failed', 'messsage'=>'Tidak berhasil menyimpan data.'], 201);
        }
    }

    public function delete_feature($request, $response, $args)
    {
        $isAllowed = $this->isAllowed($request, $response, $args);
        if ($isAllowed instanceof \Slim\Http\Response)
            return $isAllowed;

        if(!$isAllowed){
            return $this->notAllowedAction();
        }

        if (!isset($args['id'])) {
            return false;
        }

        $model = \ExtensionsModel\HostingFeatureCompanyModel::model()->findByPk($args['id']);
        $delete = \ExtensionsModel\HostingFeatureCompanyModel::model()->delete($model);
        if ($delete) {
            return $response->withJson(
                [
                    'status' => 'success',
                    'message' => 'Data berhasil dihapus.',
                ], 201);
        } else {
            return $response->withJson(
                [
                    'status' => 'failed',
                    'message' => 'Data gagal dihapus.',
                ], 201);
        }
    }

    public function create_review($request, $response, $args)
    {
        $isAllowed = $this->isAllowed($request, $response);
        if ($isAllowed instanceof \Slim\Http\Response)
            return $isAllowed;

        if (!$isAllowed) {
            return $this->notAllowedAction();
        }

        if (!isset($args['id'])) {
            return false;
        }

        $model = new \ExtensionsModel\HostingReviewModel();
        if (isset($_POST['HostingReview'])) {
            $new_reviewer = false;
            if (isset($_POST['HostingReview']['reviewer_name'])) {
                $new_reviewer = true;
            }
            if (!empty($_POST['HostingReview']['reviewer_id'])) {
                $model->reviewer_id = $_POST['HostingFeature']['reviewer_id'];
            }
            $model->hosting_company_id = $args['id'];
            $model->content = $_POST['HostingReview']['content'];
            $model->status = \ExtensionsModel\HostingReviewModel::STATUS_PUBLISHED;
            $model->created_at = date("Y-m-d H:i:s");
            $model->updated_at = date("Y-m-d H:i:s");
            try {
                $save = \ExtensionsModel\HostingReviewModel::model()->save(@$model);
            } catch (\Exception $e) {
                var_dump($e->getMessage()); exit;
            }

            if ($save) {
                // create new feature if any
                if ($new_reviewer) {
                    $check_data = \ExtensionsModel\HostingReviewerModel::model()->findByAttributes(['email' => $_POST['HostingReview']['reviewer_email']]);
                    if ($check_data instanceof \RedBeanPHP\OODBBean) {
                        $model3 = \ExtensionsModel\HostingReviewModel::model()->findByPk($model->id);
                        $model3->reviewer_id = $check_data->id;
                        $model3->updated_at = date("Y-m-d H:i:s");
                        $update = \ExtensionsModel\HostingReviewModel::model()->update($model3);
                    } else {
                        $model2 = new \ExtensionsModel\HostingReviewerModel();
                        $model2->name = $_POST['HostingReview']['reviewer_name'];
                        $model2->email = $_POST['HostingReview']['reviewer_email'];
                        $model2->created_at = date("Y-m-d H:i:s");
                        $model2->updated_at = date("Y-m-d H:i:s");
                        try {
                            $save2 = \ExtensionsModel\HostingReviewerModel::model()->save(@$model2);
                            if ($save2) {
                                $model3 = \ExtensionsModel\HostingReviewModel::model()->findByPk($model->id);
                                $model3->reviewer_id = $model2->id;
                                $model3->updated_at = date("Y-m-d H:i:s");
                                $update = \ExtensionsModel\HostingReviewModel::model()->update($model3);
                            }
                        } catch (\Exception $e) {
                            var_dump($e->getMessage()); exit;
                        }
                    }
                }
                return $response->withJson(
                    [
                        'status' => 'success',
                        'message' => 'Data berhasil disimpan.',
                    ], 201);
            } else {
                return $response->withJson(['status'=>'failed'], 201);
            }
        } else {
            return $response->withJson(['status'=>'failed', 'messsage'=>'Tidak berhasil menyimpan data.'], 201);
        }
    }

    public function update_review($request, $response, $args)
    {
        $isAllowed = $this->isAllowed($request, $response);
        if ($isAllowed instanceof \Slim\Http\Response)
            return $isAllowed;

        if (!$isAllowed) {
            return $this->notAllowedAction();
        }

        if (empty($args['id']))
            return false;
        $params = $request->getParams();
        if (isset($params['id']) && isset($params['content'])) {
            $model = \ExtensionsModel\HostingReviewModel::model()->findByPk($params['id']);
            if ($model instanceof \RedBeanPHP\OODBBean) {
                $model->content = $params['content'];
                $model->status = $params['status'];
                $model->updated_at = date("Y-m-d H:i:s");
                $update = \ExtensionsModel\HostingReviewModel::model()->update($model);
                if ($update) {
                    return $response->withJson(
                        [
                            'status' => 'success',
                            'message' => 'Data berhasil diubah.',
                        ], 201);
                }
            }
        }

        return $response->withJson(
            [
                'status' => 'failed',
                'message' => 'Data gagal diperbarui.',
            ], 201);
    }

    public function delete_review($request, $response, $args)
    {
        $isAllowed = $this->isAllowed($request, $response, $args);
        if ($isAllowed instanceof \Slim\Http\Response)
            return $isAllowed;

        if(!$isAllowed){
            return $this->notAllowedAction();
        }

        if (!isset($args['id'])) {
            return false;
        }

        $model = \ExtensionsModel\HostingReviewModel::model()->findByPk($args['id']);
        $delete = \ExtensionsModel\HostingReviewModel::model()->delete($model);
        if ($delete) {
            return $response->withJson(
                [
                    'status' => 'success',
                    'message' => 'Data berhasil dihapus.',
                ], 201);
        } else {
            return $response->withJson(
                [
                    'status' => 'failed',
                    'message' => 'Data gagal dihapus.',
                ], 201);
        }
    }
}