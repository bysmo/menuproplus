<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Helper\Reply;
use App\Models\GlobalSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Zip;

class GlobalSettingController extends Controller
{

    public function __construct()
    {
        // App-update / server-environment info and file deletion are
        // Super-Admin-only, not merely for any global-scope account.
        abort_if(!user()->hasRole('Super Admin'), 403);
    }

    public function index()
    {

        try {
            $results = DB::select('select version()');
            $mysql_version = $results[0]->{'version()'};
            $databaseType = 'MySQL Version';

            if (str_contains($mysql_version, 'Maria')) {
                $databaseType = 'Maria Version';
            }
        } catch (\Exception $e) {
            $mysql_version = null;
            $databaseType = 'MySQL Version';
        }


        $serverOs = 'Unknown';


        try {
            if (function_exists('php_uname')) {
                $serverOs = php_uname('s') . ' ' . php_uname('r') . ' ' . php_uname('m');
            } else {
                $serverOs = 'Unavailable (php_uname disabled)';
            }
        } catch (\Exception $e) {
            $serverOs = 'Unknown';
        }


        $reviewed = file_exists(storage_path('reviewed'));

        return view('app_update.index', compact('mysql_version', 'databaseType', 'reviewed', 'serverOs'));
    }

    public function store(Request $request)
    {


        config(['filesystems.default' => 'storage']);
        $path = storage_path('app') . '/Modules/' . $request->file->getClientOriginalName();

        if (file_exists($path)) {
            File::delete($path);
        }

        $request->file->storeAs('/', $request->file->getClientOriginalName());
    }

    public function deleteFile(Request $request)
    {
        // Only allow deleting a plain filename inside the module-upload
        // staging directory — never an absolute or traversal-based path.
        $baseDir = storage_path('app/Modules');
        $filename = basename((string) $request->filePath);
        $filePath = $baseDir . DIRECTORY_SEPARATOR . $filename;

        if ($filename === '' || !File::exists($filePath) || dirname(realpath($filePath)) !== realpath($baseDir)) {
            return Reply::error('Invalid file path.');
        }

        File::delete($filePath);

        return Reply::success(__('messages.deleteSuccess'));
    }
}
