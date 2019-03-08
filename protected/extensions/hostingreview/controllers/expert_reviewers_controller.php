<?php

namespace Extensions\Controllers;

use Components\BaseController as BaseController;

class ExpertReviewersController extends BaseController
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
    }

    public function accessRules()
    {
        return [
            ['allow',
                'actions' => [
                    'view', 'create', 'update', 'delete'
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

        $model = new \ExtensionsModel\HostingExpertModel();
        $datas = $model->getData();

        return $this->_container->module->render($response, 'hostings/expert_reviewer_view.html', [
            'datas' => $datas
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

        $success = false;
        if (isset($_POST['HostingExpert'])){
            $check_model = \ExtensionsModel\HostingExpertModel::model()->findByAttributes(['email' => $_POST['HostingExpert']['email']]);
            if ($check_model instanceof \RedBeanPHP\OODBBean) {
                $message = $_POST['HostingExpert']['email']. ' telah terdaftar di database.';
                $success = false;
            } else {
                $model = new \ExtensionsModel\HostingExpertModel('create');
                $model->name = $_POST['HostingExpert']['name'];
                $model->phone = $_POST['HostingExpert']['phone'];
                $model->email = $_POST['HostingExpert']['email'];
                $model->website = $_POST['HostingExpert']['website'];
                $model->profession = $_POST['HostingExpert']['profession'];
                $model->about = $_POST['HostingExpert']['about'];
                $model->status = $_POST['HostingExpert']['status'];
                $model->created_at = date('Y-m-d H:i:s');
                $save = \ExtensionsModel\HostingExpertModel::model()->save(@$model);
                if ($save) {
                    $uploadfile = null;
                    if (isset($_FILES['HostingExpert'])) {
                        $path_info = pathinfo($_FILES['HostingExpert']['name']['image']);
                        if (in_array($path_info['extension'], ['jpg','JPG','jpeg','JPEG','png','PNG'])) {
                            $upload_folder = 'uploads/images/reviews';
                            $file_name = time().'.'.$path_info['extension'];
                            $uploadfile = $upload_folder . '/' . $file_name;
                            try {
                                $upload = move_uploaded_file($_FILES['HostingExpert']['tmp_name']['image'], $uploadfile);
                                if ($upload) {
                                    if (file_exists($model->image)) {
                                        unlink($model->image);
                                    }
                                }
                            } catch (\Exception $e) {}
                        }
                    }
                    if (!empty($uploadfile)) {
                        $model2 = \ExtensionsModel\HostingExpertModel::model()->findByPk($model->id);
                        $model2->image = $uploadfile;
                        $update = \ExtensionsModel\HostingExpertModel::model()->update($model2);
                    }
                    $message = 'Data berhasil disimpan';
                    $success = true;
                } else {
                    $message = 'Gagal menyimpan data.';
                    $success = false;
                }
            }
        }

        return $this->_container->module->render($response, 'hostings/expert_reviewer_create.html', [
            'message' => ($message) ? $message : null,
            'success' => $success,
            'model' => $_POST['HostingExpert']
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

        $model = \ExtensionsModel\HostingExpertModel::model()->findByPk($args['id']);

        if (isset($_POST['HostingExpert'])){
            $model->name = $_POST['HostingExpert']['name'];
            $model->phone = $_POST['HostingExpert']['phone'];
            $model->email = $_POST['HostingExpert']['email'];
            $model->website = $_POST['HostingExpert']['website'];
            $model->profession = $_POST['HostingExpert']['profession'];
            $model->about = $_POST['HostingExpert']['about'];
            $model->status = $_POST['HostingExpert']['status'];
            $model->updated_at = date('Y-m-d H:i:s');
            $update = \ExtensionsModel\HostingExpertModel::model()->update($model);
            if ($update) {
                $uploadfile = null;
                if (isset($_FILES['HostingExpert'])) {
                    $path_info = pathinfo($_FILES['HostingExpert']['name']['image']);
                    if (in_array($path_info['extension'], ['jpg','JPG','jpeg','JPEG','png','PNG'])) {
                        $upload_folder = 'uploads/images/reviews';
                        $file_name = time().'.'.$path_info['extension'];
                        $uploadfile = $upload_folder . '/' . $file_name;
                        try {
                            $upload = move_uploaded_file($_FILES['HostingExpert']['tmp_name']['image'], $uploadfile);
                            if ($upload) {
                                if (file_exists($model->image)) {
                                    unlink($model->image);
                                }
                            }
                        } catch (\Exception $e) {}
                    }
                }
                if (!empty($uploadfile)) {
                    $model->image = $uploadfile;
                    $update2 = \ExtensionsModel\HostingExpertModel::model()->update($model);
                }
                $message = 'Data berhasil diubah';
                $success = true;
            } else {
                $message = 'Gagal merubah data.';
                $success = false;
            }
        }

        return $this->_container->module->render($response, 'hostings/expert_reviewer_update.html', [
            'model' => $model,
            'message' => $message,
            'success' => $success
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

        $model = \ExtensionsModel\HostingExpertModel::model()->findByPk($args['id']);

        // check has review or not
        $rows = \ExtensionsModel\HostingExpertReviewModel::model()->findAllByAttributes(['expert_id' => $model->id]);
        if (count($rows) > 0) {
            $model->status = \ExtensionsModel\HostingExpertModel::STATUS_DELETED;
            $model->updated_at = date('Y-m-d H:i:s');
            $update = \ExtensionsModel\HostingExpertModel::model()->update($model);
            if ($update) {
                $message = 'Data berhasil dihapus.';
                echo true;
            }
        } else {
            $delete = \ExtensionsModel\HostingExpertModel::model()->delete($model);
            if ($delete) {
                $message = 'Data berhasil dihapus.';
                echo true;
            }
        }
    }
}