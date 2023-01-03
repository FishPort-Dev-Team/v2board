<?php

namespace App\Http\Controllers\Admin\Server;

use App\Models\ServerV2ray;
use App\Models\ServerShadowsocks;
use App\Models\ServerTrojan;
use App\Services\ServerService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ManageController extends Controller
{
    public function getNodes(Request $request)
    {
        $serverService = new ServerService();
        $serverList = $serverService->getAllServers();
        for ($i=0;$i<count($serverList);$i++){
            $serverList[$i]['host'] = $serverList[$i]['host'].",".$serverList[$i]['p_host'];
        }
        return response([
            'data' => $serverList
        ]);
    }

    public function sort(Request $request)
    {
        ini_set('post_max_size', '1m');
        DB::beginTransaction();
        foreach ($request->input('sorts') as $k => $v) {
            switch ($v['key']) {
                case 'shadowsocks':
                    if (!ServerShadowsocks::find($v['value'])->update(['sort' => $k + 1])) {
                        DB::rollBack();
                        abort(500, '保存失败');
                    }
                    break;
                case 'v2ray':
                    if (!ServerV2ray::find($v['value'])->update(['sort' => $k + 1])) {
                        DB::rollBack();
                        abort(500, '保存失败');
                    }
                    break;
                case 'trojan':
                    if (!ServerTrojan::find($v['value'])->update(['sort' => $k + 1])) {
                        DB::rollBack();
                        abort(500, '保存失败');
                    }
                    break;
            }
        }
        DB::commit();
        return response([
            'data' => true
        ]);
    }
}
