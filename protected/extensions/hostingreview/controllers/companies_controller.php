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
        $app->map(['POST'], '/delete-plan/[{id}]', [$this, 'delete_plan']);
    }

    public function accessRules()
    {
        return [
            ['allow',
                'actions' => [
                    'view', 'create', 'update', 'delete',
                    'create-plan', 'delete-plan'
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

        if (isset($_POST['HostingCompany'])){
            $model->title = $_POST['HostingCompany']['title'];
            $model->description = $_POST['HostingCompany']['description'];
            $model->address = $_POST['HostingCompany']['address'];
            $model->phone = $_POST['HostingCompany']['phone'];
            $model->email = $_POST['HostingCompany']['email'];
            $model->website = $_POST['HostingCompany']['website'];
            $model->status = $_POST['HostingCompany']['status'];
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
            'plans' => $plans
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
            $model->description = $_POST['HostingPlan']['description'];
            $model->hosting_company_id = $args['id'];
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
}