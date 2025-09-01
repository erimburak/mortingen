<?php

namespace Controllers;

use Controller;
use Bulma;

use BulmaClass;
use View;
use Request\Request;
use Request\Method;
use Form;

class TestController extends Controller
{
    public function index()
    {
        $content = View::concat(
            Bulma::Title('🔀 HTTP Method Testing System', 1),
            Bulma::Subtitle('Testing setValidRequestMethods() functionality with different route configurations', 3),
            $this->displayMethodTestCards(),
            $this->displayPostTestSection(),
            $this->displayTestResults()
        );

        $pageContent = Bulma::Container($content);

        $this->response->setContentType(\Response\ContentType::TEXT_HTML);
        $this->response->setContent(
            Bulma::Html($pageContent, 'HTTP Method Testing - Mortingen Framework')
        );
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

        $heroContent = View::concat(
            Bulma::Title('✅ GET Only Route', 1, [BulmaClass::HAS_TEXT_WHITE]),
            Bulma::Subtitle('Successfully accessed with ' . $requestMethod->value . ' method', 3, [BulmaClass::HAS_TEXT_LIGHT])
        );

        $hero = Bulma::Hero($heroContent, [BulmaClass::IS_SUCCESS, BulmaClass::IS_MEDIUM]);

        $routeInfo = View::concat(
            Bulma::Title('🎯 Route Information', 4),
            Bulma::Content(View::concat(
                \HTML::p(View::concat(\HTML::strong('Route: '), '/TestController/onlyGet')),
                \HTML::p(View::concat(\HTML::strong('Allowed Methods: '), Bulma::Tag('GET', [BulmaClass::IS_SUCCESS]))),
                \HTML::p(View::concat(\HTML::strong('Current Method: '), Bulma::Tag($requestMethod->value, [BulmaClass::IS_INFO]))),
                \HTML::p(View::concat(\HTML::strong('Status: '), Bulma::Tag('✅ Access Granted', [BulmaClass::IS_SUCCESS])))
            ))
        );

        $sampleQueryParams = [
            'user' => 'SampleUser',
            'action' => 'view_page',
            'id' => '42',
            'current_time' => date('Y-m-d H:i:s'),
            'timezone' => date_default_timezone_get(),
            'method' => 'GET',
            'route' => '/TestController/onlyGet'
        ];

        $displayParams = !empty($queryParams) ? $queryParams : $sampleQueryParams;

        $rawDataBox = Bulma::Box(View::concat(
            Bulma::Title('🔍 Query Parameters (' . (!empty($queryParams) ? 'REAL' : 'SAMPLE') . '):', 5),
            \HTML::pre(\HTML::code(htmlspecialchars(json_encode($displayParams, JSON_PRETTY_PRINT)))),
            new View(!empty($queryParams) ?
                '<div class="notification is-success">✅ Real GET parameters detected!</div>' :
                '<div class="notification is-info">ℹ️ No real parameters - showing sample data</div>')
        ), [BulmaClass::HAS_BACKGROUND_DARK]);

        // Cross-method data sharing: Display POST data from other routes
        $lastPostData = $_SESSION['last_post_data'] ?? null;
        $lastPostTime = $_SESSION['last_post_time'] ?? null;
        $lastPostSource = $_SESSION['last_post_source'] ?? 'onlyPost';
        $postDataBox = null;

        if ($lastPostData)
        {
            $sourceInfo = $lastPostSource === 'getAndPost' ? '📤 Last POST Data from GET & POST Route:' : '📤 Last POST Data from Direct POST Test:';
            $sourceNotification = $lastPostSource === 'getAndPost' ?
                '<div class="notification is-success">✅ This data came from getAndPost route!</div>' :
                '<div class="notification is-warning">⚠️ This data came from onlyPost route!</div>';

            $postDataBox = Bulma::Box(View::concat(
                Bulma::Title($sourceInfo, 5),
                \HTML::pre(\HTML::code(htmlspecialchars(json_encode($lastPostData, JSON_PRETTY_PRINT)))),
                new View($sourceNotification),
                new View('<div class="notification is-info">🕒 Record Time: ' . ($lastPostTime ?? 'Unknown') . '</div>'),
                new View('<div class="notification is-primary">🕒 Current Time: ' . date('Y-m-d H:i:s') . '</div>')
            ), [BulmaClass::HAS_BACKGROUND_INFO]);
        }

        $buttons = Bulma::Buttons(View::concat(
            Bulma::ButtonLink('🏠 Back to Tests', '/mortingen/TestController', [BulmaClass::IS_PRIMARY]),
            Bulma::ButtonLink('Test POST Only', '/mortingen/TestController/onlyPost', [BulmaClass::IS_INFO]),
            Bulma::ButtonLink('Test GET & POST', '/mortingen/TestController/getAndPost', [BulmaClass::IS_WARNING])
        ));

        $infoBoxContent = $postDataBox ?
            View::concat($routeInfo, $rawDataBox, $postDataBox, $buttons) :
            View::concat($routeInfo, $rawDataBox, $buttons);

        $infoBox = Bulma::Box($infoBoxContent);

        $content = View::concat(
            $hero,
            Bulma::Section(Bulma::Container($infoBox))
        );

        $this->response->setContentType(\Response\ContentType::TEXT_HTML);
        $this->response->addContent(
            Bulma::Html($content, 'GET Only Route - Method Testing')
        );
    }

