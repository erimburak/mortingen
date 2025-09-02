<?php

namespace Controllers\api;

use \Bulma;
use \BulmaClass;
use \View;
use Request\Request;
use Request\Method;
use \Form;

class TestAPI extends \API
{
    public function index()
    {
        $this->response->setContentType(\Response\ContentType::TEXT_HTML);

        $content = View::concat(
            Bulma::Title('🚀 API Testing System', 1),
            Bulma::Subtitle('Testing setValidRequestMethods() functionality in API context', 3),
            new View('<div class="notification is-info">📍 API URL: /mortingen/api/TestAPI</div>'),
            $this->displayAPITestCards(),
            $this->displayAPITestInstructions(),
            $this->displayTestResults()
        );

        $pageContent = Bulma::Container($content);

        $this->response->setContent(
            Bulma::Html($pageContent, 'API Testing - Mortingen Framework')
        );

        $this->response->addContent($this->getJavaScript());
    }

    public function onlyGet()
    {
        $this->setValidRequestMethods('GET');

        $requestMethod = Request::getMethod();
        $queryParams = Request::getQueryAll();

        if (!isset($_SESSION))
        {
            session_start();
        }

        date_default_timezone_set('Europe/Istanbul');

        $lastPostData = $_SESSION['last_api_post_data'] ?? null;
        $lastPostTime = $_SESSION['last_api_post_time'] ?? null;
        $lastPostSource = $_SESSION['last_api_post_source'] ?? null;

        // Extra info for display
        $sessionInfo = [];
        if ($lastPostData)
        {
            $sessionInfo = [
                'last_post_from' => $lastPostSource,
                'last_post_time' => $lastPostTime,
                'last_post_data' => $lastPostData['data']['received_data'] ?? null
            ];
        }

        $responseData = [
            'status' => 'success',
            'message' => 'GET-only API endpoint accessed successfully',
            'data' => [
                'route' => '/api/TestAPI/onlyGet',
                'allowed_methods' => ['GET'],
                'current_method' => $requestMethod->value,
                'query_params' => $queryParams,
                'timestamp' => date('Y-m-d H:i:s'),
                'timezone' => date_default_timezone_get(),
                'session_info' => $sessionInfo
            ],
            'metadata' => [
                'request_id' => uniqid('api_'),
                'api_version' => '1.0',
                'framework' => 'Mortingen'
            ]
        ];

        // Save to session for cross-method testing
        $_SESSION['last_api_get_data'] = $responseData;
        $_SESSION['last_api_get_time'] = date('Y-m-d H:i:s');

        $this->response->setContent(json_encode($responseData, JSON_PRETTY_PRINT));
    }

    public function onlyPost()
    {
        $this->setValidRequestMethods('POST');

        $requestMethod = Request::getMethod();
        $postData = Request::getDataAll();

        if (!isset($_SESSION))
        {
            session_start();
        }

        date_default_timezone_set('Europe/Istanbul');

        $responseData = [
            'status' => 'success',
            'message' => 'POST-only API endpoint accessed successfully',
            'data' => [
                'route' => '/api/TestAPI/onlyPost',
                'allowed_methods' => ['POST'],
                'current_method' => $requestMethod->value,
                'received_data' => $postData,
                'timestamp' => date('Y-m-d H:i:s'),
                'timezone' => date_default_timezone_get()
            ],
            'metadata' => [
                'request_id' => uniqid('api_'),
                'api_version' => '1.0',
                'framework' => 'Mortingen',
                'content_length' => strlen(json_encode($postData))
            ]
        ];

        // Save to session for cross-method testing
        $_SESSION['last_api_post_data'] = $responseData;
        $_SESSION['last_api_post_time'] = date('Y-m-d H:i:s');
        $_SESSION['last_api_post_source'] = 'onlyPost';

        $this->response->setContent(json_encode($responseData, JSON_PRETTY_PRINT));
    }

