<?php

namespace Controllers;

use Controller;
use Bulma;
use HTML;
use BulmaClass;
use View;
use Request\Request;
use Request\Method;
use Form;

class TestForm extends Controller
{
    public function index()
    {
        $content = "";
        $alerts = [];
        
        try {
            // Real security escape test
            $content .= $this->displayBeautifulEscapeTest();
            
            $content .= Bulma::Title('🎯 Form Builder Test System', 1);
            $content .= Bulma::Subtitle('Testing comprehensive Form class with all HTML5 input types', 3);
            
            $this->handleFormSubmissions($alerts);
            
            $content .= $this->displayBasicFormTests();
            $content .= $this->displayAllInputTypesTests();
            $content .= $this->displayAdvancedFormTests();
            $content .= $this->displayCodeExamples();
            
        } catch (\Exception $e) {
            $alerts[] = Bulma::Notification(
                '❌ System Error: ' . htmlspecialchars($e->getMessage()),
                false,
                [BulmaClass::IS_DANGER]
            );
        }
        
        $alertsContent = View::concat(...$alerts);
        $pageContent = Bulma::Container(
            new View(
                $alertsContent->__toString() .
                (string)$content .
                '<div class="box has-text-centered">' .
                '<a class="button is-primary" href="/mortingen">🏠 Back to Home</a>' .
                '</div>'
            )
        );
        
        $this->response->setContentType(\Response\ContentType::TEXT_HTML);
        $this->response->setContent(
            Bulma::Html($pageContent, 'Form Builder Test - Mortingen Framework')
        );
    }
    
    private function handleFormSubmissions(&$alerts): void
    {
        if (Request::getMethod() === Method::POST) {
            $action = Request::getDataAll()['action'] ?? 'general_form';
            $formData = Request::getDataAll();
            
            try {
                switch ($action) {
                    case 'login_form':
                        $username = Request::getData('username') ?? '';
                        $password = Request::getData('password') ?? '';
                        $remember = Request::getData('remember') ?? false;
                        
                        $alerts[] = Bulma::Notification(
                            "✅ Login Form: Username='{$username}', Remember=" . ($remember ? 'Yes' : 'No'),
                            false,
                            [BulmaClass::IS_SUCCESS]
                        );
                        break;
                        
                    case 'all_types_form':
                        $typeData = [];
                        foreach ($formData as $key => $value) {
                            if ($key !== 'action') {
                                $typeData[$key] = $value;
                            }
                        }
                        
                        $alerts[] = Bulma::Notification(
                            "✅ All Types Form submitted with " . count($typeData) . " fields!",
                            false,
                            [BulmaClass::IS_SUCCESS]
                        );
                        break;
                        
                    case 'horizontal_form':
                        $name = Request::getData('h_name') ?? '';
                        $email = Request::getData('h_email') ?? '';
                        $country = Request::getData('h_country') ?? '';
                        $gender = Request::getData('h_gender') ?? '';
                        
                        $alerts[] = Bulma::Notification(
                            "✅ Horizontal Form: {$name} ({$email}) from {$country}, Gender: {$gender}",
                            false,
                            [BulmaClass::IS_INFO]
                        );
                        break;
                        
                    case 'advanced_form':
                        $username = Request::getData('adv_username') ?? '';
                        $role = Request::getData('adv_role') ?? '';
                        $terms = Request::getData('adv_terms') ?? false;
                        $marketing = Request::getData('adv_marketing') ?? false;
                        
                        $alerts[] = Bulma::Notification(
                            "✅ Advanced Form: User '{$username}' with role '{$role}', Terms: " . ($terms ? 'Accepted' : 'Not accepted'),
                            false,
                            [BulmaClass::IS_WARNING]
                        );
                        break;
                        
                    default:
                        $alerts[] = Bulma::Notification(
                            '✅ Form submitted successfully! Data: ' . json_encode($formData),
                            false,
                            [BulmaClass::IS_SUCCESS]
                        );
                }
            } catch (\Exception $e) {
                $alerts[] = Bulma::Notification(
                    "❌ Form Error: " . htmlspecialchars($e->getMessage()),
                    false,
                    [BulmaClass::IS_DANGER]
                );
            }
        }
    }
    
