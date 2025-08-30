<?php

class Bulma
{
    protected static $prepend_head = [];
    protected static $append_head = [];
    protected static $prepend_body = [];
    protected static $append_body = [];

    public static function prependHead($content)
    {
        $content = (new View($content))->escape();
        // Add the content to the beginning of the $prepend_head array
        array_unshift(static::$prepend_head, $content);
    }

    public static function appendHead($content)
    {
        $content = (new View($content))->escape();
        // Add the content to the end of the $append_head array
        static::$append_head[] = $content;
    }

    public static function prependBody($content)
    {
        $content = (new View($content))->escape();
        // Add the content to the beginning of the $prepend_body array
        array_unshift(static::$prepend_body, $content);
    }

    public static function appendBody($content)
    {
        $content = (new View($content))->escape();
        // Add the content to the end of the $append_body array
        static::$append_body[] = $content;
    }

    public static function Html($content, $title = "Mortingen Framework"): View
    {
        $content = (new View($content))->escape();
        $title = (new View($title))->escape();

        $baseHtml = "<!DOCTYPE html>" . PHP_EOL .
            HTML::html(
                View::concat(
                    HTML::head(
                        // It is required to use View::concat(...) to concatenate consecutive View objects or strings.
                        View::concat(
                            View::concat(...static::$prepend_head),
                            HTML::meta(["charset" => "UTF-8"]),
                            HTML::meta(["name" => "viewport", "content" => "width=device-width, initial-scale=1.0"]),
                            HTML::title($title),
                            HTML::css(App::getURIRoot() . "/Public/bulma/css/bulma.min.css"),
                            View::concat(...static::$append_head)
                        )
                    ),
                    HTML::body($content)
                )
            );

        return new View($baseHtml);
    }

    public static function Section($content): View
    {
        $content = (new View($content))->escape();

        return new View(HTML::section($content, ["class" => BulmaClass::SECTION]));
    }

    public static function Container($content, array $classes = []): View
    {
        $content = (new View($content))->escape();

        array_unshift($classes, BulmaClass::CONTAINER);
        $class = implode(" ", $classes);
        $class = (new View($class))->escape();

        return new View(HTML::div($content, ["class" => $class]));
    }

    public static function Cols($content, array $classes = []): View
    {
        $content = (new View($content))->escape();

        array_unshift($classes, BulmaClass::COLUMNS);
        $class = implode(" ", $classes);
        $class = (new View($class))->escape();

        return new View(HTML::div($content, ["class" => $class]));
    }

    public static function Col($content, array $classes = []): View
    {
        $content = (new View($content))->escape();

        array_unshift($classes, BulmaClass::COLUMN);
        $class = implode(" ", $classes);
        $class = (new View($class))->escape();

        return new View(HTML::div($content, ["class" => $class]));
    }

    public static function Box($content, array $classes = []): View
    {
        $content = (new View($content))->escape();

        array_unshift($classes, BulmaClass::BOX);
        $class = implode(" ", $classes);
        $class = (new View($class))->escape();

        return new View(HTML::div($content, ["class" => $class]));
    }

    // Layouts

    public static function Media($content, array $classes = []): View
    {
        $content = (new View($content))->escape();
        array_unshift($classes, BulmaClass::MEDIA);
        return new View(HTML::article($content, ["class" => implode(" ", $classes)]));
    }

    public static function MediaLeft($content, array $classes = []): View
    {
        $content = (new View($content))->escape();
        array_unshift($classes, BulmaClass::MEDIA_LEFT);
        return new View(HTML::figure($content, ["class" => implode(" ", $classes)]));
    }

    public static function MediaContent($content, array $classes = []): View
    {
        $content = (new View($content))->escape();
        array_unshift($classes, BulmaClass::MEDIA_CONTENT);
        return new View(HTML::div($content, ["class" => implode(" ", $classes)]));
    }

    public static function MediaRight($content, array $classes = []): View
    {
        $content = (new View($content))->escape();
        array_unshift($classes, BulmaClass::MEDIA_RIGHT);
        return new View(HTML::div($content, ["class" => implode(" ", $classes)]));
    }