    public function getAndPost()
    {
        $this->setValidRequestMethods('GET', 'POST');

        $requestMethod = Request::getMethod();
        $isPost = $requestMethod === Method::POST;

        if (!isset($_SESSION))
        {
            session_start();
        }

        date_default_timezone_set('Europe/Istanbul');

        $responseData = [
            'status' => 'success',
            'message' => 'Mixed GET/POST API endpoint accessed successfully',
            'data' => [
                'route' => '/api/TestAPI/getAndPost',
                'allowed_methods' => ['GET', 'POST'],
                'current_method' => $requestMethod->value,
                'timestamp' => date('Y-m-d H:i:s'),
                'timezone' => date_default_timezone_get()
            ],
            'metadata' => [
                'request_id' => uniqid('api_'),
                'api_version' => '1.0',
                'framework' => 'Mortingen'
            ]
        ];

        if ($isPost)
        {
            $postData = Request::getDataAll();
            $responseData['data']['received_data'] = $postData;
            $responseData['data']['method_type'] = 'POST';
            $responseData['metadata']['content_length'] = strlen(json_encode($postData));

            // Save to session
            $_SESSION['last_api_post_data'] = $responseData;
            $_SESSION['last_api_post_time'] = date('Y-m-d H:i:s');
            $_SESSION['last_api_post_source'] = 'getAndPost';
        }
        else
        {
            $queryParams = Request::getQueryAll();

            $lastPostData = $_SESSION['last_api_post_data'] ?? null;
            $lastPostTime = $_SESSION['last_api_post_time'] ?? null;
            $lastPostSource = $_SESSION['last_api_post_source'] ?? null;

            $sessionInfo = [];
            if ($lastPostData)
            {
                $sessionInfo = [
                    'last_post_from' => $lastPostSource,
                    'last_post_time' => $lastPostTime,
                    'last_post_data' => $lastPostData['data']['received_data'] ?? null
                ];
            }

            $responseData['data']['query_params'] = $queryParams;
            $responseData['data']['method_type'] = 'GET';
            $responseData['data']['session_info'] = $sessionInfo;

            // Save to session
            $_SESSION['last_api_get_data'] = $responseData;
            $_SESSION['last_api_get_time'] = date('Y-m-d H:i:s');
        }

        $this->response->setContent(json_encode($responseData, JSON_PRETTY_PRINT));
    }

    public function status()
    {
        // Cross-method data display endpoint
        $this->setValidRequestMethods('GET');

        if (!isset($_SESSION))
        {
            session_start();
        }

        $lastGetData = $_SESSION['last_api_get_data'] ?? null;
        $lastPostData = $_SESSION['last_api_post_data'] ?? null;
        $lastGetTime = $_SESSION['last_api_get_time'] ?? null;
        $lastPostTime = $_SESSION['last_api_post_time'] ?? null;
        $lastPostSource = $_SESSION['last_api_post_source'] ?? null;

        $statusData = [
            'status' => 'success',
            'message' => 'API status and session data retrieved',
            'data' => [
                'route' => '/api/TestAPI/status',
                'current_time' => date('Y-m-d H:i:s'),
                'session_data' => [
                    'last_get_request' => [
                        'data' => $lastGetData,
                        'timestamp' => $lastGetTime
                    ],
                    'last_post_request' => [
                        'data' => $lastPostData,
                        'timestamp' => $lastPostTime,
                        'source' => $lastPostSource
                    ]
                ]
            ],
            'metadata' => [
                'request_id' => uniqid('api_'),
                'api_version' => '1.0',
                'framework' => 'Mortingen'
            ]
        ];

        $this->response->setContent(json_encode($statusData, JSON_PRETTY_PRINT));
    }