    public function onlyPost()
    {
        $this->setValidRequestMethods('POST');

        $requestMethod = Request::getMethod();
        $postData = Request::getDataAll();
        $dataDisplay = !empty($postData) ? json_encode($postData, JSON_PRETTY_PRINT) : 'No POST data received';

        if (!isset($_SESSION))
        {
            session_start();
        }

        date_default_timezone_set('Europe/Istanbul');

        if (!empty($postData))
        {
            $_SESSION['last_post_data'] = $postData;
            $_SESSION['last_post_time'] = date('Y-m-d H:i:s');
            $_SESSION['last_post_source'] = 'onlyPost';
        }

        $heroContent = View::concat(
            Bulma::Title('✅ POST Only Route', 1, [BulmaClass::HAS_TEXT_WHITE]),
            Bulma::Subtitle('Successfully accessed with ' . $requestMethod->value . ' method', 3, [BulmaClass::HAS_TEXT_LIGHT])
        );

        $hero = Bulma::Hero($heroContent, [BulmaClass::IS_INFO, BulmaClass::IS_MEDIUM]);

        $routeInfo = View::concat(
            Bulma::Title('📊 Route Information', 4),
            Bulma::Content(View::concat(
                \HTML::p(View::concat(\HTML::strong('Route: '), '/TestController/onlyPost')),
                \HTML::p(View::concat(\HTML::strong('Allowed Methods: '), Bulma::Tag('POST', [BulmaClass::IS_INFO]))),
                \HTML::p(View::concat(\HTML::strong('Current Method: '), Bulma::Tag($requestMethod->value, [BulmaClass::IS_SUCCESS]))),
                \HTML::p(View::concat(\HTML::strong('Status: '), Bulma::Tag('✅ Access Granted', [BulmaClass::IS_SUCCESS])))
            ))
        );

        $dataBox = Bulma::Box(View::concat(
            Bulma::Title('📤 POST Data Received:', 5),
            \HTML::pre(\HTML::code(htmlspecialchars($dataDisplay))),
            new View('<div class="notification is-success">ℹ️ This data is now saved and will be visible in onlyGet page!</div>')
        ), [BulmaClass::HAS_BACKGROUND_DARK]);

        $buttons = Bulma::Buttons(View::concat(
            Bulma::ButtonLink('🏠 Back to Tests', '/mortingen/TestController', [BulmaClass::IS_PRIMARY]),
            Bulma::ButtonLink('Test GET Only', '/mortingen/TestController/onlyGet', [BulmaClass::IS_SUCCESS]),
            Bulma::ButtonLink('Test GET & POST', '/mortingen/TestController/getAndPost', [BulmaClass::IS_WARNING])
        ));

        $infoBox = Bulma::Box(View::concat($routeInfo, $dataBox, $buttons));

        $content = View::concat(
            $hero,
            Bulma::Section(Bulma::Container($infoBox))
        );

        $this->response->setContentType(\Response\ContentType::TEXT_HTML);
        $this->response->addContent(
            Bulma::Html($content, 'POST Only Route - Method Testing')
        );
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

        if ($isPost)
        {
            $postData = Request::getDataAll();
            if (!empty($postData))
            {
                $_SESSION['last_post_data'] = $postData;
                $_SESSION['last_post_time'] = date('Y-m-d H:i:s');
                $_SESSION['last_post_source'] = 'getAndPost';
            }
        }

        $heroContent = View::concat(
            Bulma::Title('✅ GET & POST Route', 1, [BulmaClass::HAS_TEXT_WHITE]),
            Bulma::Subtitle('Successfully accessed with ' . $requestMethod->value . ' method', 3, [BulmaClass::HAS_TEXT_LIGHT])
        );

        $hero = Bulma::Hero($heroContent, [BulmaClass::IS_WARNING, BulmaClass::IS_MEDIUM]);

        $routeInfo = View::concat(
            Bulma::Title('🔄 Route Information', 4),
            Bulma::Content(View::concat(
                \HTML::p(View::concat(\HTML::strong('Route: '), '/TestController/getAndPost')),
                \HTML::p(View::concat(
                    \HTML::strong('Allowed Methods: '),
                    Bulma::Tag('GET', [BulmaClass::IS_SUCCESS]),
                    ' ',
                    Bulma::Tag('POST', [BulmaClass::IS_INFO])
                )),
                \HTML::p(View::concat(\HTML::strong('Current Method: '), Bulma::Tag($requestMethod->value, [BulmaClass::IS_WARNING]))),
                \HTML::p(View::concat(\HTML::strong('Status: '), Bulma::Tag('✅ Access Granted', [BulmaClass::IS_SUCCESS])))
            ))
        );

        // Method-specific content display
        if ($isPost)
        {
            $postData = Request::getDataAll();
            $dataDisplay = !empty($postData) ? json_encode($postData, JSON_PRETTY_PRINT) : 'No POST data received';
            $methodInfoBox = Bulma::Box(View::concat(
                Bulma::Title('📤 POST Data Received:', 5),
                \HTML::pre(\HTML::code(htmlspecialchars($dataDisplay))),
                new View('<div class="notification is-success">✅ Data saved to session! Check onlyGet page to see it there too!</div>'),
                new View('<div class="notification is-primary">🔄 Source: getAndPost route</div>')
            ), [BulmaClass::HAS_BACKGROUND_INFO]);
        }
        else
        {
            $queryData = Request::getQueryAll();
            $queryDisplay = !empty($queryData) ? json_encode($queryData, JSON_PRETTY_PRINT) : 'No query parameters received';
            $methodInfoBox = Bulma::Box(View::concat(
                Bulma::Title('🔍 Query Parameters:', 5),
                \HTML::pre(\HTML::code(htmlspecialchars($queryDisplay)))
            ), [BulmaClass::HAS_BACKGROUND_SUCCESS]);
        }

        // Create test form for POST method
        $testForm = (new Form('method_test', '/mortingen/TestController/getAndPost', 'POST'))
            ->text('test_field', 'Test Field', 'Enter some test data', '')
            ->textarea('message', 'Message', 'Enter a test message', '')
            ->submit('Send POST Request', ['class' => 'is-info is-large']);

        $formBox = Bulma::Box(View::concat(
            Bulma::Title('🧪 Test POST Method', 5),
            $testForm
        ));

        $buttons = Bulma::Buttons(View::concat(
            Bulma::ButtonLink('🏠 Back to Tests', '/mortingen/TestController', [BulmaClass::IS_PRIMARY]),
            Bulma::ButtonLink('Test GET Only', '/mortingen/TestController/onlyGet', [BulmaClass::IS_SUCCESS]),
            Bulma::ButtonLink('Test POST Only', '/mortingen/TestController/onlyPost', [BulmaClass::IS_INFO])
        ));

        $infoBox = Bulma::Box(View::concat($routeInfo, $methodInfoBox, $formBox, $buttons));

        $content = View::concat(
            $hero,
            Bulma::Section(Bulma::Container($infoBox))
        );

        $this->response->setContentType(\Response\ContentType::TEXT_HTML);
        $this->response->addContent(
            Bulma::Html($content, 'GET & POST Route - Method Testing')
        );
    }

