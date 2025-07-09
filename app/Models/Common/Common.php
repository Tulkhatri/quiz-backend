<?php
namespace App\Models\Common;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Common extends Model

{
    // public function __construct()
    // {
    //     parent::__construct();
    // }

    public static function saveData($post){
        try {
             if (empty($post['insertArray']) || empty($post['table'])) {
                 return false;
            }

            if (!empty($post['editid'])) {
            $result = DB::table($post['table'])
                ->where('id', $post['editid'])
                ->update($post['insertArray']);

            } else {
                $result = DB::table($post['table'])
                    ->insert($post['insertArray']);
            }

            return $result ? true : false;

        } catch (Exception $e) {
            throw $e;
        }
    }


    public static function deleteData($post){
        try {
            $result = DB::table($post['table'])
                ->where('id', $post['deleteid'])
                ->update([
                    'status' => 'N',
                    'updated_by' => $post['userid'],
                    'updated_at'=> date('Y-m-d H:i:s')
                ]);

                return $result ? true : false;

        } catch (Exception $e) {
            throw $e;
        }
    }


    public static function getJsonData($status, $message, $response, $otherkey = false, $otherval = false){
            $array = [
                'status' => $status,
                'message' => $message,
                'response' => $response
            ];
            if (!empty($otherkey)) {
                $array[$otherkey] = $otherval;
            }
        return response()->json($array, $status); 
    }
}
