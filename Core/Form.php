<?php

class Form extends View
{
    private string $formId;
    private string $action;
    private string $method;
    private bool $isHorizontal;
    private array $attributes;
    private array $elements = [];

    public function __construct(
        string $id = '',
        string $action = '',
        string $method = '',
        bool $isHorizontal = false,
        array $attributes = []
    )
    {
        $this->formId = !empty($id) ? (new View($id))->escape()->__toString() : 'form_' . uniqid();
        $this->action = (new View($action))->escape()->__toString();
        $this->method = !empty($method) ? strtoupper((new View($method))->escape()->__toString()) : 'POST';
        $this->isHorizontal = $isHorizontal;
        $this->attributes = $this->processAttributes($attributes);

        parent::__construct('');
    }

    protected function createInputAttributes(string $type, string $name, string $placeholder = '', string $value = '', array $attributes = []): array
    {
        $elementId = $this->generateElementId($name);
        return array_merge([
            'type' => $type,
            'name' => (new View($name))->escape()->__toString(),
            'id' => $elementId,
            'placeholder' => (new View($placeholder))->escape()->__toString(),
            'value' => (new View($value))->escape()->__toString()
        ], $this->processAttributes($attributes));
    }

    protected function createInputElement(string $type, string $name, string $label = '', string $placeholder = '', string $value = '', array $attributes = []): self
    {
        $elementId = $this->generateElementId($name);
        $inputAttributes = $this->createInputAttributes($type, $name, $placeholder, $value, $attributes);

        $input = Bulma::Input($inputAttributes);
        $this->elements[] = $this->buildField($input, $label, $elementId);
        return $this;
    }

    protected function buildField(View $element, string $label = '', string $elementId = '', bool $isControl = true): View
    {
        $fieldClasses = [BulmaClass::FIELD];
        if ($this->isHorizontal)
        {
            $fieldClasses[] = BulmaClass::IS_HORIZONTAL;
        }

        if (empty($label))
        {
            if ($isControl)
            {
                $control = Bulma::Control($element);
                return Bulma::Field($control, $fieldClasses);
            }
            return Bulma::Field($element, $fieldClasses);
        }

        $labelAttributes = [];
        if (!empty($elementId))
        {
            $labelAttributes['for'] = (new View($elementId))->escape();
        }
        $escapedLabel = (new View($label))->escape();
        $labelElement = new View(HTML::label($escapedLabel, $labelAttributes));

        if ($this->isHorizontal)
        {
            $labelDiv = new View(HTML::div($labelElement, ['class' => BulmaClass::FIELD_LABEL . ' ' . BulmaClass::IS_NORMAL]));
            $controlElement = $isControl ? Bulma::Control($element) : $element;
            $bodyDiv = new View(HTML::div($controlElement, ['class' => BulmaClass::FIELD_BODY]));
            return Bulma::Field(View::concat($labelDiv, $bodyDiv), $fieldClasses);
        }
        else
        {
            $controlElement = $isControl ? Bulma::Control($element) : $element;
            return Bulma::Field(View::concat($labelElement, $controlElement), $fieldClasses);
        }
    }

    protected function processAttributes(array $attributes): array
    {
        $processed = [];
        foreach ($attributes as $key => $value)
        {
            if (is_numeric($key))
            {
                // Boolean attribute (e.g., disabled, required)
                $processed[(new View($value))->escape()->__toString()] = true;
            }
            else
            {
                // Key-value attribute - escape both key and value
                $escapedKey = (new View($key))->escape()->__toString();
                $escapedValue = (new View($value))->escape()->__toString();
                $processed[$escapedKey] = $escapedValue;
            }
        }
        return $processed;
    }

    protected function generateElementId(string $name): string
    {
        $escapedFormId = (new View($this->formId))->escape()->__toString();
        $escapedName = (new View($name))->escape()->__toString();
        return $escapedFormId . '_' . $escapedName;
    }

    public function text(string $name, string $label = '', string $placeholder = '', string $value = '', array $attributes = []): self
    {
        return $this->createInputElement('text', $name, $label, $placeholder, $value, $attributes);
    }

