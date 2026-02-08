<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\radacct;
use Illuminate\Http\Request;

class OnlineCustomerWidgetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        switch ($operator->role) {
            case 'group_admin':
                $where = [
                    ['mgid', '=', $operator->id],
                ];
                break;
            case 'manager':
                $where = [
                    ['operator_id', '=', $operator->gid],
                ];
                break;
            default:
                $where = [
                    ['operator_id', '=', $operator->id],
                ];
                break;
        }

        return radacct::where($where)
            ->whereNull('acctstoptime')
            ->count();
    }
}
