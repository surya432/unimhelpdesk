<?php

namespace App\Http\Controllers;

use Symfony\Component\Process\Process;

use Illuminate\Http\Request;

class DeployController extends Controller
{
    //
    public function deploy(Request $request)
    {

        $githubPayload = $request->getContent();
        $githubHash = $request->header('X-Hub-Signature');
        $localToken = config('app.deploy_secret');
        $localHash = 'sha1=' . hash_hmac(
            'sha1',
            $githubPayload,
            $localToken,
            false
        );
return $localHash;
        if (hash_equals($githubHash, $localHash)) {
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
}
