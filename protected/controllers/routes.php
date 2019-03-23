<?php
// Modules Routes
foreach(glob($settings['settings']['basePath'] . '/modules/*/controllers/routes.php') as $mod_routes) {
    require_once $mod_routes;
}

// Extensions routes
foreach(glob($settings['settings']['basePath'] . '/extensions/*/controllers/routes.php') as $ext_routes) {
    require_once $ext_routes;
}

$app->get('/niagahoster', function ($request, $response, $args) {
    return $response->withRedirect( 'https://goo.gl/V3dpJU' );
});

$app->get('/sitemap.xml', function ($request, $response, $args) {
    $tools = new \Components\Tool();

    $this->view->render($response, 'sitemap.xml', [
        'results' => $tools->get_sitemaps()
    ]);

    return $response->withHeader('Content-Type','text/xml');
});

$app->get('/lang/[{name}]', function ($request, $response, $args) use ($settings) {
    if (!empty($args['name'])) {
        $model = \Model\OptionsModel::model()->findByAttributes(['option_name' => 'language']);
        if ($model instanceof \RedBeanPHP\OODBBean) {
            $model->option_value = $args['name'];
            $model->updated_at = date("Y-m-d H:i:s");
            $update = \Model\OptionsModel::model()->update($model);
        } else {
            $model = new \Model\OptionsModel();
            $model->option_name = 'language';
            $model->option_value = $args['name'];
            $model->option_description = 'Language params';
            $model->autoload = 'yes';
            $model->created_at = date("Y-m-d H:i:s");
            $model->updated_at = date("Y-m-d H:i:s");
            $save = \Model\OptionsModel::model()->save($model);
        }

        try {
            $hooks = new \PanelAdmin\Components\AdminHooks($settings);
            $omodel = new \Model\OptionsModel();
            $hooks->onAfterParamsSaved($omodel->getOptions());
        } catch (Exception $e) {
            //var_dump($e->getMessage()); exit;
        }
    }

    return $response->withRedirect( '/' );
});

$app->get('/[{name}]', function ($request, $response, $args) {
    
	if (empty($args['name']))
		$args['name'] = 'index';

    $settings = $this->get('settings');
    if (!file_exists($settings['theme']['path'].'/'.$settings['theme']['name'].'/views/'.$args['name'].'.phtml')) {
        return $this->view->render($response, '404.phtml');
    }

    $exts = json_decode( $settings['params']['extensions'], true );
    $mpost = null;
    if (in_array( 'blog', $exts )) {
        $mpost = new \ExtensionsModel\PostModel();
    }

    if (isset($_GET['e']) && $_GET['e'] > 0) { // editing procedure
        $view_path = $settings['theme']['path'] . '/' . $settings['theme']['name'] . '/views';
        if (file_exists($view_path.'/'.$args['name'] . '.phtml')) {
            if (file_exists($view_path.'/staging/'.$args['name'] . '.ehtml')) {
                unlink($view_path.'/staging/'.$args['name'] . '.ehtml');
            }
            $cp = copy($view_path.'/'.$args['name'] . '.phtml', $view_path.'/staging/'.$args['name'] . '.ehtml');
            if ($cp) {
                $content = file_get_contents($view_path.'/staging/'.$args['name'] . '.ehtml');
                $parsed_content = str_replace(array("{{", "}}"), array("[[", "]]"), $content);

                $update = file_put_contents($view_path.'/staging/'.$args['name'] . '.ehtml', $parsed_content);
            }

            return $this->view->render($response, 'staging/' . $args['name'] . '.ehtml', [
                'name' => $args['name'],
                'mpost' => $mpost,
                'request' => $_GET
            ]);
        }
    }

    return $this->view->render($response, $args['name'] . '.phtml', [
        'name' => $args['name'],
        'request' => $_GET,
        'mpost' => $mpost
    ]);
});

$app->post('/kontak-kami', function ($request, $response, $args) {
    $message = 'Pesan Anda gagal dikirimkan.';
    $settings = $this->get('settings');
    if (isset($_POST['Contact'])){
        //send mail to admin
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            //Server settings
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = $settings['params']['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $settings['params']['admin_email'];
            $mail->Password = $settings['params']['smtp_secret'];
            $mail->SMTPSecure = $settings['params']['smtp_secure'];
            $mail->Port = $settings['params']['smtp_port'];

            //Recipients
            $mail->setFrom( $settings['params']['admin_email'], 'Admin slightSite' );
            $mail->addAddress( $settings['params']['admin_email'], 'Farid Efendi' );
            $mail->addReplyTo( $_POST['Contact']['email'], $_POST['Contact']['name'] );

            //Content
            $mail->isHTML(true);
            $mail->Subject = '[BestWH] Kontak Kami';
            $mail->Body = "Halo Admin, 
	        <br/><br/>
            Ada pesan baru dari pengunjung dengan data berikut:
            <br/><br/>
            <b>Judul pesan</b> : ".$_POST['Contact']['subject']." <br/>
            <b>Nama pengunjung</b> : ".$_POST['Contact']['name']." <br/> 
            <b>Alamat Email</b> : ".$_POST['Contact']['email']." <br/>
            <br/>
            <b>Isi Pesan</b> :<br/> ".$_POST['Contact']['message']."";

            $mail->send();
        } catch (Exception $e) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
            exit;
        }

        $message = 'Pesan Anda berhasil dikirim. Kami akan segera merespon pesan Anda.';
    }

    echo $message; exit;
});

