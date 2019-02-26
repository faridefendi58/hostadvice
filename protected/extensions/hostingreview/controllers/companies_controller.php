<?php

namespace Extensions\Controllers;

use Components\BaseController as BaseController;
use PHPMailer\PHPMailer\Exception;

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
        $app->map(['POST'], '/check-reviewer', [$this, 'check_reviewer']);
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
            if (isset($_POST['HostingCompany']['rangking'])) {
                $model->rangking = $_POST['HostingCompany']['rangking'];
            }
            $model->created_at = date('Y-m-d H:i:s');
            $model->created_by = $this->_user->id;
            $save = \ExtensionsModel\HostingCompanyModel::model()->save(@$model);
            if ($save) {
                $uploadfile = null;
                if (isset($_FILES['HostingCompany'])) {
                    $path_info = pathinfo($_FILES['HostingCompany']['name']['logo']);
                    if (in_array($path_info['extension'], ['jpg','JPG','jpeg','JPEG','png','PNG'])) {
                        $upload_folder = 'uploads/images/companies';
                        $file_name = time().'.'.$path_info['extension'];
                        $uploadfile = $upload_folder . '/' . $file_name;
                        try {
                            $upload = move_uploaded_file($_FILES['HostingCompany']['tmp_name']['logo'], $uploadfile);
                            if ($upload) {
                                $umodel = \ExtensionsModel\HostingCompanyModel::model()->findByPk($model->id);
                                $configs['logo'] = $uploadfile;
                                $umodel->configs = json_encode($configs);
                                $umodel->updated_by = $this->_user->id;
                                $update = \ExtensionsModel\HostingCompanyModel::model()->update($umodel);
                            }
                        } catch (\Exception $e) {}
                    }
                }

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
            if (isset($_POST['HostingCompany']['rangking'])) {
                $model->rangking = $_POST['HostingCompany']['rangking'];
            }
            $model->configs = json_encode($configs);
            $model->updated_at = date('Y-m-d H:i:s');
            $model->updated_by = $this->_user->id;
            $update = \ExtensionsModel\HostingCompanyModel::model()->update($model);
            if ($update) {
                $detail = $hcmodel->getDetail($model->id);
                $uploadfile = null;
                if (isset($_FILES['HostingCompany'])) {
                    $path_info = pathinfo($_FILES['HostingCompany']['name']['logo']);
                    if (in_array($path_info['extension'], ['jpg','JPG','jpeg','JPEG','png','PNG'])) {
                        $upload_folder = 'uploads/images/companies';
                        $file_name = time().'.'.$path_info['extension'];
                        $uploadfile = $upload_folder . '/' . $file_name;
                        try {
                            $upload = move_uploaded_file($_FILES['HostingCompany']['tmp_name']['logo'], $uploadfile);
                            if ($upload) {
                                $umodel = \ExtensionsModel\HostingCompanyModel::model()->findByPk($model->id);
                                $configs['logo'] = $uploadfile;
                                $umodel->configs = json_encode($configs);
                                $umodel->updated_by = $this->_user->id;
                                $update2 = \ExtensionsModel\HostingCompanyModel::model()->update($umodel);
                            }
                        } catch (\Exception $e) {}
                    }
                }

                if (isset($_POST['HostingCompany']['server_location'])) {
                    $srv_model1 = new \ExtensionsModel\HostingServerLocationModel();
                    // delete first
                    try {
                        if (count($_POST['HostingCompany']['server_location'])>0) {
                            $srv_model1->deleteNotIn(['hosting_company_id' => $model->id, 'country_ids' => $_POST['HostingCompany']['server_location']]);
                        } else {
                            $del = \ExtensionsModel\HostingServerLocationModel::model()->deleteAllByAttributes(['hosting_company_id' => $model->id]);
                        }
                    } catch (Exception $e) {}

                    $ctrs = [];
                    foreach ($_POST['HostingCompany']['server_location'] as $il => $country_id) {
                        $countr = \Model\CountryModel::model()->findByPk($country_id);
                        $cek_data = \ExtensionsModel\HostingServerLocationModel::model()->findByAttributes(['hosting_company_id' => $model->id, 'country_id' => $country_id]);
                        if (!$cek_data instanceof \RedBeanPHP\OODBBean) {
                            $srv_model = new \ExtensionsModel\HostingServerLocationModel();
                            $srv_model->hosting_company_id = $model->id;
                            $srv_model->country_id = $country_id;
                            $srv_model->created_at = date('Y-m-d H:i:s');
                            $save = \ExtensionsModel\HostingServerLocationModel::model()->save($srv_model);
                            if ($save) {
                                $ctrs[$country_id] = ['code' => $countr->code, 'title' => $countr->title];
                            }
                        } else {
                            $ctrs[$country_id] = ['code' => $countr->code, 'title' => $countr->title];
                        }
                    }

                    $umodel = \ExtensionsModel\HostingCompanyModel::model()->findByPk($model->id);
                    $c_configs = json_decode($umodel->configs, true);
                    $c_configs['server_location'] = $ctrs;
                    $umodel->configs = json_encode($c_configs);
                    $umodel->updated_by = $this->_user->id;
                    $update3 = \ExtensionsModel\HostingCompanyModel::model()->update($umodel);
                }

                if (isset($_POST['HostingCompany']['product_category_id'])) {
                    $cat_model = new \ExtensionsModel\HostingServerLocationModel();

                    $cats = [];
                    foreach ($_POST['HostingCompany']['product_category_id'] as $ict => $category_id) {
                        $ct_model = \ExtensionsModel\HostingProductCategoryModel::model()->findByPk($category_id);
                        $cats[$category_id] = ['id' => $ct_model->id, 'title' => $ct_model->title];
                    }

                    $umodel = \ExtensionsModel\HostingCompanyModel::model()->findByPk($model->id);
                    $c_configs = json_decode($umodel->configs, true);
                    $c_configs['product_category_id'] = $cats;
                    $umodel->configs = json_encode($c_configs);
                    $umodel->updated_by = $this->_user->id;
                    $update4 = \ExtensionsModel\HostingCompanyModel::model()->update($umodel);
                }

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
            if (!empty($_POST['HostingReview']['rate'])) {
                $a = array_filter($_POST['HostingReview']['rate']);
                $average = array_sum($a)/count($a);
                $model->rate = $average;
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
                $reviewer_id = 0;
                if ($new_reviewer) {
                    $check_data = \ExtensionsModel\HostingReviewerModel::model()->findByAttributes(['email' => $_POST['HostingReview']['reviewer_email']]);
                    if ($check_data instanceof \RedBeanPHP\OODBBean) {
                        $model3 = \ExtensionsModel\HostingReviewModel::model()->findByPk($model->id);
                        $model3->reviewer_id = $check_data->id;
                        $model3->updated_at = date("Y-m-d H:i:s");
                        $update = \ExtensionsModel\HostingReviewModel::model()->update($model3);

                        $reviewer_id = $check_data->id;
                    } else {
                        // save image if any
                        $uploadfile = null;
                        if (isset($_FILES['HostingReview'])) {
                            $path_info = pathinfo($_FILES['HostingReview']['name']['reviewer_image']);
                            if (in_array($path_info['extension'], ['jpg','JPG','jpeg','JPEG','png','PNG'])) {
                                //echo json_encode(['status'=>'failed','message'=>'Allowed file type are jpg, png']); exit;
                                $upload_folder = 'uploads/images/reviews';
                                $file_name = time().'.'.$path_info['extension'];
                                $uploadfile = $upload_folder . '/' . $file_name;
                                try {
                                    $upload = move_uploaded_file($_FILES['HostingReview']['tmp_name']['reviewer_image'], $uploadfile);
                                } catch (\Exception $e) {}
                            }
                        }

                        $model2 = new \ExtensionsModel\HostingReviewerModel();
                        $model2->name = $_POST['HostingReview']['reviewer_name'];
                        $model2->email = $_POST['HostingReview']['reviewer_email'];
                        if (!empty($uploadfile)) {
                            $model2->image = $uploadfile;
                        }
                        $model2->created_at = date("Y-m-d H:i:s");
                        $model2->updated_at = date("Y-m-d H:i:s");
                        try {
                            $save2 = \ExtensionsModel\HostingReviewerModel::model()->save(@$model2);
                            if ($save2) {
                                $model3 = \ExtensionsModel\HostingReviewModel::model()->findByPk($model->id);
                                $model3->reviewer_id = $model2->id;
                                $model3->updated_at = date("Y-m-d H:i:s");
                                $update = \ExtensionsModel\HostingReviewModel::model()->update($model3);

                                $reviewer_id = $model2->id;
                            }
                        } catch (\Exception $e) {
                            var_dump($e->getMessage()); exit;
                        }
                    }
                }

                // save the rate
                if (!empty($_POST['HostingReview']['rate']) && is_array($_POST['HostingReview']['rate'])) {
                    foreach ($_POST['HostingReview']['rate'] as $category_id => $rate_val) {
                        $model4 = new \ExtensionsModel\HostingRateModel();
                        $model4->review_id = $model->id;
                        $model4->category_id = $category_id;
                        $model4->value = $rate_val;
                        $model4->created_at = date("Y-m-d H:i:s");
                        $save3 = \ExtensionsModel\HostingRateModel::model()->save($model4);
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

    public function check_reviewer($request, $response, $args)
    {
        $isAllowed = $this->isAllowed($request, $response, $args);
        if ($isAllowed instanceof \Slim\Http\Response)
            return $isAllowed;

        if(!$isAllowed){
            return $this->notAllowedAction();
        }

        $params = $request->getParams();
        if (!isset($params['email'])) {
            return false;
        }

        $model = \ExtensionsModel\HostingReviewerModel::model()->findByAttributes(['email' => $params['email']]);
        if ($model instanceof \RedBeanPHP\OODBBean) {
            return $response->withJson(
                [
                    'status' => 'success',
                    'message' => $params['email'].' sudah ada di database.',
                ], 201);
        } else {
            return $response->withJson(
                [
                    'status' => 'failed',
                    'message' => 'Data tidak ditemukan.',
                ], 201);
        }
    }
}