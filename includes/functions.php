<?php

require_once 'config.php';

/**
 * Gerencia o cache de dados usando APCu
 */
class CacheManager {
    private static $instance = null;
    private $enabled = false;

    private function __construct() {
        $this->enabled = extension_loaded('apcu');
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get($key) {
        if (!$this->enabled) return false;
        return apcu_fetch($key);
    }

    public function set($key, $data, $ttl = 300) {
        if (!$this->enabled) return false;
        return apcu_store($key, $data, $ttl);
    }
}

/**
 * Classe para gerenciar requisições à API
 */
class ApiClient {
    private $baseUrl = 'https://v3.football.api-sports.io/';
    private $apiKey;
    private $cache;

    public function __construct() {
        $this->apiKey = API_KEY;
        $this->cache = CacheManager::getInstance();
    }

    public function request($endpoint, $params = [], $maxRetries = 3) {
        $cacheKey = 'api_' . md5($endpoint . serialize($params));
        $cachedData = $this->cache->get($cacheKey);
        
        if ($cachedData !== false) {
            return $cachedData;
        }

        $attempt = 0;
        while ($attempt < $maxRetries) {
            try {
                $url = $this->baseUrl . $endpoint . '?' . http_build_query($params);
                
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => [
                        'X-RapidAPI-Key: ' . $this->apiKey,
                        'X-RapidAPI-Host: v3.football.api-sports.io'
                    ],
                    CURLOPT_TIMEOUT => 5,
                    CURLOPT_CONNECTTIMEOUT => 3
                ]);

                $response = curl_exec($curl);
                $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                
                if ($response === false || $httpCode >= 400) {
                    throw new Exception('Falha na requisição: ' . curl_error($curl));
                }
                
                curl_close($curl);
                $data = json_decode($response, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception('Resposta JSON inválida');
                }

                $this->cache->set($cacheKey, $data);
                return $data;

            } catch (Exception $e) {
                $attempt++;
                if ($attempt === $maxRetries) {
                    error_log("API Error: " . $e->getMessage());
                    return ['error' => $e->getMessage()];
                }
                sleep(1);
            }
        }
    }
}

/**
 * Classe para gerenciar times favoritos
 */
class FavoriteTeamsManager {
    private static function getMockTeams() {
        return [
            [
                'id' => 1,
                'name' => 'Flamengo',
                'logo' => '/assets/images/teams/flamengo.png',
                'matches' => 10,
                'wins' => 6,
                'last_match' => '2024-03-20',
                'next_match' => '2024-03-27',
                'position' => 3,
                'points' => 18
            ],
            [
                'id' => 2,
                'name' => 'Palmeiras',
                'logo' => '/assets/images/teams/palmeiras.png',
                'matches' => 10,
                'wins' => 7,
                'last_match' => '2024-03-21',
                'next_match' => '2024-03-28',
                'position' => 1,
                'points' => 21
            ],
            [
                'id' => 3,
                'name' => 'São Paulo',
                'logo' => '/assets/images/teams/saopaulo.png',
                'matches' => 10,
                'wins' => 5,
                'last_match' => '2024-03-19',
                'next_match' => '2024-03-26',
                'position' => 4,
                'points' => 16
            ]
        ];
    }

    public static function getFavoriteTeams($userId = null) {
        // TODO: Implementar lógica real com banco de dados
        return self::getMockTeams();
    }

    public static function addFavoriteTeam($userId, $teamId) {
        // TODO: Implementar lógica real
        return true;
    }

    public static function removeFavoriteTeam($userId, $teamId) {
        // TODO: Implementar lógica real
        return true;
    }
}

/**
 * Classe para gerenciar buscas recentes
 */
class SearchHistoryManager {
    private static function getMockSearches() {
        return [
            [
                'id' => 1,
                'query' => 'Flamengo vs Fluminense',
                'date' => '2024-03-20',
                'type' => 'match',
                'results_count' => 3
            ],
            [
                'id' => 2,
                'query' => 'Palmeiras',
                'date' => '2024-03-19',
                'type' => 'team',
                'results_count' => 15
            ],
            [
                'id' => 3,
                'query' => 'Brasileirão 2024',
                'date' => '2024-03-18',
                'type' => 'league',
                'results_count' => 20
            ]
        ];
    }

    public static function getRecentSearches($userId = null, $limit = 5) {
        // TODO: Implementar lógica real com banco de dados
        return self::getMockSearches();
    }

    public static function addSearch($userId, $query, $type) {
        // TODO: Implementar lógica real
        return true;
    }