    private function displayBeautifulEscapeTest(): string
    {
        // Actually test the escaping functionality with View class
        $dangerousInput = '<script>alert("XSS")</script>';
        $view = new View($dangerousInput);
        $escapedOutput = $view->escape()->__toString();
        $expectedOutput = '&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;';
        
        // Real test - verify the View escape method works correctly
        $isActuallyWorking = ($escapedOutput === $expectedOutput);
        
        return '<div class="box has-background-black-bis mb-5">' .
               '<div class="level">' .
               '<div class="level-left">' .
               '<div class="level-item">' .
               '<div>' .
               '<p class="heading has-text-white">Security Test</p>' .
               '<p class="title is-4 has-text-white">🔒 XSS Protection Test</p>' .
               '</div>' .
               '</div>' .
               '</div>' .
               '<div class="level-right">' .
               '<div class="level-item">' .
               '<span class="tag is-large ' . ($isActuallyWorking ? 'is-success' : 'is-danger') . '">' .
               ($isActuallyWorking ? '✅ WORKING' : '❌ FAILED') .
               '</span>' .
               '</div>' .
               '</div>' .
               '</div>' .
               '<div class="content">' .
               '<p class="has-text-white"><strong>Dangerous Input:</strong></p>' .
               '<div class="box has-background-danger-light">' .
               '<code class="has-text-red">' . htmlspecialchars($dangerousInput) . '</code>' .
               '</div>' .
               '<p class="has-text-white"><strong>After Escaping:</strong></p>' .
               '<div class="box has-background-success-light">' .
               '<code class="has-text-green">' . htmlspecialchars($escapedOutput) . '</code>' .
               '</div>' .
               '<p class="has-text-white"><strong>Expected Result:</strong></p>' .
               '<div class="box has-background-info-light">' .
               '<code class="has-text-red">' . htmlspecialchars($expectedOutput) . '</code>' .
               '</div>' .
               '<div class="notification ' . ($isActuallyWorking ? 'is-success' : 'is-danger') . '">' .
               '<p class="has-text-green"><strong>' .
               ($isActuallyWorking ? 
                   '✓ Escaping is working correctly! Dangerous scripts are neutralized.' : 
                   '✗ WARNING: Escaping failed! This is a security vulnerability.'
               ) .
               '</strong></p>' .
               '</div>' .
               '</div>' .
               '</div>';
    }
    
    private function displayBasicFormTests(): string
    {
        $content = Bulma::Title('📝 Basic Form Tests', 3);
        
        $content .= new View(
            '<div class="columns is-multiline">' .
            
            // Login Form Test
            '<div class="column is-6">' .
            '<div class="box">' .
            '<h6 class="title is-6 has-text-primary">Login Form Test</h6>'
        );
        
        $loginForm = (new Form('login_form', '/TestForm', 'POST'))
            ->hidden('action', 'login_form')
            ->text('username', 'Username', 'Enter your username', '', ['required' => true])
            ->password('password', 'Password', 'Enter your password', '', ['required' => true])
            ->checkbox('remember', 'Remember me')
            ->view(Bulma::Field(
                View::concat(
                    Bulma::Control(Bulma::Button('Login', [BulmaClass::IS_PRIMARY], ['type' => 'submit'])),
                    Bulma::Control(Bulma::Button('Cancel', [BulmaClass::IS_LIGHT], ['type' => 'button']))
                ),
                [BulmaClass::IS_GROUPED]
            ));
        
        $content .= new View($loginForm);
        $content .= new View('</div></div>');
        
        // Registration Form Test
        $content .= new View(
            '<div class="column is-6">' .
            '<div class="box">' .
            '<h6 class="title is-6 has-text-success">Registration Form Test</h6>'
        );
        
        $regForm = (new Form('reg_form', '/TestForm', 'POST'))
            ->hidden('action', 'registration_form')
            ->text('reg_name', 'Full Name', 'Enter your full name', '', ['required' => true])
            ->email('reg_email', 'Email', 'Enter your email', '', ['required' => true])
            ->password('reg_password', 'Password', 'Choose a password', '', ['required' => true, 'minlength' => '6'])
            ->select('reg_country', 'Country', 'Select your country', [
                'us' => 'United States',
                'tr' => 'Turkey',
                'de' => 'Germany',
                'fr' => 'France',
                'uk' => 'United Kingdom',
                'ca' => 'Canada',
                'au' => 'Australia',
                'jp' => 'Japan'
            ])
            ->textarea('reg_bio', 'Bio', 'Tell us about yourself...', '')
            ->checkbox('reg_terms', 'I agree to terms and conditions', '', '1', ['required' => true])
            ->submit('Register', ['class' => 'is-success']);
        
        $content .= new View($regForm);
        $content .= new View('</div></div></div>');
        
        return $content;
    }
    
