<?php
/**
 * Created by PhpStorm.
 * User: zimovid
 * Date: 16.09.15
 * Time: 18:14
 */

namespace app\commands;

use Faker\Factory;
use udokmeci\yii2beanstalk\BeanstalkController;
use yii\helpers\Console;
use Yii;
use yii\helpers\FileHelper;

class WorkerController extends BeanstalkController
{

    // Those are the default values you can override

    const DELAY_PIRORITY = "1000"; //Default priority
    const DELAY_TIME = 5; //Default delay time

    // Used for Decaying. When DELAY_MAX reached job is deleted or delayed with
    const DELAY_MAX = 3;

    public function listenTubes(){
        return ["tube",'tube2'];
    }

    /**
     *
     * @param  Job $job
     * @return string  self::BURY
     *                 self::RELEASE
     *                 self::DELAY
     *                 self::DELETE
     *                 self::NO_ACTION
     *                 self::DECAY
     *
     */
    public function actionTube($job){
        $sentData = $job->getData();
        try {
            if($sentData){
                $jobID = $job->getId();
                fwrite(STDOUT, Console::ansiFormat("Job id $jobID - Отправлено в трубу - время  $sentData"
                    ."\n",
                    [Console::FG_GREEN]));
                return self::DELETE;
            }

            else{
                fwrite(STDOUT, Console::ansiFormat("- Ошибка"."\n", [Console::FG_RED]));
                return self::DECAY;
            }

        } catch (\Exception $e) {
            fwrite(STDERR, Console::ansiFormat($e."\n", [Console::FG_RED]));
            return self::BURY;
        }
    }

    public function actionTube2($job){
        $sentData = $job->getData();
        try {
            if($sentData){
                $faker = Factory::create();
                $content = $faker->text;
                $name = uniqid();
                $fp = fopen(Yii::getAlias('@runtime') . "/$name.txt","wb");
                fwrite($fp,$content);
                fclose($fp);
                fwrite(STDOUT, Console::ansiFormat("- Создано и у спешно записан файл"."\n", [Console::FG_GREEN]));
                return self::DELETE;
            }
            else{
                fwrite(STDOUT, Console::ansiFormat("- Ошибка"."\n", [Console::FG_GREEN]));
                return self::DECAY;
            }


        } catch (\Exception $e) {
            fwrite(STDERR, Console::ansiFormat($e."\n", [Console::FG_RED]));
            return self::BURY;
        }
    }
}