$app->post('/tracking', function ($request, $response, $args) {
    if (isset($_POST['s'])){
        $model = new \Model\VisitorModel('create');
        $model->client_id = 0;
        if(!empty($_POST['s'])){
            $model->session_id = $model->getCookie('_ma',false);
            if (!empty($model->cookie)){
                $model->date_expired = $model->cookie;
            } else {
                //Yii::app()->request->cookies->remove('_ma');
                $model->date_expired = date("Y-m-d H:i:s",time()+1800);
            }
        }
        $model->ip_address = $_SERVER['REMOTE_ADDR'];
        $model->page_title = $_POST['t'];
        $model->url = $_POST['u'];
        $model->url_referrer = $_POST['r'];
        $model->created_at = date('Y-m-d H:i:s');
        $model->platform = $_POST['p'];
        $model->user_agent = $_POST['b'];

        require_once $this->settings['basePath'] . '/components/mobile_detect.php';
        $mobile_detect = new \Components\MobileDetect();
        $model->mobile = ($mobile_detect->isMobile())? 1 : 0;

        $create = \Model\VisitorModel::model()->save(@$model);

        if ($create > 0) {
            if ($model->session_id == 'false' || empty($model->session_id)) {
                $model2 = \Model\VisitorModel::model()->findByPk($model->id);
                $model2->session_id = md5($create);
                $update = \Model\VisitorModel::model()->update(@$model2);
                //$cookie_time = (3600 * 0.5); // 30 minute
                //setcookie("ma_session", $model->session_id, time() + $cookie_time, '/');
            }
            //set notaktif
            $model->deactivate($model->session_id);
            // update the current record
            if (!is_object($model2))
                $model2 = \Model\VisitorModel::model()->findByPk($model->id);
            $model2->active = 1;
            $update2 = \Model\VisitorModel::model()->update($model2);

            echo $model2->session_id;
        }else{
            echo 'failed';
        }

        exit;
    }
});

$app->post('/tulis-ulasan', function ($request, $response, $args) {
    $params = $request->getParams();

    $success = 0; $message = 'Failed to save';
    if (isset($params['Review'])) {
        $errors = [];
        if (empty($params['Review']['title'])) {
            $errors['title'] = 'Title is required';
        }

        if (empty($params['Review']['company_id'])) {
            $errors['company_id'] = 'Company is required';
        }

        if (empty($params['Review']['product_id'])) {
            $errors['product_id'] = 'Product is required';
        }

        if (count($errors) > 0) {
            var_dump($errors); exit;
        } else {
            $rmodel = \ExtensionsModel\HostingReviewerModel::model()->findByAttributes(['email' => $params['Review']['email']]);
            $reviewer_id = 0;
            if ($rmodel instanceof \RedBeanPHP\OODBBean) {
                $reviewer_id = $rmodel->id;
            } else {
                $model = new \ExtensionsModel\HostingReviewerModel();
                $model->name = $params['Review']['name'];
                $model->email = $params['Review']['email'];
                $model->phone = $params['Review']['phone'];
                if (isset($params['Review']['website'])) {
                    $model->website = $params['Review']['website'];
                }
                $model->status = \ExtensionsModel\HostingReviewerModel::STATUS_INACTIVE;
                $model->created_at = date("Y-m-d H:i:s");
                $model->updated_at = date("Y-m-d H:i:s");
                $save = \ExtensionsModel\HostingReviewerModel::model()->save(@$model);
                if ($save) {
                    $reviewer_id = $model->id;
                }
            }

            $model2 = new \ExtensionsModel\HostingReviewModel();
            $model2->hosting_company_id = $params['Review']['company_id'];
            $model2->reviewer_id = $reviewer_id;
            $model2->product_id = $params['Review']['product_id'];
            $model2->title = $params['Review']['title'];
            $model2->content = $params['Review']['content'];
            $a = array_filter($params['Review']['rate']);
            $average = array_sum($a)/count($a);
            $model2->rate = $average;
            $model2->status = \ExtensionsModel\HostingReviewModel::STATUS_PENDING;
            $model2->created_at = date("Y-m-d H:i:s");
            $model2->updated_at = date("Y-m-d H:i:s");
            $save2 = \ExtensionsModel\HostingReviewModel::model()->save(@$model2);
            if ($save2) {
                foreach ($params['Review']['rate'] as $category_id => $rate) {
                    $model3 = new \ExtensionsModel\HostingRateModel();
                    $model3->review_id = $model2->id;
                    $model3->category_id = $category_id;
                    $model3->value = $rate;
                    $model3->created_at = date("Y-m-d H:i:s");
                    $save3 = \ExtensionsModel\HostingRateModel::model()->save(@$model3);
                }

                $success = 1;
                $message = 'Your review is successfully saved. We will check soon.';
            }
        }
    }

    return $this->view->render($response, 'tulis-ulasan.phtml', [
        'success' => $success,
        'message' => $message
    ]);
});