    public static function Level($left, $right, array $classes = []): View
    {
        $left = (new View($left))->escape();
        $right = (new View($right))->escape();

        array_unshift($classes, BulmaClass::LEVEL);
        $levelLeft = HTML::div($left, ["class" => BulmaClass::LEVEL_LEFT]);
        $levelRight = HTML::div($right, ["class" => BulmaClass::LEVEL_RIGHT]);

        return new View(HTML::nav(View::concat($levelLeft, $levelRight), ["class" => implode(" ", $classes)]));
    }

    public static function LevelItem($content, array $classes = []): View
    {
        $content = (new View($content))->escape();
        array_unshift($classes, BulmaClass::LEVEL_ITEM);
        return new View(HTML::div($content, ["class" => implode(" ", $classes)]));
    }

    public static function Hero($content, array $classes = []): View
    {
        $content = (new View($content))->escape();
        array_unshift($classes, BulmaClass::HERO);
        $class = implode(" ", $classes);
        return new View(HTML::section($content, ["class" => (new View($class))->escape()]));
    }

    public static function HeroHead($content, array $classes = []): View
    {
        $content = (new View($content))->escape();
        array_unshift($classes, BulmaClass::HERO_HEAD);
        $class = implode(" ", $classes);
        return new View(HTML::div($content, ["class" => (new View($class))->escape()]));
    }

    public static function HeroBody($content, array $classes = []): View
    {
        $content = (new View($content))->escape();
        array_unshift($classes, BulmaClass::HERO_BODY);
        $class = implode(" ", $classes);
        return new View(HTML::div($content, ["class" => (new View($class))->escape()]));
    }

    public static function HeroFoot($content, array $classes = []): View
    {
        $content = (new View($content))->escape();
        array_unshift($classes, BulmaClass::HERO_FOOT);
        $class = implode(" ", $classes);
        return new View(HTML::div($content, ["class" => (new View($class))->escape()]));
    }

    public static function Footer($content, array $classes = []): View
    {
        $content = (new View($content))->escape();
        array_unshift($classes, BulmaClass::FOOTER);
        $class = implode(" ", $classes);
        return new View(HTML::footer($content, ["class" => (new View($class))->escape()]));
    }

    // Elements

    public static function Button($text, array $classes = [], array $attributes = []): View
    {
        $text = (new View($text))->escape();
        array_unshift($classes, BulmaClass::BUTTON);
        $attributes['class'] = implode(" ", $classes);
        return new View(HTML::button($text, $attributes));
    }

    public static function ButtonLink($text, $href = '#', array $classes = [], array $attributes = []): View
    {
        $text = (new View($text))->escape();
        $href = (new View($href))->escape();
        array_unshift($classes, BulmaClass::BUTTON);
        $attributes['class'] = implode(" ", $classes);
        $attributes['href'] = $href;
        return new View(HTML::a($text, $attributes));
    }

    public static function Buttons($content, array $classes = []): View
    {
        $content = (new View($content))->escape();
        array_unshift($classes, BulmaClass::BUTTONS);
        $class = implode(" ", $classes);
        return new View(HTML::div($content, ["class" => (new View($class))->escape()]));
    }

    public static function Content($content, array $classes = [], bool $isRawHtml = false): View
    {
        if (!$isRawHtml)
        {
            $content = (new View($content))->escape();
        }

        array_unshift($classes, BulmaClass::CONTENT);
        $class = implode(" ", $classes);
        return new View(HTML::div($content, ["class" => (new View($class))->escape()]));
    }

    public static function Icon($iconClasses, array $classes = []): View
    {
        $iconClasses = (new View($iconClasses))->escape(); // e.g. "fas fa-home"
        array_unshift($classes, BulmaClass::ICON);
        $class = implode(" ", $classes);
        $icon = HTML::i("", ["class" => $iconClasses]);
        return new View(HTML::span($icon, ["class" => (new View($class))->escape()]));
    }

