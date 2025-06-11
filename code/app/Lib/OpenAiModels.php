<?php
// File: app/Lib/ModelEnum.php

namespace App\Lib;

enum OpenAiModels: string
{
    case GPT_3_5_TURBO = 'gpt-3.5-turbo';
    case GPT_4_TURBO = 'gpt-4-turbo';
    case GPT_4O = 'gpt-4o';
    case GPT_4O_MINI = 'gpt-4o-mini';
    case GPT_41 = 'gpt-4.1';
    case GPT_41_MINI = 'gpt-4.1-mini';
    case GPT_41_NANO = 'gpt-4.1-nano';
}
