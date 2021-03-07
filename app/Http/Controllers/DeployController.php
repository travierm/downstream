<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class DeployController extends Controller
{
    public function deploy(Request $request)
    {
        ini_set('max_execution_time', 180);

        $localToken = config('app.deploy_secret');
        $senderToken = $request->header('X-Hub-Signature');

        if ($localToken === $senderToken) {
            $root_path = base_path();
            $process = new Process(['./deploy.sh']);
            $process->setWorkingDirectory($root_path);
            $process->run(function ($type, $buffer) {
                echo $buffer;
            });
        }else{
            echo "Failure bad token";
            exit;
        }
    }
}
