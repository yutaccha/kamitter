<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\WebProcessor;

class CustomizeFormatter
{

    /**
     * ログのフォーマット
     * @var string
     */
    private $logFormat = '[%datetime% %level_name% %extra.class%::%extra.function%(%extra.line%)] %message% %context%' . PHP_EOL;

    /**
     * 日付のフォーマット
     * @var string
     */
    private $dateFormat = 'Y/m/d H:i:s.v';

    /**
     * 渡されたロガーインスタンスのカスタマイズ
     *
     * @param \Illuminate\Log\Logger  $logger
     * @return void
     */
    public function __invoke($monolog)
    {
        // フォーマットを指定
        $formatter = new LineFormatter($this->logFormat, $this->dateFormat, true, true);

        // extraフィールドの追加
        $ip = new IntrospectionProcessor(Logger::DEBUG, ['Illuminate\\']);

//        // ip等の情報追加
//        $wp = new WebProcessor();

        foreach ($monolog->getHandlers() as $handler) {
            $handler->setFormatter($formatter);
            $handler->pushProcessor($ip);
//            $handler->pushProcessor($wp);
        }
    }
}