    private function displayAPITestCards(): View
    {
        $cardsContent = new View(
            '<div class="columns is-multiline">' .
                '<div class="column is-6">' .
                '<div class="card">' .
                '<div class="card-header has-background-success">' .
                '<p class="card-header-title has-text-white">' .
                '<span class="icon"><i class="fas fa-download"></i></span>' .
                '<span>GET Only API</span>' .
                '</p>' .
                '</div>' .
                '<div class="card-content">' .
                '<div class="content">' .
                '<p><strong>Endpoint:</strong> /api/TestAPI/onlyGet</p>' .
                '<p><strong>Method:</strong> <span class="tag is-success">GET</span></p>' .
                '<p><strong>Returns:</strong> JSON response with query parameters</p>' .
                '</div>' .
                '</div>' .
                '<div class="card-footer">' .
                '<a href="/mortingen/api/TestAPI/onlyGet" class="card-footer-item button is-success">Test GET API</a>' .
                '</div>' .
                '</div>' .
                '</div>' .

                '<div class="column is-6">' .
                '<div class="card">' .
                '<div class="card-header has-background-info">' .
                '<p class="card-header-title has-text-white">' .
                '<span class="icon"><i class="fas fa-upload"></i></span>' .
                '<span>POST Only API</span>' .
                '</p>' .
                '</div>' .
                '<div class="card-content">' .
                '<div class="content">' .
                '<p><strong>Endpoint:</strong> /api/TestAPI/onlyPost</p>' .
                '<p><strong>Method:</strong> <span class="tag is-info">POST</span></p>' .
                '<p><strong>Returns:</strong> JSON response with POST data</p>' .
                '</div>' .
                '</div>' .
                '<div class="card-footer">' .
                '<button onclick="testPostAPI()" class="card-footer-item button is-info">Test POST API</button>' .
                '</div>' .
                '</div>' .
                '</div>' .

                '<div class="column is-6">' .
                '<div class="card">' .
                '<div class="card-header has-background-warning">' .
                '<p class="card-header-title has-text-black">' .
                '<span class="icon"><i class="fas fa-exchange-alt"></i></span>' .
                '<span>GET & POST API</span>' .
                '</p>' .
                '</div>' .
                '<div class="card-content">' .
                '<div class="content">' .
                '<p><strong>Endpoint:</strong> /api/TestAPI/getAndPost</p>' .
                '<p><strong>Methods:</strong> <span class="tag is-success">GET</span> <span class="tag is-info">POST</span></p>' .
                '<p><strong>Returns:</strong> JSON response based on method</p>' .
                '</div>' .
                '</div>' .
                '<div class="card-footer">' .
                '<a href="/mortingen/api/TestAPI/getAndPost" class="card-footer-item button is-warning">Test Mixed API</a>' .
                '</div>' .
                '</div>' .
                '</div>' .

                '<div class="column is-6">' .
                '<div class="card">' .
                '<div class="card-header has-background-primary">' .
                '<p class="card-header-title has-text-white">' .
                '<span class="icon"><i class="fas fa-chart-line"></i></span>' .
                '<span>API Status</span>' .
                '</p>' .
                '</div>' .
                '<div class="card-content">' .
                '<div class="content">' .
                '<p><strong>Endpoint:</strong> /api/TestAPI/status</p>' .
                '<p><strong>Method:</strong> <span class="tag is-primary">GET</span></p>' .
                '<p><strong>Returns:</strong> Session data and API status</p>' .
                '</div>' .
                '</div>' .
                '<div class="card-footer">' .
                '<a href="/mortingen/api/TestAPI/status" class="card-footer-item button is-primary">Check Status</a>' .
                '</div>' .
                '</div>' .
                '</div>' .

                '</div>'
        );

        return Bulma::Section(Bulma::Container($cardsContent));
    }

    private function displayAPITestInstructions(): View
    {
        $testForm = (new Form('api_test_form', '/mortingen/api/TestAPI/onlyPost', 'POST'))
            ->text('api_test_data', 'Test Data', 'Enter API test data', 'Hello API!')
            ->textarea('api_message', 'Message', 'Enter test message', 'This is an API test message!')
            ->submit('Send POST to API', ['class' => 'is-info is-large']);

        $instructionsContent = new View(
            '<div class="columns">' .
                '<div class="column is-half">' .
                '<div class="box">' .
                '<h3 class="title is-4">📡 API POST Test</h3>' .
                '<p class="subtitle is-6">Test API endpoints with POST data</p>' .
                $testForm .
                '</div>' .
                '</div>' .
                '<div class="column is-half">' .
                '<div class="box has-background-info-dark">' .
                '<h3 class="title is-4 has-text-white">🔧 API Testing Guide</h3>' .
                '<div class="content has-text-white">' .
                '<p><strong>Available Endpoints:</strong></p>' .
                '<ul>' .
                '<li><code>/api/TestAPI/onlyGet</code> - GET only</li>' .
                '<li><code>/api/TestAPI/onlyPost</code> - POST only</li>' .
                '<li><code>/api/TestAPI/getAndPost</code> - Both methods</li>' .
                '<li><code>/api/TestAPI/status</code> - Session status</li>' .
                '</ul>' .
                '<p><strong>Testing with cURL:</strong></p>' .
                '<pre><code>curl -X GET http://localhost/mortingen/api/TestAPI/onlyGet</code></pre>' .
                '<pre><code>curl -X POST -d "test=data" http://localhost/mortingen/api/TestAPI/onlyPost</code></pre>' .
                '</div>' .
                '</div>' .
                '</div>' .
                '</div>'
        );

        return Bulma::Section(Bulma::Container($instructionsContent));
    }