    private function displayMethodTestCards(): View
    {
        $cardsContent = new View(
            '<div class="columns is-multiline">' .

                // GET Only Card
                '<div class="column is-4">' .
                '<div class="card">' .
                '<div class="card-header has-background-success">' .
                '<p class="card-header-title has-text-white">' .
                '<span class="icon"><i class="fas fa-download"></i></span>' .
                '<span>GET Only Route</span>' .
                '</p>' .
                '</div>' .
                '<div class="card-content">' .
                '<div class="content">' .
                '<p><strong>Endpoint:</strong> /mortingen/TestController/onlyGet</p>' .
                '<p><strong>Allowed Methods:</strong> <span class="tag is-success">GET</span></p>' .
                '<p><strong>Test:</strong> Only GET requests should be allowed</p>' .
                '</div>' .
                '</div>' .
                '<div class="card-footer">' .
                '<a href="/mortingen/TestController/onlyGet" class="card-footer-item button is-success">Test GET</a>' .
                '</div>' .
                '</div>' .
                '</div>' .

                // POST Only Card  
                '<div class="column is-4">' .
                '<div class="card">' .
                '<div class="card-header has-background-info">' .
                '<p class="card-header-title has-text-white">' .
                '<span class="icon"><i class="fas fa-upload"></i></span>' .
                '<span>POST Only Route</span>' .
                '</p>' .
                '</div>' .
                '<div class="card-content">' .
                '<div class="content">' .
                '<p><strong>Endpoint:</strong> /mortingen/TestController/onlyPost</p>' .
                '<p><strong>Allowed Methods:</strong> <span class="tag is-info">POST</span></p>' .
                '<p><strong>Test:</strong> Only POST requests should be allowed</p>' .
                '</div>' .
                '</div>' .
                '<div class="card-footer">' .
                '<form method="POST" action="/mortingen/TestController/onlyPost">' .
                '<input type="hidden" name="test" value="post_test">' .
                '<button type="submit" class="card-footer-item button is-info">Test POST</button>' .
                '</form>' .
                '</div>' .
                '</div>' .
                '</div>' .

                // GET & POST Card
                '<div class="column is-4">' .
                '<div class="card">' .
                '<div class="card-header has-background-warning">' .
                '<p class="card-header-title has-text-black">' .
                '<span class="icon"><i class="fas fa-exchange-alt"></i></span>' .
                '<span>GET & POST Route</span>' .
                '</p>' .
                '</div>' .
                '<div class="card-content">' .
                '<div class="content">' .
                '<p><strong>Endpoint:</strong> /mortingen/TestController/getAndPost</p>' .
                '<p><strong>Allowed Methods:</strong> <span class="tag is-success">GET</span> <span class="tag is-info">POST</span></p>' .
                '<p><strong>Test:</strong> Both GET and POST should be allowed</p>' .
                '</div>' .
                '</div>' .
                '<div class="card-footer">' .
                '<a href="/mortingen/TestController/getAndPost" class="card-footer-item button is-warning">Test Both</a>' .
                '</div>' .
                '</div>' .
                '</div>' .

                '</div>'
        );

        return Bulma::Section(
            Bulma::Container($cardsContent)
        );
    }