    public static function Image($src, $alt = "", array $containerClasses = [], array $imgAttributes = []): View
    {
        $src = (new View($src))->escape();
        $alt = (new View($alt))->escape();
        array_unshift($containerClasses, BulmaClass::IMAGE);
        $class = implode(" ", $containerClasses);
        $imgAttributes['src'] = $src;
        $imgAttributes['alt'] = $alt;
        $img = HTML::img($imgAttributes);
        return new View(HTML::figure($img, ["class" => (new View($class))->escape()]));
    }

    public static function Notification($content, bool $closable = false, array $classes = []): View
    {
        $content = (new View($content))->escape();
        array_unshift($classes, BulmaClass::NOTIFICATION);
        $class = implode(" ", $classes);

        if ($closable)
        {
            $deleteButton = HTML::button("", ["class" => BulmaClass::DELETE]);
            $content = View::concat($deleteButton, $content);
        }

        return new View(HTML::div($content, ["class" => (new View($class))->escape()]));
    }

    public static function Progress($value, $max = "100", array $classes = []): View
    {
        $value = (new View($value))->escape();
        $max = (new View($max))->escape();
        array_unshift($classes, BulmaClass::PROGRESS);
        $class = implode(" ", $classes);
        return new View(HTML::progress($value, ["class" => (new View($class))->escape(), "value" => $value, "max" => $max]));
    }

    public static function Table($head, $body, array $classes = [], bool $isBordered = false, bool $isStriped = false, bool $isNarrow = false, bool $isHoverable = false, bool $isFullwidth = false): View
    {
        $head = (new View($head))->escape();
        $body = (new View($body))->escape();
        array_unshift($classes, BulmaClass::TABLE);
        if ($isBordered) $classes[] = BulmaClass::IS_BORDERED;
        if ($isStriped) $classes[] = BulmaClass::IS_STRIPED;
        if ($isNarrow) $classes[] = BulmaClass::IS_NARROW;
        if ($isHoverable) $classes[] = BulmaClass::IS_HOVERABLE;
        if ($isFullwidth) $classes[] = BulmaClass::IS_FULLWIDTH;
        $class = implode(" ", $classes);
        $thead = HTML::thead($head);
        $tbody = HTML::tbody($body);
        $table = HTML::table(View::concat($thead, $tbody), ["class" => (new View($class))->escape()]);
        return new View(HTML::div($table, ["class" => BulmaClass::TABLE_CONTAINER]));
    }

    public static function Tag($content, array $classes = []): View
    {
        $content = (new View($content))->escape();
        array_unshift($classes, BulmaClass::TAG);
        $class = implode(" ", $classes);
        return new View(HTML::span($content, ["class" => (new View($class))->escape()]));
    }

    public static function Tags($content, array $classes = []): View
    {
        $content = (new View($content))->escape();
        array_unshift($classes, BulmaClass::TAGS);
        $class = implode(" ", $classes);
        return new View(HTML::div($content, ["class" => (new View($class))->escape()]));
    }

    public static function Title($content, $size = "3", array $classes = []): View
    {
        $content = (new View($content))->escape();
        $intSize = intval($size);
        if ($intSize < 1 || $intSize > 6)
        {
            $intSize = 3;
        }

        array_unshift($classes, BulmaClass::TITLE);
        $classes[] = "is-" . $intSize;

        $class = implode(" ", $classes);
        $tag = 'h' . $intSize;
        return new View(HTML::$tag($content, ["class" => (new View($class))->escape()]));
    }

    public static function Subtitle($content, $size = "5", array $classes = []): View
    {
        $content = (new View($content))->escape();
        $intSize = intval($size);
        if ($intSize < 1 || $intSize > 6)
        {
            $intSize = 5;
        }

        array_unshift($classes, BulmaClass::SUBTITLE);
        $classes[] = "is-" . $intSize;

        $class = implode(" ", $classes);
        $tag = 'h' . $intSize;
        return new View(HTML::$tag($content, ["class" => (new View($class))->escape()]));
    }

