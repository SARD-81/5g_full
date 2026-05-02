<?php

namespace Modules\Server\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Server\Service\GUI\GuiRunner;

class GUIController extends Controller
{

    public function run(Request $request)
    {
        $runner = new GuiRunner();
        return $runner->execute();
    }
}
