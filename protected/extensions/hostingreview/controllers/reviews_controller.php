<?php

namespace Extensions\Controllers;

use Components\BaseController as BaseController;

class ReviewsController extends BaseController
{
    public function __construct($app, $user)
    {
        parent::__construct($app, $user);
    }

    public function register($app)
    {
        $app->map(['GET'], '/view', [$this, 'view']);
        $app->map(['GET', 'POST'], '/update/[{id}]', [$this, 'update']);
        $app->map(['POST'], '/delete/[{id}]', [$this, 'delete']);
    }

    public function accessRules()
    {
        return [
            ['allow',
                'actions' => [
                    'view', 'update', 'delete'
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

        $model = new \ExtensionsModel\HostingReviewModel();
        $datas = $model->getData($request->getParams());

        return $this->_container->module->render($response, 'hostings/reviews_view.html', [
            'datas' => $datas,
            'params' => $request->getParams()
        ]);
    }

    public function update($request, $response, $args)
    {
        $isAllowed = $this->isAllowed($request, $response);
        if ($isAllowed instanceof \Slim\Http\Response)
            return $isAllowed;

        if (!$isAllowed) {
            return $this->notAllowedAction();
        }

        if (empty($args['id']))
            return false;

        $model = \ExtensionsModel\HostingReviewModel::model()->findByPk($args['id']);

        $params = $request->getParams();
        if (isset($_POST['HostingReview'])) {
            // avoid double execution
            $current_time = time();
            if(isset($_SESSION['HostingReview']) && !empty($_SESSION['HostingReview'])) {
                $selisih = $current_time - $_SESSION['HostingReview'];
                if ($selisih <= 10) {
                    return $response->withJson(
                        [
                            'status' => 'success',
                            'message' => 'Data berhasil disimpan.',
                        ], 201);
                } else {
                    $_SESSION['HostingReview'] = $current_time;
                }
            } else {
                $_SESSION['HostingReview'] = $current_time;
            }

            $model->reviewer_id = $_POST['HostingReview']['reviewer_id'];
            if (!empty($_POST['HostingReview']['rate'])) {
                $a = array_filter($_POST['HostingReview']['rate']);
                $average = array_sum($a)/count($a);
                $model->rate = $average;
            }
            if (isset($_POST['HostingReview']['hosting_company_id'])) {
                $model->hosting_company_id = $_POST['HostingReview']['hosting_company_id'];
            }
            if (isset($_POST['HostingReview']['product_id'])) {
                $model->product_id = $_POST['HostingReview']['product_id'];
            }
            if (isset($_POST['HostingReview']['content'])) {
                $model->content = $_POST['HostingReview']['content'];
            }
            if (isset($_POST['HostingReview']['status'])) {
                $model->status = $_POST['HostingReview']['status'];
            }
            $model->updated_at = date("Y-m-d H:i:s");
            try {
                $update = \ExtensionsModel\HostingReviewModel::model()->update($model);
            } catch (\Exception $e) {
                var_dump($e->getMessage()); exit;
            }

            if ($update) {
                if (!empty($_POST['HostingReview']['rate']) && is_array($_POST['HostingReview']['rate'])) {
                    $hr_model = new \ExtensionsModel\HostingRateModel();
                    $rdata = $hr_model->getRateByReview(['review_id' => $model->id, 'reviewer_id' => $model->reviewer_id]);
                    foreach ($_POST['HostingReview']['rate'] as $category_id => $rate_val) {
                        $rate_new_record = true;
                        $model4 = new \ExtensionsModel\HostingRateModel();
                        if (!empty($rdata[$category_id])) {
                            $model5 = \ExtensionsModel\HostingRateModel::model()->findByPk($rdata[$category_id]['id']);
                            if ($model5 instanceof \RedBeanPHP\OODBBean) {
                                $rate_new_record = false;
                                $model4 = $model5;
                            }
                        }
                        $model4->review_id = $model->id;
                        $model4->category_id = $category_id;
                        $model4->value = $rate_val;
                        $model4->created_at = date("Y-m-d H:i:s");
                        if ($rate_new_record) {
                            $save3 = \ExtensionsModel\HostingRateModel::model()->save($model4);
                        } else {
                            $update3 = \ExtensionsModel\HostingRateModel::model()->update($model4);
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
        }

        return $this->_container->module->render($response, 'hostings/reviews_update.html', [
            'model' => $model,
        ]);
    }

    public function delete($request, $response, $args)
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

        $model = \ExtensionsModel\HostingReviewModel::model()->findByPk($args['id']);
        $delete = \ExtensionsModel\HostingReviewModel::model()->delete($model);
        if ($delete) {
            $delete2 = \ExtensionsModel\HostingRateModel::model()->deleteAllByAttributes(['review_id' => $args['id']]);
            $message = 'Data berhasil dihapus.';
            echo true;
        }
    }
}