    private function displayTestResults(): View
    {
        $resultsContent = new View(
            '<div class="box has-background-dark">' .
                '<h3 class="title is-3 has-text-white">🧪 API Method Validation Tests</h3>' .
                '<div class="content has-text-white">' .
                '<h4 class="title is-4 has-text-white">✅ Expected API Behaviors:</h4>' .
                '<ul>' .
                '<li><strong>GET Only API:</strong> Returns JSON data for GET requests only</li>' .
                '<li><strong>POST Only API:</strong> Returns JSON data for POST requests only</li>' .
                '<li><strong>Mixed API:</strong> Handles both GET and POST with appropriate responses</li>' .
                '<li><strong>Status API:</strong> Shows cross-method session data</li>' .
                '</ul>' .

                '<h4 class="title is-4 has-text-white">❌ Error Responses:</h4>' .
                '<div class="notification is-warning">' .
                '<p><strong>Method restrictions should return:</strong></p>' .
                '<ul>' .
                '<li>HTTP 405 Method Not Allowed status</li>' .
                '<li>"Method not allowed." message</li>' .
                '<li>No JSON response for invalid methods</li>' .
                '</ul>' .
                '</div>' .

                '<h4 class="title is-4 has-text-white">🔧 Testing Commands:</h4>' .
                '<div class="box has-background-grey-darker has-text-light">' .
                '<p><strong>Test method restrictions:</strong></p>' .
                '<pre><code># This should work (GET to GET-only endpoint)
curl -X GET http://localhost/mortingen/api/TestAPI/onlyGet

# This should fail (POST to GET-only endpoint)  
curl -X POST http://localhost/mortingen/api/TestAPI/onlyGet

# This should work (POST to POST-only endpoint)
curl -X POST -d "test=data" http://localhost/mortingen/api/TestAPI/onlyPost

# This should fail (GET to POST-only endpoint)
curl -X GET http://localhost/mortingen/api/TestAPI/onlyPost</code></pre>' .
                '</div>' .
                '</div>' .
                '</div>'
        );

        return Bulma::Section(Bulma::Container($resultsContent));
    }

    private function getJavaScript(): View
    {
        return new View('
        <script>
        function testPostAPI() {
            fetch("/mortingen/api/TestAPI/onlyPost", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "test_data=API_POST_Test&message=Direct+POST+from+JavaScript"
            })
            .then(response => {
                return response.text().then(text => {
                    // Warning\'leri temizle (PHP warning\'leri JSON\'a karışıyor)
                    let cleanText = text;
                    if (text.includes("<br />")) {
                        // Warning\'ler varsa, JSON kısmını çıkar
                        const jsonStart = text.indexOf("{");
                        if (jsonStart !== -1) {
                            cleanText = text.substring(jsonStart);
                        }
                    }
                    
                    try {
                        const data = JSON.parse(cleanText);
                        return { success: true, data: data };
                    } catch (e) {
                        return { success: false, error: "JSON Parse Error", rawText: text };
                    }
                });
            })
            .then(result => {
                if (result.success) {
                    alert("✅ API Response Success!\\n" + JSON.stringify(result.data, null, 2));
                } else {
                    alert("❌ Error: " + result.error + "\\n\\nRaw Response:\\n" + result.rawText.substring(0, 200) + "...");
                }
            })
            .catch(error => {
                alert("😱 Network Error: " + error.message);
            });
        }
        </script>
        ');
    }
}
