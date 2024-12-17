<?php

namespace App\Http\Controllers\MercadoLivre;

use App\goHoltz\API\Response as API;
use App\Http\Controllers\Controller;
use App\Models\Users as mUsers;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class oAuth extends Controller
{
    /**
     * @var array<string, mixed>
     */
    protected array $fields = [];

    /**
     * @var GuzzleClient $client
     */
    private GuzzleClient $client;

    /**
     * Constructor
     * 
     * @param Request $request
     */
    public function __construct(protected Request $request)
    {
        $this->fields = $this->request->all();
        $this->client = new GuzzleClient([
            'timeout' => 30,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Redirect to Mercado Livre oAuth
     * 
     * @return RedirectResponse
     */
    public function redirect(): RedirectResponse
    {
        $clientId = env('ML_CLIENT_ID');
        $redirectUri = env('ML_REDIRECT_URI', route('ml.oauth.callback'));

        return redirect("https://auth.mercadolivre.com.br/authorization?response_type=code&client_id={$clientId}&redirect_uri={$redirectUri}");
    }

    /**
     * oAuth Callback from Mercado Livre
     * 
     * @return JsonResponse
     */
    public function callback(): JsonResponse
    {
        if (($params = API::validate([
            'code' => ['required', 'string', 'min:1', 'max:255', 'regex:/^[a-zA-Z0-9-_]+$/'],
        ], $this->fields, $response)) instanceof JsonResponse) {
            return $params;
        }

        try {
            $resp = $this->client->post('https://api.mercadolibre.com/oauth/token', [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'client_id' => env('ML_CLIENT_ID'),
                    'client_secret' => env('ML_CLIENT_SECRET'),
                    'code' => $params['code'],
                    'redirect_uri' => env('ML_REDIRECT_URI', route('ml.oauth.callback')),
                ],
            ]);
            $resp = json_decode($resp->getBody()->getContents());

            $data = [];
            $data['access_token'] = $resp->access_token;
            $data['refresh_token'] = $resp->refresh_token;
            $data['expires_in'] = now()->addSeconds($resp->expires_in);
            $data['user_id'] = $resp->user_id;
            $data['scopes'] = explode(' ', $resp->scope);

            return API::success($response, $data);
        } catch (ClientException $e) {
            $resp = json_decode($e->getResponse()->getBody()->getContents());
            return API::fail($response, $resp->message, $e->getCode());
        } catch (Exception $e) {
            return API::fail($response, $e->getMessage(), API::HTTP_INTERNAL_SERVER_ERROR);
        }

        return API::fail($response, 'Internal server error.', API::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Notifications from Mercado Livre (Webhook)
     * 
     * @return JsonResponse
     */
    public function notifications(): JsonResponse
    {
        if (($params = API::validate([], $this->fields, $response)) instanceof JsonResponse) {
            return $params;
        }

        return API::success($response, $this->request->all());
    }
}