    // Components
    public static function Breadcrumb($items, array $classes = [], array $attributes = []): View
    {
        $listItems = "";
        foreach ($items as $item)
        {
            $itemText = (new View($item['text']))->escape();
            $itemLink = isset($item['href']) ? (new View($item['href']))->escape() : '#';
            $isActive = isset($item['active']) && $item['active'];
            $liClass = $isActive ? BulmaClass::IS_ACTIVE : '';
            $link = HTML::a($itemText, ['href' => $itemLink]);
            $listItems = View::concat($listItems, HTML::li($link, ['class' => $liClass]));
        }
        array_unshift($classes, BulmaClass::BREADCRUMB);
        $attributes['class'] = implode(" ", $classes);
        $attributes['aria-label'] = 'breadcrumbs';
        $ul = HTML::ul($listItems);
        return new View(HTML::nav($ul, $attributes));
    }

    public static function Card($content, array $classes = []): View
    {
        $content = (new View($content))->escape();
        array_unshift($classes, BulmaClass::CARD);
        $class = implode(" ", $classes);
        return new View(HTML::div($content, ["class" => (new View($class))->escape()]));
    }

    public static function CardHeader($content, array $classes = []): View
    {
        $content = (new View($content))->escape();
        array_unshift($classes, BulmaClass::CARD_HEADER);
        $class = implode(" ", $classes);
        return new View(HTML::header($content, ["class" => (new View($class))->escape()]));
    }

    public static function CardHeaderTitle($title, array $classes = []): View
    {
        $title = (new View($title))->escape();
        array_unshift($classes, BulmaClass::CARD_HEADER_TITLE);
        $class = implode(" ", $classes);
        return new View(HTML::p($title, ["class" => (new View($class))->escape()]));
    }

    public static function CardImage($src, $alt = "", array $containerClasses = [], array $imgAttributes = []): View
    {
        array_unshift($containerClasses, BulmaClass::CARD_IMAGE);
        return self::Image($src, $alt, $containerClasses, $imgAttributes);
    }

    public static function CardContent($content, array $classes = []): View
    {
        $content = (new View($content))->escape();
        array_unshift($classes, BulmaClass::CARD_CONTENT);
        $class = implode(" ", $classes);
        return new View(HTML::div($content, ["class" => (new View($class))->escape()]));
    }

    public static function CardFooter($content, array $classes = []): View
    {
        $content = (new View($content))->escape();
        array_unshift($classes, BulmaClass::CARD_FOOTER);
        $class = implode(" ", $classes);
        return new View(HTML::footer($content, ["class" => (new View($class))->escape()]));
    }

    public static function CardFooterItem($content, $isLink = true, $href = '#', array $classes = []): View
    {
        $content = (new View($content))->escape();
        $href = (new View($href))->escape();
        array_unshift($classes, BulmaClass::CARD_FOOTER_ITEM);
        $class = implode(" ", $classes);
        if ($isLink)
        {
            return new View(HTML::a($content, ["href" => $href, "class" => (new View($class))->escape()]));
        }
        return new View(HTML::p($content, ["class" => (new View($class))->escape()]));
    }

    public static function Message($header, $body, array $classes = [], bool $closable = false): View
    {
        $header = (new View($header))->escape();
        $body = (new View($body))->escape();
        array_unshift($classes, BulmaClass::MESSAGE);
        $class = implode(" ", $classes);
        $messageHeader = self::MessageHeader($header, $closable);
        $messageBody = self::MessageBody($body);
        return new View(HTML::article(View::concat($messageHeader, $messageBody), ["class" => (new View($class))->escape()]));
    }

    public static function MessageHeader($content, bool $closable = false): View
    {
        $content = (new View($content))->escape();
        if ($closable)
        {
            $deleteButton = HTML::button("", ["class" => BulmaClass::DELETE, "aria-label" => "delete"]);
            $content = View::concat($content, $deleteButton);
        }
        return new View(HTML::div($content, ["class" => BulmaClass::MESSAGE_HEADER]));
    }