    private function displayTestResults(): View
    {
        $instructionsContent = new View(
            '<div class="box has-background-dark">' .
                '<h3 class="title is-3">🧪 How to Test Method Restrictions</h3>' .
                '<div class="content">' .
                '<h4 class="title is-4">✅ Expected Behaviors:</h4>' .
                '<ul>' .
                '<li><strong>GET Only Route:</strong> Clicking the link should work (GET request)</li>' .
                '<li><strong>POST Only Route:</strong> Clicking the button should work (POST request)</li>' .
                '<li><strong>GET & POST Route:</strong> Both link and form submission should work</li>' .
                '</ul>' .

                '<h4 class="title is-4">❌ Testing Restrictions:</h4>' .
                '<div class="notification is-warning">' .
                '<p><strong>To test restrictions manually:</strong></p>' .
                '<ul>' .
                '<li>Try accessing <code>/mortingen/TestController/onlyPost</code> via browser (GET) - should get "Method not allowed"</li>' .
                '<li>Use curl or Postman to send wrong method types to endpoints</li>' .
                '<li>Check if HTTP 405 status is returned correctly</li>' .
                '</ul>' .
                '</div>' .

                '<h4 class="title is-4">🔧 Testing Commands:</h4>' .
                '<div class="box has-background-dark has-text-light">' .
                '<p><strong>Test POST restriction with curl:</strong></p>' .
                '<pre><code>curl -X GET http://localhost/mortingen/TestController/onlyPost</code></pre>' .
                '<p><strong>Test GET restriction with curl:</strong></p>' .
                '<pre><code>curl -X POST http://localhost/mortingen/TestController/onlyGet</code></pre>' .
                '</div>' .
                '</div>' .
                '</div>'
        );

        return Bulma::Section(
            Bulma::Container($instructionsContent)
        );
    }

