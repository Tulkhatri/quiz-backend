<?php
namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Common extends Model
{
    static $errormessages = [
        'listData'    => 'Successfully listed your record.',
        'saveData'    => 'Successfully save your record.',
        'notData'     => 'No data Available.',
        'invalidReq'  => 'Wops Wrong Requested',
        'errorPro'    => 'Error Processing',
    ];


    public function __construct()
    {
        parent::__construct();

        $this->type = 'success';
        $this->data = [
            'listData'    => 'Successfully listed your record.',
            'saveData'    => 'Successfully save your record.',
            'notData'     => 'No data Available.',
            'invalidReq'  => 'Wops Wrong Requested',
            'errorPro'    => 'Error Processing',
        ];
    }



  public static function saveData($post){
        try {
            if (!$post['insertArray'])
                return false;

            if (@$post['editid']) {
                $result = DB::table($post['table'])->whereRaw('"' . $post['editkey'] . '" = ' . $post['editid'] . '')->update(@$post['insertArray[0]'] ? $post['insertArray[0]'] : $post['insertArray']);

            } else {
                $result = DB::table($post['table'])->insert($post['insertArray']);
            }

            if (!$result)
                return false;

            return true;

        } catch (Exception $e) {
            throw $e;
        }
    }


    public static function deleteData($post){
        try {
            $result  = DB::table($post['table'])->whereRaw('"' . $post['deletekey'] . '" = ' . $post['deleteid'] . '')->delete();
            if (!$result)
                return false;

            return true;

        } catch (Exception $e) {
            throw $e;
        }
    }



public static function getJsonData(int $status,string $message, array ...$arrays)
{
    // Merge all arrays passed as arguments into one response array
    $response = [];
    foreach ($arrays as $arr) {
        if (is_array($arr)) {
            $response = array_merge($response, $arr);
        }
    }

    // Remove null or empty recursively
    $response = self::arrayFilterRecursive($response);
    $array = [
        'message' => $message,
    ];

    if (!empty($response)) {
        $array['response'] = $response;
    }

    return response()->json($array, $status);
}

   // Recursive filter to remove null or empty arrays
    private static function arrayFilterRecursive(array $input): array
    {
        foreach ($input as $key => &$value) {
            if (is_array($value)) {
                $value = self::arrayFilterRecursive($value);
                if (empty($value)) {
                    unset($input[$key]);
                }
            } elseif (is_null($value)) {
                unset($input[$key]);
            }
        }
        return $input;
    }
}