    public static function MessageBody($content): View
    {
        $content = (new View($content))->escape();
        return new View(HTML::div($content, ["class" => BulmaClass::MESSAGE_BODY]));
    }

    public static function Modal($content, array $classes = []): View
    {
        $content = (new View($content))->escape();
        array_unshift($classes, BulmaClass::MODAL);
        $class = implode(" ", $classes);
        $background = HTML::div("", ["class" => BulmaClass::MODAL_BACKGROUND]);
        $modalContent = HTML::div($content, ["class" => BulmaClass::MODAL_CONTENT]);
        $closeButton = HTML::button("", ["class" => BulmaClass::MODAL_CLOSE, "aria-label" => "close"]);
        return new View(HTML::div(View::concat($background, $modalContent, $closeButton), ["class" => (new View($class))->escape()]));
    }

    public static function ModalCard($head, $body, $foot, array $classes = []): View
    {
        $head = (new View($head))->escape();
        $body = (new View($body))->escape();
        $foot = (new View($foot))->escape();
        array_unshift($classes, BulmaClass::MODAL);
        $class = implode(" ", $classes);

        $cardHead = HTML::header(View::concat(HTML::p($head, ["class" => BulmaClass::MODAL_CARD_TITLE]), HTML::button("", ["class" => BulmaClass::DELETE, "aria-label" => "close"])), ["class" => BulmaClass::MODAL_CARD_HEAD]);
        $cardBody = HTML::section($body, ["class" => BulmaClass::MODAL_CARD_BODY]);
        $cardFoot = HTML::footer($foot, ["class" => BulmaClass::MODAL_CARD_FOOT]);

        $modalCard = HTML::div(View::concat($cardHead, $cardBody, $cardFoot), ["class" => BulmaClass::MODAL_CARD]);
        $background = HTML::div("", ["class" => BulmaClass::MODAL_BACKGROUND]);

        return new View(HTML::div(View::concat($background, $modalCard), ["class" => (new View($class))->escape()]));
    }

    public static function Dropdown($trigger, $content, array $classes = []): View
    {
        $trigger = (new View($trigger))->escape();
        $content = (new View($content))->escape();

        array_unshift($classes, BulmaClass::DROPDOWN);
        // JS kullanmadığımız için üzerine gelince açılan bir örnek yapalım.
        $classes[] = BulmaClass::IS_HOVERABLE;

        $triggerDiv = HTML::div($trigger, ["class" => BulmaClass::DROPDOWN_TRIGGER]);
        $menuContent = HTML::div($content, ["class" => BulmaClass::DROPDOWN_CONTENT]);
        $menuDiv = HTML::div($menuContent, ["class" => BulmaClass::DROPDOWN_MENU]);

        return new View(HTML::div(View::concat($triggerDiv, $menuDiv), ["class" => implode(" ", $classes)]));
    }

    public static function Navbar($brand, $menu, array $classes = [], array $attributes = []): View
    {
        $brand = (new View($brand))->escape();
        $menu = (new View($menu))->escape();
        array_unshift($classes, BulmaClass::NAVBAR);
        $attributes['class'] = implode(" ", $classes);
        $attributes['role'] = 'navigation';
        $attributes['aria-label'] = 'main navigation';
        $container = self::Container(View::concat($brand, $menu));
        return new View(HTML::nav($container, $attributes));
    }

    public static function NavbarBrand($content): View
    {
        $content = (new View($content))->escape();
        return new View(HTML::div($content, ["class" => BulmaClass::NAVBAR_BRAND]));
    }

    public static function NavbarBurger(string $target): View
    {
        $target = (new View($target))->escape();
        $spans = View::concat(HTML::span(['aria-hidden' => 'true']), HTML::span(['aria-hidden' => 'true']), HTML::span(['aria-hidden' => 'true']));
        return new View(HTML::a($spans, [
            "role" => "button",
            "class" => BulmaClass::NAVBAR_BURGER,
            "aria-label" => "menu",
            "aria-expanded" => "false",
            "data-target" => $target
        ]));
    }

