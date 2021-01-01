<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class DeployController extends Controller
{
    public function deploy(Request $request)
    {
        $githubPayload = $request->getContent();
        $githubHash = $request->header('X-Hub-Signature');
        echo($request->getRequestUri());
        exit;

        dd($githubPayload);
 
        $localToken = config('app.deploy_secret');
        $localHash = 'sha1=' . hash_hmac('sha1', $githubPayload, $localToken, false);
 
        if (hash_equals($githubHash, $localHash)) {
            $root_path = base_path();
	    $process = new Process(['./deploy.sh']);
	    $process->setWorkingDirectory($root_path);
            $process->run(function ($type, $buffer) {
                echo $buffer;
            });
        }
    }
}