    private function displayAllInputTypesTests(): string
    {
        $content = Bulma::Title('🔢 All HTML5 Input Types Test', 3);
        
        $content .= new View(
            '<div class="columns">' .
            '<div class="column is-8 is-offset-2">' .
            '<div class="box">' .
            '<h6 class="title is-6 has-text-info">Complete HTML5 Input Types</h6>'
        );
        
        $allTypesForm = (new Form('all_types_form', '/TestForm', 'POST'))
            ->hidden('action', 'all_types_form')
            ->text('text_input', 'Text Input', 'Enter text', 'Default text')
            ->password('password_input', 'Password Input', 'Enter password')
            ->email('email_input', 'Email Input', 'Enter email', 'user@example.com')
            ->number('number_input', 'Number Input', 'Enter number', '42', ['min' => '0', 'max' => '100'])
            ->tel('tel_input', 'Telephone Input', 'Enter phone', '+1-555-123-4567')
            ->url('url_input', 'URL Input', 'Enter URL', 'https://example.com')
            ->search('search_input', 'Search Input', 'Search...', 'search term')
            ->date('date_input', 'Date Input', '', '2025-08-31')
            ->time('time_input', 'Time Input', '', '14:30')
            ->datetime('datetime_input', 'DateTime Input', '', '2025-08-31T14:30')
            ->color('color_input', 'Color Input', '', '#ff5733')
            ->range('range_input', 'Range Input', '', '50', ['min' => '0', 'max' => '100', 'step' => '10', 'class' => 'is-white'])
            ->file('file_input', 'File Input', '', '', ['accept' => '.txt,.pdf,.jpg'])
            ->textarea('textarea_input', 'Textarea Input', 'Enter your message...', 'Default textarea content')
            ->select('select_input', 'Select Input', 'Choose an option', [
                'option1' => 'Option 1',
                'option2' => 'Option 2', 
                'option3' => 'Option 3',
                'option4' => 'Long Option Text to Test Dropdown Width',
                'option5' => 'Another Option'
            ])
            ->checkbox('checkbox_input', 'Checkbox Input')
            ->radio('radio_input', 'Radio Option 1', '', 'value1')
            ->radio('radio_input', 'Radio Option 2', '', 'value2')
            ->submit('Test All Types', ['class' => 'is-success is-large']);
        
        $content .= new View($allTypesForm);
        $content .= new View('</div></div></div>');
        
        return $content;
    }
    
