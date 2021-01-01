<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class DeployController extends Controller
{
    public function deploy(Request $request)
    {
        set_time_limit(180);

        $githubPayload = $request->getContent();
        $githubHash = $request->header('X-Hub-Signature');

        // Only deploy on pushes to master
        $requestData = json_decode($request->payload);
        if ($requestData->ref !== 'refs/heads/master') {
            echo "Only deploying on pushes to master";
            exit;
        }

        $localToken = config('app.deploy_secret');
        $localHash = 'sha1=' . hash_hmac('sha1', $githubPayload, $localToken, false);

        if (hash_equals($githubHash, $localHash)) {
            $root_path = base_path();
            $process = new Process(['./deploy.sh']);
            $process->setWorkingDirectory($root_path);
            $process->run(function ($type, $buffer) {
                echo $buffer;
            });

            echo "Deployed script started";
            exit;
        }

        echo "Failure hash mismatch";
        exit;
    }
}
