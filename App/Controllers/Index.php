<?php

namespace Controllers;

use App;
use Request\Request;
use Session;
use Bulma;
use Controller;

class Index extends Controller
{
    public function index(...$args)
    {
        // var_dump(Request::getPath());
        // var_dump(App::getAbsoluteProjectPath());
        // var_dump(App::getProjectPath());
        $this->response->addContent("Hello! This is Index. " . Index::class);
        $this->response->addContent("<br>Func args: " . implode(", ", $args));
        $this->response->addContent("<br>Args: " . implode(", ", Request::getArgs()));
        $this->response->addContent("<br>Args Assoc: " . print_r(Request::getArgsAssoc(), true));
        $this->response->addContent("<br>Path: " . Request::getPath());
        $this->response->addContent("<br>Path Segs: " . implode(", ", Request::getPathSegments()));
    }

    public function test2()
    {
        $this->response->addContent("Hello! This is test2. " . Index::class);
        $this->response->addContent("<br>Args: " . implode(", ", Request::getArgs()));
        $this->response->addContent("<br>Path: " . Request::getPath());
    }

    public function test($a1, ?int $a2 = null, ?string $a3)
    {
        Session::set("deneme", 123);
        var_dump(Request::getArgsAssoc());
        var_dump(Request::getArgs());
        $this->response->addContent("Test this!");
        $this->response->addContent("---" . Session::get("denem") . "---");
        var_dump($a2);
    }

    public function testcode()
    {
        $captcha = new \Captcha\Captcha(8, 100);
        $img = $captcha->render();
        $this->response->setContentType(\Response\ContentType::IMAGE_PNG);
        $this->response->setContent($img);
    }

    public function bulma()
    {
        Bulma::appendHead(
            \HTML::css(App::getURIRoot() . "/Public/custom.css")
        );

        $left_content = Bulma::Box("This is a box on the left side.", ["has-background-success-light", "has-text-weight-bold", "has-text-danger-dark"]);

        $main_content = Bulma::Box(
            Bulma::Box("This is a box in another box. <b>This sentence is escaped.</b>", ["has-background-info", "has-text-weight-bold", "has-text-success-dark"]),
            ["has-background-link-light"]
        );

        $right_content = Bulma::Box("This is a box on the right side.", ["has-background-danger-light", "has-text-weight-bold", "has-text-info-dark"]);

        $this->response->addContent($this->layout3Cols($left_content, $main_content, $right_content));
    }

    private function layout3Cols($col1, $col2, $col3)
    {
        return Bulma::Html(
            Bulma::Section(
                Bulma::Container(
                    Bulma::Cols(
                        \View::concat(
                            Bulma::Col($col1, [\BulmaClass::IS_3]),
                            Bulma::Col($col2, [\BulmaClass::IS_6]),
                            Bulma::Col($col3, [\BulmaClass::IS_3])
                        )
                    ),
                    [\BulmaClass::IS_FLUID]
                )
            ),
            "Bulma Test in Mortingen Framework"
        );
    }

    public function db()
    {
        $Q = "\DB\Query";

        //$q = $Q::select("*")->from(\DB\Identifier::{"MyTable"}())->where(\DB\Identifier::{"Username"}(), "=", new \DB\Param("my name"));

        $q = $Q::select("*")->from(\DB\Identifier::{"MyTable"}())->where(\DB\Identifier::{"Username"}(), "=", new \DB\Param("my name"))->and(\DB\Identifier::{"Name"}(), "LIKE", new \DB\Param("%search name"))->and($Q::_(\DB\Identifier::{"Name"}())->likeEscape(new \DB\Param("%search name_")));

        echo $q;
        echo "<br>\n";
        print_r($q->getNamedParams());
    }
}
