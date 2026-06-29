<?php
namespace app\components;

use Yii;
use GuzzleHttp\Client;

class InstanceConnector
{
    protected Client $client;

    public function __construct(array $options = [])
    {
        $this->client = new Client($options);
    }

    /**
     * Obtiene datos desde una instancia externa configurada en la tabla external_instances.
     * Por defecto intenta GET {base_url}/api/v1/checkins
     *
     * @param int $instanceId
     * @param string $path
     * @param array $queryParams
     * @return array|null
     */
    public function fetchData(int $instanceId, string $path = '/api/v1/checkins', array $queryParams = []): ?array
    {
        $db = Yii::$app->db;
        $inst = $db->createCommand('SELECT id, name, base_url, api_key FROM external_instances WHERE id = :id')
            ->bindValue(':id', $instanceId)
            ->queryOne();

        if (!$inst) {
            throw new \InvalidArgumentException('Instance not found: ' . $instanceId);
        }

        $base = rtrim($inst['base_url'], '/');
        $url = $base . $path;

        $headers = [
            'Accept' => 'application/json',
        ];
        if (!empty($inst['api_key'])) {
            $headers['X-Api-Key'] = $inst['api_key'];
        }

        try {
            $resp = $this->client->request('GET', $url, [
                'headers' => $headers,
                'query' => $queryParams,
                'timeout' => 10,
                'connect_timeout' => 5,
            ]);

            $code = $resp->getStatusCode();
            $body = (string)$resp->getBody();
            $data = json_decode($body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['raw' => $body, 'status_code' => $code];
            }
            return $data;
        } catch (\Throwable $e) {
            Yii::error('InstanceConnector error: ' . $e->getMessage() . ' for instance ' . $instanceId, __METHOD__);
            return null;
        }
    }
}

