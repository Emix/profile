<?php

namespace micro\controllers;

use app\components\BitrixRest;
use app\components\RequestCleaner;
use micro\components\ComagicUpdater;
use micro\models\Whitelist;
use micro\models\WhitelistSearch;
use Yii;
use yii\httpclient\Exception;
use yii\web\Controller;



class SiteController extends Controller
{
    public function actionIndex()
    {
        //$r = RequestCleaner::getToFromDB('1558084975.1570738');

        //var_dump($r);die;
        $body = Yii::$app->getRequest()->getRawBody();
        $phone = RequestCleaner::parseRaw($body);
        Yii::debug('Ищем trunk в списке белых номеров '.$phone);
        $hasInBase = WhitelistSearch::isExistPhone($phone);

        Yii::debug('результат '.$hasInBase === false ? 'Провальный':'Положительный');


        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $hasInBase;
    }

    public function actionLead()
    {
        $body = Yii::$app->getRequest()->getRawBody();
        $userId = RequestCleaner::getUserIdFromRow($body);


        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $userId;
    }

    public function actionResponsible(): bool{
        $phone = Yii::$app->getRequest()->post('phone');
        $phoneRes = Yii::$app->getRequest()->post('phone_answering');

        //Чел недождался и оборвался
        if(empty($phoneRes)){
            $this->actionIvr();
        }else{
            $userId = RequestCleaner::setResponsibleByPhone($phone, $phoneRes);
        }



        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return true;
    }

    public function actionIvr()
    {
        $body = Yii::$app->getRequest()->post('phone');
        $result = $body;

        $result = BitrixRest::createLead($result);

        Yii::debug($result);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $result;
    }

    public function actionCreate($phone = '***'){

        if(!WhitelistSearch::isExistPhone($phone)){
            $model = new Whitelist();
            $model->phone = $phone;
            return $model->save();
        }

        return false;
    }

    public function actionList(){
        $model = Whitelist::find()->all();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $model;
    }

    public function actionDeleteAll(){
        $model = Whitelist::deleteAll();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $model;
    }






}