    public function password(string $name, string $label = '', string $placeholder = '', string $value = '', array $attributes = []): self
    {
        return $this->createInputElement('password', $name, $label, $placeholder, $value, $attributes);
    }

    public function email(string $name, string $label = '', string $placeholder = '', string $value = '', array $attributes = []): self
    {
        return $this->createInputElement('email', $name, $label, $placeholder, $value, $attributes);
    }

    public function number(string $name, string $label = '', string $placeholder = '', string $value = '', array $attributes = []): self
    {
        return $this->createInputElement('number', $name, $label, $placeholder, $value, $attributes);
    }

    public function tel(string $name, string $label = '', string $placeholder = '', string $value = '', array $attributes = []): self
    {
        return $this->createInputElement('tel', $name, $label, $placeholder, $value, $attributes);
    }

    public function url(string $name, string $label = '', string $placeholder = '', string $value = '', array $attributes = []): self
    {
        return $this->createInputElement('url', $name, $label, $placeholder, $value, $attributes);
    }

    public function search(string $name, string $label = '', string $placeholder = '', string $value = '', array $attributes = []): self
    {
        return $this->createInputElement('search', $name, $label, $placeholder, $value, $attributes);
    }

    public function date(string $name, string $label = '', string $placeholder = '', string $value = '', array $attributes = []): self
    {
        return $this->createInputElement('date', $name, $label, $placeholder, $value, $attributes);
    }

    public function time(string $name, string $label = '', string $placeholder = '', string $value = '', array $attributes = []): self
    {
        return $this->createInputElement('time', $name, $label, $placeholder, $value, $attributes);
    }

    public function datetime(string $name, string $label = '', string $placeholder = '', string $value = '', array $attributes = []): self
    {
        return $this->createInputElement('datetime-local', $name, $label, $placeholder, $value, $attributes);
    }

    public function color(string $name, string $label = '', string $placeholder = '', string $value = '', array $attributes = []): self
    {
        return $this->createInputElement('color', $name, $label, $placeholder, $value, $attributes);
    }

    public function range(string $name, string $label = '', string $placeholder = '', string $value = '', array $attributes = []): self
    {
        return $this->createInputElement('range', $name, $label, $placeholder, $value, $attributes);
    }

    public function file(string $name, string $label = '', string $placeholder = '', $value = '', array $attributes = []): self
    {
        return $this->createInputElement('file', $name, $label, $placeholder, $value, $attributes);
    }

    public function hidden(string $name, string $value = '', array $attributes = []): self
    {
        $elementId = $this->generateElementId($name);
        $inputAttributes = array_merge([
            'type' => 'hidden',
            'name' => (new View($name))->escape()->__toString(),
            'id' => $elementId,
            'value' => (new View($value))->escape()->__toString()
        ], $this->processAttributes($attributes));

        $input = Bulma::Input($inputAttributes);
        $this->elements[] = $input;
        return $this;
    }

    public function textarea(string $name, string $label = '', string $placeholder = '', string $value = '', array $attributes = []): self
    {
        $elementId = $this->generateElementId($name);
        $textareaAttributes = array_merge([
            'name' => (new View($name))->escape()->__toString(),
            'id' => $elementId,
            'placeholder' => (new View($placeholder))->escape()->__toString()
        ], $this->processAttributes($attributes));

        $escapedValue = (new View($value))->escape()->__toString();
        $textarea = Bulma::Textarea($escapedValue, $textareaAttributes);
        $this->elements[] = $this->buildField($textarea, $label, $elementId);
        return $this;
    }