    public static function NavbarMenu($startContent, $endContent, string $id, array $classes = []): View
    {
        $startContent = (new View($startContent))->escape();
        $endContent = (new View($endContent))->escape();
        $id = (new View($id))->escape();
        array_unshift($classes, BulmaClass::NAVBAR_MENU);
        $class = implode(" ", $classes);
        $start = HTML::div($startContent, ["class" => BulmaClass::NAVBAR_START]);
        $end = HTML::div($endContent, ["class" => BulmaClass::NAVBAR_END]);
        return new View(HTML::div(View::concat($start, $end), ["id" => $id, "class" => (new View($class))->escape()]));
    }

    public static function NavbarItem($content, $isLink = true, $href = '#', array $classes = []): View
    {
        $content = (new View($content))->escape();
        $href = (new View($href))->escape();
        array_unshift($classes, BulmaClass::NAVBAR_ITEM);
        $class = implode(" ", $classes);
        if ($isLink)
        {
            return new View(HTML::a($content, ["href" => $href, "class" => (new View($class))->escape()]));
        }
        return new View(HTML::div($content, ["class" => (new View($class))->escape()]));
    }

    public static function NavbarLink($text, array $classes = []): View
    {
        $text = (new View($text))->escape();
        array_unshift($classes, BulmaClass::NAVBAR_LINK);
        $class = implode(" ", $classes);
        return new View(HTML::a($text, ["class" => (new View($class))->escape()]));
    }

    public static function NavbarDropdown($link, $content, array $classes = []): View
    {
        $link = (new View($link))->escape();
        $content = (new View($content))->escape();
        array_unshift($classes, BulmaClass::HAS_DROPDOWN, BulmaClass::IS_HOVERABLE);
        $item = self::NavbarItem(View::concat(
            $link,
            HTML::div($content, ["class" => BulmaClass::NAVBAR_DROPDOWN])
        ), false, '#', $classes);
        return new View($item);
    }

    public static function NavbarDivider(array $classes = []): View
    {
        array_unshift($classes, BulmaClass::NAVBAR_DIVIDER);
        $class = implode(" ", $classes);
        return new View(HTML::hr([], ["class" => (new View($class))->escape()]));
    }


    public static function Tabs($content, array $classes = []): View
    {
        $content = (new View($content))->escape();
        array_unshift($classes, BulmaClass::TABS);
        return new View(HTML::div($content, ["class" => implode(" ", $classes)]));
    }

    // Form
    public static function Field($content, array $classes = []): View
    {
        $content = (new View($content))->escape();
        array_unshift($classes, BulmaClass::FIELD);
        $class = implode(" ", $classes);
        return new View(HTML::div($content, ["class" => (new View($class))->escape()]));
    }

    public static function Label($text, array $classes = []): View
    {
        $text = (new View($text))->escape();
        array_unshift($classes, BulmaClass::LABEL);
        $class = implode(" ", $classes);
        return new View(HTML::label($text, ["class" => (new View($class))->escape()]));
    }

    public static function Control($content, array $classes = []): View
    {
        $content = (new View($content))->escape();
        array_unshift($classes, BulmaClass::CONTROL);
        $class = implode(" ", $classes);
        return new View(HTML::div($content, ["class" => (new View($class))->escape()]));
    }

    public static function Input(array $attributes = [], array $classes = []): View
    {
        array_unshift($classes, BulmaClass::INPUT);
        $attributes['class'] = implode(" ", $classes);
        return new View(HTML::input($attributes));
    }

    public static function Textarea($content = "", array $attributes = [], array $classes = []): View
    {
        $content = (new View($content))->escape();
        array_unshift($classes, BulmaClass::TEXTAREA);
        $attributes['class'] = implode(" ", $classes);
        return new View(HTML::textarea($content, $attributes));
    }

    public static function Select($options, array $attributes = [], array $classes = []): View
    {
        $options = (new View($options))->escape();
        array_unshift($classes, BulmaClass::SELECT);
        $class = implode(" ", $classes);
        $select = HTML::select($options, $attributes);
        return new View(HTML::div($select, ["class" => (new View($class))->escape()]));
    }

