<?php

namespace Extensions\Controllers;

use Components\BaseController as BaseController;

class ReviewersController extends BaseController
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

        $model = new \ExtensionsModel\HostingReviewerModel();
        $datas = $model->getData();

        return $this->_container->module->render($response, 'hostings/reviewer_view.html', [
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

        if (isset($_POST['HostingReviewer'])){
            $model = new \ExtensionsModel\HostingReviewerModel('create');
            $model->name = $_POST['HostingReviewer']['name'];
            $model->phone = $_POST['HostingReviewer']['phone'];
            $model->email = $_POST['HostingReviewer']['email'];
            $model->website = $_POST['HostingReviewer']['website'];
            $model->status = $_POST['HostingReviewer']['status'];
            $model->created_at = date('Y-m-d H:i:s');
            $save = \ExtensionsModel\HostingReviewerModel::model()->save(@$model);
            if ($save) {
                $uploadfile = null;
                if (isset($_FILES['HostingReviewer'])) {
                    $path_info = pathinfo($_FILES['HostingReviewer']['name']['image']);
                    if (in_array($path_info['extension'], ['jpg','JPG','jpeg','JPEG','png','PNG'])) {
                        $upload_folder = 'uploads/images/reviews';
                        $file_name = time().'.'.$path_info['extension'];
                        $uploadfile = $upload_folder . '/' . $file_name;
                        try {
                            $upload = move_uploaded_file($_FILES['HostingReviewer']['tmp_name']['image'], $uploadfile);
                            if ($upload) {
                                if (file_exists($model->image)) {
                                    unlink($model->image);
                                }
                            }
                        } catch (\Exception $e) {}
                    }
                }
                if (!empty($uploadfile)) {
                    $model2 = \ExtensionsModel\HostingReviewerModel::model()->findByPk($model->id);
                    $model2->image = $uploadfile;
                    $update = \ExtensionsModel\HostingReviewerModel::model()->update($model2);
                }
                $message = 'Data berhasil disimpan';
                $success = true;
            } else {
                $message = 'Gagal menyimpan data.';
                $success = false;
            }
        }

        return $this->_container->module->render($response, 'hostings/reviewer_create.html', [
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

        $model = \ExtensionsModel\HostingReviewerModel::model()->findByPk($args['id']);

        if (isset($_POST['HostingReviewer'])){
            $model->name = $_POST['HostingReviewer']['name'];
            $model->phone = $_POST['HostingReviewer']['phone'];
            $model->email = $_POST['HostingReviewer']['email'];
            $model->website = $_POST['HostingReviewer']['website'];
            $model->status = $_POST['HostingReviewer']['status'];
            $model->updated_at = date('Y-m-d H:i:s');
            $update = \ExtensionsModel\HostingReviewerModel::model()->update($model);
            if ($update) {
                $uploadfile = null;
                if (isset($_FILES['HostingReviewer'])) {
                    $path_info = pathinfo($_FILES['HostingReviewer']['name']['image']);
                    if (in_array($path_info['extension'], ['jpg','JPG','jpeg','JPEG','png','PNG'])) {
                        $upload_folder = 'uploads/images/reviews';
                        $file_name = time().'.'.$path_info['extension'];
                        $uploadfile = $upload_folder . '/' . $file_name;
                        try {
                            $upload = move_uploaded_file($_FILES['HostingReviewer']['tmp_name']['image'], $uploadfile);
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
                    $update2 = \ExtensionsModel\HostingReviewerModel::model()->update($model);
                }
                $message = 'Data berhasil diubah';
                $success = true;
            } else {
                $message = 'Gagal merubah data.';
                $success = false;
            }
        }

        return $this->_container->module->render($response, 'hostings/reviewer_update.html', [
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

        $model = \ExtensionsModel\HostingReviewerModel::model()->findByPk($args['id']);
        $model->status = \ExtensionsModel\HostingReviewerModel::STATUS_DELETED;
        $model->updated_at = date('Y-m-d H:i:s');
        $update = \ExtensionsModel\HostingReviewerModel::model()->update($model);
        if ($update) {
            $message = 'Data berhasil dihapus.';
            echo true;
        }
    }
}