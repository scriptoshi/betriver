<?php

namespace App\Enums;

use App\Gateways\Sms\Clickatell;
use App\Gateways\Sms\MessageBird;
use App\Gateways\Sms\SmspohApi;
use App\Gateways\Sms\TextMagic;
use App\Gateways\Sms\TouchSms;
use App\Gateways\Sms\Vonage;

enum SmsDrivers: string
{
    case smspoh = 'smspoh';
    case touchsms = 'touchsms';
    case clickatell = 'clickatell';
    case messagebird = 'messagebird';
    case vonage = 'vonage';
    case textmagic = 'textmagic';

    public function driver()
    {
        return match ($this) {
            static::smspoh => app(SmspohApi::class),
            static::touchsms  => app(TouchSms::class),
            static::clickatell =>  app(Clickatell::class),
            static::messagebird  => app(MessageBird::class),
            static::vonage =>  app(Vonage::class),
            static::textmagic =>  app(TextMagic::class),
        };
    }

    public function info()
    {
        return match ($this) {
            static::smspoh => [
                'label' => 'SMSpoh',
                'img' => 'https://smspoh.com/images/brand.png',
                'value' => $this->value
            ],
            static::touchsms  => [
                'label' => 'Touch SMS',
                'img' => 'https://www.touchsms.com.au/wp-content/uploads/2020/03/touchSMS_Icon-favicon.png',
                'value' => $this->value
            ],
            static::clickatell  => [
                'label' => 'Clickatell',
                'img' => 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iMjYiIHZpZXdCb3g9IjAgMCA0MCAyNiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTE3LjgwODEgMS44OTkxNUMxMS44NTY3IC0xLjI4MDM2IDQuNDc4NTcgMS4wODM4OSAxLjM4MDU4IDcuMjM5MUMtMS43MTc0MSAxMy4zNTM2IDAuNTY1MzIyIDIwLjkzNTUgNi41NTc0OCAyNC4xMTVDOS41MzMxOCAyNS43MDQ3IDEyLjgzNSAyNS45MDg2IDE1LjgxMDcgMjQuOTMwMkMxNS44MTA3IDI0LjkzMDIgMTYuNDIyMSAyNC44NDg3IDE3LjkzMDQgMjMuOTUxOUMxNy45MzA0IDIzLjk1MTkgMTYuNjY2NyAyMy4xNzc0IDE1LjE5OTIgMjAuNzMxN0MxNS4xOTkyIDIwLjczMTcgMTUuMTk5MiAyMC43MzE3IDE1LjE5OTIgMjAuNjkwOUMxMy4wNzk2IDIxLjU0NjkgMTAuNTkzIDIxLjU0NjkgOC40MzI1OCAyMC4zNjQ4QzQuNDM3ODEgMTguMjQ1MSAyLjg4ODgxIDEzLjE5MDUgNC45Njc3MiA5LjExNDJDNy4wNDY2NCA1LjAzNzkgMTEuOTM4MiAzLjQwNzM4IDE1LjkzMyA1LjU2NzgyQzE4LjMzOCA2LjgzMTQ3IDE5Ljg0NjIgOS4xOTU3MyAyMC4yMTMxIDExLjc2MzhMMjQuMzMwMSAxMy45NjVDMjQuNjk3IDkuMTE0MiAyMi4yOTIgNC4zMDQxNyAxNy44MDgxIDEuODk5MTVaIiBmaWxsPSIjMjlDM0VDIi8+CjxwYXRoIGQ9Ik0yMi4yMDk3IDI0LjExNTNDMjguMjAxOCAyNy4yOTQ4IDM1LjUzOTIgMjQuODQ5IDM4LjYzNzIgMTguNjkzOEM0MS43MzUxIDEyLjUzODYgMzkuMzcwOSA0Ljk5NzQxIDMzLjM3ODcgMS44NTg2NkMzMC40MDMgMC4yNjg5MDEgMjcuMTAxMiAwLjEwNTg0OSAyNC4xMjU1IDEuMDg0MTZDMjQuMTI1NSAxLjA4NDE2IDIzLjUxNDEgMS4xNjU2OSAyMi4wMDU5IDIuMDYyNDdDMjIuMDA1OSAyLjA2MjQ3IDIzLjI2OTUgMi44MzY5NyAyNC43MzcgNS4yNDE5OUMyNC43MzcgNS4yNDE5OSAyNC43MzcgNS4yNDE5OSAyNC43MzcgNS4yODI3NUMyNi44NTY3IDQuMzg1OTYgMjkuMzAyNCA0LjQyNjczIDMxLjUwMzYgNS41NjgwOUMzNS40OTg0IDcuNjg3NzcgMzcuMDQ3NCAxMi43MDE2IDM1LjAwOTMgMTYuODE4N0MzMi45MzAzIDIwLjkzNTcgMjguMDM4OCAyMi41MjU1IDI0LjA0NCAyMC40NDY2QzIxLjYzOSAxOS4xODI5IDIwLjEzMDggMTYuODU5NCAxOS43MjMxIDE0LjI5MTRMMTUuNjA2MSAxMi4xMzA5QzE1LjI4IDE2Ljk0MSAxNy43MjU3IDIxLjc1MSAyMi4yMDk3IDI0LjExNTNaIiBmaWxsPSIjOERDNjNGIi8+Cjwvc3ZnPgo=',
                'value' => $this->value
            ],
            static::messagebird  => [
                'label' => 'Message Bird',
                'img' => 'https://framerusercontent.com/images/73WBvABujFcVXVNpVaSs29RDqc.png',
                'value' => $this->value
            ],
            static::vonage  => [
                'label' => 'Vonage',
                'value' => $this->value
            ],
            static::textmagic  => [
                'label' => 'Text Magic',
                'img' => 'https://www.textmagic.com/wp-content/uploads/2023/08/favicon-1.svg',
                'value' => $this->value
            ]
        };
    }
}
