<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use DomainException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\Key;
use InvalidArgumentException;
use UnexpectedValueException;
use Illuminate\Support\Facades\Hash;
use Throwable;

class JwtAuth{
    private $secretKey = '77217A25432A462D4A614E645267556B58703272357538782F413F4428472B4B';

    public function userLogin($email, $pass, $getToken = null)
    {
        $login = false;

        $user = User::where([
            'email' => $email
        ])->first();

        try{
            $hashedPass = Hash::check($pass, $user->password);
            if ($hashedPass === true && is_object($user)) {
                $login = true;
            }
        }catch(Throwable $th){
            $login = false;
        }

        if($user->status == 2){
            $login = true;
        }else{
            $login = false;
        }

        if ($login) {
            $data = array(
                'sub' => $user->id,
                'name' => $user->name,
                'surname' => $user->surname,
                'email' => $user->email,
                'rol' => $user->rol,
                'status' => $user->status,
                'iat' => time(),
                'exp' => time() + (60 * 60) //1 hrs
            );

            $jwtToken = JWT::encode($data, $this->secretKey, 'HS256');

            $decodeJwt = JWT::decode($jwtToken, new key($this->secretKey, 'HS256'));

            if (is_null($getToken)) {
                $data = $jwtToken;
            } else {
                $data = $decodeJwt;
            }
        } else {
            $data = array(
                'code' => 404,
                'status' => 'Error',
                'traceId' => '102L',
                'message' => 'Failed authentication attempt.'
            );
        }

        return $data;
    }

    public function checkToken($jwt, $getIdentity = false)
    {
        $response = false;

        try {
            if (strpos($jwt, 'Bearer') !== false) {
                $jwt = str_replace(array('"'), '', $jwt);
                $jwt = ltrim($jwt, 'Bearer');
                $jwt = ltrim($jwt, ' ');
            } else {
                $jwt = str_replace(array('"'), '', $jwt);
            }

            $decoded = JWT::decode($jwt, new key($this->secretKey, 'HS256'));
        } catch (DomainException $e) {
            $response = 'Unsupported algorithm or bad key was specified';
        } catch (ExpiredException $e) {
            $response = 'Expired token';
        } catch (InvalidArgumentException $e) {
            $response = 'Key may not be empty';
        } catch (UnexpectedValueException $e) {
            $response = 'Wrong number of segments';
        }

        if (!empty($decoded) && is_object($decoded) && isset($decoded->name) && isset($decoded->email)) {
            $response = true;
        }

        if ($getIdentity) {
            return $decoded;
        }

        return $response;
    }

    // public function update(){

    // }
}