    public static function Checkbox($label, array $attributes = [], array $classes = []): View
    {
        $label = (new View($label))->escape();
        array_unshift($classes, BulmaClass::CHECKBOX);
        $attributes['type'] = 'checkbox';
        $input = HTML::input($attributes);
        $content = View::concat($input, " ", $label);
        return new View(HTML::label($content, ["class" => implode(" ", $classes)]));
    }

    public static function Radio($label, array $attributes = [], array $classes = []): View
    {
        $label = (new View($label))->escape();
        array_unshift($classes, BulmaClass::RADIO);
        $attributes['type'] = 'radio';
        $input = HTML::input($attributes);
        $content = View::concat($input, " ", $label);
        return new View(HTML::label($content, ["class" => implode(" ", $classes)]));
    }

    public static function Help($text, array $classes = []): View
    {
        $text = (new View($text))->escape();
        array_unshift($classes, BulmaClass::HELP);
        $class = implode(" ", $classes);
        return new View(HTML::p($text, ["class" => (new View($class))->escape()]));
    }

    public static function File($label, $inputName, array $classes = []): View
    {
        $label = (new View($label))->escape();
        $inputName = (new View($inputName))->escape();

        array_unshift($classes, BulmaClass::FILE);

        $fileInput = HTML::input(["type" => "file", "name" => $inputName, "class" => BulmaClass::FILE_INPUT]);
        $uploadIcon = HTML::span(Bulma::Icon('fas fa-upload'), ["class" => BulmaClass::FILE_ICON]);
        $fileLabelSpan = HTML::span($label, ["class" => BulmaClass::FILE_LABEL]);
        $fileCta = HTML::span(View::concat($uploadIcon, $fileLabelSpan), ["class" => BulmaClass::FILE_CTA]);
        $fileNameSpan = HTML::span("No file selected", ["class" => BulmaClass::FILE_NAME]);
        $classes[] = 'has-name';

        $labelElement = HTML::label(View::concat($fileInput, $fileCta, $fileNameSpan), ["class" => implode(" ", $classes)]);

        return new View($labelElement);
    }

    public static function Panel($content, array $classes = []): View
    {
        $content = (new View($content))->escape();
        array_unshift($classes, BulmaClass::PANEL);
        $class = implode(" ", $classes);
        return new View(HTML::nav($content, ["class" => (new View($class))->escape()]));
    }

    public static function PanelHeading($content, array $classes = []): View
    {
        $content = (new View($content))->escape();
        array_unshift($classes, BulmaClass::PANEL_HEADING);
        $class = implode(" ", $classes);
        return new View(HTML::p($content, ["class" => (new View($class))->escape()]));
    }

    public static function PanelBlock($content, $isLink = false, $href = '#', array $classes = []): View
    {
        $content = (new View($content))->escape();
        array_unshift($classes, BulmaClass::PANEL_BLOCK);
        $class = implode(" ", $classes);
        if ($isLink)
        {
            $href = (new View($href))->escape();
            return new View(HTML::a($content, ["href" => $href, "class" => (new View($class))->escape()]));
        }
        return new View(HTML::div($content, ["class" => (new View($class))->escape()]));
    }

    public static function PanelTabs($content, array $classes = []): View
    {
        $content = (new View($content))->escape();
        array_unshift($classes, BulmaClass::PANEL_TABS);
        $class = implode(" ", $classes);
        return new View(HTML::div($content, ["class" => (new View($class))->escape()]));
    }

    public static function PanelIcon($iconClasses, array $classes = []): View
    {
        $iconClasses = (new View($iconClasses))->escape();
        array_unshift($classes, BulmaClass::PANEL_ICON);
        $class = implode(" ", $classes);
        $icon = HTML::i("", ["class" => $iconClasses, 'aria-hidden' => 'true']);
        return new View(HTML::span($icon, ["class" => (new View($class))->escape()]));
    }
}