    private function displayAdvancedFormTests(): string
    {
        $content = Bulma::Title('↔️ Advanced Form Features Test', 3);
        
        $content .= new View('<div class="columns is-multiline">');
        
        // Horizontal Form Test
        $content .= new View(
            '<div class="column is-6">' .
            '<div class="box">' .
            '<h6 class="title is-6 has-text-warning">Horizontal Layout Form</h6>'
        );
        
        $horizontalForm = (new Form('horizontal_form', '/TestForm', 'POST', true))
            ->hidden('action', 'horizontal_form')
            ->text('h_name', 'Full Name', 'Enter your full name', '', ['required' => true])
            ->email('h_email', 'Email Address', 'Enter your email', '', ['required' => true])
            ->textarea('h_bio', 'Biography', 'Tell us about yourself...')
            ->select('h_country', 'Country', 'Select your country', [
                'us' => 'United States',
                'tr' => 'Turkey',
                'de' => 'Germany',
                'fr' => 'France',
                'jp' => 'Japan'
            ])
            ->checkbox('h_newsletter', 'Subscribe to newsletter')
            ->radio('h_gender', 'Male', '', 'male')
            ->radio('h_gender', 'Female', '', 'female')
            ->radio('h_gender', 'Other', '', 'other')
            ->submit('Submit Horizontal', ['class' => 'is-warning'])
            ->reset('Clear Form', ['class' => 'is-light']);
        
        $content .= new View($horizontalForm);
        $content .= new View('</div></div>');
        
        // Custom Views Integration Test
        $content .= new View(
            '<div class="column is-6">' .
            '<div class="box">' .
            '<h6 class="title is-6 has-text-danger">Custom Views Integration</h6>'
        );
        
        $customForm = (new Form('advanced_form', '/TestForm', 'POST', false, ['novalidate' => true]))
            ->hidden('action', 'advanced_form')
            ->view(Bulma::Notification('This form demonstrates custom view integration', false, [BulmaClass::IS_INFO]))
            ->text('adv_username', 'Username', 'Choose a username', '', ['minlength' => '3', 'maxlength' => '20', 'pattern' => '[a-zA-Z0-9_]+'])
            ->password('adv_password', 'Password', 'Enter password', '', ['minlength' => '8', 'required' => true])
            ->password('adv_confirm', 'Confirm Password', 'Confirm your password', '', ['minlength' => '8', 'required' => true])
            ->view(new View('<hr class="my-4">'))
            ->select('adv_role', 'User Role', 'Select a role', [
                'admin' => 'Administrator',
                'editor' => 'Editor',
                'viewer' => 'Viewer',
                'guest' => 'Guest'
            ], ['required' => true])
            ->checkbox('adv_terms', 'I agree to the terms and conditions', '', '1', ['required' => true])
            ->checkbox('adv_marketing', 'I want to receive marketing emails')
            ->view(Bulma::Field(
                View::concat(
                    Bulma::Control(Bulma::Button('Create Account', [BulmaClass::IS_SUCCESS, BulmaClass::IS_LARGE], ['type' => 'submit'])),
                    Bulma::Control(Bulma::Button('Go Back', [BulmaClass::IS_LIGHT, BulmaClass::IS_LARGE], ['type' => 'button', 'onclick' => 'history.back()']))
                ),
                [BulmaClass::IS_GROUPED]
            ))
            ->view(Bulma::Help('All fields marked with * are required', [BulmaClass::IS_DANGER]));
        
        $content .= new View($customForm);
        $content .= new View('</div></div></div>');
        
        return $content;
    }
    