    public function select(string $name, string $label = '', string $placeholder = '', array $options = [], array $attributes = [], string $selected_key = ''): self
    {
        $elementId = $this->generateElementId($name);
        $selectAttributes = array_merge([
            'name' => (new View($name))->escape()->__toString(),
            'id' => $elementId,
            'class' => 'select'
        ], $this->processAttributes($attributes));

        $optionsHtml = '';
        if (!empty($placeholder))
        {
            $escapedPlaceholder = (new View($placeholder))->escape()->__toString();
            $optionsHtml .= '<option value="" disabled selected>' . $escapedPlaceholder . '</option>';
        }

        foreach ($options as $key => $value)
        {
            $escapedKey = (new View($key))->escape()->__toString();
            $escapedValue = (new View($value))->escape()->__toString();
            $selectedAttr = ($selected_key !== '' && $selected_key == $key) ? ' selected' : '';
            $optionsHtml .= '<option value="' . $escapedKey . '"' . $selectedAttr . '>' . $escapedValue . '</option>';
        }

        // Direct HTML select element creation
        $selectHtml = '<div class="select is-fullwidth"><select name="' . $selectAttributes['name'] . '" id="' . $elementId . '">' . $optionsHtml . '</select></div>';
        $selectElement = new View($selectHtml);
        $this->elements[] = $this->buildField($selectElement, $label, $elementId);
        return $this;
    }

    protected function createCheckboxRadioElement(string $type, string $name, string $label, string $value, array $attributes = []): self
    {
        $elementId = $this->generateElementId($name . ($type === 'radio' ? '_' . $value : ''));
        $inputAttributes = array_merge([
            'type' => $type,
            'name' => (new View($name))->escape()->__toString(),
            'id' => $elementId,
            'value' => (new View($value))->escape()->__toString()
        ], $this->processAttributes($attributes));

        $element = $type === 'checkbox'
            ? Bulma::Checkbox((new View($label))->escape()->__toString(), $inputAttributes)
            : Bulma::Radio((new View($label))->escape()->__toString(), $inputAttributes);

        $this->elements[] = $this->buildField($element, '', $elementId, false);
        return $this;
    }

    public function checkbox(string $name, string $label = '', string $placeholder = '', string $value = '1', array $attributes = []): self
    {
        return $this->createCheckboxRadioElement('checkbox', $name, $label, $value, $attributes);
    }

    public function radio(string $name, string $label = '', string $placeholder = '', string $value = '', array $attributes = []): self
    {
        return $this->createCheckboxRadioElement('radio', $name, $label, $value, $attributes);
    }

    protected function createButtonElement(string $text, string $type = 'button', array $attributes = [], array $bulmaClasses = []): self
    {
        $buttonAttributes = array_merge([
            'type' => $type
        ], $this->processAttributes($attributes));

        $button = Bulma::Button((new View($text))->escape()->__toString(), $bulmaClasses, $buttonAttributes);
        $this->elements[] = $this->buildField($button, '', '', false);
        return $this;
    }

    public function button(string $text, array $attributes = []): self
    {
        return $this->createButtonElement($text, 'button', $attributes, []);
    }

    public function submit(string $text = 'Submit', array $attributes = []): self
    {
        return $this->createButtonElement($text, 'submit', $attributes, [BulmaClass::IS_PRIMARY]);
    }

    public function reset(string $text = 'Reset', array $attributes = []): self
    {
        return $this->createButtonElement($text, 'reset', $attributes, [BulmaClass::IS_LIGHT]);
    }

    public function view(View $view): self
    {
        $this->elements[] = $view;
        return $this;
    }

    public function testEscape(): string
    {
        $dangerous = '<script>alert("XSS")</script>';
        $escaped = (new View($dangerous))->escape()->__toString();

        return "ESCAPE TEST:<br>" .
            "Original: " . htmlspecialchars($dangerous) . "<br>" .
            "Escaped: " . htmlspecialchars($escaped) . "<br>" .
            "Working: " . ($escaped === '&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;' ? 'YES' : 'NO');
    }

    public function __toString(): string
    {
        $formAttributes = array_merge([
            'id' => $this->formId,
            'action' => $this->action,
            'method' => $this->method
        ], $this->processAttributes($this->attributes));

        $formClasses = [];
        if ($this->isHorizontal)
        {
            $formClasses[] = BulmaClass::IS_HORIZONTAL;
        }

        if (!empty($formClasses))
        {
            $formAttributes['class'] = implode(' ', $formClasses);
        }

        $formContent = View::concat(...$this->elements);
        return (string)HTML::form($formContent, $formAttributes);
    }
}
