<?php

namespace App\Core;

use Exception;
use Illuminate\Http\Response;
use stdClass;

class BaseResponse
{
    protected static $message = [
        'success_created' => 'Successfully saved data.',
        'failed_created' => 'Failed to save data.',
        'success_updated' => 'Successfully changed data.',
        'failed_updated' => 'Failed to update data.',
        'success_deleted' => 'Successfully deleted data.',
        'failed_deleted' => 'Failed to delete data, There was an error on the server.',
        'access_denied' => 'Access denied',
        'success_add_queue' => 'Successfully added queue data.',
        'failed_add_queue' => 'Failed to add queue data.',
        'failed_transaction' => 'Failed to make transaction'
    ];

    static function json($data = [], $code = 200, array $header = [])
    {
        return response()->json($data, $code, $header);
    }

    static function created($data)
    {
        return self::response('Created', $data);
    }

    static function updated($data)
    {
        return self::response('Updated', $data);
    }

    static function deleted($data)
    {
        return self::response('Deleted', $data);
    }

    static function errorTransaction(Exception $e = null)
    {
        $e = $e ?? new Exception(self::$message['failed_transaction'], Response::HTTP_UNPROCESSABLE_ENTITY);
        $code = ($e->getCode()) ? $e->getCode() : Response::HTTP_UNPROCESSABLE_ENTITY;
        $message = ($e->getMessage()) ? $e->getMessage() : self::$message['failed_transaction'];
        
        return self::errorMessage($message, $code);
    }

    static function response($typeFn, $data)
    {
        $response = new stdClass;
        if ($typeFn == 'Created' || $typeFn == 'Updated' || $typeFn == 'Deleted') {
            $codeSuccess = ($typeFn == 'Created' || $typeFn == 'Deleted') ? 200 : 201;
            if (is_object($data)) {
                if (isset($data->success) && !$data->success) {
                    $response = $data;
                    $response->message = self::getMessage($typeFn, false);
                } else {
                    if ($data->getAttributes()) {
                        $response->success = true;
                        $response->code = $codeSuccess;
                        $response->status = $typeFn;
                        $response->errors = null;
                        $response->message = self::getMessage($typeFn, true);
                        $response->data = $data->getAttributes();
                    } else {
                        $response = $data;
                    }
                }
            } else {
                $response->success = ($data) ? true : false;
                $response->code = ($data) ? $codeSuccess : 422;
                $response->status = $typeFn;
                $response->errors = null;
                $response->message = ($data) ? self::getMessage($typeFn, true) : self::getMessage($typeFn, false);
            }
        }

        return self::json($response, $response->code);   
    }

    static function getMessage($type, $success)
    {
        if($type == 'Created') {
            return ($success) ? self::$message['success_created'] : self::$message['failed_created'];
        }

        if($type == 'Updated') {
            return ($success) ? self::$message['success_updated'] : self::$message['failed_updated'];
        }
        
        if($type == 'Deleted') {
            return ($success) ? self::$message['success_deleted'] : self::$message['failed_deleted'];
        }
    }

    static function errorMessage($message = null, $code= 422, $status = '')
    {
        $response = new stdClass;
        $response->success = false;
        $response->message = $message ?? self::$message['failed_transaction'];

        return self::json($response, $code);
    }
}
