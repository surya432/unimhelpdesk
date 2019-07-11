<?php

namespace App\Http\Controllers;

use Symfony\Component\Process\Process;

use Illuminate\Http\Request;

class DeployController extends Controller
{
    //
    public function deploy(Request $request)
    {


        $root_path = base_path();
        $process =
            new Process('cd ' . $root_path . '; ./deploy.sh');
        $process->run(
            function ($type, $buffer) {

                echo $buffer;
            }
        );
    }
}
