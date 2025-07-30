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
                    
            if (!empty($post['id'])) {
            $result = DB::table($post['table'])
                ->where('id', $post['id'])
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


    public static function getJsonData($statusCode, $message, $response= false){
            $array = [
                'message' => $message,
            ];
            if(!empty($response)){
                 $array['response'] = $response;
            }
        return response()->json($array, $statusCode); 
    }
}