    private function displayPostTestSection(): View
    {
        $postTestForm = (new Form('post_test_form', '/mortingen/TestController/onlyPost', 'POST'))
            ->text('test_data', 'Test Data', 'Enter test data', 'Hello POST!')
            ->textarea('message', 'Message', 'Enter your message', 'This is a POST test message!')
            ->submit('Send POST Request', ['class' => 'is-info is-large']);

        $postTestContent = new View(
            '<div class="columns">' .
                '<div class="column is-half">' .
                '<div class="box">' .
                '<h3 class="title is-4">📤 Direct POST Test</h3>' .
                '<p class="subtitle is-6">Test onlyPost route directly with this form</p>' .
                $postTestForm .
                '</div>' .
                '</div>' .
                '<div class="column is-half">' .
                '<div class="box has-background-warning-dark">' .
                '<h3 class="title is-4">ℹ️ How to Use</h3>' .
                '<div class="content">' .
                '<p><strong>Purpose:</strong></p>' .
                '<ul>' .
                '<li>Test onlyPost route visually</li>' .
                '<li>See POST request in action</li>' .
                '<li>Verify method validation works</li>' .
                '</ul>' .
                '<p><strong>Test Steps:</strong></p>' .
                '<ol>' .
                '<li>Fill out the form on the left</li>' .
                '<li>Click "Send POST Request" button</li>' .
                '<li>Watch onlyPost page display your data!</li>' .
                '</ol>' .
                '<div class="notification is-info">' .
                '<strong>Note:</strong> This form sends a POST request, so onlyPost route will work!' .
                '</div>' .
                '</div>' .
                '</div>' .
                '</div>' .
                '</div>'
        );

        return Bulma::Section(
            Bulma::Container($postTestContent)
        );
    }
}