    private function displayCodeExamples(): string
    {
        $content = Bulma::Title('💻 Code Examples & Usage', 3);
        
        $basicExample = htmlspecialchars(
            '$loginForm = (new Form())' . "\n" .
            '    ->text("username", "Username", "Enter username")' . "\n" .
            '    ->password("password", "Password", "Enter password")' . "\n" .
            '    ->checkbox("remember", "Remember me")' . "\n" .
            '    ->submit("Login");' . "\n\n" .
            'echo Bulma::Box($loginForm);'
        );
        
        $horizontalExample = htmlspecialchars(
            '$form = (new Form("my_form", "/submit", "POST", true))' . "\n" .
            '    ->text("name", "Full Name", "Enter name")' . "\n" .
            '    ->email("email", "Email", "Enter email")' . "\n" .
            '    ->textarea("message", "Message", "Your message")' . "\n" .
            '    ->select("country", "Country", "Choose", ["us" => "USA", "tr" => "Turkey"])' . "\n" .
            '    ->checkbox("newsletter", "Subscribe to newsletter")' . "\n" .
            '    ->submit("Send Message");'
        );
        
        $advancedExample = htmlspecialchars(
            '$form = (new Form("contact", "/contact", "POST", false, ["class" => "custom"]))' . "\n" .
            '    ->view(Bulma::Notification("Please fill all fields", false, [BulmaClass::IS_INFO]))' . "\n" .
            '    ->text("name", "Name", "", "", ["required" => true, "maxlength" => "50"])' . "\n" .
            '    ->file("attachment", "Attachment", "", "", ["accept" => ".pdf,.doc"])' . "\n" .
            '    ->hidden("csrf_token", "abc123")' . "\n" .
            '    ->view(new View("<hr>"))' . "\n" .
            '    ->submit("Submit", ["class" => "is-primary"]);'
        );
        
        $allTypesExample = htmlspecialchars(
            '// All HTML5 input types supported:' . "\n" .
            '$form->text()      // <input type="text">      ' . "\n" .
            '$form->password()  // <input type="password">  ' . "\n" .
            '$form->email()     // <input type="email">     ' . "\n" .
            '$form->number()    // <input type="number">    ' . "\n" .
            '$form->tel()       // <input type="tel">       ' . "\n" .
            '$form->url()       // <input type="url">       ' . "\n" .
            '$form->search()    // <input type="search">    ' . "\n" .
            '$form->date()      // <input type="date">      ' . "\n" .
            '$form->time()      // <input type="time">      ' . "\n" .
            '$form->datetime()  // <input type="datetime-local">' . "\n" .
            '$form->color()     // <input type="color">     ' . "\n" .
            '$form->range()     // <input type="range">     ' . "\n" .
            '$form->file()      // <input type="file">      ' . "\n" .
            '$form->hidden()    // <input type="hidden">    ' . "\n" .
            '$form->textarea()  // <textarea>              ' . "\n" .
            '$form->select()    // <select>                ' . "\n" .
            '$form->checkbox()  // <input type="checkbox">  ' . "\n" .
            '$form->radio()     // <input type="radio">     ' . "\n" .
            '$form->button()    // <button type="button">   ' . "\n" .
            '$form->submit()    // <button type="submit">   ' . "\n" .
            '$form->reset()     // <button type="reset">    '
        );
        
        $content .= new View(
            '<div class="columns is-multiline">' .
            '<div class="column is-6">' .
            '<div class="box">' .
            '<h5 class="title is-5 has-text-primary">Basic Usage</h5>' .
            '<pre class="has-background-light p-3"><code>' . $basicExample . '</code></pre>' .
            '</div>' .
            '</div>' .
            '<div class="column is-6">' .
            '<div class="box">' .
            '<h5 class="title is-5 has-text-warning">Horizontal Layout</h5>' .
            '<pre class="has-background-light p-3"><code>' . $horizontalExample . '</code></pre>' .
            '</div>' .
            '</div>' .
            '<div class="column is-6">' .
            '<div class="box">' .
            '<h5 class="title is-5 has-text-info">Advanced Features</h5>' .
            '<pre class="has-background-light p-3"><code>' . $advancedExample . '</code></pre>' .
            '</div>' .
            '</div>' .
            '<div class="column is-6">' .
            '<div class="box">' .
            '<h5 class="title is-5 has-text-success">All Input Types</h5>' .
            '<pre class="has-background-light p-3" style="font-size: 0.8em;"><code>' . $allTypesExample . '</code></pre>' .
            '</div>' .
            '</div>' .
            '</div>'
        );
        
        // Form Features Summary
        $content .= new View(
            '<div class="box has-background-info-light">' .
            '<h5 class="title is-5 has-text-info">✨ Form Class Features</h5>' .
            '<div class="columns">' .
            '<div class="column">' .
            '<ul>' .
            '<li>✅ All HTML5 input types supported</li>' .
            '<li>✅ Automatic ID generation (form_id + "_" + element_name)</li>' .
            '<li>✅ Proper label association with "for" attribute</li>' .
            '<li>✅ Bulma CSS framework integration</li>' .
            '<li>✅ Method chaining for easy form building</li>' .
            '</ul>' .
            '</div>' .
            '<div class="column">' .
            '<ul>' .
            '<li>✅ Horizontal and vertical layouts</li>' .
            '<li>✅ Custom View integration with view() method</li>' .
            '<li>✅ Attribute handling (both key-value and boolean)</li>' .
            '<li>✅ Inherits from View class for string conversion</li>' .
            '<li>✅ Automatic HTML escaping for security</li>' .
            '</ul>' .
            '</div>' .
            '</div>' .
            '</div>'
        );
        
        return $content;
    }
}