    public static function clearHistory($userId) {
        // TODO: Implementar lógica real
        return true;
    }
}

// Funções auxiliares
function getCurrentRound($leagueId, $season) {
    $api = new ApiClient();
    $response = $api->request('fixtures/rounds', [
        'league' => $leagueId,
        'season' => $season,
        'current' => 'true'
    ]);

    if (!empty($response['response'])) {
        return preg_replace('/[^0-9]/', '', $response['response'][0]);
    }

    return getCurrentRoundSimplified($leagueId, $season);
}

function getCurrentRoundSimplified($leagueId, $season) {
    $currentMonth = (int)date('m');
    $currentDay = (int)date('d');
    
    if ($currentMonth < 4) return '1';
    
    $monthsSinceStart = $currentMonth - 4;
    $estimatedRound = min(38, max(1, ($monthsSinceStart * 4) + (int)($currentDay / 7)));
    
    return (string)$estimatedRound;
}

// Funções de compatibilidade para manter o código existente funcionando
function getFavoriteTeams($userId = null) {
    return FavoriteTeamsManager::getFavoriteTeams($userId);
}

function getRecentSearches($userId = null) {
    return SearchHistoryManager::getRecentSearches($userId);
}

function getMatches($leagueId, $season, $round = null) {
    $api = new ApiClient();
    $params = [
        'league' => $leagueId,
        'season' => $season
    ];
    
    if ($round) {
        $params['round'] = $round;
    }

    return $api->request('fixtures', $params);
}

function getTeamStats($teamName, $leagueId, $season) {
    $api = new ApiClient();
    $teamId = getTeamIdByName($teamName);
    
    if (!$teamId) return null;
    
    $response = $api->request('teams/statistics', [
        'team' => $teamId,
        'league' => $leagueId,
        'season' => $season
    ]);
    
    if (!empty($response['response'])) {
        $stats = $response['response'];
        return [
            'goals' => $stats['goals']['for']['total']['total'] ?? 0,
            'victories' => $stats['fixtures']['wins']['total'] ?? 0,
            'draws' => $stats['fixtures']['draws']['total'] ?? 0,
            'clean_sheets' => $stats['clean_sheets']['total'] ?? 0,
            'failed_to_score' => $stats['failed_to_score']['total'] ?? 0,
            'avg_goals_per_game' => $stats['goals']['for']['average']['total'] ?? 0,
            'biggest_win' => $stats['biggest']['wins']['home'] ?? 'N/A',
            'biggest_loss' => $stats['biggest']['loses']['away'] ?? 'N/A'
        ];
    }
    
    return null;
}

function getTeamMatches($teamId, $leagueId, $season) {
    return fetchData('fixtures', [
        'league' => $leagueId,
        'season' => $season,
        'team' => $teamId
    ]);
}

function getTeamMatchesByName($teamName, $leagueId, $season) {
    global $api_key;
    
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api-football-v1.p.rapidapi.com/v3/fixtures?" . 
                       "league={$leagueId}&" . 
                       "season={$season}&" . 
                       "team=" . getTeamIdByName($teamName),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "X-RapidAPI-Host: api-football-v1.p.rapidapi.com",
            "X-RapidAPI-Key: " . $api_key
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return ["error" => "cURL Error #:" . $err];
    }

    return json_decode($response, true);
}

function getTeamIdByName($teamName) {
    $url = "https://api-football-v1.p.rapidapi.com/v3/teams";
    
    $queryParams = [
        'search' => $teamName
    ];
    
    $response = makeApiRequest($url, $queryParams);
    
    if (!empty($response['response'])) {
        return $response['response'][0]['team']['id'];
    }
    
    return null;
}

function makeApiRequest($url, $queryParams = []) {
    if (!empty($queryParams)) {
        $url .= '?' . http_build_query($queryParams);
    }
    
    $curl = curl_init();
    
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "X-RapidAPI-Host: api-football-v1.p.rapidapi.com",
            "X-RapidAPI-Key: " . API_KEY
        ],
    ]);
    
    // Tentar obter dados do cache primeiro
    $cacheKey = 'api_' . md5($url . serialize($queryParams));
    $cachedData = getCachedData($cacheKey);
    
    if ($cachedData !== false) {
        return $cachedData;
    }
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    curl_close($curl);
    
    if ($err) {
        return [
            'error' => true,
            'message' => "cURL Error #:" . $err
        ];
    }
    
    $decodedResponse = json_decode($response, true);
    
    // Armazenar em cache por 5 minutos (300 segundos)
    setCachedData($cacheKey, $decodedResponse, 300);
    
    return $decodedResponse;
}
?>
